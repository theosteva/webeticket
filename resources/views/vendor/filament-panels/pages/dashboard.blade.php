<x-filament-panels::page class="fi-dashboard-page">
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    @php
        $user = filament()->auth()->user();
        $roles = $user->getRoleNames()->implode(', ');
        $divisions = $user->divisions->pluck('name')->implode(', ') ?: '-';
    @endphp

    <div class="mb-6 p-4 bg-white rounded-lg shadow flex flex-col gap-2">
        <div><strong>Role:</strong> {{ $roles }}</div>
        <div><strong>Divisi:</strong> {{ $divisions }}</div>
    </div>

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
