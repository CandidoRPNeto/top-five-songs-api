<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::middleware('auth:api')->prefix('/request')->group(function () {
    Route::get('/', [RequestController::class, 'index'])->name('request.index');
    Route::post('/', [RequestController::class, 'send'])->name('request.send');
    Route::patch('/accept', [RequestController::class, 'acceptRequest'])->name('request.accept');
    Route::patch('/refuse', [RequestController::class, 'refuseRequest'])->name('request.refuse');
});
Route::prefix('/songs')->group(function () {
    Route::get('/index/{page}', [SongController::class, 'index'])->name('songs.index');
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [SongController::class, 'store'])->name('songs.store');
        Route::get('/{id}', [SongController::class, 'show'])->name('songs.show');
        Route::put('/{id}', [SongController::class, 'update'])->name('songs.update');
        Route::delete('/{id}', [SongController::class, 'destroy'])->name('songs.delete');
    });
});

