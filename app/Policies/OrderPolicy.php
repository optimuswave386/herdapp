<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /** Customers see their own orders; admins see all of them. */
    public function view(User $user, Order $order): bool
    {
        return $user->is_admin || $order->user_id === $user->id;
    }

    public function update(User $user): bool
    {
        return (bool) $user->is_admin;
    }
}
