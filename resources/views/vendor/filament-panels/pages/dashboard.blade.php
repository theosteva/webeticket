<x-filament-panels::page class="fi-dashboard-page">
    <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-200 rounded-2xl shadow flex flex-col items-center justify-center py-8 px-6">
            <div class="flex flex-col items-center mb-4 w-full">
                @if(auth()->user()->photo)
                    <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border-4 border-blue-200 shadow mb-2">
                @else
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center text-3xl text-white shadow mb-2">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
                <div class="text-lg font-bold text-gray-800 flex items-center justify-center w-full">
                    <i class="fas fa-id-badge mr-2 text-blue-500"></i> {{ auth()->user()->name }}
                </div>
            </div>
            <div class="flex flex-col gap-2 w-full mt-2">
                <div class="flex items-center text-left">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-sm font-semibold">
                        <i class="fas fa-users mr-1"></i>
                        <span>Divisi:</span>
                        <span class="ml-1 font-normal">
                            @if(auth()->user()->divisions && auth()->user()->divisions->count())
                                {{ auth()->user()->divisions->pluck('name')->join(', ') }}
                            @else
                                -
                            @endif
                        </span>
                    </span>
                </div>
                <div class="flex items-center text-left">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-sm font-semibold">
                        <i class="fas fa-user-shield mr-1"></i>
                        <span>Role:</span>
                        <span class="ml-1 font-normal">
                            @if(auth()->user()->roles && auth()->user()->roles->count())
                                {{ auth()->user()->roles->pluck('name')->join(', ') }}
                            @else
                                -
                            @endif
                        </span>
                    </span>
                </div>
                <div class="flex items-center text-left">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-pink-50 text-pink-700 text-sm font-semibold">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        <span>Lokasi:</span>
                        <span class="ml-1 font-normal">{{ auth()->user()->lokasi ?? '-' }}</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl shadow flex flex-col justify-between py-8 px-6 col-span-1">
            <div class="flex items-center mb-4">
                <i class="fas fa-bullhorn text-2xl text-yellow-500 mr-3"></i>
                <span class="text-lg font-bold text-yellow-800">Pengumuman / Informasi Penting</span>
            </div>
            <div class="text-sm text-gray-900 space-y-3">
                @php
                    $announcements = \App\Models\Announcement::where('is_active', true)->orderByDesc('created_at')->limit(5)->get();
                @endphp
                @forelse($announcements as $announcement)
                    <div class="rounded-lg px-4 py-2 mb-2 bg-yellow-50 border-l-4 @if($announcement->type=='update') border-blue-400 @elseif($announcement->type=='warning') border-red-400 @else border-yellow-400 @endif">
                        <div class="font-semibold mb-1">
                            @if($announcement->type=='update')
                                <i class="fas fa-sync-alt text-blue-400 mr-1"></i>
                                Update Sistem
                            @elseif($announcement->type=='warning')
                                <i class="fas fa-exclamation-triangle text-red-400 mr-1"></i>
                                Warning
                            @else
                                <i class="fas fa-info-circle text-yellow-400 mr-1"></i>
                                Info
                            @endif
                            : {{ $announcement->title }}
                        </div>
                        <div>{!! nl2br(e($announcement->content)) !!}</div>
                    </div>
                @empty
                    <div class="text-gray-400 italic">Belum ada pengumuman aktif.</div>
                @endforelse
            </div>
        </div>
    </div>

    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
