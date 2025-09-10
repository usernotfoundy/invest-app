<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PowerBI\PowerBIController;

Route::middleware('guest')->group(
    function () {
        Route::get('/powerbi/get-data', [PowerBIController::class, 'getData'])
            ->name('powerbi-get-data');
        Route::get('/powerbi/{childId}', [PowerBIController::class, 'getData'])
            ->name('powerbi-get-data');
    }
);