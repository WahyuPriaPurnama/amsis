<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $EmployeePermissions = [
            'employee.list',
            'employee.view',
            'employee.create',
            'employee.edit',
            'employee.delete',
            'employee-organization.edit',
        ];
        $SubsidiaryPermissions = [
            'subsidiary.list',
            'subsidiary.view',
            'subsidiary.create',
            'subsidiary.edit',
            'subsidiary.delete',
        ];
        $UserPermissions = [
            'user.list',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
        ];
        $VehiclePermissions = [
            'vehicle.list',
            'vehicle.view',
            'vehicle.create',
            'vehicle.edit',
            'vehicle.delete',
        ];
        $AssetPermissions = [
            'asset.list',
            'asset.view',
            'asset.create',
            'asset.edit',
            'asset.delete',
        ];
        $RequestOrderPermissions = [
            'request-order.list',
            'request-order.view',
            'request-order.create',
            'request-order.edit',
            'request-order.delete',
            'request-order.receive',
            // Permissions Approve
            'request-order.approve-division',
            'request-order.approve-manager',
            'request-order.approve-bod',

            // Permissions Unapprove (Terpisah per jabatan)
            'request-order.unapprove-division',
            'request-order.unapprove-manager',
            'request-order.unapprove-bod',
        ];
        $RequestPaymentPermissions = [
            'request-payment.list',
            'request-payment.view',
            'request-payment.create',
            'request-payment.edit',
            'request-payment.delete',
            'request-payment.approve-manager',
            'request-payment.approve-bod',
            'request-payment.unapprove',
        ];


        $VendorPermissions = [
            'vendor.list',
            'vendor.view',
            'vendor.create',
            'vendor.edit',
            'vendor.delete',
            'vendor.approve',
            'vendor.reject',
            'vendor.review',
            'vendor.update-contract',

        ];


        $allPermissions = array_merge(
            $SubsidiaryPermissions,
            $UserPermissions,
            $VehiclePermissions,
            $EmployeePermissions,
            $AssetPermissions,
            $RequestOrderPermissions,
            $RequestPaymentPermissions,
            $VendorPermissions
        );
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        Role::findOrCreate('super-admin')->syncPermissions($allPermissions);
    }
}
