<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Home & Garden', 'description' => 'Home improvement and garden supplies'],
            ['name' => 'Books', 'description' => 'Books and educational materials'],
            ['name' => 'Sports & Outdoors', 'description' => 'Sports equipment and outdoor gear'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}
