<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

Route::prefix('/songs')->group(function () {
    Route::get('/index/{page}', [SongController::class, 'index']);
    Route::middleware('auth')->group(function () {
        Route::post('/', [SongController::class, 'store']);
        Route::get('/{id}', [SongController::class, 'show']);
        Route::put('/{id}', [SongController::class, 'update']);
        Route::delete('/{id}', [SongController::class, 'destroy']);
    });
});

Route::middleware('auth')->prefix('/request')->group(function () {
    Route::post('/', [RequestController::class, 'send']);
    Route::patch('/accept', [RequestController::class, 'acceptRequest']);
    Route::patch('/refuse', [RequestController::class, 'refuseRequest']);
});

