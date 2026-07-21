<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();

});

Route::prefix('auth')
        ->name('auth.')
        ->group(function () {
                Route::post('/login', [AuthController::class, 'login']);
        });