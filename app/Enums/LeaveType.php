<?php

namespace App\Enums;

enum LeaveType: string
{
    case SickLeave = 'sickLeave';
    case Vacation = 'vacation';
    case PersonalLeave = 'personalLeave';
    case MaternityLeave = 'maternityLeave';
    case PaternityLeave = 'paternityLeave';
    case TimeOff = 'timeOff';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SickLeave => 'Sick Leave',
            self::Vacation => 'Vacation',
            self::PersonalLeave => 'Personal Leave',
            self::MaternityLeave => 'Maternity Leave',
            self::PaternityLeave => 'Paternity Leave',
            self::TimeOff => 'Time Off',
            self::Other => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SickLeave => 'danger',
            self::Vacation => 'primary',
            self::PersonalLeave => 'warning',
            self::MaternityLeave => 'pink',
            self::PaternityLeave => 'info',
            self::TimeOff => 'gray',
            self::Other => 'success',
        };
    }

    public function calendarColor(): string
    {
        return match ($this) {
            self::SickLeave => '#ef4444',
            self::Vacation => '#3b82f6',
            self::PersonalLeave => '#8b5cf6',
            self::MaternityLeave => '#ec4899',
            self::PaternityLeave => '#06b6d4',
            self::TimeOff => '#1f2937',
            self::Other => '#10b981',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
