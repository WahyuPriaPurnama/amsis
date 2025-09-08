<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeePermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'employee-list',
            'employee-view',
            'employee-create',
            'employee-edit-own',
            'employee-edit-all',
            'employee-delete',
            'employee-restore',
            'employee-force-delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRoles = ['super-admin', 'bofi-admin', 'holding-admin', 'haka-admin', 'eln-admin', 'eln2-admin', 'rmm-admin'];

        foreach ($adminRoles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions([
                'employee-list',
                'employee-view',
                'employee-create',
                'employee-edit-all',
                'employee-delete',
                'employee-restore',
                'employee-force-delete',
            ]);
        }

        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employeeRole->syncPermissions([
            'employee-list',
            'employee-view',
            'employee-edit-own',
        ]);
    }
}
