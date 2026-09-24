<?php

namespace App\Policies;

use App\Models\User;

/** Anyone may browse products; only admins may change the catalogue. */
class ProductPolicy
{
    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function delete(User $user): bool
    {
        return (bool) $user->is_admin;
    }
}
