<?php

namespace App\Filament\Resources\TicketLogResource\Pages;

use App\Filament\Resources\TicketLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTicketLog extends ViewRecord
{
    protected static string $resource = TicketLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
