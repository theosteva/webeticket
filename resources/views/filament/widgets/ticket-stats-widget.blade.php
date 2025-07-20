@php
    $stats = $this->getStats();
    $labels = [
        'Total Tiket Masuk',
        'Ticket Diterima',
        'Tiket Dalam Proses',
        'Tiket Selesai',
        'Tiket Belum Di-assign',
        'Tiket Melebihi SLA',
        'Tiket Ditutup',
    ];
@endphp

<div>
    {{-- Statistik utama (grid) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($stats as $stat)
            <div class="filament-stats-overview-widget-stat p-4 rounded shadow bg-white">
                <div class="text-lg font-bold">{{ $stat->getLabel() }}</div>
                <div class="text-2xl font-extrabold">{{ $stat->getValue() }}</div>
                <div class="text-sm text-gray-500 flex items-center mt-1">
                    @if($stat->getDescriptionIcon())
                        <x-dynamic-component :component="$stat->getDescriptionIcon()" class="w-4 h-4 mr-1" />
                    @endif
                    {{ $stat->getDescription() }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tabel statistik data --}}
    <div class="mt-8">
        <h3 class="text-lg font-bold mb-2">Tabel Statistik Tiket</h3>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">Statistik</th>
                    <th class="px-4 py-2 border-b">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats as $i => $stat)
                    <tr>
                        <td class="px-4 py-2 border-b">{{ $labels[$i] ?? $stat->getLabel() }}</td>
                        <td class="px-4 py-2 border-b font-bold">{{ $stat->getValue() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> 