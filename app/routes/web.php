<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

Route::get('/', [TicketController::class, 'index'])
    ->name('tickets.index');

Route::post('/tickets', [TicketController::class, 'store'])
    ->name('tickets.store');

Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
    ->name('tickets.status');   
