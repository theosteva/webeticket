<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ticket;
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
        return [
            Stat::make('Total Tiket Masuk', Ticket::count())
                ->description('Semua tiket yang telah dibuat')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),
            Stat::make('Tiket Belum Di-assign', Ticket::whereDoesntHave('divisions')->count())
                ->description('Tiket yang belum ditugaskan ke divisi')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('Tiket Sudah Di-assign', Ticket::whereHas('divisions')->count())
                ->description('Tiket yang sudah ditugaskan ke divisi')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
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
            Stat::make('Tiket Ditutup', Ticket::where('status', 'Ditutup')->count())
                ->description('Tiket yang telah ditutup')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('gray'),
        ];
    }
}
