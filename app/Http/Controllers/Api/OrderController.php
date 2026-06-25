<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $user = $request->user();
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);

        $order = DB::transaction(function () use ($user, $cartItems, $total, $request) {
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip' => $request->shipping_zip,
                'shipping_phone' => $request->shipping_phone,
                'notes' => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            Cart::where('user_id', $user->id)->delete();

            return $order;
        });

        $order->load('orderItems.product');

        return response()->json([
            'message' => 'Order placed successfully',
            'data' => new OrderResource($order),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::with('orderItems.product')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load('orderItems.product');

        return response()->json(new OrderResource($order));
    }
}
