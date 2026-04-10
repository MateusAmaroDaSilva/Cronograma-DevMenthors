<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Schedule;
use App\Models\Substitute;
use App\Services\ScheduleValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubstituteController extends Controller
{
    protected ScheduleValidator $validator;

    public function __construct(ScheduleValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Assign a substitute
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
            $schedule = Schedule::forMonth($validated['month'], $validated['year']);

            $validation = $this->validator->validateSubstitutePlacement($schedule, $person, $validated['day'], $validated['class_id']);

            if (!$validation['valid']) {
                return response()->json(['valid' => false, 'reason' => $validation['reason']], 422);
            }

            Substitute::create([
                'schedule_id' => $schedule->id,
                'class_id' => $validated['class_id'],
                'person_id' => $validated['person_id'],
                'day_index' => $validated['day'],
            ]);

            return response()->json(['valid' => true, 'message' => 'Suplente alocado com sucesso']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['valid' => false, 'reason' => $e->errors()[array_key_first($e->errors())][0] ?? 'Erro de validação'], 422);
        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'reason' => $e->getMessage() ?: 'Erro ao alocar suplente'], 500);
        }
    }

    /**
     * Remove a substitute
     */
    public function remove(Request $request)
    {
        $validated = $request->validate([
            'substitute_id' => 'required|integer|exists:substitutes,id',
        ]);

        Substitute::destroy($validated['substitute_id']);

        return response()->json(['success' => true, 'message' => 'Suplente removido com sucesso']);
    }
}
