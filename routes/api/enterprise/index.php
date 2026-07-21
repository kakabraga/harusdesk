<?php

use App\Http\Controllers\Enterprise\EnterpriseController;
use Illuminate\Support\Facades\Route;


Route::prefix('enterprises')->name('enterprises.')->group(function () {

    Route::post('/', [EnterpriseController::class, 'register']);

});