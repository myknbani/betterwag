<?php

namespace Database\Factories;

use App\Enums\DonationStatus;
use App\Enums\DonationType;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'user_id' => User::factory(),
            'type' => DonationType::OneTime,
            'amount' => fake()->numberBetween(100, 10000),
            'status' => DonationStatus::Paid,
            'stripe_payment_intent_id' => 'pi_test_fake_'.fake()->regexify('[a-zA-Z0-9]{24}'),
            'stripe_subscription_id' => null,
            'paid_at' => now(),
        ];
    }

    public function recurring(): static
    {
        return $this->state([
            'type' => DonationType::Recurring,
            'stripe_payment_intent_id' => null,
            'stripe_subscription_id' => 'sub_test_fake_'.fake()->regexify('[a-zA-Z0-9]{24}'),
        ]);
    }

    public function pending(): static
    {
        return $this->state([
            'status' => DonationStatus::Pending,
            'paid_at' => null,
        ]);
    }
}
