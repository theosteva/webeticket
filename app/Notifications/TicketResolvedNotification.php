<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketResolvedNotification extends Notification
{
    use Queueable;

    public $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tiket Selesai/Resolved')
            ->greeting('Halo!')
            ->line('Tiket Anda telah selesai/resolved dengan detail berikut:')
            ->line('Nomor Tiket: ' . $this->ticket->id)
            ->line('Kategori: ' . $this->ticket->kategori)
            ->line('Deskripsi: ' . $this->ticket->deskripsi)
            ->action('Lihat Tiket', url('/admin/tickets/' . $this->ticket->id . '/edit'))
            ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->ticket->id,
            'kategori' => $this->ticket->kategori,
            'deskripsi' => $this->ticket->deskripsi,
        ];
    }
}
