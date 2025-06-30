<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ticket;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class TicketStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Tiket Masuk', Ticket::count()),
            Stat::make('Ticket Diterima', Ticket::where('status', 'Ticket Diterima')->count()),
            Stat::make('Tiket Dalam Proses', Ticket::where('status', 'In Progress')->count()),
            Stat::make('Tiket Selesai', Ticket::where('status', 'Resolved')->count()),
            Stat::make('Ticket Dibuat', Ticket::where('status', 'Ticket Dibuat')->count()),
        ];
    }
}
