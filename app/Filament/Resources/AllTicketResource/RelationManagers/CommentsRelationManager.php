<?php

namespace App\Filament\Resources\AllTicketResource\RelationManagers;

use App\Models\Comment;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Komentar';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('body')
                ->label('Komentar')
                ->required(),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('Oleh'),
            Tables\Columns\TextColumn::make('body')->label('Komentar')->limit(80),
            Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i'),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public function afterCreate($record, $data)
    {
        $ticket = $record->ticket;
        if ($ticket && $ticket->user) {
            $ticket->user->notify(new \App\Notifications\CommentAddedNotification($record));
        }
    }
} 