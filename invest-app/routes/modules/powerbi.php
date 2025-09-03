<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SectorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ChildController;
use App\Http\Controllers\Encoding\DataInputController;
use App\Http\Controllers\Website\WebsiteController;

Route::middleware('guest')->group(
    function () {
        Route::get('/powerbi/data/{name}', [WebsiteController::class, 'data'])
            ->name('power-data');
        Route::get('/powerbi/get-data', [WebsiteController::class, 'getData'])
            ->name('powerbi-get-data');
    }
);