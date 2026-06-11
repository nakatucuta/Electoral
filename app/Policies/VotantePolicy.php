<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Votante;

class VotantePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Votante $votante): bool
    {
        return $votante->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    public function update(User $user, Votante $votante): bool
    {
        return $votante->user_id === $user->id;
    }

    public function delete(User $user, Votante $votante): bool
    {
        return $votante->user_id === $user->id;
    }
}
