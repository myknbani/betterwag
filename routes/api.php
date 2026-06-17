<?php

use App\Http\Controllers\DogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', fn (Request $request) => $request->user());
        Route::apiResource('dogs', DogController::class);
    });
