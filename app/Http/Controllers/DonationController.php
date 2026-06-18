<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDonationRequest;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationController extends Controller
{
    public function store(CreateDonationRequest $request, Campaign $campaign): JsonResource
    {
        $donation = Donation::create([
            ...$request->validated(),
            'campaign_id' => $campaign->id,
            'user_id' => $request->user()->id,
        ]);

        // TODO: create Stripe PaymentIntent via StripeService
        // $paymentIntent = $this->stripeService->createPaymentIntent($donation);
        // return (new DonationResource($donation))->additional(['client_secret' => $paymentIntent->client_secret]);

        return $donation->toResource();
    }

    public function show(Donation $donation): JsonResource
    {
        $this->authorize('view', $donation);

        return $donation->toResource();
    }
}
