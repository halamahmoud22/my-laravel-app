@extends('layouts.guest')

@section('page-title', 'Welcome')

@section('content')

    <section class="bg-primary py-15 py-lg-20 mx-n12 mt-n12">
        <div class="container-xxl">
            <div class="row align-items-center gy-8">
                <div class="col-lg-7">
                    <span class="badge badge-light-primary text-primary fs-7 mb-5">{{ __('store.curated_collection') }}</span>
                    <h1 class="text-white fw-bolder display-5 mb-5">{{ __('store.welcome_hero_title') }}</h1>
                    <p class="text-white opacity-75 fs-4 fw-semibold mw-lg-600px mb-0">{{ __('store.welcome_hero_subtitle') }}</p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <i class="ki-duotone ki-shop text-white opacity-25" style="font-size: 10rem;"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 py-lg-15">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4 mb-8">
            <div>
                <h2 class="text-gray-900 fw-bolder mb-2">{{ __('store.latest_arrivals') }}</h2>
                <p class="text-muted fw-semibold mb-0">{{ __('store.latest_arrivals_desc') }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-light btn-active-light-primary">{{ __('store.view_all_products') }} <i class="ki-duotone ki-arrow-right fs-3 ms-2"><span class="path1"></span><span class="path2"></span></i></a>
        </div>

        @include('partials.product-grid')
    </section>

@endsection
