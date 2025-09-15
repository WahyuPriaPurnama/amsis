<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('vehicle-list');
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->can('vehicle-view') && ($user->hasRole('holding-admin') ||
            $user->subsidiary_id === $vehicle->subsidiary_id);
    }

    public function create(User $user): bool
    {
        return $user->can('vehicle-create');
    }

    public function update(User $user, Vehicle $vehicle)
    {
        return $user->can('vehicle-edit-own') &&
            $user->subsidiary_id === $vehicle->subsidiary_id;
    }

    public function delete(User $user, Vehicle $vehicle)
    {
        return $user->can('vehicle-delete-own') &&
            $user->subsidiary_id === $vehicle->subsidiary_id;
    }


    public function restore(User $user, Vehicle $vehicle): bool
    {
        return $user->can('vehicle-restore');
    }

    public function forceDelete(User $user, Vehicle $vehicle): bool
    {
        return $user->can('vehicle-force-delete');
    }
}
