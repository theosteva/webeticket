<div class="grid grid-cols-1 gap-4 text-sm">
    <div class="flex justify-between items-center">
        <span class="font-semibold text-gray-700">ID Ticket:</span>
        <span class="text-gray-900">{{ $ticket->id }}</span>
    </div>
    <div class="flex justify-between items-center">
        <span class="font-semibold text-gray-700">Nomor Tiket:</span>
        <span class="text-gray-900">{{ $ticket->nomor_tiket }}</span>
    </div>
    <div class="flex justify-between items-center">
        <span class="font-semibold text-gray-700">Kategori:</span>
        <span class="text-blue-700 font-medium">
            {{ $ticket->kategori }}
            @php
                $kategori = \App\Models\TicketCategory::where('name', $ticket->kategori)->first();
            @endphp
            @if($kategori)
                <span class="ml-2 text-xs text-purple-700 font-semibold">({{ ucfirst($kategori->tipe) }})</span>
            @endif
        </span>
    </div>
    <div class="flex justify-between items-center">
        <span class="font-semibold text-gray-700">Tipe:</span>
        <span class="text-purple-700 font-medium">{{ ucfirst($ticket->tipe) }}</span>
    </div>
    <div>
        <span class="font-semibold text-gray-700 block mb-1">Deskripsi:</span>
        <div class="bg-gray-50 rounded p-2 text-gray-800 border border-gray-100">{{ $ticket->deskripsi }}</div>
    </div>
    <div>
        <span class="font-semibold text-gray-700 block mb-1">Lampiran:</span>
        @if($ticket->lampiran)
            @if(Str::endsWith(strtolower($ticket->lampiran), ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                <img src="{{ asset('storage/' . $ticket->lampiran) }}" alt="Lampiran" class="max-h-40 rounded shadow border mb-2">
            @endif
            <a href="{{ asset('storage/' . $ticket->lampiran) }}" target="_blank" class="text-blue-600 underline">Lihat Lampiran</a>
        @else
            <span class="text-gray-500">Tidak ada</span>
        @endif
    </div>
    <div class="flex justify-between items-center">
        <span class="font-semibold text-gray-700">Status:</span>
        <span class="px-2 py-1 rounded text-xs font-bold {{
            match(strtolower($ticket->status)) {
                'ticket dibuat' => 'bg-indigo-100 text-indigo-800',
                'ticket diterima' => 'bg-blue-100 text-blue-800',
                'in progress' => 'bg-yellow-100 text-yellow-800',
                'pending' => 'bg-gray-200 text-gray-700',
                'resolved' => 'bg-green-100 text-green-800',
                'closed' => 'bg-red-100 text-red-800',
                default => 'bg-gray-100 text-gray-800',
            }
        }}">
            {{ $ticket->status }}
        </span>
    </div>
    <div class="flex justify-between items-center">
        <span class="font-semibold text-gray-700">Tanggal Dibuat:</span>
        <span class="text-gray-900">{{ $ticket->created_at->format('d-m-Y H:i') }}</span>
    </div>

    {{-- Komentar --}}
    <div class="mt-6">
        <span class="font-semibold text-gray-700 block mb-2">Komentar:</span>
        <div class="space-y-3 max-h-60 overflow-y-auto mb-4">
            @forelse($ticket->comments as $i => $comment)
                <div class="border rounded p-2 comment-bg-{{ $i % 10 }}">
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
        @if(auth()->id() === $ticket->user_id || auth()->user()?->can('manage tickets'))
        <div class="bg-yellow-100 text-yellow-800 px-3 py-2 rounded mb-2">
            Untuk menambah komentar, silakan buka halaman detail ticket.
        </div>
        @endif
    </div>
</div> 