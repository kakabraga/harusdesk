<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->name('users.')->group(function () {

    Route::post('/', [UserController::class, 'store']);
    Route::get('/me', [UserController::class, 'me'])->middleware('auth:sanctum');
    Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('auth:sanctum');

});