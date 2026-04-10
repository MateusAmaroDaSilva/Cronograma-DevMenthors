<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = ['year', 'month'];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class);
    }

    public function substitutes(): HasMany
    {
        return $this->hasMany(Substitute::class);
    }

    /**
     * Get or create a schedule for the given month/year
     */
    public static function forMonth($month, $year)
    {
        return self::firstOrCreate([
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Get number of Saturdays in the month
     */
    public function getSaturdayCount(): int
    {
        return 4; // Fixed to 4 for our system
    }
}
