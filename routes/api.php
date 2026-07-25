<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\ZoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::post('/auth/token', [AuthTokenController::class, 'issue'])
        ->middleware('throttle:api-token')
        ->name('auth.token');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/revoke', [AuthTokenController::class, 'revoke'])->name('auth.revoke');

        Route::middleware('abilities:bookings')->group(function () {
            Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
            Route::get('/bookings/{orderNumber}', [BookingController::class, 'show'])->name('bookings.show');
        });

        Route::middleware('abilities:contacts')->group(function () {
            Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        });

        Route::middleware('abilities:vehicles')->group(function () {
            Route::apiResource('vehicles', VehicleController::class);
        });

        Route::middleware('abilities:tours')->group(function () {
            Route::apiResource('tours', TourController::class);
        });

        Route::middleware('abilities:zones')->group(function () {
            Route::get('/zones', [ZoneController::class, 'index'])->name('zones.index');
        });
    });
});
