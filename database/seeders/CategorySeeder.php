<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'children' => ['Smartphones', 'Laptops', 'Tablets', 'Accessories']],
            ['name' => 'Fashion', 'children' => ['Men\'s Clothing', 'Women\'s Clothing', 'Footwear', 'Bags']],
            ['name' => 'Books', 'children' => ['Fiction', 'Non-Fiction', 'Academic', 'Comics']],
            ['name' => 'Home & Living', 'children' => ['Furniture', 'Kitchen', 'Decor', 'Garden']],
            ['name' => 'Sports', 'children' => ['Gym', 'Outdoor', 'Team Sports', 'Sportswear']],
        ];

        foreach ($categories as $cat) {
            $parent = Category::create([
                'name' => $cat['name'],
                'is_active' => true,
            ]);

            foreach ($cat['children'] as $child) {
                Category::create([
                    'name' => $child,
                    'parent_id' => $parent->id,
                    'is_active' => true,
                ]);
            }
        }
    }
}
