<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Dog;
use App\Models\Shelter;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        // One recurring shelter-level campaign per shelter
        Shelter::all()->each(function (Shelter $shelter) {
            Campaign::factory()->create([
                'shelter_id' => $shelter->id,
                'title' => "Support {$shelter->name}",
            ]);
        });

        // One-off emergency campaigns for a few dogs
        Dog::inRandomOrder()->take(3)->get()->each(function (Dog $dog) {
            Campaign::factory()->oneOff()->forDog($dog)->create();
        });
    }
}
