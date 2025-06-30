<?php

namespace App\Filament\Resources\AllTicketResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class TicketLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketLogs';
    protected static ?string $title = 'Log Tiket';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d-m-Y H:i'),
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('action')->label('Aksi'),
                Tables\Columns\TextColumn::make('description')->label('Deskripsi')->limit(50),
            ])
            ->defaultSort('created_at', 'desc');
    }
} 