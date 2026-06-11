<?php

use App\Http\Controllers\Web\LinkController;
use App\Http\Controllers\Web\LinkAnalyticsController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LinkController::class, 'index'])->name('home');
Route::post('/', [LinkController::class, 'store'])->name('links.store');
Route::get('/links/{code}/analytics', [LinkAnalyticsController::class, 'show'])->name('links.analytics');

// Catch-all per gli short link: tenere come ultima route.
Route::get('/{code}', RedirectController::class)
    ->where('code', '[0-9a-zA-Z\-_]{3,16}')
    ->name('links.redirect');
