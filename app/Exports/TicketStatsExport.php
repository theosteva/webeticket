<?php

namespace App\Exports;

use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TicketStatsExport implements FromView
{
    public function view(): View
    {
        // Data statistik yang sama seperti di widget
        $totalTiket = Ticket::count();
        $tiketDiterima = Ticket::where('status', 'Ticket Diterima')->count();
        $tiketDalamProses = Ticket::where('status', 'Dalam Proses')->count();
        $tiketSelesai = Ticket::where('status', 'Selesai')->count();
        $tiketBelumAssign = Ticket::whereDoesntHave('divisions')->count();
        $tiketDitutup = Ticket::where('status', 'Ditutup')->count();
        $overdueTickets = Ticket::whereHas('divisions')
            ->whereNotIn('status', ['Selesai', 'Ditutup'])
            ->get()
            ->filter(function($ticket) {
                $category = TicketCategory::where('name', $ticket->kategori)->first();
                if (!$category || !$category->sla_hours) {
                    return false;
                }
                $deadline = $ticket->created_at->copy()->addHours($category->sla_hours);
                return now()->gt($deadline);
            })
            ->count();

        return view('exports.ticket-stats', [
            'totalTiket' => $totalTiket,
            'tiketDiterima' => $tiketDiterima,
            'tiketDalamProses' => $tiketDalamProses,
            'tiketSelesai' => $tiketSelesai,
            'tiketBelumAssign' => $tiketBelumAssign,
            'tiketDitutup' => $tiketDitutup,
            'overdueTickets' => $overdueTickets,
        ]);
    }
} 