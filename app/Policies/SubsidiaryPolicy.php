<?php

namespace App\Policies;

use App\Models\Subsidiary;
use App\Models\User;

class SubsidiaryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'subsidiary-list',
            'subsidiary-view',
            'subsidiary-edit-own',
            'subsidiary-delete-own',
        ]);
    }


    public function view(User $user, Subsidiary $subsidiary)
    {
        return $user->can('subsidiary-view') && (
            $user->hasRole('holding-admin') ||
            $user->subsidiary_id === $subsidiary->id
        );
    }
    
    public function create(User $user)
    {
        return $user->can('subsidiary-create');
    }

    public function update(User $user, Subsidiary $subsidiary)
    {
        return $user->can('subsidiary-edit-own') &&
            $user->subsidiary_id === $subsidiary->id;
    }


    public function delete(User $user, Subsidiary $subsidiary)
    {
        return $user->can('subsidiary-delete-own') &&
            $user->subsidiary_id === $subsidiary->id;
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
