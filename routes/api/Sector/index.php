<?php

use App\Http\Controllers\Sector\SectorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('sectors', SectorController::class);
});
