<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Person extends Model
{
    protected $fillable = ['id', 'name', 'type'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'type' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(static function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function scheduleEntries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class);
    }

    public function substitutes(): HasMany
    {
        return $this->hasMany(Substitute::class);
    }

    public function isProfessor(): bool
    {
        return $this->type === 'professor';
    }

    public function isMentor(): bool
    {
        return $this->type === 'mentor';
    }

    /**
     * Get count of distinct days allocated for this person in the given schedule
     */
    public function getAllocationCount($month, $year): int
    {
        return ScheduleEntry::whereHas('schedule', function ($query) use ($month, $year) {
                $query->where('month', $month)->where('year', $year);
            })
            ->where('person_id', $this->id)
            ->distinct('day_index')
            ->count('day_index');
    }
}
