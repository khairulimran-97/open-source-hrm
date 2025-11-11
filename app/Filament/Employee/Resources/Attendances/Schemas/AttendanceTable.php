<?php

namespace App\Filament\Employee\Resources\Attendances\Schemas;

use App\Models\Attendance;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AttendanceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->where('employee_id', Auth::id())
                    ->with(['shift'])
                    ->latest('date')
            )
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->label('Date')
                    ->sortable(),
                TextColumn::make('shift.name')
                    ->label('Shift')
                    ->default('N/A'),
                TextColumn::make('clock_in')
                    ->time('H:i')
                    ->label('Clock In'),
                TextColumn::make('clock_out')
                    ->time('H:i')
                    ->label('Clock Out')
                    ->default('Not clocked out'),
                TextColumn::make('hours')
                    ->numeric(decimalPlaces: 2)
                    ->label('Hours Worked')
                    ->suffix(' hrs'),
                TextColumn::make('remarks')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Remarks'),
            ])
            ->filters([
                Filter::make('date')
                    ->schema([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('to')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['to'], fn ($q, $date) => $q->whereDate('date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn ($record) => ! $record->clock_out),
                ]),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['employee_id'] = Auth::id();
                        $data['date'] = now()->toDateString();

                        return $data;
                    })
                    ->disabled(function () {
                        // Disable if already clocked in today
                        return Attendance::where('employee_id', Auth::id())
                            ->whereDate('date', now())
                            ->whereNull('clock_out')
                            ->exists();
                    }),
            ]);
    }
}
