<?php

namespace Database\Seeders;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShelterSeeder::class,
            DogSeeder::class,
            CampaignSeeder::class,
        ]);

        $shelter = Shelter::first();

        User::factory()->admin()->create(['email' => 'admin@example.com']);
        User::factory()->shelterManager($shelter)->create(['email' => 'manager@example.com']);
        User::factory()->external()->create(['email' => 'donor@example.com']);
    }
}
