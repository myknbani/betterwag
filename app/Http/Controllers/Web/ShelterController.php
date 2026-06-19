<?php

namespace App\Http\Controllers\Web;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\DogResource;
use App\Http\Resources\ShelterResource;
use App\Models\Shelter;
use Inertia\Inertia;
use Inertia\Response;

class ShelterController extends Controller
{
    public function show(Shelter $shelter): Response
    {
        $dogs = $shelter->dogs()->with('media')->paginate(8);
        $campaigns = $shelter->campaigns()->whereStatus(CampaignStatus::Active)->get();

        return Inertia::render('shelters/Show', [
            'shelter' => new ShelterResource($shelter),
            'dogs' => DogResource::collection($dogs),
            'campaigns' => CampaignResource::collection($campaigns),
        ]);
    }
}
