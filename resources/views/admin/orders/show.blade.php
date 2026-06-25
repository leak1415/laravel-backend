@extends('admin.layouts.app')
@section('title', 'Order #' . $order->id)
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Order #{{ $order->id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">Order Details</div>
                <div class="card-body">
                    <p><strong>User:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
                    <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">{{ $order->status }}</span></p>
                    <p><strong>Date:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">Shipping Details</div>
                <div class="card-body">
                    <p><strong>Address:</strong> {{ $order->shipping_address }}</p>
                    <p><strong>City:</strong> {{ $order->shipping_city }}</p>
                    @if($order->shipping_state)<p><strong>State:</strong> {{ $order->shipping_state }}</p>@endif
                    @if($order->shipping_zip)<p><strong>ZIP:</strong> {{ $order->shipping_zip }}</p>@endif
                    @if($order->shipping_phone)<p><strong>Phone:</strong> {{ $order->shipping_phone }}</p>@endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Update Status</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-auto">
                    <select name="status" class="form-control">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <h3>Order Items</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
            <tr>
                <td>{{ $item->product->name ?? $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->unit_price, 2) }}</td>
                <td>${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
