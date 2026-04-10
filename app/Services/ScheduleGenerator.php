<?php

namespace App\Services;

use App\Models\Person;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\ScheduleEntry;
use Illuminate\Support\Collection;

use App\Models\Substitute;

class ScheduleGenerator
{
    protected ScheduleValidator $validator;

    public function __construct(ScheduleValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Generate a complete schedule for the given month/year
     */
    public function generateSchedule(Schedule $schedule): array
    {
        // Clear existing entries for this schedule
        ScheduleEntry::where('schedule_id', $schedule->id)->delete();
        Substitute::where('schedule_id', $schedule->id)->delete();

        $classes = SchoolClass::all();
        $professors = Person::where('type', 'professor')->get();
        $mentors = Person::where('type', 'mentor')->get();

        $warnings = [];

        // 1. Fill advanced classes first (they're more restrictive — professors only)
        foreach (range(0, 3) as $day) {
            foreach ($classes->where('level', 'advanced') as $class) {
                $required = $class->getRequiredComposition();
                $placed = 0;
                $maxAttempts = $professors->count() * 2;
                $attempts = 0;

                while ($placed < $required['professors'] && $attempts < $maxAttempts) {
                    $attempts++;
                    $professor = $this->selectBestPerson($schedule, $professors, $day, $class->id);

                    if (!$professor) {
                        $warnings[] = "Sem professores disponíveis para {$class->name} no Sábado " . ($day + 1);
                        break;
                    }

                    $validation = $this->validator->validatePlacement($schedule, $professor, $day, $class->id);
                    if ($validation['valid']) {
                        ScheduleEntry::create([
                            'schedule_id' => $schedule->id,
                            'class_id' => $class->id,
                            'person_id' => $professor->id,
                            'day_index' => $day,
                        ]);
                        $placed++;
                    }
                }
            }
        }

        // 2. Fill beginner classes (1 professor + 2 mentors)
        foreach (range(0, 3) as $day) {
            foreach ($classes->where('level', 'beginner') as $class) {
                $required = $class->getRequiredComposition();

                // Place 1 professor
                $professor = $this->selectBestPerson($schedule, $professors, $day, $class->id);
                if ($professor) {
                    $validation = $this->validator->validatePlacement($schedule, $professor, $day, $class->id);
                    if ($validation['valid']) {
                        ScheduleEntry::create([
                            'schedule_id' => $schedule->id,
                            'class_id' => $class->id,
                            'person_id' => $professor->id,
                            'day_index' => $day,
                        ]);
                    } else {
                        $warnings[] = "Não foi possível alocar professor em {$class->name} no Sábado " . ($day + 1);
                    }
                }

                // Place 2 mentors
                $placed = 0;
                $maxAttempts = $mentors->count() * 2;
                $attempts = 0;

                while ($placed < $required['mentors'] && $attempts < $maxAttempts) {
                    $attempts++;
                    $mentor = $this->selectBestPerson($schedule, $mentors, $day, $class->id);

                    if (!$mentor) {
                        $warnings[] = "Sem mentorados disponíveis para {$class->name} no Sábado " . ($day + 1);
                        break;
                    }

                    $validation = $this->validator->validatePlacement($schedule, $mentor, $day, $class->id);
                    if ($validation['valid']) {
                        ScheduleEntry::create([
                            'schedule_id' => $schedule->id,
                            'class_id' => $class->id,
                            'person_id' => $mentor->id,
                            'day_index' => $day,
                        ]);
                        $placed++;
                    }
                }
            }
        }

        // 3. Generate Substitutes
        foreach (range(0, 3) as $day) {
            foreach ($classes as $class) {
                // Determine candidate pool for substitutes
                $pool = $class->level === 'advanced' ? $professors : $professors->merge($mentors);
                
                // Select 1 substitute per class per day
                $substitute = $this->selectBestSubstitute($schedule, $pool, $day, $class->id);
                
                if ($substitute) {
                    Substitute::create([
                        'schedule_id' => $schedule->id,
                        'class_id' => $class->id,
                        'person_id' => $substitute->id,
                        'day_index' => $day,
                    ]);
                }
            }
        }

        // Always return success — show what was generated even if partially filled
        return [
            'success' => true,
            'warnings' => $warnings,
        ];
    }

    /**
     * Select the best person for substitute (balanced allocation)
     */
    protected function selectBestSubstitute(Schedule $schedule, Collection $people, int $day, string $classId): ?Person
    {
        $available = $people->filter(function (Person $person) use ($schedule, $day, $classId) {
            $validation = $this->validator->validateSubstitutePlacement($schedule, $person, $day, $classId);
            return $validation['valid'];
        });

        if ($available->isEmpty()) {
            return null;
        }

        // Sort by total combined allocation (schedule + substitute) to keep it fair
        return $available->sortBy(function (Person $person) use ($schedule) {
            $mainCount = ScheduleEntry::where('schedule_id', $schedule->id)->where('person_id', $person->id)->count();
            $subCount = Substitute::where('schedule_id', $schedule->id)->where('person_id', $person->id)->count();
            return $mainCount + $subCount;
        })->first();
    }

    /**
     * Select the best person for placement (balanced allocation with randomness)
     */
    protected function selectBestPerson(Schedule $schedule, Collection $people, int $day, string $classId): ?Person
    {
        // Filter to only valid placements
        $available = $people->filter(function (Person $person) use ($schedule, $day, $classId) {
            $validation = $this->validator->validatePlacement($schedule, $person, $day, $classId);
            return $validation['valid'];
        });

        if ($available->isEmpty()) {
            return null;
        }

        // Sort by current allocation count (ascending) for balanced distribution
        $sorted = $available->sortBy(function (Person $person) use ($schedule) {
            return ScheduleEntry::where('schedule_id', $schedule->id)
                ->where('person_id', $person->id)
                ->distinct('day_index')
                ->count('day_index');
        });

        // Get the minimum allocation count via direct query (not the broken relationship)
        $firstPerson = $sorted->first();
        $minCount = ScheduleEntry::where('schedule_id', $schedule->id)
            ->where('person_id', $firstPerson->id)
            ->distinct('day_index')
            ->count('day_index');

        // Get all candidates with the minimum count
        $candidates = $sorted->filter(function (Person $person) use ($minCount, $schedule) {
            $count = ScheduleEntry::where('schedule_id', $schedule->id)
                ->where('person_id', $person->id)
                ->distinct('day_index')
                ->count('day_index');
            return $count === $minCount;
        });

        // Pick random from best candidates
        return $candidates->random();
    }
}
