<?php

namespace App\Filament\Resources\TicketLogResource\Pages;

use App\Filament\Resources\TicketLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListTicketLogs extends ListRecords
{
    protected static string $resource = TicketLogResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make(),
        ];

        $user = auth()->user();
        $allowedRoles = ['Supervisor'];
        $userRoles = $user?->roles->pluck('name')->toArray() ?? [];
        if (!empty(array_intersect($allowedRoles, $userRoles))) {
            $actions[] = Action::make('downloadStats')
                ->label('Unduh Statistik')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('ticket.exportStats'))
                ->openUrlInNewTab();
        }

        return $actions;
    }
}
