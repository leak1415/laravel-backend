<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Get all active categories
     */
    public function index()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount([
                'products' => fn ($query) => $query->where('is_active', true),
            ])
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Get single category
     */
    public function show(Category $category)
    {
        $category->loadCount([
            'products' => fn ($query) => $query->where('is_active', true),
        ]);

        return new CategoryResource($category);
    }
}
