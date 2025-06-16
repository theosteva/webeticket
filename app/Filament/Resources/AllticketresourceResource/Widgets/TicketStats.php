<?php

namespace App\Filament\Resources\AllticketresourceResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Tiket', \App\Models\Ticket::count()),
            // Tambahkan statistik lain jika perlu
        ];
    }
}
