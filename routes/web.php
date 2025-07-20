<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketStatsExportController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/ticket/{ticket}/add-comment', [
    App\Http\Controllers\Controller::class, 'addComment'
])->name('ticket.addComment');

Route::get('/export-ticket-stats', [
    TicketStatsExportController::class, 'exportTicketStats'
])->middleware('auth')->name('ticket.exportStats');
