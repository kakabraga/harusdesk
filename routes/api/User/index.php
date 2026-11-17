<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->name('users.')->middleware('auth:sanctum')->group(function () {

    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/me', [UserController::class, 'me'])->name('me');
    Route::post('/{user}/sectors', [UserController::class, 'syncSectors'])->name('syncSectors');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

});
