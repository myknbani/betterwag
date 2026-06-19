<?php

namespace Database\Factories;

use App\Enums\AdoptionStatus;
use App\Enums\Gender;
use App\Models\Dog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dog>
 */
class DogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'breed' => fake()->randomElement(['German Shepherd', 'Labrador', 'Shih Tzu', 'Golden Retriever', 'Poodle', 'Beagle']),
            'age_months' => fake()->optional()->numberBetween(1, 180),
            'gender' => fake()->randomElement(Gender::cases()),
            'description' => fake()->optional()->paragraph(),
            'adoption_status' => AdoptionStatus::Available,
            'is_urgent' => false,
            'rescued_at' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
        ];
    }

    public function urgent(): static
    {
        return $this->state(['is_urgent' => true]);
    }

    public function rescued(): static
    {
        return $this->state(['adoption_status' => AdoptionStatus::Rescued]);
    }
}
