<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\SectorController;

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
        Route::put('/admin/update-thumbnail', [SectorController::class, 'updateThumbnail'])
            ->name('admin.updateThumbnail');
    }
);