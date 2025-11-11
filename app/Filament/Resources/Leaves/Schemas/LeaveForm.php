<?php

namespace App\Filament\Resources\Leaves\Schemas;

use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeaveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //

                Select::make('employee_id')
                    ->options(function () {
                        return Employee::all()->pluck('full_name', 'id');
                    })
                    ->searchable(
                        [
                            'first_name',
                            'last_name',
                        ]
                    )
                    ->required()
                    ->label('Employee'),
                Select::make('leave_type')
                    ->options(\App\Enums\LeaveType::options())
                    ->required()
                    ->live(),
                DatePicker::make('start_date')
                    ->required()
                    ->label('Start Date')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::generateDailyDurations($state, $get('end_date'), $set);
                    }),
                DatePicker::make('end_date')
                    ->required()
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
                Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ])
                    ->default('Pending')
                    ->required(),
                Textarea::make('rejection_reason')
                    ->nullable()
                    ->columnSpan('full')
                    ->label('Rejection Reason'),
                Textarea::make('notes')
                    ->nullable()
                    ->columnSpan('full')
                    ->label('Notes'),

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
