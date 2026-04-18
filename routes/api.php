<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\FileController;

Route::apiResource('files', FileController::class);