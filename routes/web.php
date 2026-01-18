<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// Custom View Account page (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/account/view', function () {
        return view('ViewAccount');
    })->name('account.view');
});
