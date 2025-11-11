<?php

namespace App\Filament\Employee\Resources\Leaves\Pages;

use App\Filament\Employee\Resources\Leaves\LeaveResource;
use Filament\Resources\Pages\ListRecords;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
