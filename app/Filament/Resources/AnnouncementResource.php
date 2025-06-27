<?php

namespace App\Filament\Resources;

use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Pengumuman';
    protected static ?string $navigationGroup = 'Informasi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('Judul')
                ->required(),
            Forms\Components\Textarea::make('content')
                ->label('Isi Pengumuman')
                ->rows(4)
                ->required(),
            Forms\Components\Select::make('type')
                ->label('Tipe')
                ->options([
                    'info' => 'Info',
                    'update' => 'Update',
                    'warning' => 'Warning',
                ])
                ->default('info')
                ->required(),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
            Tables\Columns\TextColumn::make('type')->label('Tipe')->badge(),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i'),
        ])->filters([
            // Tambahkan filter jika perlu
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AnnouncementResource\Pages\ListAnnouncements::route('/'),
            'create' => AnnouncementResource\Pages\CreateAnnouncement::route('/create'),
            'edit' => AnnouncementResource\Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        $permissions = $user->getAllPermissions();
        foreach ($permissions as $permission) {
            $resources = $permission->resource ?? [];
            if (is_string($resources)) $resources = [$resources];
            if (in_array('Announcement', $resources)) {
                return true;
            }
        }
        return false;
    }
} 