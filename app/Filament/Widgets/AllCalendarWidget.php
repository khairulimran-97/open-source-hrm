<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AllCalendarWidget extends CalendarWidget
{
    protected static ?string $title = 'Staff Holiday Calendar';

    protected static ?int $sort = 2;

    protected bool $eventDragEnabled = false;

    protected bool $dateClickEnabled = false;

    protected bool $eventClickEnabled = true;

    protected bool $eventResizeEnabled = false;

    public function viewEventAction(): ViewAction
    {
        return $this->viewAction()
            ->icon('heroicon-o-eye')
            ->schema([
                TextEntry::make('employee.full_name')
                    ->label('Employee'),
                TextEntry::make('leave_type')
                    ->label('Leave Type')
                    ->badge()
                    ->color(fn ($record) => match ($record->leave_type) {
                        'Sick Leave' => 'danger',
                        'Vacation' => 'primary',
                        'Personal Leave' => 'warning',
                        'Maternity Leave' => 'pink',
                        'Paternity Leave' => 'info',
                        'Bereavement Leave' => 'gray',
                        default => 'success',
                    }),
                Grid::make(2)
                    ->schema([
                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->date(),
                        TextEntry::make('end_date')
                            ->label('End Date')
                            ->date(),
                    ]),
                TextEntry::make('duration')
                    ->label('Total Duration')
                    ->suffix(' days'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'secondary',
                    }),
                TextEntry::make('notes')
                    ->label('Notes')
                    ->default('N/A')
                    ->columnSpan('full'),
            ]);
    }

    public function getEventClickContextMenuActions(): array
    {
        return [
            $this->viewEventAction(),
        ];
    }

    public function getEvents(FetchInfo $info): array|Collection|Builder
    {
        $start = $info->start;
        $end = $info->end;

        return Leave::query()
            ->with('employee')
            ->where('status', 'Approved')
            ->where(function ($query) use ($start, $end) {
                $query
                    ->orWhereBetween('end_date', [$start, $end])
                    ->whereBetween('start_date', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('start_date', '<', $start)
                            ->where('end_date', '>', $end);
                    });
            });
    }
}
