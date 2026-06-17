<?php

namespace Database\Seeders;

use App\Models\Dog;
use Illuminate\Database\Seeder;

class DogSeeder extends Seeder
{
    public function run(): void
    {
        Dog::factory()->count(8)->create();
        Dog::factory()->urgent()->count(2)->create();
    }
}
