<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $wishlist = Wishlist::with('product.category')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json(WishlistResource::collection($wishlist));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => [
                'required',
                Rule::exists('products', 'id')->where('is_active', true),
            ],
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        $wishlist->load('product.category');

        return response()->json([
            'message' => 'Added to wishlist',
            'data' => new WishlistResource($wishlist),
        ]);
    }

    public function destroy(Request $request, Wishlist $wishlist): JsonResponse
    {
        if ($wishlist->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $wishlist->delete();

        return response()->json(['message' => 'Removed from wishlist']);
    }
}
