<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'body',
        'type',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($comment) {
            \App\Models\TicketLog::create([
                'ticket_id' => $comment->ticket_id,
                'user_id' => $comment->user_id,
                'action' => 'commented',
                'description' => 'Komentar ditambahkan',
            ]);
        });
    }
} 