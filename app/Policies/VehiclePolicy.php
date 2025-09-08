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

    public function view(User $user): bool
    {
        return $user->can('vehicle-view');
    }

    public function create(User $user): bool
    {
        return $user->can('vehicle-create');
    }

    public function update(User $user): bool
    {
        return $user->can('vehicle-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('vehicle-delete');
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
