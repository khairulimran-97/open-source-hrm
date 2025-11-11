<?php

namespace App\Models;

use Carbon\Carbon;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model implements Eventable
{
    //
    protected $table = 'leave';

    protected $fillable = [
        'employee_id',
        'actioned_by',
        'leave_type',
        'leave_duration',
        'daily_durations',
        'start_date',
        'end_date',
        'status',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'leave_date' => 'datetime:H:i',
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_durations' => 'array',
        'leave_type' => \App\Enums\LeaveType::class,
    ];

    protected $appends = [
        'duration',

    ];

    public function getDurationAttribute()
    {
        // If daily_durations is set, calculate from the detailed breakdown
        if (! empty($this->daily_durations)) {
            $totalDays = 0;

            foreach ($this->daily_durations as $day) {
                if ($day['duration'] === 'Full Day') {
                    $totalDays += 1;
                } elseif (in_array($day['duration'], ['Half Day - Morning', 'Half Day - Evening'])) {
                    $totalDays += 0.5;
                }
            }

            return $totalDays;
        }

        // Fallback to old calculation for backward compatibility
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $days = $start->diffInDays($end) + 1; // Add 1 to include both start and end dates

        // If it's a half day, return 0.5 days
        if ($this->leave_duration === 'Half Day - Morning' || $this->leave_duration === 'Half Day - Evening') {
            return 0.5;
        }

        return $days;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function toCalendarEvent(): CalendarEvent
    {
        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate = Carbon::parse($this->end_date)->endOfDay();

        $title = $this->employee->full_name.' - '.$this->leave_type->label();

        if ($this->leave_duration && $this->leave_duration !== 'Full Day') {
            $title .= ' ('.$this->leave_duration.')';
        }

        return CalendarEvent::make($this)
            ->title($title)
            ->start($startDate)
            ->end($endDate)
            ->allDay(true)
            ->backgroundColor($this->leave_type->calendarColor())
            ->textColor('#ffffff');
    }
}
