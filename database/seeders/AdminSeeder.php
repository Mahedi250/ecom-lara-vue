<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@nityogadget.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');

        $staff = User::firstOrCreate(
            ['email' => 'staff@nityogadget.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $staff->assignRole('staff');
    }
}
