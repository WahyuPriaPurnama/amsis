<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SubsidiaryPermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'subsidiary-list',
            'subsidiary-view',
            'subsidiary-create',
            'subsidiary-edit',
            'subsidiary-delete',
            'subsidiary-restore',
            'subsidiary-force-delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        Role::findOrCreate('super-admin')->syncPermissions($permissions);
        Role::findOrCreate('holding-admin')->syncPermissions([
            'subsidiary-list',
            'subsidiary-view',
            'subsidiary-create',
            'subsidiary-edit',
        ]);
    }
}
