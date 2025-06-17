<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.edit-profile';
    protected static ?string $navigationLabel = 'Edit Profile';
    protected static ?int $navigationSort = 1;

    public $name;
    public $email;
    public $lokasi;
    public $photo;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $user = auth()->user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'lokasi' => $user->lokasi,
            'photo' => $user->photo,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(1)->schema([
                TextInput::make('name')->label('Nama')->required(),
                TextInput::make('email')->label('Email')->email()->required(),
                TextInput::make('lokasi')->label('Lokasi'),
                FileUpload::make('photo')->label('Foto Profil')->image()->directory('profile-photos')->maxSize(2048),
                TextInput::make('password')->label('Password Baru')->password()->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)->dehydrated(fn($state) => filled($state)),
                TextInput::make('password_confirmation')->label('Konfirmasi Password')->password(),
            ]),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        $user = auth()->user();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->lokasi = $data['lokasi'];
        if (!empty($data['photo'])) {
            $user->photo = $data['photo'];
        }
        if (!empty($data['password'])) {
            if ($data['password'] === $data['password_confirmation']) {
                $user->password = $data['password'];
            } else {
                Notification::make()->title('Password dan konfirmasi tidak sama!')->danger()->send();
                return;
            }
        }
        $user->save();
        Notification::make()->title('Profil berhasil diperbarui!')->success()->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
} 