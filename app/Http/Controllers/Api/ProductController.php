<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function baseQuery(): Builder
    {
        return Product::query()
            ->where('is_active', true)
            ->whereHas('category', fn (Builder $query) => $query->where('is_active', true))
            ->with('category')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->baseQuery();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function (Builder $searchQuery) use ($search) {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(12);

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category' => fn ($relation) => $relation->where('is_active', true), 'reviews.user']);
        abort_unless($product->is_active && $product->category, 404);
        $product->loadCount('reviews')->loadAvg('reviews', 'rating');

        return response()->json(new ProductResource($product));
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|max:255']);

        $query = $this->baseQuery();
        $search = $request->input('q');

        $query->where(function (Builder $searchQuery) use ($search) {
            $searchQuery->where('name', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        });

        $products = $query->paginate(12);

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}
