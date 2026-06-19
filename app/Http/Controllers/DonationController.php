<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDonationRequest;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\PaymentService;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function store(CreateDonationRequest $request, Campaign $campaign): JsonResource
    {
        $validatedRequest = $request->validated();
        $donation = Donation::create([
            ...$validatedRequest,
            'campaign_id' => $campaign->id,
            'user_id' => $request->user()->id,
        ]);

        $paymentIntent = $this->paymentService->createPaymentIntent($donation, $validatedRequest['payment_method_id'] ?? null);
        $donation->update(['stripe_payment_intent_id' => $paymentIntent->asStripePaymentIntent()->id]);

        return $donation->refresh()->toResource();
    }

    public function show(Donation $donation): JsonResource
    {
        $this->authorize('view', $donation);

        return $donation->toResource();
    }
}
