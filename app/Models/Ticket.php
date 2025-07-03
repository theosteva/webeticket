<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'tipe',
        'kategori',
        'deskripsi',
        'nomor_tiket',
        'status',
        'lampiran',
        'application_id',
        'kontak',
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

    public function ticketLogs()
    {
        return $this->hasMany(\App\Models\TicketLog::class, 'ticket_id');
    }

    public function application()
    {
        return $this->belongsTo(\App\Models\Application::class, 'application_id');
    }

    public function scopeClosedOver30Days($query)
    {
        return $query->where('status', 'Ditutup')->where('updated_at', '<', now()->subDays(30));
    }

    public function scopeShouldBeDeleted($query)
    {
        return $query->where('status', 'Ditutup')->where('updated_at', '<', now()->subDays(30));
    }

    protected static function booted()
    {
        parent::booted();
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
