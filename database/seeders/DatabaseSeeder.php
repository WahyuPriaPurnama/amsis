<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Sensor;
use App\Models\Subsidiary;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            AssignAllRolesSeeder::class,
            // EmployeeAccountsSeeder::class,
            EmployeePermissionRoleSeeder::class,
            SubsidiaryPermissionRoleSeeder::class,
            UserPermissionRoleSeeder::class,
            VehiclePermissionRoleSeeder::class,
            RequestOrderSeeder::class,
        ]);
    }
}
