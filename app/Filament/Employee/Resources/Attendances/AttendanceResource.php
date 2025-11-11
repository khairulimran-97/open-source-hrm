<?php

namespace App\Filament\Employee\Resources\Attendances;

use App\Filament\Employee\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Employee\Resources\Attendances\Schemas\AttendanceForm;
use App\Filament\Employee\Resources\Attendances\Schemas\AttendanceTable;
use App\Models\Attendance;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Work space';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'My Attendance';

    protected static ?string $modelLabel = 'Attendance';

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
        ];
    }
}
