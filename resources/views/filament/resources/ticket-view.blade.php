@extends('filament::page')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h2 class="text-2xl font-bold mb-4">Detail Tiket</h2>
    <div class="bg-white rounded shadow p-4 mb-6">
        <div class="mb-2"><span class="font-semibold">Nomor Tiket:</span> {{ $ticket->nomor_tiket }}</div>
        <div class="mb-2"><span class="font-semibold">Kategori:</span> {{ $ticket->kategori }}
        @php
            $kategori = \App\Models\TicketCategory::where('name', $ticket->kategori)->first();
        @endphp
        @if($kategori)
            <span class="ml-2 text-xs text-purple-700 font-semibold">({{ ucfirst($kategori->tipe) }})</span>
        @endif
        </div>
        <div class="mb-2"><span class="font-semibold">Tipe:</span> {{ ucfirst($ticket->tipe) }}</div>
        <div class="mb-2"><span class="font-semibold">Status:</span> {{ $ticket->status }}</div>
        <div class="mb-2"><span class="font-semibold">Deskripsi:</span> {{ $ticket->deskripsi }}</div>
        <div class="mb-2"><span class="font-semibold">Lampiran:</span>
            @if($ticket->lampiran)
                <a href="{{ asset('storage/' . $ticket->lampiran) }}" target="_blank" class="text-blue-600 underline">Lihat Lampiran</a>
            @else
                <span class="text-gray-500">Tidak ada</span>
            @endif
        </div>
        <div class="mb-2"><span class="font-semibold">Tanggal Dibuat:</span> {{ $ticket->created_at->format('d-m-Y H:i') }}</div>
        <div class="mb-2"><span class="font-semibold">Nama Pelapor:</span> {{ $ticket->user->name ?? '-' }}</div>
    </div>

    {{-- Komentar --}}
    <div class="bg-white rounded shadow p-4 mb-6">
        <h3 class="font-semibold mb-2">Komentar</h3>
        <div class="space-y-3 max-h-60 overflow-y-auto mb-4">
            @forelse($ticket->comments as $comment)
                <div class="bg-gray-50 border rounded p-2">
                    <div class="flex items-center mb-1">
                        <span class="font-semibold text-blue-700 text-xs mr-2">{{ $comment->user->name ?? 'User' }}</span>
                        <span class="text-gray-400 text-xs">{{ $comment->created_at->format('d-m-Y H:i') }}</span>
                    </div>
                    <div class="text-gray-800">{{ $comment->body }}</div>
                </div>
            @empty
                <div class="text-gray-500">Belum ada komentar.</div>
            @endforelse
        </div>
        @if(session('error'))
            <div class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-3 py-2 rounded mb-2">
                {{ session('success') }}
            </div>
        @endif
        @if(auth()->id() === $ticket->user_id || auth()->user()?->can('manage tickets'))
        <form method="POST" action="{{ route('ticket.addComment', $ticket->id) }}">
            @csrf
            <textarea name="body" rows="2" class="w-full border rounded px-3 py-2 text-gray-800 focus:ring focus:ring-primary-200 focus:outline-none mb-2" placeholder="Tulis komentar..."></textarea>
            <button type="submit" class="px-4 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded text-sm">Kirim</button>
        </form>
        @endif
    </div>

    <a href="{{ route('filament.admin.resources.tickets.index') }}" class="text-blue-600 underline">&larr; Kembali ke daftar tiket</a>
</div>
@endsection 