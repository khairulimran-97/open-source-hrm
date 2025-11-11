<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type',
        'allowed_days',
        'year',
        'is_enabled',
        'notes',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'allowed_days' => 'decimal:1',
        'leave_type' => \App\Enums\LeaveType::class,
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getUsedDaysAttribute()
    {
        return Leave::where('employee_id', $this->employee_id)
            ->where('leave_type', $this->leave_type)
            ->where('status', 'Approved')
            ->whereYear('start_date', $this->year)
            ->get()
            ->sum('duration');
    }

    public function getRemainingDaysAttribute()
    {
        return max(0, $this->allowed_days - $this->used_days);
    }

    public static function checkLeaveEligibility($employeeId, $leaveType, $requestedDays, $year = null)
    {
        $year = $year ?? now()->year;

        $policy = static::where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->where('year', $year)
            ->first();

        if (! $policy) {
            return [
                'allowed' => false,
                'message' => 'No leave policy found for this leave type.',
            ];
        }

        if (! $policy->is_enabled) {
            return [
                'allowed' => false,
                'message' => 'This leave type is currently disabled for you.',
            ];
        }

        if ($policy->remaining_days < $requestedDays) {
            return [
                'allowed' => false,
                'message' => "Insufficient leave balance. You have {$policy->remaining_days} days remaining out of {$policy->allowed_days} allowed days.",
            ];
        }

        return [
            'allowed' => true,
            'remaining' => $policy->remaining_days,
            'allowed_days' => $policy->allowed_days,
        ];
    }
}
