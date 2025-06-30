<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function getViewData(): array
    {
        $this->record->load(['comments.user', 'divisions.users']);
        return [
            'ticket' => $this->record,
        ];
    }
} 