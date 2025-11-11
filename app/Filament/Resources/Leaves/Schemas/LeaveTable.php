<?php

namespace App\Filament\Resources\Leaves\Schemas;

use App\Models\Leave;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeaveTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Leave::query()
                    ->with(['employee'])

                    ->latest()
            )
            ->columns([
                TextColumn::make('employee.employee_number')
                    ->label('Employee No.')
                    ->searchable(

                    )
                    ->sortable(),
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable([
                        'first_name',
                        'last_name',
                    ])
                    ->sortable([
                        'first_name',
                        'last_name',
                    ]),
                TextColumn::make('leave_type')
                    ->label('Leave Type')
                    ->badge()
                    ->color(fn ($state) => $state instanceof \App\Enums\LeaveType ? $state->color() : \App\Enums\LeaveType::from($state)->color())
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\LeaveType ? $state->label() : \App\Enums\LeaveType::from($state)->label())
                    ->searchable(),
                TextColumn::make('leave_duration')
                    ->label('Duration Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Full Day' => 'primary',
                        'Half Day - Morning' => 'warning',
                        'Half Day - Evening' => 'info',
                        default => 'secondary',
                    }),
                TextColumn::make('start_date')
                    ->date()
                    ->label('Start Date'),
                TextColumn::make('end_date')
                    ->date()
                    ->label('End Date'),
                TextColumn::make('duration')
                    ->label('Duration(Days)'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'secondary',
                    })
                    ->label('Status'),
                TextColumn::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->default('N/A')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->default('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
            ])
            ->filters(
                [
                    //
                    SelectFilter::make('employee_id')
                        ->label('Employee')
                        ->searchable()
                        ->options(
                            // Employee::all()->pluck('full_name', 'id')
                            Leave::query()
                                ->with('employee')
                                ->get()
                                ->pluck('employee.full_name', 'employee.id')
                        )
                        ->default(null),
                    SelectFilter::make('status')
                        ->label('Status')
                        ->options([
                            'Pending' => 'Pending',
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
                        ])
                        ->default(null),
                    SelectFilter::make('leave_type')
                        ->label('Leave Type')
                        ->options(\App\Enums\LeaveType::options())
                        ->default(null),

                ]

            )
            ->recordActions([
                ActionGroup::make([

                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                ]),
            ]);

    }
}
