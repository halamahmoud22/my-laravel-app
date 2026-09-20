<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'sanctum']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'sanctum']);

        // Permissions
        $permissionNames = [
            'products.view', 'products.create', 'products.update', 'products.delete',
            'categories.view', 'categories.create', 'categories.update', 'categories.delete',
            'orders.view-any', 'orders.mark-shipped', 'orders.cancel-any',
            'refunds.approve',
            'admins.create', 'admins.delete',
        ];

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'sanctum']);
        }

        // customer: no Spatie permissions - self-service handled by ownership Policies

        // admin: everything except admin-management
        $admin->syncPermissions(collect($permissionNames)
            ->reject(fn ($name) => str_starts_with($name, 'admins.')));

        // super_admin: everything
        $superAdmin->syncPermissions($permissionNames);
    }
}
