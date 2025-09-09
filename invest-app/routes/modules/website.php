<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Website\WebsiteController;

Route::middleware('guest')->group(
    function () {
        Route::get('/public/get-sectors', [WebsiteController::class, 'index'])
            ->name('get-sectors');
    }
);