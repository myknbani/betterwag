<?php

namespace App\Http\Resources;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Campaign */
class CampaignResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shelterId' => $this->shelter_id,
            'dogId' => $this->dog_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'goalAmount' => $this->goal_amount,
            'closedAt' => $this->closed_at,
            'closedReason' => $this->closed_reason,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'collectedAmount' => $this->collectedAmount(),
        ];
    }
}
