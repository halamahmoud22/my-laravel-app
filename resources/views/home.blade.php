@extends('layouts.metronic')

@section('page-title', 'Store')

@section('content')
    <div class="d-flex flex-column gap-7 gap-lg-10">
        {{-- Welcome section --}}
        <div class="card card-flush">
            <div class="card-body">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-8">
                        <h1 class="text-gray-900 fw-bolder mb-3">{{ __('store.welcome', ['name' => auth()->user()->name]) }}</h1>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <i class="ki-duotone ki-shop text-primary opacity-50" style="font-size: 6rem;"><span
                                class="path1"></span><span class="path2"></span><span class="path3"></span><span
                                class="path4"></span></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product grid section --}}
        <div>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-4 mb-8">
                <div>
                    <h2 class="text-gray-900 fw-bolder mb-2">{{ __('store.latest_arrivals') }}</h2>
                    <p class="text-muted fw-semibold mb-0">{{ __('store.latest_arrivals_desc') }}</p>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-light btn-active-light-primary">{{ __('store.view_all') }} <i
                        class="ki-duotone ki-arrow-right fs-3 ms-2"><span class="path1"></span><span
                            class="path2"></span></i></a>
            </div>

            @include('partials.product-grid')
        </div>
    </div>
@endsection
