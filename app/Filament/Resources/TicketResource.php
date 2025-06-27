<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationDescription = 'Kelola dan pantau semua tiket pengaduan, permintaan, dan status penanganan secara online.';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('nama_pelapor')
                        ->label('Nama Pelapor')
                        ->default(fn() => auth()->user()?->name)
                        ->required()
                        ->extraAttributes(['class' => 'mb-4 border-blue-400 focus:border-blue-600']),
                    Forms\Components\Select::make('tipe')
                        ->label('Jenis Laporan')
                        ->options([
                            'incident' => 'Incident',
                            'request' => 'Request',
                        ])
                        ->required()
                        ->reactive()
                        ->extraAttributes(['class' => 'mb-4 border-purple-300 focus:border-purple-500']),

                    Forms\Components\Select::make('kategori')
                        ->label('Kategori')
                        ->options(function (callable $get) {
                            $tipe = $get('tipe');
                            return $tipe
                                ? \App\Models\TicketCategory::where('tipe', $tipe)->pluck('name', 'name')
                                : [];
                        })
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->extraAttributes(['class' => 'mb-4 border-indigo-300 focus:border-indigo-500']),
                        Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->required()
                        ->extraAttributes(['class' => 'mb-4 border-pink-300 focus:border-pink-500']),
                    Forms\Components\FileUpload::make('lampiran')
                        ->label('Lampiran')
                        ->disk('public')
                        ->directory('lampiran')
                        ->nullable()
                        ->extraAttributes(['class' => 'mb-4']),
                ])->extraAttributes([
                    'class' => 'bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 p-8 rounded-xl shadow-lg'
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_tiket')->label('Nomor Tiket')->sortable(),
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('kategori')->label('Kategori'),
                Tables\Columns\TextColumn::make('tipe')->label('Tipe'),
                Tables\Columns\TextColumn::make('deskripsi')->label('Deskripsi')->limit(40),
                Tables\Columns\TextColumn::make('lampiran')->label('Lampiran')->limit(20),
                Tables\Columns\BadgeColumn::make('status')->label('Status')
                    ->colors([
                        'primary' => fn($state) => strtolower(trim($state)) === 'ticket diterima',
                        'success' => fn($state) => strtolower(trim($state)) === 'resolved',
                        'danger' => fn($state) => strtolower(trim($state)) === 'closed',
                        'warning' => fn($state) => strtolower(trim($state)) === 'in progress',
                        'secondary' => fn($state) => strtolower(trim($state)) === 'pending',
                        'info' => fn($state) => strtolower(trim($state)) === 'in review',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Dibuat')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => strtolower(trim($record->status)) === 'ticket diterima'),
                Tables\Actions\Action::make('cek_status')
                    ->label('Cek Status')
                    ->url(fn($record) => url('/cek-status/'.$record->id))
                    ->color('success'),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'E-Ticket';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-ticket';
    }
}
