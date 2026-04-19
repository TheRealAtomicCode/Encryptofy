<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AuthController;

Route::middleware(['auth:sanctum'])->group(function () {

    // admin, manager, user
    Route::get('/files', [FileController::class, 'index'])
        ->middleware('role:admin|manager|user');

    // admin + manager
    Route::get('/files/{id}', [FileController::class, 'show'])
        ->middleware('role:admin|manager');

    // admin only
    Route::post('/files', [FileController::class, 'store'])
        ->middleware('role:admin');

 
});

   Route::post('/login', [AuthController::class, 'login']);

// unprotected routes

// use App\Http\Controllers\FileController;
// use App\Http\Controllers\AuthController;

// Route::apiResource('files', FileController::class);

// Route::post('/login', [AuthController::class, 'login']);

