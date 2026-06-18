<?php

namespace App\Policies;

use App\Enums\CampaignStatus;
use App\Enums\Role;
use App\Models\Campaign;
use App\Models\Shelter;
use App\Models\User;

class CampaignPolicy
{
    public function create(User $user, Shelter $shelter): bool
    {
        return $user->role === Role::ShelterManager && $user->isOwnShelter($shelter);
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return $user->role === Role::ShelterManager && $user->isOwnCampaign($campaign);
    }

    public function close(User $user, Campaign $campaign): bool
    {
        return $user->role === Role::ShelterManager
            && $user->isOwnCampaign($campaign)
            && $campaign->status === CampaignStatus::Active;
    }
}
