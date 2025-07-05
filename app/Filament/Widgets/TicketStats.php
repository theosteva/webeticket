<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class TicketStats extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // Cek apakah user memiliki role Operator atau Supervisor
        $allowedRoles = ['Operator', 'Supervisor'];
        $userRoles = $user->roles->pluck('name')->toArray();
        
        return !empty(array_intersect($allowedRoles, $userRoles));
    }

    protected function getStats(): array
    {
        // Query untuk tiket yang melebihi batas waktu SLA
        $overdueTickets = Ticket::whereHas('divisions') // Hanya tiket yang sudah di-assign
            ->whereNotIn('status', ['Selesai', 'Ditutup']) // Tidak termasuk tiket yang sudah selesai atau ditutup
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

        return [
            Stat::make('Total Tiket Masuk', Ticket::count())
                ->description('Semua tiket yang telah dibuat')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),
            Stat::make('Ticket Diterima', Ticket::where('status', 'Ticket Diterima')->count())
                ->description('Tiket yang telah diterima untuk diproses')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning'),
            Stat::make('Tiket Dalam Proses', Ticket::where('status', 'Dalam Proses')->count())
                ->description('Tiket yang sedang dalam proses penanganan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Tiket Selesai', Ticket::where('status', 'Selesai')->count())
                ->description('Tiket yang telah selesai ditangani')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Tiket Belum Di-assign', Ticket::whereDoesntHave('divisions')->count())
                ->description('Tiket yang belum ditugaskan ke divisi')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('Tiket Melebihi SLA', $overdueTickets)
                ->description('Tiket yang sudah melebihi batas waktu SLA')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
            Stat::make('Tiket Ditutup', Ticket::where('status', 'Ditutup')->count())
                ->description('Tiket yang telah ditutup')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('gray'),
        ];
    }
}
