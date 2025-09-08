<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        // Semua user bisa lihat daftar employee
        return $user->can('employee-list');
    }

    public function view(User $user): bool
    {
        return $user->can('employee-view');
    }

    public function create(User $user): bool
    {
        return $user->can('employee-create');
    }

    public function update(User $authUser, Employee $targetUser): bool
    {
        // Admin bisa update siapa saja
        if ($authUser->can('employee-edit-all')) {
            return true;
        }

        // Karyawan hanya bisa update dirinya sendiri
        if ($authUser->can('employee-edit-own')) {
            return $authUser->employee_id === $targetUser->id;
        }

        return false;
    }

    public function delete(User $user): bool
    {
        return $user->can('employee-delete');
    }

    public function restore(User $user): bool
    {
        return $user->can('employee-restore');
    }

    public function forceDelete(User $user): bool
    {
        return $user->can('employee-force-delete');
    }
}
