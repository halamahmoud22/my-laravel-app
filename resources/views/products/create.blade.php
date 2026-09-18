@extends('layouts.metronic')

@section('content')
    <div class="app-main flex-column flex-row-fluid">
        <div class="d-flex flex-column flex-column-fluid">
            <!-- Header -->
            <div id="kt_app_header" class="app-header">
                <div class="app-container container-xxl d-flex align-items-center">
                    <div class="d-flex align-items-center flex-grow-1">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            {{ __('user.create') }} {{ __('store.product') }}
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
                                    <label class="required form-label">{{ __('user.name') }}</label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="{{ __('store.enter_product_name') }}">
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="required form-label">{{ __('store.price') }}</label>
                                    <input type="number" step="0.01" name="price" class="form-control"
                                        placeholder="0.00">
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="required form-label">{{ __('store.stock') }}</label>
                                    <input type="number" name="stock" class="form-control" placeholder="0">
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="required form-label">{{ __('store.category') }}</label>
                                    <select name="category_id" class="form-select">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="form-label">{{ __('store.product_description') }}</label>
                                    <textarea name="description" class="form-control" rows="3"
                                        placeholder="{{ __('store.enter_product_description') }}"></textarea>
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="form-label">{{ __('store.image') }} {{ __('store.product') }} (e.g.
                                        1.png - 22.png)</label>
                                    <div class="d-flex align-items-center gap-4">
                                        <input type="text" name="image" class="form-control" placeholder="1.png">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('products.index') }}"
                                        class="btn btn-light me-3">{{ __('user.cancel') }}</a>
                                    <button type="submit" class="btn btn-primary">{{ __('user.create') }}
                                        {{ __('store.product') }}</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
