<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Comment;

class CommentAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Komentar Baru pada Tiket')
            ->greeting('Halo,')
            ->line('Ada komentar baru pada tiket Anda:')
            ->line($this->comment->body)
            ->action('Lihat Tiket', url('/all-tickets/' . $this->comment->ticket_id))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->comment->ticket_id,
            'body' => $this->comment->body,
            'user' => $this->comment->user->name,
        ];
    }
} 