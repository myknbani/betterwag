<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DogController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationWebhookController;
use App\Http\Controllers\ShelterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('shelters', ShelterController::class)->only(['index', 'show']);
Route::apiResource('shelters.dogs', DogController::class)->only(['index', 'show'])->shallow();
Route::apiResource('shelters.campaigns', CampaignController::class)->only(['index', 'show'])->shallow();
Route::post('webhooks/stripe', [DonationWebhookController::class, 'handleWebhook']);

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', fn (Request $request) => $request->user());

        Route::apiResource('shelters', ShelterController::class)->except(['index', 'show']);
        Route::apiResource('shelters.dogs', DogController::class)->shallow()->except(['index', 'show']);
        Route::apiResource('shelters.campaigns', CampaignController::class)
            ->shallow()
            ->except(['index', 'show']);
        Route::patch('campaigns/{campaign}/close', [CampaignController::class, 'close'])->name('campaigns.close');
        Route::post('campaigns/{campaign}/donate', [DonationController::class, 'store'])->name('donations.store');
        Route::get('donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
    });
