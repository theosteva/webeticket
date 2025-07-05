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
                // Kirim notifikasi ke semua user terkait ticket
                $notifiedUserIds = [];
                // Pembuat ticket
                if ($this->record->user) {
                    $this->record->user->notify(
                        $data['status'] === 'Resolved' && $this->record->status !== 'Resolved'
                            ? new \App\Notifications\TicketResolvedNotification($this->record)
                            : new \App\Notifications\TicketUpdatedNotification($this->record)
                    );
                    $notifiedUserIds[] = $this->record->user->id;
                }
                // Semua user division terkait
                foreach ($this->record->divisions as $division) {
                    foreach ($division->users as $user) {
                        if (!in_array($user->id, $notifiedUserIds)) {
                            $user->notify(
                                $data['status'] === 'Resolved' && $this->record->status !== 'Resolved'
                                    ? new \App\Notifications\TicketResolvedNotification($this->record)
                                    : new \App\Notifications\TicketUpdatedNotification($this->record)
                            );
                            $notifiedUserIds[] = $user->id;
                        }
                    }
                }
            }
        }
        return $data;
    }

    public function isFormDisabled(): bool
    {
        return strtolower(trim($this->record->status)) !== 'ticket dibuat';
    }
}
