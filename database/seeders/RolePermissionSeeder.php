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
        $staff->syncPermissions(
            // Defensive: firstOrCreate instead of find-by-name so the seeder
            // can never fail if a permission is somehow still missing.
            collect([
                'products.view', 'products.create', 'products.edit',
                'categories.view', 'categories.create', 'categories.edit',
                'brands.view', 'brands.create', 'brands.edit',
                'orders.view', 'orders.edit',
                'users.view',
                'media.view', 'media.create',
            ])->map(fn ($name) => Permission::firstOrCreate(['name' => $name]))
        );

        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'vendor']);
    }
}
