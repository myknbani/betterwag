<?php

namespace Database\Seeders;

use App\Models\Dog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DogPhotoSeeder extends Seeder
{
    /** @var array<string, string> */
    private const BREED_SLUGS = [
        'Beagle' => 'beagle',
        'German Shepherd' => 'german/shepherd',
        'Golden Retriever' => 'retriever/golden',
        'Labrador' => 'labrador',
        'Poodle' => 'poodle',
        'Shih Tzu' => 'shihtzu',
    ];

    public function run(): void
    {
        Dog::all()->each(function (Dog $dog) {
            $slug = self::BREED_SLUGS[$dog->breed] ?? null;

            $endpoint = $slug
                ? "https://dog.ceo/api/breed/{$slug}/images/random"
                : 'https://dog.ceo/api/breeds/image/random';

            $url = Http::get($endpoint)->json('message');

            if (is_string($url)) {
                $dog->addMediaFromUrl($url)->toMediaCollection('photos');
            }
        });
    }
}
