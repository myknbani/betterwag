<?php

use App\Http\Controllers\DogController;
use App\Http\Controllers\ShelterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', fn (Request $request) => $request->user());
        Route::apiResource('dogs', DogController::class);
        Route::apiResource('shelters', ShelterController::class);
    });
