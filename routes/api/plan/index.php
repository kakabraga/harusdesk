<?php

use App\Http\Controllers\Plan\PlanController;
use Illuminate\Support\Facades\Route;



Route::prefix('plans')
    ->name('plans.')
    ->group(function () {

        Route::post('/', [PlanController::class, 'store']);
        Route::delete('/{id}', [PlanController::class, 'destroy']);
    });