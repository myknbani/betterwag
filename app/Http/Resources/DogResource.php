<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shelterId' => $this->shelter_id,
            'name' => $this->name,
            'breed' => $this->breed,
            'ageMonths' => $this->age_months,
            'gender' => $this->gender,
            'description' => $this->description,
            'adoptionStatus' => $this->adoption_status,
            'isUrgent' => $this->is_urgent,
            'rescuedAt' => $this->rescued_at,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
