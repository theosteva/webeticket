<x-filament-panels::page>
    <div class="w-full h-full min-h-screen flex flex-col justify-start items-stretch">
        <div class="w-full px-4 pt-8 pb-2">
            <form wire:submit="save" class="space-y-4 w-full">
                <div class="flex flex-col items-center mb-2">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-20 h-20 rounded-full object-cover border border-gray-300 mb-2">
                    @endif
                    <input type="file" wire:model="photo" accept="image/*" class="text-sm text-gray-600 file:bg-gray-50 file:border file:border-gray-300 file:rounded file:px-3 file:py-1 file:text-sm hover:file:bg-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" wire:model.defer="name" required class="w-full border rounded px-3 py-2 text-gray-800 focus:ring focus:ring-primary-200 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model.defer="email" required class="w-full border rounded px-3 py-2 text-gray-800 focus:ring focus:ring-primary-200 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <input type="text" wire:model.defer="lokasi" class="w-full border rounded px-3 py-2 text-gray-800 focus:ring focus:ring-primary-200 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                    <input type="tel" wire:model.defer="nomor_hp" class="w-full border rounded px-3 py-2 text-gray-800 focus:ring focus:ring-primary-200 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" wire:model.defer="password" autocomplete="new-password" class="w-full border rounded px-3 py-2 text-gray-800 focus:ring focus:ring-primary-200 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" wire:model.defer="password_confirmation" autocomplete="new-password" class="w-full border rounded px-3 py-2 text-gray-800 focus:ring focus:ring-primary-200 focus:outline-none" />
                </div>
                <button type="submit" class="w-full py-2 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded transition duration-200">
                    Simpan
                </button>
            </form>
        </div>
    </div>
</x-filament-panels::page>
