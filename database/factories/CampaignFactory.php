<?php

namespace Database\Factories;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Models\Campaign;
use App\Models\Dog;
use App\Models\Shelter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'shelter_id' => Shelter::factory(),
            'dog_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'type' => CampaignType::Recurring,
            'status' => CampaignStatus::Active,
            'goal_amount' => null,
            'closed_at' => null,
            'closed_reason' => null,
        ];
    }

    public function oneOff(?int $goalAmount = null): static
    {
        return $this->state([
            'type' => CampaignType::OneOff,
            'dog_id' => Dog::factory(),
            'goal_amount' => $goalAmount ?? fake()->numberBetween(5000, 50000),
        ]);
    }

    public function forDog(Dog $dog): static
    {
        return $this->state([
            'shelter_id' => $dog->shelter_id,
            'dog_id' => $dog->id,
        ]);
    }

    public function closed(): static
    {
        return $this->state([
            'status' => CampaignStatus::Closed,
            'closed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'status' => CampaignStatus::Cancelled,
            'closed_at' => now(),
            'closed_reason' => fake()->sentence(),
        ]);
    }
}
