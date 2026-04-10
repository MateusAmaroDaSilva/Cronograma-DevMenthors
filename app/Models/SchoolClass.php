<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $table = 'classes';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['id', 'name', 'level'];

    protected $casts = [
        'level' => 'string',
    ];

    public function scheduleEntries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class, 'class_id');
    }

    public function isAdvanced(): bool
    {
        return $this->level === 'advanced';
    }

    public function isBeginner(): bool
    {
        return $this->level === 'beginner';
    }

    /**
     * Get required composition for this class:
     * Advanced: 2 professors, 0 mentors
     * Beginner: 1 professor, 2 mentors
     */
    public function getRequiredComposition(): array
    {
        if ($this->isAdvanced()) {
            return ['professors' => 2, 'mentors' => 0];
        }
        return ['professors' => 1, 'mentors' => 2];
    }
}
