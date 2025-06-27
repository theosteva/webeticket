<?php

namespace App\Filament\Resources\AllTicketResource\Pages;

use App\Filament\Resources\AllTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Pages\Actions\ButtonAction;

class EditAllTicket extends EditRecord
{
    protected static string $resource = AllTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('editTicket')
                ->label('Edit Ticket')
                ->action('enableEdit')
                ->visible(fn ($livewire) => !$livewire->editEnabled),
            ...parent::getFormActions(),
        ];
    }

    public $editEnabled = false;

    public function enableEdit()
    {
        $this->editEnabled = true;
    }

    public function getEditEnabledState()
    {
        return $this->editEnabled;
    }

    protected function mutateFormSchema(array $schema): array
    {
        if (!$this->editEnabled) {
            foreach ($schema as $component) {
                $this->disableComponentRecursive($component);
            }
        }
        return $schema;
    }

    private function disableComponentRecursive($component)
    {
        if (method_exists($component, 'getChildComponents')) {
            foreach ($component->getChildComponents() as $child) {
                $this->disableComponentRecursive($child);
            }
        }
        if (method_exists($component, 'disabled')) {
            $component->disabled();
        }
    }
}
