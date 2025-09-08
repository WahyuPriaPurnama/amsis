<?php

namespace App\Policies;

use App\Models\Subsidiary;
use App\Models\User;

class SubsidiaryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('subsidiary-list');
    }

    public function view(User $user): bool
    {
        return $user->can('subsidiary-view');
    }

    public function create(User $user): bool
    {
        return $user->can('subsidiary-create');
    }

    public function update(User $user): bool
    {
        return $user->can('subsidiary-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('subsidiary-delete');
    }

    public function restore(User $user, Subsidiary $subsidiary): bool
    {
        return $user->can('subsidiary-restore');
    }

    public function forceDelete(User $user, Subsidiary $subsidiary): bool
    {
        return $user->can('subsidiary-force-delete');
    }
}
