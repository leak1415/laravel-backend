<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['category_slug' => 'electronics', 'name' => 'Wireless Headphones', 'description' => 'High-quality wireless headphones with noise cancellation.', 'price' => 79.99, 'stock' => 50],
            ['category_slug' => 'electronics', 'name' => 'Smart Watch', 'description' => 'Feature-rich smartwatch with health tracking.', 'price' => 199.99, 'stock' => 30],
            ['category_slug' => 'electronics', 'name' => 'Bluetooth Speaker', 'description' => 'Portable Bluetooth speaker with deep bass.', 'price' => 49.99, 'stock' => 100],
            ['category_slug' => 'clothing', 'name' => 'Classic T-Shirt', 'description' => 'Comfortable cotton t-shirt available in multiple colors.', 'price' => 19.99, 'stock' => 200],
            ['category_slug' => 'clothing', 'name' => 'Denim Jacket', 'description' => 'Stylish denim jacket for all seasons.', 'price' => 89.99, 'stock' => 40],
            ['category_slug' => 'clothing', 'name' => 'Running Shoes', 'description' => 'Lightweight running shoes with excellent cushioning.', 'price' => 129.99, 'stock' => 60],
            ['category_slug' => 'home-and-garden', 'name' => 'Garden Tool Set', 'description' => 'Complete set of essential garden tools.', 'price' => 39.99, 'stock' => 75],
            ['category_slug' => 'home-and-garden', 'name' => 'Indoor Plant Pot', 'description' => 'Ceramic plant pot with modern design.', 'price' => 24.99, 'stock' => 150],
            ['category_slug' => 'books', 'name' => 'Laravel for Beginners', 'description' => 'A comprehensive guide to Laravel framework.', 'price' => 34.99, 'stock' => 80],
            ['category_slug' => 'books', 'name' => 'JavaScript: The Good Parts', 'description' => 'Deep dive into JavaScript fundamentals.', 'price' => 29.99, 'stock' => 60],
            ['category_slug' => 'sports-and-outdoors', 'name' => 'Yoga Mat', 'description' => 'Non-slip yoga mat with carrying strap.', 'price' => 29.99, 'stock' => 90],
            ['category_slug' => 'sports-and-outdoors', 'name' => 'Camping Tent', 'description' => '4-person waterproof camping tent.', 'price' => 159.99, 'stock' => 25],
        ];

        foreach ($products as $product) {
            $category = \App\Models\Category::where('slug', $product['category_slug'])->first();
            if ($category) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']),
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                ]);
            }
        }
    }
}
