<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\DogResource;
use App\Http\Resources\ShelterResource;
use App\Models\Shelter;
use Inertia\Inertia;
use Inertia\Response;

class ShelterController extends Controller
{
    public function show(Shelter $shelter): Response
    {
        $shelter->load(['dogs' => fn ($q) => $q->with('media')]);

        return Inertia::render('shelters/Show', [
            'shelter' => new ShelterResource($shelter),
            'dogs' => DogResource::collection($shelter->dogs),
        ]);
    }
}
