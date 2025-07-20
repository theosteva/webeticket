<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use Illuminate\Support\Carbon;

class TicketTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Tiket Masuk & Selesai (12 Bulan Terakhir)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $labels = [];
        $dataMasuk = [];
        $dataSelesai = [];

        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->translatedFormat('M Y');
            $dataMasuk[] = Ticket::whereYear('created_at', substr($bulan, 0, 4))
                ->whereMonth('created_at', substr($bulan, 5, 2))
                ->count();
            $dataSelesai[] = Ticket::whereYear('created_at', substr($bulan, 0, 4))
                ->whereMonth('created_at', substr($bulan, 5, 2))
                ->where('status', 'Selesai')
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tiket Masuk',
                    'data' => $dataMasuk,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)', // biru
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'fill' => false,
                ],
                [
                    'label' => 'Tiket Selesai',
                    'data' => $dataSelesai,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)', // hijau
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
} 