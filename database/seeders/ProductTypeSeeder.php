<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Electronics', 'Fashion', 'Books', 'Digital', 'Physical', 'Bundle'];

        foreach ($types as $type) {
            ProductType::create(['name' => $type, 'is_active' => true]);
        }
    }
}
