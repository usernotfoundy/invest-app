<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ChildController;

Route::middleware(['auth:sanctum', 'role:admin'])->group(
    function () {
        Route::post('/create-sector-child', [ChildController::class, 'createChild'])
            ->name('create-sector-child');
        Route::post('/sector-child/clear-data', [ChildController::class, 'clearData'])
            ->name('clear-data-child-sector');
        Route::get('/sector-child/view-data/{id}', [ChildController::class, 'viewChildData'])
            ->name('view-data-child-sector');
        Route::get('/sector-children/{id}', [ChildController::class, 'listChildren'])
            ->name('sector-children');
        Route::delete('/sector-child/delete', [ChildController::class, 'destroyChild'])
            ->name('delete-sector-child');
        Route::patch('/sector-child/update-template', [ChildController::class, 'updateTemplate'])
            ->name('update-template-child-sector');
        Route::get('/sector-child/view-template', [ChildController::class, 'viewDataTemplate'])
            ->name('view-template-child-sector');
    }
);