<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_returns_products_for_active_categories(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Active category',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'SKU-001',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'price' => 19.99,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
    }

    public function test_product_show_returns_product_for_an_active_category(): void
    {
        $category = Category::create([
            'name' => 'Audio',
            'slug' => 'audio',
            'description' => 'Active category',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Headphones',
            'slug' => 'headphones',
            'sku' => 'SKU-002',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'price' => 49.99,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/products/'.$product->id);

        $response->assertOk();
        $response->assertJsonPath('id', $product->id);
    }
}
