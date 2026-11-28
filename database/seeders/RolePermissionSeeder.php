<?php

namespace Database\Seeders;

use App\Support\AdminMenu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Sync permissions from the admin menu registry — new menu items
        // automatically get their permissions created here.
        AdminMenu::syncToDatabase();

        $allPermissions = Permission::whereIn('name', AdminMenu::permissions())->get();

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($allPermissions);

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'products.view', 'products.create', 'products.edit',
            'categories.view', 'categories.create', 'categories.edit',
            'brands.view', 'brands.create', 'brands.edit',
            'orders.view', 'orders.edit',
            'users.view',
            'media.view', 'media.create',
        ]);

        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'vendor']);
    }
}
