<?php

namespace Database\Seeders;

use App\Models\Dog;
use App\Models\Shelter;
use Illuminate\Database\Seeder;

class DogSeeder extends Seeder
{
    public function run(): void
    {
        $shelterIds = Shelter::pluck('id');

        Dog::factory()->count(8)->create([
            'shelter_id' => $shelterIds->random(),
        ]);
        Dog::factory()->urgent()->count(2)->create([
            'shelter_id' => $shelterIds->random(),
        ]);
    }
}
