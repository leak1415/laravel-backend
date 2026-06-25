@extends('admin.layouts.app')
@section('title', 'Products')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Products</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" width="50" alt="">
                    @else
                        <span class="text-muted">No image</span>
                    @endif
                </td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? 'N/A' }}</td>
                <td>${{ number_format($product->price, 2) }}</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $products->links() }}
@endsection
