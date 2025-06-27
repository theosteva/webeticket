<?php

namespace App\Filament\Resources\TicketLogResource\Pages;

use App\Filament\Resources\TicketLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketLog extends EditRecord
{
    protected static string $resource = TicketLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
