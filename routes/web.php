<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/ticket/{ticket}/add-comment', [
    App\Http\Controllers\Controller::class, 'addComment'
])->name('ticket.addComment');
