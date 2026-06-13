<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'brands.view', 'brands.create', 'brands.edit', 'brands.delete',
            'orders.view', 'orders.edit', 'orders.delete',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'coupons.view', 'coupons.create', 'coupons.edit', 'coupons.delete',
            'settings.view', 'settings.edit',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'products.view', 'products.create', 'products.edit',
            'categories.view', 'categories.create', 'categories.edit',
            'brands.view', 'brands.create', 'brands.edit',
            'orders.view', 'orders.edit',
            'users.view',
        ]);

        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'vendor']);
    }
}
