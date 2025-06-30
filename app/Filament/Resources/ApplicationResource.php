<?php

namespace App\Filament\Resources;

use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ApplicationResource\Pages;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationLabel = 'Application List';
    protected static ?string $navigationGroup = 'Manage Tickets';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Aplikasi')
                ->required()
                ->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama Aplikasi')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        $permissions = $user->getAllPermissions();
        foreach ($permissions as $permission) {
            $resources = $permission->resource ?? [];
            if (is_string($resources)) $resources = [$resources];
            if (in_array('Application', $resources)) {
                return true;
            }
        }
        return false;
    }
} 