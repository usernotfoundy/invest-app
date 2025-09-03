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
        Route::post('/create-sector-child', [SectorChildController::class, 'createChild'])
            ->name('create-sector-child');
        Route::post('/sector-child/clear-data', [SectorChildController::class, 'clearData'])
            ->name('clear-data-child-sector');
        Route::get('/sector-child/view-data/{id}', [SectorChildController::class, 'viewChildData'])
            ->name('view-data-child-sector');
        Route::get('/sector-children/{id}', [SectorChildController::class, 'listChildren'])
            ->name('sector-children');
        Route::delete('/sector-child/delete', [SectorChildController::class, 'destroyChild'])
            ->name('delete-sector-child');
        Route::patch('/sector-child/update-template', [SectorChildController::class, 'updateTemplate'])
            ->name('update-template-child-sector');
        Route::get('/sector-child/view-template', [SectorChildController::class, 'viewDataTemplate'])
            ->name('view-template-child-sector');
    }
);