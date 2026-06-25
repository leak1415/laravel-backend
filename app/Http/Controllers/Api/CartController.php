<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cartItems = Cart::with('product.category')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json(CartResource::collection($cartItems));
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        $cart = Cart::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
            ],
            [
                'quantity' => $request->quantity,
            ]
        );

        $cart->load('product.category');

        return response()->json([
            'message' => 'Item added to cart',
            'data' => new CartResource($cart),
        ]);
    }

    public function update(UpdateCartRequest $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart->update(['quantity' => $request->quantity]);
        $cart->load('product.category');

        return response()->json([
            'message' => 'Cart updated',
            'data' => new CartResource($cart),
        ]);
    }

    public function destroy(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
