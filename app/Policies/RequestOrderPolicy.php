<?php

namespace App\Policies;

use App\Models\RequestOrder;
use App\Models\User;

class RequestOrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}
    public function delete(User $user, RequestOrder $order)
    {
        return $user->id === $order->request_id;
    }
}
