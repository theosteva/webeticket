<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketCategoryResource\Pages;
use App\Filament\Resources\TicketCategoryResource\RelationManagers;
use App\Models\TicketCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketCategoryResource extends Resource
{
    protected static ?string $model = TicketCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Kategori')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\Select::make('tipe')
                ->label('Jenis Laporan')
                ->options([
                    'incident' => 'Incident',
                    'request' => 'Request',
                ])
                ->required(),
            Forms\Components\Select::make('urgensi')
                ->label('Tingkat Urgensi')
                ->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ])
                ->required(),
            Forms\Components\TextInput::make('sla_hours')
                ->label('SLA (jam)')
                ->numeric()
                ->minValue(1)
                ->maxValue(168)
                ->helperText('Maksimal waktu penyelesaian tiket dalam jam untuk kategori ini.')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama Kategori'),
            Tables\Columns\TextColumn::make('tipe')->label('Jenis Laporan')->formatStateUsing(fn($state) => ucfirst($state))->sortable(),
            Tables\Columns\BadgeColumn::make('urgensi')->label('Urgensi')
                ->colors([
                    'success' => 'low',
                    'warning' => 'medium',
                    'danger' => 'high',
                ]),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketCategories::route('/'),
            'create' => Pages\CreateTicketCategory::route('/create'),
            'edit' => Pages\EditTicketCategory::route('/{record}/edit'),
        ];
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Manage Tickets';
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        $permissions = $user->getAllPermissions();
        foreach ($permissions as $permission) {
            $resources = $permission->resource ?? [];
            if (is_string($resources)) $resources = [$resources];
            if (in_array('TicketCategory', $resources)) {
                return true;
            }
        }
        return false;
    }
}
