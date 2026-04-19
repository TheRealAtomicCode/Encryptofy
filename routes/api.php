<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Auth;


Route::middleware('auth:sanctum')->group(function () {

    // admin, manager and user
    Route::get('/files', [FileController::class, 'index'])
        ->middleware('role:admin|manager|user');

    // admin and manager
    Route::get('/files/{id}', [FileController::class, 'show'])
        ->middleware('role:admin|manager');

    // admin 
    Route::post('/files', [FileController::class, 'store'])
        ->middleware('role:admin');

});

   Route::post('/login', [AuthController::class, 'login']);


   Route::get('/me', function () {
    return [
        'authenticated' => auth()->check(),
        'user' => auth()->user(),
        'session_id' => session()->getId(),
    ];
});

// unprotected routes

// use App\Http\Controllers\FileController;
// use App\Http\Controllers\AuthController;

// Route::apiResource('files', FileController::class);

// Route::post('/login', [AuthController::class, 'login']);

