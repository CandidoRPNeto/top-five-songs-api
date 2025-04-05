<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::middleware('auth:api')->prefix('/request')->group(function () {
    Route::post('/', [RequestController::class, 'send'])->name('request.send');
    Route::middleware('admin')->get('/', [RequestController::class, 'index'])->name('request.index');
    Route::middleware('admin')->prefix('/{request_id}')->group(function () {
        Route::patch('/accept', [RequestController::class, 'acceptRequest'])->name('request.accept');
        Route::patch('/refuse', [RequestController::class, 'refuseRequest'])->name('request.refuse');
    });
});
Route::prefix('/songs')->group(function () {
    Route::get('/index', [SongController::class, 'index'])->name('songs.index');
    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::post('/', [SongController::class, 'store'])->name('songs.store');
        Route::prefix('/{song_id}')->group(function () {
            Route::get('/', [SongController::class, 'show'])->name('songs.show');
            Route::put('/', [SongController::class, 'update'])->name('songs.update');
            Route::delete('/', [SongController::class, 'destroy'])->name('songs.delete');
        });
    });
});

