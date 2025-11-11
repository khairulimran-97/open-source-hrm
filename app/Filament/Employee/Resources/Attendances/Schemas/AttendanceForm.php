<?php

namespace App\Filament\Employee\Resources\Attendances\Schemas;

use App\Models\Shift;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shift_id')
                    ->options(function () {
                        return Shift::all()->pluck('name', 'id');
                    })
                    ->preload()
                    ->label('Shift')
                    ->searchable()
                    ->required(),
                TimePicker::make('clock_in')
                    ->required()
                    ->label('Clock In Time')
                    ->default(now())
                    ->seconds(false),
                TimePicker::make('clock_out')
                    ->label('Clock Out Time')
                    ->seconds(false),
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->maxLength(255)
                    ->nullable()
                    ->autosize()
                    ->columnSpanFull(),
            ]);
    }
}
