<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Notifications\TicketUpdatedNotification;
use App\Notifications\TicketResolvedNotification;
use Filament\Notifications\Notification;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('batalkan_ticket')
                ->label('Batalkan Ticket')
                ->color('danger')
                ->visible(fn($action) => $action->getRecord() && $action->getRecord()->status !== 'Dibatalkan')
                ->action(function () {
                    $this->record->status = 'Dibatalkan';
                    $this->record->save();
                    // Log pembatalan
                    \App\Models\TicketLog::create([
                        'ticket_id' => $this->record->id,
                        'user_id' => auth()->id(),
                        'action' => 'cancelled',
                        'description' => 'Ticket dibatalkan',
                    ]);
                    Notification::make()
                        ->title('Ticket berhasil dibatalkan.')
                        ->success()
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Cek status resolved
        if (isset($this->record) && isset($data['status'])) {
            if ($data['status'] !== $this->record->status) {
                // Log perubahan status
                \App\Models\TicketLog::create([
                    'ticket_id' => $this->record->id,
                    'user_id' => auth()->id(),
                    'action' => 'status_changed',
                    'description' => 'Status diubah menjadi ' . $data['status'],
                ]);
            }
            if ($data['status'] === 'Resolved' && $this->record->status !== 'Resolved') {
                $this->record->user->notify(new TicketResolvedNotification($this->record));
            } else {
                $this->record->user->notify(new TicketUpdatedNotification($this->record));
            }
        }
        return $data;
    }

    public function isFormDisabled(): bool
    {
        return $this->record->status === 'Dalam Proses';
    }
}
