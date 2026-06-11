<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:30,1'])->group(function () {
    Route::apiResource('links', \App\Http\Controllers\Api\LinkController::class);
});
