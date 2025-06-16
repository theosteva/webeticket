<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    public function mount(): void
    {
        parent::mount();
        if (static::getModel()::where('user_id', auth()->id())->count() === 0) {
            $this->redirect(TicketResource::getUrl('create'));
        }
    }

    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return static::getModel()::query()->where('user_id', auth()->id());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
