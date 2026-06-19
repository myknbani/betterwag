<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDonationRequest;
use App\Http\Resources\DonationResource;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Cashier\Exceptions\IncompletePayment;

class DonationController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function store(CreateDonationRequest $request, Campaign $campaign): JsonResponse
    {
        $data = $request->validated();
        $donation = Donation::create([
            ...$data,
            'campaign_id' => $campaign->id,
            'user_id' => $request->user()->id,
        ]);

        $clientSecret = null;

        try {
            $payment = $this->paymentService->createPaymentIntent($donation, $data['payment_method_id'] ?? null);
            $donation->update(['stripe_payment_intent_id' => $payment->asStripePaymentIntent()->id]);
        } catch (IncompletePayment $e) {
            $donation->update(['stripe_payment_intent_id' => $e->payment->asStripePaymentIntent()->id]);
            $clientSecret = $e->payment->asStripePaymentIntent()->client_secret;
        }

        return (new DonationResource($donation->refresh()))
            ->additional(['clientSecret' => $clientSecret])
            ->response();
    }

    public function show(Donation $donation): JsonResource
    {
        $this->authorize('view', $donation);

        return $donation->toResource();
    }
}
