<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeePermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        $employeePermissions = [
            'employee-list',
            'employee-view',
            'employee-create',
            'employee-edit-own',
            'employee-edit-all',
            'employee-delete',
            'employee-restore',
            'employee-force-delete',
        ];

        $vehiclePermissions = [
            'vehicle-list',
            'vehicle-view',
            'vehicle-create',
            'vehicle-edit-own',
            'vehicle-edit-all',
            'vehicle-delete',
            'vehicle-restore',
            'vehicle-force-delete',
        ];

        $subsidiaryPermissions = [
            'subsidiary-list',
            'subsidiary-view',
            'subsidiary-create',
            'subsidiary-edit-own',
            'subsidiary-edit-all',
            'subsidiary-delete',
            'subsidiary-restore',
            'subsidiary-force-delete',
        ];

        $allPermissions = array_merge(
            $employeePermissions,
            $vehiclePermissions,
            $subsidiaryPermissions
        );

        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Super Admin: full access
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($allPermissions);

        // Other Admins: limited to own subsidiary
        $adminRoles = ['bofi-admin', 'holding-admin', 'haka-admin', 'eln-admin', 'eln2-admin', 'rmm-admin'];

        $adminPermissions = [
            // Employee
            'employee-list',
            'employee-view',
            'employee-create',
            'employee-edit-own',
            'employee-delete',
            

            // Vehicle
            'vehicle-list',
            'vehicle-view',
            'vehicle-create',
            'vehicle-edit-own',
            'vehicle-delete',

            // Subsidiary
            'subsidiary-list',
            'subsidiary-view',
            'subsidiary-edit-own',
        ];

        foreach ($adminRoles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($adminPermissions);
        }

        // Employee Role
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employeeRole->syncPermissions([
            'employee-list',
            'employee-view',
            'employee-edit-own',
        ]);
    }
}
