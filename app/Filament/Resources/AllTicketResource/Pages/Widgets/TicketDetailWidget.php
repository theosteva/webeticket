<?php

namespace App\Filament\Resources\AllTicketResource\Pages\Widgets;

use Filament\Widgets\Widget;

class TicketDetailWidget extends Widget
{
    protected static string $view = 'filament.resources.all-ticket-resource.pages.widgets.ticket-detail-widget';

    public $record;
} 