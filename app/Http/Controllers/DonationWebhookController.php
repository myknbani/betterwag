<?php

namespace App\Http\Controllers;

use App\Jobs\HandleSuccessfulPayment;
use App\Models\Donation;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
// this is not extending Laravel's usual controller, so we need Sf's Response instead of Illuminate's
use Symfony\Component\HttpFoundation\Response;

class DonationWebhookController extends CashierWebhookController
{
    /** @param array<string, mixed> $payload */
    public function handlePaymentIntentSucceeded(array $payload): Response
    {
        $paymentIntentId = $payload['data']['object']['id'];

        $donation = Donation::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if ($donation) {
            HandleSuccessfulPayment::dispatch($donation);
        }

        return $this->successMethod();
    }
}
