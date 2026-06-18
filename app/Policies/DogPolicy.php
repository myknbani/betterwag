<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Dog;
use App\Models\Shelter;
use App\Models\User;

class DogPolicy
{
    public function create(User $user, Shelter $shelter): bool
    {
        return $user->role === Role::ShelterManager && $user->isOwnShelter($shelter);
    }

    public function update(User $user, Dog $dog): bool
    {
        return $user->role === Role::ShelterManager && $user->isOwnDog($dog);
    }

    public function delete(User $user, Dog $dog): bool
    {
        return $user->role === Role::ShelterManager && $user->isOwnDog($dog);
    }
}
