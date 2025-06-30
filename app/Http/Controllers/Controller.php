<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    public function addComment(\Illuminate\Http\Request $request, $ticketId)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);
        $ticket = \App\Models\Ticket::findOrFail($ticketId);
        // Cek hak akses: hanya pembuat ticket atau admin
        if (auth()->id() !== $ticket->user_id && !auth()->user()?->can('manage tickets')) {
            return redirect()->back()->with('error', 'Anda tidak diizinkan menambah komentar pada ticket ini.');
        }
        $comment = new \App\Models\Comment();
        $comment->ticket_id = $ticket->id;
        $comment->user_id = auth()->id();
        $comment->body = $request->body;
        $comment->save();

        // Notifikasi
        if (auth()->id() == $ticket->user_id) {
            // Jika pelapor yang komentar, notifikasi ke semua user division yang di-assign
            foreach ($ticket->divisions as $division) {
                foreach ($division->users as $user) {
                    if ($user->id != auth()->id()) {
                        $user->notify(new \App\Notifications\CommentAddedNotification($comment));
                    }
                }
            }
        } else {
            // Jika admin/teknisi yang komentar, notifikasi ke pelapor
            if ($ticket->user && $ticket->user->id != auth()->id()) {
                $ticket->user->notify(new \App\Notifications\CommentAddedNotification($comment));
            }
        }

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
