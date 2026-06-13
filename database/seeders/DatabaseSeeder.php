<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
            CategorySeeder::class,
            ProductTypeSeeder::class,
            AttributeSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
