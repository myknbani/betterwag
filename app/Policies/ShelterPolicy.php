<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Shelter;
use App\Models\User;

class ShelterPolicy
{
    public function create(User $user): bool
    {
        return false; // Admins are allowed via Gate
    }

    public function update(User $user, Shelter $shelter): bool
    {
        return $user->role === Role::ShelterManager && $user->isOwnShelter($shelter);
    }

    public function delete(User $user, Shelter $shelter): bool
    {
        return false; // Admins are allowed via Gate
    }
}
