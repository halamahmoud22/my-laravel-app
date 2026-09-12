@extends('layouts.metronic')

@section('content')
<div class="app-main flex-column flex-row-fluid">
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Header -->
        <div id="kt_app_header" class="app-header">
            <div class="app-container container-xxl d-flex align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Edit Product
                    </h1>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div class="app-container container-xxl">
                <div class="card">
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="fv-row mb-10">
                                <label class="required form-label">Name</label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" placeholder="Enter product name">
                            </div>

                            <div class="fv-row mb-10">
                                <label class="required form-label">Price</label>
                                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="form-control" placeholder="0.00">
                            </div>

                            <div class="fv-row mb-10">
                                <label class="required form-label">Stock</label>
                                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="form-control" placeholder="0">
                            </div>

                            <div class="fv-row mb-10">
                                <label class="required form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="fv-row mb-10">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div class="fv-row mb-10">
                                <label class="form-label">Product Image (e.g. 1.png - 22.png or custom filename)</label>
                                <div class="d-flex align-items-center gap-4">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='{{ asset('assets/media/products/1.png') }}'" />
                                    <input type="text" name="image" value="{{ old('image', $product->image) }}" class="form-control" placeholder="1.png">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('products.index') }}" class="btn btn-light me-3">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Product</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
