<?php

// use Illuminate\Support\Facades\Route;

// Route::inertia('/', 'Welcome')->name('home');

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');