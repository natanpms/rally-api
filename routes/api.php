<?php

use App\Http\Controllers\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [UserController::class, 'store'])
            ->middleware('throttle:register')
            ->name('user.store');

        Route::post('/login', [UserController::class, 'login'])
            ->middleware('throttle:login')
            ->name('user.login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [UserController::class, 'show'])->name('user.show');
        Route::put('/user', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::get('/test', function () {
        return response()->json(['message' => 'API is working!']);
    })->name('test');
});
