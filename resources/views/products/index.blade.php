@extends('layouts.metronic')

@section('content')

    <div class="app-main flex-column flex-row-fluid">
        <div class="d-flex flex-column flex-column-fluid">
            <!-- Header -->
            <div id="kt_app_header" class="app-header">
                <div class="app-container container-xxl d-flex align-items-center">
                    <div class="d-flex align-items-center flex-grow-1">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Products
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div class="app-container container-xxl">
                    <!-- Add Product Button -->
                    <div class="mb-6">
                        @can('admin')
                            <a href="{{ route('admin.products.create')}}" class="btn btn-primary">
                                <i class="ki-duotone ki-plus fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Add Product
                            </a>
                        @endcan
                    </div>

                    <!-- Products Table -->
                    <div class="card">
                        <div class="card-body pt-0">
                            <table class="table table-row-dashed table-row-gray-300 gy-7">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800">
                                        <th>Product Name</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-50px me-4">
                                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="object-fit-cover rounded" style="width: 50px; height: 50px;" onerror="this.src='{{ asset('assets/media/products/1.png') }}'" />
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <a href="{{ route('products.show', $product->id) }}" class="text-gray-900 text-hover-primary fw-bold fs-6">{{ $product->name }}</a>
                                                        <span class="text-muted fw-semibold fs-7">{{ $product->category->name ?? 'General' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>${{ number_format($product->price, 2) }}</td>
                                            <td class="text-end">
                                                @can('update', $product)
                                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-light-primary btn-sm me-2">
                                                        <i class="ki-duotone ki-pencil fs-5 me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Edit
                                                    </a>
                                                @endcan
                                                @can('delete', $product)
                                                    {{-- delete form --}}
                                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-light-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">
                                                            <i class="ki-duotone ki-trash fs-5 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
