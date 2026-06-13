<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Smartphones (cat 2)
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'category_id' => 2,
                'product_type_id' => 1,
                'price' => 139999,
                'sale_price' => 124999,
                'stock' => 45,
                'is_featured' => true,
                'short_description' => '6.8" Dynamic AMOLED 2X, 200MP camera, S Pen included, 5000mAh battery.',
                'description' => 'The Samsung Galaxy S24 Ultra redefines flagship smartphones with its stunning 6.8-inch Dynamic AMOLED display, powerful 200MP quad-camera system, and built-in S Pen for creativity on the go. Powered by Snapdragon 8 Gen 3, it handles any task with ease.',
                'sku' => 'SAM-S24-ULTRA-001',
                'meta_title' => 'Samsung Galaxy S24 Ultra Price in Bangladesh',
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'category_id' => 2,
                'product_type_id' => 1,
                'price' => 169999,
                'sale_price' => null,
                'stock' => 20,
                'is_featured' => true,
                'short_description' => '6.7" Super Retina XDR, A17 Pro chip, Titanium design, 48MP camera.',
                'description' => "Apple's most powerful iPhone features the revolutionary A17 Pro chip, a stunning 6.7-inch Super Retina XDR display, and a pro-grade 48MP camera system with 5x optical zoom. The premium titanium frame makes it both lightweight and incredibly durable.",
                'sku' => 'APL-IP15-PROMAX-001',
                'meta_title' => 'iPhone 15 Pro Max Price in Bangladesh',
            ],
            [
                'name' => 'Xiaomi Redmi Note 13 Pro',
                'category_id' => 2,
                'product_type_id' => 1,
                'price' => 34999,
                'sale_price' => 29999,
                'stock' => 120,
                'is_featured' => false,
                'short_description' => '6.67" AMOLED, 200MP camera, 5100mAh, 67W fast charging.',
                'description' => 'Get flagship features at mid-range price. The Redmi Note 13 Pro packs a 200MP camera, large 5100mAh battery with 67W HyperCharge, and a beautiful 6.67-inch AMOLED display with 120Hz refresh rate.',
                'sku' => 'XMI-RN13P-001',
            ],
            [
                'name' => 'OnePlus 12',
                'category_id' => 2,
                'product_type_id' => 1,
                'price' => 89999,
                'sale_price' => 79999,
                'stock' => 35,
                'is_featured' => true,
                'short_description' => 'Snapdragon 8 Gen 3, Hasselblad camera, 100W SUPERVOOC charging.',
                'description' => 'OnePlus 12 brings flagship performance with Snapdragon 8 Gen 3, Hasselblad-tuned triple camera system, and blazing-fast 100W SUPERVOOC charging that fills up in just 25 minutes.',
                'sku' => 'OP-12-001',
            ],

            // Laptops (cat 3)
            [
                'name' => 'MacBook Air M3',
                'category_id' => 3,
                'product_type_id' => 1,
                'price' => 149999,
                'sale_price' => null,
                'stock' => 15,
                'is_featured' => true,
                'short_description' => '13.6" Liquid Retina, M3 chip, 18hr battery, 8GB RAM, 256GB SSD.',
                'description' => "The world's best consumer laptop gets even better. MacBook Air with M3 delivers incredible performance, a stunning Liquid Retina display, and up to 18 hours of battery life — all in a fanless design that never slows down.",
                'sku' => 'APL-MBA-M3-001',
                'meta_title' => 'MacBook Air M3 Price in Bangladesh',
            ],
            [
                'name' => 'Dell XPS 15',
                'category_id' => 3,
                'product_type_id' => 1,
                'price' => 179999,
                'sale_price' => 159999,
                'stock' => 8,
                'is_featured' => false,
                'short_description' => '15.6" OLED 3.5K, Intel Core i7-13700H, 16GB RAM, 512GB SSD, RTX 4060.',
                'description' => 'Dell XPS 15 combines stunning OLED display with powerhouse Intel Core i7 and NVIDIA RTX 4060 graphics, perfect for creative professionals and power users who demand the best.',
                'sku' => 'DELL-XPS15-001',
            ],
            [
                'name' => 'HP Pavilion Laptop 15',
                'category_id' => 3,
                'product_type_id' => 1,
                'price' => 65000,
                'sale_price' => 58000,
                'stock' => 30,
                'is_featured' => false,
                'short_description' => 'AMD Ryzen 5 7530U, 8GB RAM, 512GB SSD, Full HD IPS display.',
                'description' => 'The HP Pavilion 15 is the perfect everyday laptop for students and professionals. Powered by AMD Ryzen 5, it handles multitasking with ease while the Full HD IPS display delivers crisp visuals.',
                'sku' => 'HP-PAV15-001',
            ],

            // Fashion - Men's Clothing (cat 7)
            [
                'name' => 'Premium Cotton Polo Shirt',
                'category_id' => 7,
                'product_type_id' => 2,
                'price' => 1299,
                'sale_price' => 999,
                'stock' => 200,
                'is_featured' => true,
                'short_description' => '100% premium cotton polo, breathable fabric, available in 8 colors.',
                'description' => 'Elevate your casual wardrobe with our Premium Cotton Polo Shirt. Made from 100% combed cotton, it offers superior softness and breathability. The classic polo collar and two-button placket give it a timeless look perfect for any occasion.',
                'sku' => 'POLO-MEN-COT-001',
            ],
            [
                'name' => 'Slim Fit Chino Pants',
                'category_id' => 7,
                'product_type_id' => 2,
                'price' => 2499,
                'sale_price' => 1899,
                'stock' => 150,
                'is_featured' => false,
                'short_description' => 'Stretch chino fabric, slim fit cut, versatile for office and casual.',
                'description' => 'Our Slim Fit Chino Pants feature a modern slim cut with just enough stretch for all-day comfort. Made from premium cotton-spandex blend, these pants transition effortlessly from the office to weekend outings.',
                'sku' => 'CHINO-MEN-SLM-001',
            ],

            // Women's Clothing (cat 8)
            [
                'name' => 'Floral Maxi Dress',
                'category_id' => 8,
                'product_type_id' => 2,
                'price' => 3499,
                'sale_price' => 2599,
                'stock' => 85,
                'is_featured' => true,
                'short_description' => 'Lightweight chiffon floral print, floor-length, V-neck design.',
                'description' => 'Embrace effortless elegance with our Floral Maxi Dress. Crafted from lightweight chiffon with a beautiful floral print, this floor-length dress features a flattering V-neck and flowing silhouette perfect for summer days and special occasions.',
                'sku' => 'DRESS-WOM-FLR-001',
            ],
            [
                'name' => "Women's Casual Hoodie",
                'category_id' => 8,
                'product_type_id' => 2,
                'price' => 1899,
                'sale_price' => null,
                'stock' => 120,
                'is_featured' => false,
                'short_description' => 'Soft fleece interior, oversized fit, kangaroo pocket, 5 colors.',
                'description' => "Stay cozy and stylish with our Women's Casual Hoodie. Features a plush fleece interior, relaxed oversized fit, and handy kangaroo pocket. Available in five versatile colors to match any outfit.",
                'sku' => 'HOOD-WOM-CAS-001',
            ],

            // Books - Fiction (cat 12)
            [
                'name' => 'The Psychology of Money',
                'category_id' => 13,
                'product_type_id' => 3,
                'price' => 650,
                'sale_price' => 499,
                'stock' => 500,
                'is_featured' => true,
                'short_description' => 'Morgan Housel. Timeless lessons on wealth, greed, and happiness.',
                'description' => 'In The Psychology of Money, award-winning author Morgan Housel shares 19 short stories exploring the strange ways people think about money and teaches you how to make better sense of one of life\'s most important topics.',
                'sku' => 'BOOK-PSY-MON-001',
            ],
            [
                'name' => 'Atomic Habits',
                'category_id' => 13,
                'product_type_id' => 3,
                'price' => 750,
                'sale_price' => 599,
                'stock' => 400,
                'is_featured' => true,
                'short_description' => 'James Clear. An easy and proven way to build good habits & break bad ones.',
                'description' => "No matter your goals, Atomic Habits offers a proven framework for improving every day. James Clear, one of the world's leading experts on habit formation, reveals practical strategies that will teach you exactly how to form good habits, break bad ones, and master the tiny behaviors that lead to remarkable results.",
                'sku' => 'BOOK-ATM-HAB-001',
            ],

            // Home & Living - Kitchen (cat 18)
            [
                'name' => 'Philips Air Fryer HD9200',
                'category_id' => 18,
                'product_type_id' => 1,
                'price' => 12999,
                'sale_price' => 10499,
                'stock' => 60,
                'is_featured' => true,
                'short_description' => '4.1L capacity, Rapid Air Technology, 90% less fat, digital display.',
                'description' => 'Cook your favorite dishes with up to 90% less fat using the Philips Air Fryer. Its unique Rapid Air Technology circulates hot air around your food for a crispy finish without excessive oil. Perfect for fries, chicken wings, and more.',
                'sku' => 'PHIL-AF-HD9200-001',
            ],
            [
                'name' => 'Ceramic Non-Stick Cookware Set (5pcs)',
                'category_id' => 18,
                'product_type_id' => 1,
                'price' => 4999,
                'sale_price' => 3799,
                'stock' => 40,
                'is_featured' => false,
                'short_description' => '5-piece set, PFOA-free ceramic coating, induction compatible.',
                'description' => 'Upgrade your kitchen with this professional 5-piece ceramic cookware set. Features PFOA-free ceramic non-stick coating for healthier cooking, comfortable stay-cool handles, and induction compatibility. Includes frying pan, saucepan, and casserole dish.',
                'sku' => 'COOK-CER-5PC-001',
            ],

            // Sports - Gym (cat 22)
            [
                'name' => 'Adjustable Dumbbell Set 2-32kg',
                'category_id' => 22,
                'product_type_id' => 1,
                'price' => 18999,
                'sale_price' => 15999,
                'stock' => 25,
                'is_featured' => true,
                'short_description' => 'Quick-adjust selector, replaces 15 pairs of dumbbells, compact storage.',
                'description' => 'Save space and money with our Adjustable Dumbbell Set. The innovative dial selector system lets you change weights in seconds, replacing 15 pairs of traditional dumbbells. Perfect for home gyms, covering everything from light toning to heavy strength training.',
                'sku' => 'GYM-DUMB-ADJ-001',
            ],
            [
                'name' => 'Yoga Mat Premium 6mm',
                'category_id' => 22,
                'product_type_id' => 1,
                'price' => 1899,
                'sale_price' => 1499,
                'stock' => 200,
                'is_featured' => false,
                'short_description' => 'Extra-thick 6mm, non-slip texture, eco-friendly TPE, carry strap included.',
                'description' => 'Our Premium Yoga Mat provides the perfect balance of cushioning and stability. The 6mm thickness protects joints during intense sessions while the dual-layer non-slip texture keeps you grounded. Made from eco-friendly TPE material, it\'s free from harmful chemicals.',
                'sku' => 'YOGA-MAT-6MM-001',
            ],
            [
                'name' => 'Running Shoes Pro Boost',
                'category_id' => 9,
                'product_type_id' => 2,
                'price' => 5999,
                'sale_price' => 4799,
                'stock' => 80,
                'is_featured' => true,
                'short_description' => 'Lightweight mesh upper, energy-return foam sole, breathable & durable.',
                'description' => 'Engineered for performance runners, the Pro Boost features a lightweight engineered mesh upper for maximum breathability and our proprietary energy-return foam that propels you forward with every step. The reinforced heel counter provides stability on any terrain.',
                'sku' => 'SHOE-RUN-PRO-001',
            ],
            [
                'name' => 'JBL Tune 760NC Headphones',
                'category_id' => 5,
                'product_type_id' => 1,
                'price' => 12999,
                'sale_price' => 9999,
                'stock' => 55,
                'is_featured' => true,
                'short_description' => 'Active Noise Cancelling, 35hr battery, Bluetooth 5.0, foldable design.',
                'description' => 'Immerse yourself in music with JBL Pure Bass Sound and Active Noise Cancelling technology. The JBL Tune 760NC delivers up to 35 hours of wireless playtime, hands-free calls, and JBL Headphones App support — all in a sleek foldable design.',
                'sku' => 'JBL-T760NC-001',
            ],
        ];

        foreach ($products as $data) {
            $data['status'] = ProductStatus::Active;
            $data['manage_stock'] = true;
            $data['slug'] = Str::slug($data['name']);
            $data['meta_title'] = $data['meta_title'] ?? $data['name'] . ' | NityoGadget';
            $data['meta_description'] = $data['short_description'] ?? '';

            Product::create($data);
        }
    }
}
