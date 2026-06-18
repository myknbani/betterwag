<?php

namespace App\Http\Controllers;

use App\Http\Requests\CloseCampaignRequest;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\Shelter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Shelter $shelter): ResourceCollection
    {
        $campaigns = $shelter
            ->campaigns()
            ->orderByDesc('created_at')
            ->paginate(10);

        return $campaigns->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCampaignRequest $request, Shelter $shelter): JsonResource
    {
        $campaign = Campaign::create([...$request->validated(), 'shelter_id' => $shelter->id]);

        return $campaign->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign): JsonResource
    {
        return $campaign->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign): JsonResource
    {
        $campaign->update($request->validated());

        return $campaign->refresh()->toResource();
    }

    /**
     * Close the specified campaign.
     */
    public function close(CloseCampaignRequest $request, Campaign $campaign): JsonResource
    {
        $validatedBody = $request->validated();
        $campaign->close($validatedBody['closed_reason'] ?? null);

        return $campaign->refresh()->toResource();
    }
}
