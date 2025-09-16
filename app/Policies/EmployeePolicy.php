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

    public function update(User $user, Employee $employee): bool
    {
        // Admin bisa update siapa saja
        if ($user->can('employee-edit-all')) {
            return true;
        }

        // Karyawan hanya bisa update dirinya sendiri
        if ($user->can('employee-edit-own')) {
            return $user->employee_id === $employee->id;
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
