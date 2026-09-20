<div class="row g-6 g-xl-8">
    <div class="col-12">
        <form action="{{ route('products.index') }}" method="GET" class="d-flex mb-8">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control me-3"
                placeholder="{{ __('store.search_placeholder') }}" />
            <button type="submit" class="btn btn-primary">
                <i class="ki-duotone ki-magnifier fs-2"><span class="path1"></span><span class="path2"></span></i>
                {{ __('store.search') }}
            </button>
        </form>
    </div>
    @forelse ($products as $product)
        <div class="col-sm-6 col-xl-4 col-xxl-3">
            <div class="card card-flush h-100">
                <a href="{{ route('products.show', $product) }}" class="d-block bg-light rounded-top overflow-hidden">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                        class="w-100 object-fit-cover" style="height: 220px;"
                        onerror="this.src='{{ asset('assets/media/products/1.png') }}'" />
                </a>
                <div class="card-body d-flex flex-column p-6">
                    <div class="text-muted fs-7 fw-bold mb-2">{{ $product->category->name ?? 'Featured product' }}</div>
                    <a href="{{ route('products.show', $product) }}"
                        class="text-gray-900 text-hover-primary fs-4 fw-bolder mb-3">{{ $product->name }}</a>
                    <div class="d-flex align-items-center justify-content-between mt-auto pt-4">
                        <span
                            class="text-gray-900 fs-2 fw-bolder">${{ number_format((float) $product->price, 2) }}</span>
                        @auth
                            <form method="POST" action="{{ route('cart.add', $product) }}">
                                @csrf
                                <button type="submit" class="btn btn-icon btn-light-primary" title="{{ __('store.add_to_cart') }}"
                                    aria-label="Add {{ $product->name }} to cart">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-icon btn-light-primary"
                                title="{{ __('store.login_to_purchase') }}" aria-label="Log in to purchase {{ $product->name }}"><i
                                    class="bi bi-cart-plus"><span class="path1"></span><span
                                        class="path2"></span></i></a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-body text-center py-15">
                    <i class="ki-duotone ki-package fs-5x text-muted mb-5"><span class="path1"></span><span
                            class="path2"></span><span class="path3"></span></i>
                    <h3 class="text-gray-900 fw-bolder">{{ __('store.no_products_yet') }}</h3>
                    <p class="text-muted mb-0">{{ __('store.no_products_desc') }}</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if (method_exists($products, 'hasPages') && $products->hasPages())
    <div class="d-flex justify-content-center mt-10">{{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
@endif
