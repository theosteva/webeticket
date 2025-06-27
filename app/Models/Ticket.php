<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'tipe',
        'kategori',
        'deskripsi',
        'nomor_tiket',
        'status',
        'lampiran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function divisions()
    {
        return $this->belongsToMany(\App\Models\Division::class, 'division_ticket');
    }

    protected static function booted()
    {
        static::deleting(function ($ticket) {
            \App\Models\TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'description' => 'Ticket dihapus',
            ]);
        });
        static::updating(function ($ticket) {
            if ($ticket->isDirty('status')) {
                \App\Models\TicketLog::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => auth()->id(),
                    'action' => 'status_changed',
                    'description' => 'Status diubah -> ' . $ticket->status,
                ]);
            }
        });
    }
}
