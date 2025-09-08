<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('user-list');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('user-view');
    }

    public function create(User $user): bool
    {
        return $user->can('user-create');
    }

    public function update(User $user): bool
    {
        return $user->can('user-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('user-delete');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can('user-restore');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('user-force-delete');
    }
}
