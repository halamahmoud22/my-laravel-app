@extends('layouts.metronic')

@section('content')
<div class="app-main flex-column flex-row-fluid">
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Header -->
        <div id="kt_app_header" class="app-header">
            <div class="app-container container-xxl d-flex align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Create Product
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

                        <form action="{{ route('admin.products.store') }}" method="POST">
                            @csrf

                            <div class="fv-row mb-10">
                                <label class="required form-label">Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                            </div>

                            <div class="fv-row mb-10">
                                <label class="required form-label">Price</label>
                                <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="form-control">
                            </div>

                            <div class="fv-row mb-10">
                                <label class="required form-label">Stock</label>
                                <input type="number" name="stock" value="{{ old('stock') }}" class="form-control">
                            </div>

                            <div class="fv-row mb-10">
                                <label class="required form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Create Product</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
