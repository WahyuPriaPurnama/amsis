<?php

namespace App\Policies;

use App\Models\Purchasing\RequestOrder;
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

    public function approve(User $user, RequestOrder $order)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        // Subsidiary khusus: Plant Manager merangkap BOD
        if ($order->subsidiary_id == 2 && $order->status == 'approved_by_div_head' && $user->hasRole('plant-manager')) {
            return true; // langsung dianggap approve BOD juga
        }

        return match ($order->status) {
            'pending' => $user->hasRole('div-head'),
            'approved_by_div_head' => $user->hasRole('plant-manager'),
            'approved_by_manager' => $user->hasRole('bod'),
            default => false,
        };
    }
}
