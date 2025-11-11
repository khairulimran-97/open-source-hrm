<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('New Employee')
                ->url(fn (): string => EmployeeResource::getUrl('create'))
                ->icon('heroicon-o-plus'),
        ];
    }
}
