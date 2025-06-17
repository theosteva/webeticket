<x-filament-panels::page>
    <form method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4 max-w-lg mx-auto">
            <x-filament::input name="name" label="Nama" :value="auth()->user()->name" required />
            <x-filament::input name="email" label="Email" type="email" :value="auth()->user()->email" required />
            <x-filament::input name="lokasi" label="Lokasi" :value="auth()->user()->lokasi" />
            <x-filament::file-upload name="photo" label="Foto Profil" />
            <x-filament::input name="password" label="Password Baru" type="password" autocomplete="new-password" />
            <x-filament::input name="password_confirmation" label="Konfirmasi Password" type="password" autocomplete="new-password" />
            <x-filament::button type="submit" color="primary">Simpan</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
