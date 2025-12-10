<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignAllRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super-admin',
            'holding-admin',
            'eln-admin',
            'eln2-admin',
            'haka-admin',
            'bofi-admin',
            'rmm-admin',
            'employee',
            'div-head',
            'manager',
            'bod',
        ];

        // Pastikan semua role tersedia
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Assign role ke user ID 1–7
        $roleAssignments = [
            1 => 'super-admin',
            2 => 'holding-admin',
            3 => 'eln-admin',
            4 => 'eln2-admin',
            5 => 'haka-admin',
            6 => 'bofi-admin',
            7 => 'rmm-admin',
        ];

        foreach ($roleAssignments as $userId => $roleName) {
            $user = User::find($userId);
            if ($user) {
                $user->assignRole($roleName);
                $this->command->info("User ID $userId assigned to role '$roleName'.");
            } else {
                $this->command->warn("User ID $userId not found.");
            }
        }

        // Assign role 'employee' ke semua user ID ≥ 8
        $employeeUsers = User::where('id', '>=', 8)->get();
        foreach ($employeeUsers as $user) {
            $user->assignRole('employee');
            $this->command->info("User ID {$user->id} assigned to role 'employee'.");
        }
        $this->command->info("Roles 'div-head', 'manager', dan 'bod' berhasil dibuat tanpa assignment.");
    }
}
