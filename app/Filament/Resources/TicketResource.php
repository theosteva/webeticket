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

    protected static ?string $navigationDescription = 'Kelola dan pantau semua tiket pengaduan, permintaan, dan status penanganan secara online.\n\nTiket dengan status "Ditutup" akan dihapus otomatis setelah 30 hari.';

    protected static ?string $navigationGroup = 'E-Ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('kontak')
                        ->label('Kontak HP atau Email')
                        ->required()
                        ->maxLength(100)
                        ->extraAttributes(['class' => 'mb-4 border-green-400 focus:border-green-600'])
                        ->helperText('Masukkan nomor HP atau email yang bisa dihubungi, kami akan mengirim notifikasi update dari laporan anda.')
                        ->disabled(fn($record) => $record && strtolower(trim($record->git)) !== 'ticket dibuat'),
                    Forms\Components\Select::make('tipe')
                        ->label('Jenis Laporan')
                        ->options([
                            'incident' => 'Incident',
                            'request' => 'Request',
                        ])
                        ->required()
                        ->reactive()
                        ->extraAttributes(['class' => 'mb-4 border-purple-300 focus:border-purple-500'])
                        ->disabled(fn($record) => $record && strtolower(trim($record->status)) !== 'ticket dibuat'),
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
                        ->extraAttributes(['class' => 'mb-4 border-indigo-300 focus:border-indigo-500'])
                        ->disabled(fn($record) => $record && strtolower(trim($record->status)) !== 'ticket dibuat'),
                    Forms\Components\TextInput::make('judul')
                        ->label('Judul Laporan (Opsional)')
                        ->maxLength(255)
                        ->extraAttributes(['class' => 'mb-4 border-orange-400 focus:border-orange-600'])
                        ->disabled(fn($record) => $record && strtolower(trim($record->status)) !== 'ticket dibuat'),
                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi Masalah / Permintaan')
                        ->required()
                        ->extraAttributes(['class' => 'mb-4 border-pink-300 focus:border-pink-500'])
                        ->disabled(fn($record) => $record && strtolower(trim($record->status)) !== 'ticket dibuat'),
                    Forms\Components\FileUpload::make('lampiran')
                        ->label('Lampiran (Opsional)')
                        ->disk('public')
                        ->directory('lampiran')
                        ->nullable()
                        ->extraAttributes(['class' => 'mb-4'])
                        ->disabled(fn($record) => $record && strtolower(trim($record->status)) !== 'ticket dibuat'),
                    Forms\Components\Select::make('application_id')
                        ->label('Aplikasi Terkait (Opsional)')
                        ->options(\App\Models\Application::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable()
                        ->disabled(fn($record) => $record && strtolower(trim($record->status)) !== 'ticket dibuat'),
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
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Dibuat')->dateTime(),
                Tables\Columns\TextColumn::make('kategori')->label('Kategori'),
                Tables\Columns\TextColumn::make('tipe')->label('Tipe'),
                Tables\Columns\BadgeColumn::make('status')->label('Status')
                    ->colors([
                        'primary' => fn($state) => strtolower(trim($state)) === 'ticket diterima' || strtolower(trim($state)) === 'ticket dibuat',
                        'success' => fn($state) => strtolower(trim($state)) === 'resolved',
                        'danger' => fn($state) => strtolower(trim($state)) === 'closed',
                        'warning' => fn($state) => strtolower(trim($state)) === 'in progress',
                        'secondary' => fn($state) => strtolower(trim($state)) === 'pending',
                        'info' => fn($state) => strtolower(trim($state)) === 'in review',
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        if (strtolower(trim($state)) === 'ditutup') {
                            return 'Ditutup <span class="text-xs text-red-500">(dihapus otomatis setelah 30 hari)</span>';
                        }
                        return $state;
                    })
                    ->html(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Lihat Detail')
                    ->extraAttributes([
                        'class' => 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded shadow transition-colors',
                    ])
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
            \App\Filament\Resources\TicketResource\RelationManagers\CommentsRelationManager::class,
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
