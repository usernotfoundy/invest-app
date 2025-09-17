<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\InProfileController;

Route::middleware('guest')->group(
    function () {
        Route::get('/inprofile/list', [InProfileController::class, 'index'])
            ->name('inprofile.list');
        Route::post('/inprofile/create', [InProfileController::class, 'store'])
            ->name('inprofile.create');
        Route::delete('/inprofile/delete/{id}', [InProfileController::class, 'destroy'])
            ->name('inprofile.delete');
        Route::get('/inprofile/economicIndicator', [InProfileController::class, 'economicIndicator'])
            ->name('inprofile.economicIndicator');
        Route::get('/inprofile/crimeEfficiency', [InProfileController::class, 'crimeEfficiency'])
            ->name('inprofile.crimeEfficiency');
        Route::get('/inprofile/povertyIncidence', [InProfileController::class, 'povertyIncidence'])
            ->name('inprofile.povertyIncidence');
        Route::get('/inprofile/literacyRate', [InProfileController::class, 'literacyRate'])
            ->name('inprofile.literacyRate');
    }
);