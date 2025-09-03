<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SectorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ChildController;
use App\Http\Controllers\Encoding\DataInputController;
use App\Http\Controllers\Website\WebsiteController;

Route::middleware(['auth:sanctum', 'role:agency'])->group(
    function () {
        Route::post('/encoding/data-input', [DataInputController::class, 'addChildData'])
            ->name('data-input');
        Route::patch('/encoding/update-row', [DataInputController::class, 'updateChildData'])
            ->name('data-input-update');
        Route::get('/encoding/view-children/{sector_id}', [DataInputController::class, 'viewSectorChildren'])
            ->name('viewSectorChildren');
        Route::get('/encoding/view-child-data/{child_id}', [DataInputController::class, 'viewChildData'])
            ->name('view-child-data');
        Route::get('/encoding/download-template/{child_id}', [DataInputController::class, 'downloadChildTemplate'])
            ->name('child-template-download');
        Route::post('/encoding/upload-template', [DataInputController::class, 'uploadChildData'])
            ->name('upload-template');
        Route::delete('/encoding/delete-row', [DataInputController::class, 'deleteChildData'])
            ->name('encoder.delete-row');

    }
);