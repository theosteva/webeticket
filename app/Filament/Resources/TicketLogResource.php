<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketLogResource\Pages;
use App\Filament\Resources\TicketLogResource\RelationManagers;
use App\Models\TicketLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class TicketLogResource extends Resource
{
    protected static ?string $model = TicketLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'E-Ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ticket_id')
                    ->relationship('ticket', 'nomor_tiket')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('action')
                    ->required(),
                Forms\Components\Textarea::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('ticket.nomor_tiket')->label('Nomor Tiket')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('User')->sortable(),
                Tables\Columns\TextColumn::make('action')->sortable(),
                Tables\Columns\TextColumn::make('description')->limit(30)
                    ->badge()
                    ->color(fn($record) => match($record->action) {
                        'created' => 'success',
                        'status_changed' => 'info',
                        'cancelled' => 'danger',
                        'commented' => 'warning',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d-m-Y H:i')->label('Waktu'),
            ])
            ->filters([
                SelectFilter::make('range')
                    ->label('Rentang Waktu')
                    ->options([
                        '30' => '30 Hari Terakhir',
                        '15' => '15 Hari Terakhir',
                        '60' => '60 Hari Terakhir',
                        '90' => '90 Hari Terakhir',
                    ])
                    ->default('30')
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (in_array($value, ['15', '30', '60', '90'])) {
                            $query->where('created_at', '>=', now()->subDays((int)$value));
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTicketLogs::route('/'),
            'create' => Pages\CreateTicketLog::route('/create'),
            'view' => Pages\ViewTicketLog::route('/{record}'),
            'edit' => Pages\EditTicketLog::route('/{record}/edit'),
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
            if (in_array('TicketLog', $resources)) {
                return true;
            }
        }
        return false;
    }
}
