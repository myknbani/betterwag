<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campaignId' => $this->campaign_id,
            'userId' => $this->user_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'status' => $this->status,
            'paidAt' => $this->paid_at,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
