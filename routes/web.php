<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Catch-all per gli short link: tenere come ultima route.
Route::get('/{code}', RedirectController::class)
    ->where('code', '[0-9a-zA-Z\-_]{3,16}')
    ->name('links.redirect');
