<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Color',
                'type' => 'color',
                'is_filterable' => true,
                'values' => [
                    ['value' => 'red', 'label' => 'Red', 'color_code' => '#EF4444'],
                    ['value' => 'blue', 'label' => 'Blue', 'color_code' => '#3B82F6'],
                    ['value' => 'green', 'label' => 'Green', 'color_code' => '#22C55E'],
                    ['value' => 'black', 'label' => 'Black', 'color_code' => '#000000'],
                    ['value' => 'white', 'label' => 'White', 'color_code' => '#FFFFFF'],
                ],
            ],
            [
                'name' => 'Size',
                'type' => 'select',
                'is_filterable' => true,
                'values' => [
                    ['value' => 'xs', 'label' => 'XS'],
                    ['value' => 's', 'label' => 'S'],
                    ['value' => 'm', 'label' => 'M'],
                    ['value' => 'l', 'label' => 'L'],
                    ['value' => 'xl', 'label' => 'XL'],
                    ['value' => 'xxl', 'label' => 'XXL'],
                ],
            ],
            [
                'name' => 'RAM',
                'type' => 'select',
                'is_filterable' => true,
                'values' => [
                    ['value' => '4gb', 'label' => '4GB'],
                    ['value' => '8gb', 'label' => '8GB'],
                    ['value' => '16gb', 'label' => '16GB'],
                    ['value' => '32gb', 'label' => '32GB'],
                ],
            ],
            [
                'name' => 'Storage',
                'type' => 'select',
                'is_filterable' => true,
                'values' => [
                    ['value' => '64gb', 'label' => '64GB'],
                    ['value' => '128gb', 'label' => '128GB'],
                    ['value' => '256gb', 'label' => '256GB'],
                    ['value' => '512gb', 'label' => '512GB'],
                    ['value' => '1tb', 'label' => '1TB'],
                ],
            ],
        ];

        foreach ($attributes as $index => $attrData) {
            $values = $attrData['values'];
            unset($attrData['values']);

            $attribute = Attribute::create(array_merge($attrData, ['sort_order' => $index]));

            foreach ($values as $vIndex => $value) {
                $attribute->values()->create(array_merge($value, ['sort_order' => $vIndex]));
            }
        }
    }
}
