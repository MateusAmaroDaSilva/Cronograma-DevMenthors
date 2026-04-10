<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\ScheduleEntry;
use App\Models\Substitute;
use App\Models\Lesson;
use App\Services\ScheduleValidator;
use App\Services\ScheduleGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected ScheduleValidator $validator;
    protected ScheduleGenerator $generator;

    public function __construct(ScheduleValidator $validator, ScheduleGenerator $generator)
    {
        $this->validator = $validator;
        $this->generator = $generator;
    }

    /**
     * Show the schedule board
     */
    public function index(Request $request)
    {
        $now = Carbon::now();
        $month = (int) $request->input('month', $now->month);
        $year = (int) $request->input('year', $now->year);

        // Validate range
        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }
        if ($year < 2020 || $year > 2035) {
            $year = $now->year;
        }

        return $this->loadSchedule($month, $year);
    }

    /**
     * Load schedule for specific month/year
     */
    protected function loadSchedule($month, $year)
    {
        $schedule = Schedule::forMonth($month, $year);
        $classes = SchoolClass::all();
        
        // Pré-calcular alocações para este mês (Fix N+1 e Erro no Blade)
        $allocations = ScheduleEntry::where('schedule_id', $schedule->id)
            ->select('person_id', \Illuminate\Support\Facades\DB::raw('count(distinct day_index) as total'))
            ->groupBy('person_id')
            ->get()
            ->pluck('total', 'person_id');

        $people = Person::all()->each(function($person) use ($allocations) {
            $person->current_allocations = $allocations[$person->id] ?? 0;
        });

        // Build grid data
        $gridData = [];
        $lessonsData = Lesson::where('schedule_id', $schedule->id)->get()->groupBy('class_id');
        
        foreach ($classes as $class) {
            $gridData[$class->id] = [];
            foreach (range(0, 3) as $day) {
                $entries = ScheduleEntry::where('schedule_id', $schedule->id)
                    ->where('class_id', $class->id)
                    ->where('day_index', $day)
                    ->with('person')
                    ->get();

                $substitutes = Substitute::where('schedule_id', $schedule->id)
                    ->where('class_id', $class->id)
                    ->where('day_index', $day)
                    ->with('person')
                    ->get();

                $lesson = $lessonsData->has($class->id) 
                    ? $lessonsData[$class->id]->firstWhere('day_index', $day) 
                    : null;

                $gridData[$class->id][$day] = [
                    'entries' => $entries,
                    'substitutes' => $substitutes,
                    'lesson_name' => $lesson ? $lesson->lesson_name : '',
                ];
            }
        }

        return view('schedule.index', [
            'schedule' => $schedule,
            'classes' => $classes,
            'people' => $people,
            'gridData' => $gridData,
            'currentMonth' => $month,
            'currentYear' => $year,
        ]);
    }

    /**
     * Generate schedule automatically
     */
    public function generate(Request $request)
    {
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $schedule = Schedule::forMonth($month, $year);
        $result = $this->generator->generateSchedule($schedule);

        $message = 'Rodízio gerado com sucesso!';
        if (!empty($result['warnings'])) {
            $message .= ' (com ' . count($result['warnings']) . ' avisos)';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'warnings' => $result['warnings'] ?? [],
        ]);
    }

    /**
     * Clear entire schedule
     */
    public function clear(Request $request)
    {
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $schedule = Schedule::forMonth($month, $year);
        ScheduleEntry::where('schedule_id', $schedule->id)->delete();
        Substitute::where('schedule_id', $schedule->id)->delete();
        Lesson::where('schedule_id', $schedule->id)->delete();

        return response()->json(['success' => true, 'message' => 'Cronograma limpo']);
    }

    /**
     * Assign a person to a schedule slot
     */
    public function assign(Request $request)
    {
        try {
            $validated = $request->validate([
                'person_id' => 'required|uuid|exists:people,id',
                'day' => 'required|integer|between:0,3',
                'class_id' => 'required|string|exists:classes,id',
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer',
            ]);

            $person = Person::find($validated['person_id']);
            $schedule = Schedule::forMonth((int) $validated['month'], (int) $validated['year']);

            $validation = $this->validator->validatePlacement($schedule, $person, $validated['day'], $validated['class_id']);

            if (!$validation['valid']) {
                return response()->json(['valid' => false, 'reason' => $validation['reason']], 422);
            }

            ScheduleEntry::create([
                'schedule_id' => $schedule->id,
                'class_id' => $validated['class_id'],
                'person_id' => $validated['person_id'],
                'day_index' => $validated['day'],
            ]);

            // Remove as substitute if exists
            Substitute::where('schedule_id', $schedule->id)
                ->where('person_id', $validated['person_id'])
                ->where('day_index', $validated['day'])
                ->delete();

            return response()->json(['valid' => true, 'message' => 'Pessoa alocada com sucesso']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['valid' => false, 'reason' => $e->errors()[array_key_first($e->errors())][0] ?? 'Erro de validação'], 422);
        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'reason' => $e->getMessage() ?: 'Erro ao alocar pessoa'], 500);
        }
    }

    /**
     * Remove a person from a schedule slot
     */
    public function remove(Request $request)
    {
        $validated = $request->validate([
            'entry_id' => 'required|integer|exists:schedule_entries,id',
        ]);

        ScheduleEntry::destroy($validated['entry_id']);

        return response()->json(['success' => true, 'message' => 'Removido com sucesso']);
    }

    /**
     * Update lesson name for all entries in a cell (schedule + class + day)
     */
    public function updateLessonName(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|integer|exists:schedules,id',
            'class_id' => 'required|string|exists:classes,id',
            'day_index' => 'required|integer|between:0,3',
            'lesson_name' => 'nullable|string|max:255',
        ]);

        $lesson = \App\Models\Lesson::updateOrCreate(
            [
                'schedule_id' => $validated['schedule_id'],
                'class_id' => $validated['class_id'],
                'day_index' => $validated['day_index'],
            ],
            [
                'lesson_name' => $validated['lesson_name'],
            ]
        );

        return response()->json(['success' => true, 'message' => 'Aula atualizada']);
    }

    /**
     * Change month
     */
    public function changeMonth(Request $request)
    {
        $month = (int) $request->input('month');
        $year = (int) $request->input('year');

        if ($month < 1 || $month > 12) {
            return response()->json(['success' => false], 422);
        }

        return response()->json(['success' => true]);
    }
}
