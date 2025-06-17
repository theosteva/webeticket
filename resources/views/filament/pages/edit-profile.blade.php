<x-filament::page>
    <form wire:submit.prevent="submit" class="space-y-4 max-w-lg mx-auto">
        {{ $this->form }}
        <x-filament::button type="submit" color="primary">Simpan</x-filament::button>
    </form>
</x-filament::page> 