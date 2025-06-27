<x-dynamic-component
    :component="static::isSimple() ? 'filament-panels::page.simple' : 'filament-panels::page'"
>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 py-8">
        @if (session('success'))
            <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50">
                <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-bounce">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        <form id="form" wire:submit="save" class="bg-white border-2 border-blue-300 rounded-2xl shadow-2xl p-8 w-full max-w-lg transition-all duration-300 hover:shadow-3xl">
            <div class="flex flex-col items-center mb-8" x-data="{ photoPreview: null }">
                <label for="photo-upload" class="relative cursor-pointer group">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" alt="Preview" class="w-28 h-28 rounded-full shadow border-4 border-blue-200 object-cover transition-transform duration-300 group-hover:scale-105">
                    </template>
                    <template x-if="!photoPreview">
                        @if(auth()->user()->photo)
                            <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Avatar" class="w-28 h-28 rounded-full shadow border-4 border-blue-200 object-cover transition-transform duration-300 group-hover:scale-105">
                        @else
                            <div class="w-28 h-28 rounded-full bg-gradient-to-br from-blue-300 to-purple-300 flex items-center justify-center text-4xl text-white mb-2 transition-transform duration-300 group-hover:scale-105">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </template>
                    <div class="absolute bottom-2 right-2 bg-blue-500 text-white rounded-full p-2 shadow-lg opacity-80 group-hover:opacity-100 transition-all duration-200">
                        <i class="fas fa-camera"></i>
                    </div>
                    <input id="photo-upload" name="photo" type="file" class="hidden" @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = e => photoPreview = e.target.result; reader.readAsDataURL(file); }">
                </label>
                <span class="font-semibold text-xl text-gray-700 mt-2">{{ auth()->user()->name }}</span>
            </div>
            <div class="space-y-6">
                <div class="space-y-4 pb-4 border-b-2 border-blue-200">
                    <div class="relative">
                        <label class="block text-sm font-bold text-blue-600 mb-1">Nama</label>
                        <span class="absolute left-3 top-9 text-blue-400"><i class="fas fa-user"></i></span>
                        <input name="name" value="{{ auth()->user()->name }}" required class="w-full pl-10 py-2 rounded-lg border border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" />
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-bold text-purple-600 mb-1">Email</label>
                        <span class="absolute left-3 top-9 text-purple-400"><i class="fas fa-envelope"></i></span>
                        <input name="email" type="email" value="{{ auth()->user()->email }}" required class="w-full pl-10 py-2 rounded-lg border border-purple-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition-all" />
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-bold text-pink-600 mb-1">Lokasi</label>
                        <span class="absolute left-3 top-9 text-pink-400"><i class="fas fa-map-marker-alt"></i></span>
                        <input name="lokasi" value="{{ auth()->user()->lokasi }}" class="w-full pl-10 py-2 rounded-lg border border-pink-200 focus:border-pink-500 focus:ring-2 focus:ring-pink-100 transition-all" />
                    </div>
                </div>
                <div class="space-y-4 pt-4">
                    <div class="relative">
                        <label class="block text-sm font-bold text-indigo-600 mb-1">Password Baru</label>
                        <span class="absolute left-3 top-9 text-indigo-400"><i class="fas fa-lock"></i></span>
                        <input name="password" type="password" autocomplete="new-password" class="w-full pl-10 py-2 rounded-lg border border-indigo-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all" />
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-bold text-blue-400 mb-1">Konfirmasi Password</label>
                        <span class="absolute left-3 top-9 text-blue-300"><i class="fas fa-lock"></i></span>
                        <input name="password_confirmation" type="password" autocomplete="new-password" class="w-full pl-10 py-2 rounded-lg border border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" />
                    </div>
                </div>
                <div class="pt-6">
                    <button type="submit" class="w-full py-3 rounded-lg bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 text-white text-lg font-bold shadow-lg transition-all duration-300 hover:from-pink-500 hover:to-blue-500 hover:scale-105">Simpan</button>
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</x-dynamic-component>
