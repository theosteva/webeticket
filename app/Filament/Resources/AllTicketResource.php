<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AllTicketResource\Pages;
use App\Filament\Resources\AllTicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AllTicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationLabel = 'All Ticket';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'Open' => 'Open',
                    'In Progress' => 'In Progress',
                    'Pending' => 'Pending',
                    'Resolved' => 'Resolved',
                    'Closed' => 'Closed',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('kategori')->label('Kategori'),
                Tables\Columns\TextColumn::make('tipe')->label('Tipe'),
                Tables\Columns\TextColumn::make('deskripsi')->label('Deskripsi'),
                Tables\Columns\TextColumn::make('lampiran')->label('Lampiran')->limit(20),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'Open',
                        'warning' => 'In Progress',
                        'secondary' => 'Pending',
                        'success' => 'Resolved',
                        'danger' => 'Closed'
                    ]),
                Tables\Columns\BadgeColumn::make('kategori_urgensi')
                    ->label('Urgensi')
                    ->getStateUsing(fn($record) => optional(\App\Models\TicketCategory::where('name', $record->kategori)->first())->urgensi)
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Submit')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Tiket')
                    ->color('primary'),
                Tables\Actions\Action::make('detail_ticket')
                    ->label('Detail Ticket')
                    ->url(fn($record) => url('/all-tickets/'.$record->id))
                    ->color('info')
                    ->button(),
                Tables\Actions\Action::make('ubah_status')
                    ->label('Ubah Status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Open' => 'Open',
                                'In Progress' => 'In Progress',
                                'Pending' => 'Pending',
                                'Resolved' => 'Resolved',
                                'Closed' => 'Closed',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, $data) {
                        $record->status = $data['status'];
                        $record->save();
                    })
                    ->color('success')
                    ->button(),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListAllTickets::route('/'),
        ];
    }
}
