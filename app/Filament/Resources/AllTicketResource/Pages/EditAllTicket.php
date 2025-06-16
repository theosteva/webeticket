<?php

namespace App\Filament\Resources\AllTicketResource\Pages;

use App\Filament\Resources\AllTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAllTicket extends EditRecord
{
    protected static string $resource = AllTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
