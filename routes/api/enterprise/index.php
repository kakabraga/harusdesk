<?php

use App\Http\Controllers\Enterprise\EnterpriseController;
use Illuminate\Support\Facades\Route;


Route::prefix('enterprise')->name('enterprise.')->group(function () {

    Route::post('/', [EnterpriseController::class, 'register']);

});