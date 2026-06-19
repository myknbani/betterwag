<?php

namespace App\Services;

use App\Models\Donation;
use Laravel\Cashier\Payment;

/**
 * Handles Stripe interaction without touching our DB.
 */
class PaymentService
{
    public function createPaymentIntent(Donation $donation, ?string $paymentMethodId): Payment
    {
        $user = $donation->user;

        if (! $user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        $resolvedMethod = $paymentMethodId ?? $user->defaultPaymentMethod()?->id;

        if ($resolvedMethod === null) {
            throw new \InvalidArgumentException('No payment method provided and user has no default payment method.');
        }

        return $user->charge(
            $donation->amount,
            $resolvedMethod,
            ['payment_method_types' => ['card']],
        );
    }
}
