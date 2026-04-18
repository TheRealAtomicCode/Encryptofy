<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\FileController;
use App\Http\Controllers\AuthController;

Route::apiResource('files', FileController::class);

Route::post('/login', [AuthController::class, 'login']);