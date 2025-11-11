<?php

namespace App\Filament\Employee\Resources\Leaves\Schemas;

use App\Models\Leave;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LeaveTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Leave::query()
                    ->where('employee_id', Auth::id())
                    ->latest()
            )
            ->columns([
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
                    ->label('Start Date')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->label('End Date')
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duration (Days)')
                    ->suffix(' days'),
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
                    ->label('Applied On'),
            ])
            ->filters([
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
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                ]),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['employee_id'] = Auth::id();
                        $data['status'] = 'Pending';

                        return $data;
                    }),
            ]);
    }
}
