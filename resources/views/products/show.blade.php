@extends(auth()->check() ? 'layouts.metronic' : 'layouts.guest')

@section('page-title', $product->name)

@section('content')
    <div class="app-main flex-column flex-row-fluid">
        <div class="d-flex flex-column flex-column-fluid">
            <!-- Header -->
            <div id="kt_app_header" class="app-header">
                <div class="app-container container-xxl d-flex align-items-center">
                    <div class="d-flex align-items-center flex-grow-1">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            {{ $product->name }}
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div class="app-container container-xxl">
                    <div class="row g-6 mb-6">
                        <!-- Product Details Card -->
                        <div class="col-lg-8">
                            <div class="card card-flush">
                                <div class="card-header border-0 pt-6">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-gray-900">{{ __('store.product_details') }}</span>
                                    </h3>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row mb-8">
                                        <div class="col-lg-6">
                                            <div class="mb-6">
                                                <label class="form-label fw-bold text-gray-900 mb-3">{{ __('store.category') }}</label>
                                                <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">
                                                    {{ $product->category->name }}
                                                </span>
                                            </div>
                                            <div class="mb-6">
                                                <label class="form-label fw-bold text-gray-900 mb-3">{{ __('store.price') }}</label>
                                                <span
                                                    class="fw-bold text-gray-900 fs-5">${{ number_format($product->price, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-6">
                                                <label class="form-label fw-bold text-gray-900 mb-3">{{ __('store.stock') }}</label>
                                                <span class="fw-bold text-gray-900 fs-5">
                                                    @if ($product->stock > 0)
                                                        <span class="badge badge-light-success">{{ $product->stock }}
                                                            units</span>
                                                    @else
                                                        <span class="badge badge-light-danger">{{ __('store.out_of_stock') }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-6">
                                        <label class="form-label fw-bold text-gray-900 mb-3">{{ __('store.product_description') }}</label>
                                        <p class="text-gray-700">{{ $product->description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions & Image Column -->
                        <div class="col-lg-4">
                            <!-- Product Image Card -->
                            <div class="card card-flush mb-6">
                                <div class="card-body text-center p-6 bg-light rounded">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                        class="img-fluid rounded" style="max-height: 280px; object-fit: contain;"
                                        onerror="this.src='{{ asset('assets/media/products/1.png') }}'" />
                                </div>
                            </div>

                            <div class="card card-flush">
                                <div class="card-body pt-0">
                                    <!-- Add to Cart -->
                                    @if ($product->stock > 0)
                                        <div class="d-flex gap-3 mt-5">
                                            <a href="{{ route('home') }}" class="btn btn-light">{{ __('store.continue_shopping') }}</a>

                                            @auth
                                                <form action="{{ route('cart.add', $product) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">{{ __('store.add_to_cart') }}</button>
                                                </form>
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-primary">{{ __('store.login_to_purchase') }}</a>
                                            @endauth
                                        </div>
                                    @else
                                        <button type="button" class="btn btn-secondary w-100" disabled>
                                            {{ __('store.out_of_stock') }}
                                        </button>
                                    @endif

                                    <!-- Admin Actions -->
                                    @can('admin', $product)
                                        <div class="separator my-6"></div>
                                        <div class="mb-4">
                                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                                class="btn btn-light-primary w-100 mb-3">
                                                <span class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none">
                                                        <path
                                                            d="M17.896 13.6915L9.5 22.0871M21.5563 11.4065L12.1995 20.7633C11.9069 21.056 11.4935 21.3569 10.8158 21.3569C10.1382 21.3569 9.72479 21.056 9.43214 20.7633L3.5 14.8211V3.5H14.3433L20.2854 9.44214C20.5781 9.73479 21.5563 10.5832 21.5563 11.4065Z"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M12.5 8.5H8.5" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                {{ __('user.edit') }} {{ __('store.product') }}
                                            </a>
                                        </div>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light-danger w-100"
                                                onclick="return confirm('Are you sure you want to delete this product?')">
                                                <span class="svg-icon svg-icon-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none">
                                                        <path
                                                            d="M5 7h14M8 7V4c0-1 1-2 2-2h4c1 0 2 1 2 2v3M10 11v6M14 11v6M6 7h12v11c0 1-1 2-2 2H8c-1 0-2-1-2-2V7Z"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                {{ __('user.delete') }} {{ __('store.product') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
