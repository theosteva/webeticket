<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $data['user_id'] = auth()->id();
        // Generate nomor tiket unik
        $prefix = $data['tipe'] === 'incident' ? 'IC' : 'RE';
        $lastId = \App\Models\Ticket::max('id') + 1;
        $data['nomor_tiket'] = $prefix . '-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);
        $data['status'] = 'Ticket Dibuat';
        $ticket = static::getModel()::create($data);
        // Tambah log otomatis
        \App\Models\TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'action' => 'created',
            'description' => 'Ticket dibuat',
        ]);
        return $ticket;
    }
}
