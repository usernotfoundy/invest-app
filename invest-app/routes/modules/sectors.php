<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SectorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ChildController;
use App\Http\Controllers\Encoding\DataInputController;
use App\Http\Controllers\Website\WebsiteController;

Route::middleware(['auth:sanctum', 'role:admin'])->group(
    function () {
        Route::post('/create-sector', [SectorController::class, 'store'])
            ->name('create-sector');
        Route::delete('/delete-sector/{id}', [SectorController::class, 'destroy'])
            ->name('delete-sector');
        Route::get('/show-sector/{id}', [SectorController::class, 'show'])
            ->name('show-sector');
        Route::get('/get-sectors', [SectorController::class, 'index'])
            ->name('get-sectors');
        Route::put('/update-sector/{id}', [SectorController::class, 'update'])
            ->name('update-sector');
    }
);