<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display all categories.
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->withCount([
                'products' => fn($query) => $query->where('is_active', true),
            ])
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id'   => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $category = Category::create($validated);

        return new CategoryResource($category);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->loadCount([
            'products' => fn($query) => $query->where('is_active', true),
        ]);

        return new CategoryResource($category);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'parent_id'   => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $category->update($validated);

        return new CategoryResource($category);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
