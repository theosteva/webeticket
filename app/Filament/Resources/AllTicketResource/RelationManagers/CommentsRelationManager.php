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
            Forms\Components\Select::make('type')
                ->label('Tipe Komentar')
                ->options([
                    'internal' => 'Internal',
                    'public' => 'Ke Pelapor',
                ])
                ->default('internal')
                ->required(),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('body')
                ->label('Komentar')
                ->formatStateUsing(function ($state, $record) {
                    $user = $record->user;
                    if (!$user) return $state;
                    $divisi = $user->divisions->pluck('name')->join(', ');
                    $role = $user->roles->pluck('name')->join(', ');
                    $foto = $user->photo ? asset('storage/' . $user->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name);
                    $waktu = $record->created_at ? $record->created_at->format('d M Y H:i') : '';
                    $isInternal = $record->type === 'internal';
                    $isPelapor = $record->user_id === optional($record->ticket)->user_id;
                    $colorIndex = $record->id % 2;
                    $bgColor = 'comment-bg-' . $colorIndex;
                    $bgStyle = '';
                    switch ($colorIndex) {
                        case 0: $bgStyle = 'background-color: #fef08a !important;'; break;
                        case 1: $bgStyle = 'background-color: #bae6fd !important;'; break;
                    }
                    $badge = $isInternal
                        ? '<span class="inline-block bg-yellow-400 text-black text-xs font-bold px-2 py-1 rounded mr-2">INTERNAL</span>'
                        : '';
                    return '<div class="flex items-start gap-3 mb-2">
                        <img src="' . $foto . '" class="w-10 h-10 rounded-full border shadow" alt="Foto Profil">
                        <div class="' . $bgColor . ' border rounded-xl px-4 py-2 shadow-sm max-w-xl" style="' . $bgStyle . '">
                            <div class="font-semibold text-blue-700">' . e($user->name) . '</div>
                            <div class="text-xs text-gray-500 mb-1">' . ($divisi ?: '-') . ' | ' . ($role ?: '-') . '</div>
                            ' . $badge . '<span class="text-gray-800">' . nl2br(e($state)) . '</span>
                            <div class="text-xs text-gray-400 mt-1">' . $waktu . '</div>
                        </div>
                    </div>';
                })
                ->html(),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make()
                ->using(function (array $data, $record) {
                    if (!isset($data['ticket_id']) && $this->getOwnerRecord()) {
                        $data['ticket_id'] = $this->getOwnerRecord()->getKey();
                    }
                    $data['user_id'] = auth()->id();
                    return Comment::create($data);
                }),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->modifyQueryUsing(function ($query) {
            $user = auth()->user();
            if (!$user) return $query->whereRaw('0=1');
            if ($user->can('assign ticket')) {
                // Internal bisa lihat semua
                return $query;
            }
            // User pelapor hanya bisa lihat komentar type public dan komentarnya sendiri
            return $query->where(function($q) use ($user) {
                $q->where('type', 'public')
                  ->orWhere('user_id', $user->id);
            });
        });
    }

    public function afterCreate($record, $data)
    {
        $ticket = $record->ticket;
        if ($ticket && $ticket->user && ($record->type === 'public')) {
            $ticket->user->notify(new \App\Notifications\CommentAddedNotification($record));
        }
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
} 