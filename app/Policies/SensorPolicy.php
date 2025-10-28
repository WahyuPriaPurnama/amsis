<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sensor;

class SensorPolicy
{
    /**
     * Determine whether the user can view any sensors.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('sensor-list');
    }

    /**
     * Determine whether the user can view the sensor.
     */
    public function view(User $user, Sensor $sensor): bool
    {
        return $user->can('sensor-view');
    }

    /**
     * Determine whether the user can create sensors.
     */
    public function create(User $user): bool
    {
        return $user->can('sensor-create');
    }

    /**
     * Determine whether the user can update the sensor.
     */
    public function update(User $user, Sensor $sensor): bool
    {
        return $user->can('sensor-edit') || (
            $user->can('sensor-edit-own') && $sensor->created_by === $user->id
        );
    }

    /**
     * Determine whether the user can delete the sensor.
     */
    public function delete(User $user, Sensor $sensor): bool
    {
        return $user->can('sensor-delete');
    }

    /**
     * Determine whether the user can restore the sensor.
     */
    public function restore(User $user, Sensor $sensor): bool
    {
        return $user->can('sensor-restore');
    }

    /**
     * Determine whether the user can permanently delete the sensor.
     */
    public function forceDelete(User $user, Sensor $sensor): bool
    {
        return $user->can('sensor-force-delete');
    }
}
