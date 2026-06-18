<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    public function view(User $user, Donation $donation): bool
    {
        return $user->id === $donation->user_id;
    }

    public function create(User $user, Campaign $campaign): bool
    {
        return $campaign->status->value === 'active';
    }
}
