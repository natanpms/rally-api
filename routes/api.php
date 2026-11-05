<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:register')
            ->name('register');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:login')
            ->name('login');

        Route::post('/logout', [AuthController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->name('logout');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [UserController::class, 'show'])->name('user.show');
        Route::put('/user', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy');
    });
});
