<?php

namespace App\Policies;

use App\Models\HRD\Employee;
use App\Models\User;

class EmployeePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Employee $employee)
    {
        return $user->id == $employee->user_id;
    }

    public function edit(User $user, Employee $employee)
    {
        return $user->id == $employee->user_id;
    }
}
