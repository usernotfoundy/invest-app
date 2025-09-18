<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\UserController;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/user/profile', [UserController::class, 'userProfile'])
            ->name('user-profile');
    }
);

Route::middleware(['auth:sanctum', 'role:admin'])->group(
    function () {
        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->name('register');
        Route::post('/assign-role/{userId}/{roleName}', [UserController::class, 'setUserRole'])
            ->name('assign-role');
        Route::post('/user/assign-sector', [UserController::class, 'assignSectorToUser'])
            ->name('assign-sector');
        Route::get('/user-roles/{id}', [UserController::class, 'getUserRoles'])
            ->name('user-roles');
        Route::delete('/delete-user/{id}', [UserController::class, 'deleteUser'])
            ->name('delete-user');
        Route::get('/user-count', [UserController::class, 'getUserCounts'])
            ->name('user-count');
        Route::get('/get-users', [UserController::class, 'getUsers'])
            ->name('get-users');
        Route::get('/roles', [UserController::class, 'viewRoles'])
            ->name('view-roles');
        Route::get('/logs', [UserController::class, 'getLogs'])
            ->name('logs');
    }
);
