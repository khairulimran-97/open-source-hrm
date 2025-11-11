<?php

namespace App\Filament\Employee\Resources\Leaves\Schemas;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeaveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('leave_type')
                    ->options([
                        'Sick Leave' => 'Sick Leave',
                        'Vacation' => 'Vacation',
                        'Personal Leave' => 'Personal Leave',
                        'Maternity Leave' => 'Maternity Leave',
                        'Paternity Leave' => 'Paternity Leave',
                        'Bereavement Leave' => 'Bereavement Leave',
                        'Other' => 'Other',
                    ])
                    ->required()
                    ->label('Leave Type')
                    ->live(),
                DatePicker::make('start_date')
                    ->required()
                    ->minDate(now())
                    ->label('Start Date')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::generateDailyDurations($state, $get('end_date'), $set);
                    }),
                DatePicker::make('end_date')
                    ->required()
                    ->minDate(fn ($get) => $get('start_date') ?? now())
                    ->label('End Date')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::generateDailyDurations($get('start_date'), $state, $set);
                    }),
                Repeater::make('daily_durations')
                    ->label('Daily Duration Details')
                    ->schema([
                        TextInput::make('date')
                            ->label('Date')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('day')
                            ->label('Day')
                            ->disabled()
                            ->dehydrated(),
                        Select::make('duration')
                            ->label('Duration')
                            ->options([
                                'Full Day' => 'Full Day',
                                'Half Day - Morning' => 'Half Day - Morning',
                                'Half Day - Evening' => 'Half Day - Evening',
                            ])
                            ->default('Full Day')
                            ->required(),
                    ])
                    ->columns(3)
                    ->reorderable(false)
                    ->addable(false)
                    ->deletable(false)
                    ->columnSpan('full')
                    ->hidden(fn ($get) => ! $get('start_date') || ! $get('end_date')),
                Textarea::make('notes')
                    ->nullable()
                    ->columnSpan('full')
                    ->rows(3)
                    ->label('Reason / Notes'),
            ]);
    }

    protected static function generateDailyDurations($startDate, $endDate, $set): void
    {
        if (! $startDate || ! $endDate) {
            return;
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($end->lt($start)) {
            return;
        }

        $period = CarbonPeriod::create($start, $end);
        $dailyDurations = [];

        foreach ($period as $date) {
            $dailyDurations[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'duration' => 'Full Day',
            ];
        }

        $set('daily_durations', $dailyDurations);
    }
}
