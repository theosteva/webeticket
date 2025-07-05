@php
    $user = auth()->user();
    $notifications = $user ? $user->notifications()->orderBy('created_at', 'desc')->limit(10)->get() : collect();
    $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
    $activeNotificationId = request()->get('active_notification');
@endphp

@if($user)
    <div class="relative mr-2">
        <button id="notificationDropdownButton" type="button" class="relative focus:outline-none" onclick="document.getElementById('notificationDropdown').classList.toggle('hidden')">
            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @if($unreadCount > 0)
                <span style="top:0; right:16px;" class="absolute inline-flex items-center justify-center px-1 py-0.5 text-xs font-bold leading-none text-red-600">
                    <span class="absolute inset-0 bg-red-600 rounded-full"></span>
                    <span class="relative z-10">{{ $unreadCount }}</span>
                </span>
            @endif
        </button>
        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-[40rem] bg-white border border-gray-200 rounded-lg shadow-lg z-50">
        <div class="p-4 border-b font-semibold text-gray-800 text-lg">Notifikasi</div>
    <ul class="max-h-[700px] overflow-y-auto">
        @forelse($notifications as $notification)
            <li class="px-5 py-3 hover:bg-gray-100 border-b last:border-b-0 transition-all duration-200 cursor-pointer group"
                onclick="this.classList.toggle('active-notification')"
                @if($activeNotificationId == $notification->id) style="background: #f3f4f6;" @endif
            >
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                    @if(is_null($notification->read_at))
                        <span class="inline-block bg-blue-500 text-white text-sm px-2 py-0.5 rounded">Baru</span>
                    @endif
                </div>
                <div class="text-base text-gray-900 mt-2 leading-relaxed group-[.active-notification]:text-lg group-[.active-notification]:font-bold group-[.active-notification]:py-6 group-[.active-notification]:px-2 group-[.active-notification]:leading-loose">
                    @if(isset($notification->data['kategori']))
                        <span class="font-bold">[Tiket]</span> Status: {{ $notification->data['kategori'] }}
                    @elseif(isset($notification->data['body']))
                        <span class="font-bold">[Komentar]</span> {{ $notification->data['body'] }}
                    @else
                        <span class="font-bold">[Notifikasi]</span> {{ json_encode($notification->data) }}
                    @endif
                </div>
                <a href="{{ route('filament.admin.resources.tickets.edit', $notification->data['id'] ?? '') }}" class="text-blue-700 hover:underline text-sm mt-2 block group-[.active-notification]:text-lg group-[.active-notification]:font-bold">Lihat Detail</a>
            </li>
        @empty
            <li class="px-5 py-3 text-gray-500 text-base">Tidak ada notifikasi.</li>
        @endforelse
    </ul>
</div>

    </div>
@endif
<script>
    document.addEventListener('click', function(event) {
        var dropdown = document.getElementById('notificationDropdown');
        var button = document.getElementById('notificationDropdownButton');
        if (!dropdown || !button) return;
        if (!dropdown.classList.contains('hidden') && !button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>

<style>
.active-notification {
    background: #f3f4f6 !important;
    font-size: 1.25rem !important;
    font-weight: bold !important;
    padding-top: 2rem !important;
    padding-bottom: 2rem !important;
    padding-left: 1rem !important;
    padding-right: 1rem !important;
    line-height: 2 !important;
}
</style>
