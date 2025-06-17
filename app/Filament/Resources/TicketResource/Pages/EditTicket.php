<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Notifications\TicketUpdatedNotification;
use App\Notifications\TicketResolvedNotification;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Cek status resolved
        if (isset($this->record) && isset($data['status'])) {
            if ($data['status'] === 'Resolved' && $this->record->status !== 'Resolved') {
                $this->record->user->notify(new TicketResolvedNotification($this->record));
            } else {
                $this->record->user->notify(new TicketUpdatedNotification($this->record));
            }
        }
        return $data;
    }
}
