<?php

use App\Http\Controllers\Ticket\TicketController;
use App\Http\Controllers\Ticket\TicketInteractionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tickets', TicketController::class);
    Route::get('tickets/{ticket}/interactions', [TicketInteractionController::class, 'index']);
    Route::post('tickets/{ticket}/interactions', [TicketInteractionController::class, 'store']);
});
