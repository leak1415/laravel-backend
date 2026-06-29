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
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $user = $request->user();
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $cartItems->each(function ($item): void {
            if (! $item->product || ! $item->product->is_active || ! $item->product->category || ! $item->product->category->is_active) {
                throw ValidationException::withMessages([
                    'cart' => 'One or more products in your cart are no longer available.',
                ]);
            }

            if ($item->product->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'cart' => "Not enough stock for {$item->product->name}.",
                ]);
            }
        });

        $subtotal = round($cartItems->sum(function ($item) {
            $price = $item->product->sale_price ?? $item->product->price;

            return (float) $price * $item->quantity;
        }), 2);

        $shippingFee = 0.0;
        $tax = 0.0;
        $discount = 0.0;
        $total = round($subtotal + $shippingFee + $tax - $discount, 2);
        $validated = $request->validated();
        $shippingName = $validated['shipping_name'] ?? $user->name;
        $shippingEmail = $validated['shipping_email'] ?? $user->email;
        $shippingCountry = $validated['shipping_country'] ?? null;
        $paymentMethod = $validated['payment_method'] ?? null;
        $shippingPhone = $validated['shipping_phone'] ?? null;
        $shippingState = $validated['shipping_state'] ?? null;
        $shippingZip = $validated['shipping_zip'] ?? null;
        $notes = $validated['notes'] ?? null;

        $order = DB::transaction(function () use (
            $user,
            $cartItems,
            $subtotal,
            $shippingFee,
            $tax,
            $discount,
            $total,
            $shippingName,
            $shippingEmail,
            $shippingCountry,
            $paymentMethod,
            $shippingPhone,
            $shippingState,
            $shippingZip,
            $notes
        ) {
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'shipping_name' => $shippingName,
                'shipping_email' => $shippingEmail,
                'shipping_address' => request()->input('shipping_address'),
                'shipping_city' => request()->input('shipping_city'),
                'shipping_state' => $shippingState,
                'shipping_zip' => $shippingZip,
                'shipping_country' => $shippingCountry,
                'shipping_phone' => $shippingPhone,
                'notes' => $notes,
            ]);

            foreach ($cartItems as $item) {
                $unitPrice = (float) ($item->product->sale_price ?? $item->product->price);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => round($unitPrice * $item->quantity, 2),
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
