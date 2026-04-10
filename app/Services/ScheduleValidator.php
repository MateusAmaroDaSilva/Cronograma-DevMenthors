<?php

namespace App\Services;

use App\Models\Person;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\ScheduleEntry;
use App\Models\Substitute;

class ScheduleValidator
{
    /**
     * Validate if a person can be placed at a specific slot
     */
    public function validatePlacement(Schedule $schedule, Person $person, int $day, string $classId): array
    {
        $class = SchoolClass::find($classId);

        if (!$class) {
            return ['valid' => false, 'reason' => 'Turma não encontrada'];
        }

        // Rule 1: Mentors cannot teach advanced classes
        if ($person->isMentor() && $class->isAdvanced()) {
            return ['valid' => false, 'reason' => 'Mentorados não podem dar aula em turmas avançadas'];
        }

        // Rule 2: Person already allocated on this day (in any class)
        $existingEntry = ScheduleEntry::where('schedule_id', $schedule->id)
            ->where('person_id', $person->id)
            ->where('day_index', $day)
            ->first();

        if ($existingEntry) {
            return ['valid' => false, 'reason' => 'Já alocado(a) neste sábado'];
        }

        // Rule 3: Check class capacity BY TYPE (not total count)
        $composition = $class->getRequiredComposition();

        if ($person->isProfessor()) {
            $profCount = ScheduleEntry::where('schedule_id', $schedule->id)
                ->where('class_id', $classId)
                ->where('day_index', $day)
                ->whereHas('person', fn($q) => $q->where('type', 'professor'))
                ->count();

            if ($profCount >= $composition['professors']) {
                return ['valid' => false, 'reason' => 'Limite de professores atingido para esta turma'];
            }
        } else {
            $mentorCount = ScheduleEntry::where('schedule_id', $schedule->id)
                ->where('class_id', $classId)
                ->where('day_index', $day)
                ->whereHas('person', fn($q) => $q->where('type', 'mentor'))
                ->count();

            if ($mentorCount >= $composition['mentors']) {
                return ['valid' => false, 'reason' => 'Limite de mentorados atingido para esta turma'];
            }
        }

        // Rule 4: Mentors cannot teach on consecutive Saturdays (check BOTH directions)
        if ($person->isMentor()) {
            // Check previous day
            if ($day > 0) {
                $previousDayEntry = ScheduleEntry::where('schedule_id', $schedule->id)
                    ->where('person_id', $person->id)
                    ->where('day_index', $day - 1)
                    ->first();

                if ($previousDayEntry) {
                    return ['valid' => false, 'reason' => 'Mentorados não podem dar aula em sábados consecutivos'];
                }
            }
            // Check next day (bidirectional)
            if ($day < 3) {
                $nextDayEntry = ScheduleEntry::where('schedule_id', $schedule->id)
                    ->where('person_id', $person->id)
                    ->where('day_index', $day + 1)
                    ->first();

                if ($nextDayEntry) {
                    return ['valid' => false, 'reason' => 'Mentorados não podem dar aula em sábados consecutivos'];
                }
            }
        }

        // Rule 5: Everyone must have at least 1 day off (max 3 days in a month)
        $allocatedDays = ScheduleEntry::where('schedule_id', $schedule->id)
            ->where('person_id', $person->id)
            ->distinct('day_index')
            ->count('day_index');

        if ($allocatedDays >= 3) {
            return ['valid' => false, 'reason' => 'Pessoa já tem muitas alocações. Mínimo 1 dia de folga por mês'];
        }

        return ['valid' => true];
    }

    /**
     * Validate substitute placement
     */
    public function validateSubstitutePlacement(Schedule $schedule, Person $person, int $day, string $classId): array
    {
        $class = SchoolClass::find($classId);

        if (!$class) {
            return ['valid' => false, 'reason' => 'Turma não encontrada'];
        }

        // Substitute must not be in the main schedule for this day
        $mainEntry = ScheduleEntry::where('schedule_id', $schedule->id)
            ->where('person_id', $person->id)
            ->where('day_index', $day)
            ->first();

        if ($mainEntry) {
            return ['valid' => false, 'reason' => 'Esta pessoa já está no cronograma principal para este sábado'];
        }

        // Mentors cannot be substitutes for advanced classes
        if ($person->isMentor() && $class->isAdvanced()) {
            return ['valid' => false, 'reason' => 'Mentorados não podem ser suplentes em turmas avançadas'];
        }

        // Person cannot be substitute in multiple classes for the same day
        $existingSubstitute = Substitute::where('schedule_id', $schedule->id)
            ->where('person_id', $person->id)
            ->where('day_index', $day)
            ->first();

        if ($existingSubstitute) {
            return ['valid' => false, 'reason' => 'Pessoa já é suplente em outra turma neste sábado'];
        }

        return ['valid' => true];
    }
}
