@extends('admin.layouts.app')
@section('title', 'Create Category')
@section('content')
    <h1>Create Category</h1>
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
