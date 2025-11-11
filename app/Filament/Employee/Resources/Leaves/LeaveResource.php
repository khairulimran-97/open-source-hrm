<?php

namespace App\Filament\Employee\Resources\Leaves;

use App\Filament\Employee\Resources\Leaves\Pages\ListLeaves;
use App\Filament\Employee\Resources\Leaves\Schemas\LeaveForm;
use App\Filament\Employee\Resources\Leaves\Schemas\LeaveTable;
use App\Models\Leave;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Work space';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'My Leaves';

    protected static ?string $modelLabel = 'Leave Request';

    public static function form(Schema $schema): Schema
    {
        return LeaveForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveTable::configure($table);
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
            'index' => ListLeaves::route('/'),
        ];
    }
}
