@extends('layouts.metronic')

@section('page-title', 'Store')

@section('content')
    <div class="d-flex flex-column gap-7 gap-lg-10">
        {{-- Welcome section --}}
        <div class="card card-flush">
            <div class="card-body">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-8">
                        <h1 class="text-gray-900 fw-bolder mb-3">Welcome, {{ auth()->user()->name }}!</h1>
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
                    <h2 class="text-gray-900 fw-bolder mb-2">Latest arrivals</h2>
                    <p class="text-muted fw-semibold mb-0">Check out the newest additions to our collection.</p>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-light btn-active-light-primary">View all <i
                        class="ki-duotone ki-arrow-right fs-3 ms-2"><span class="path1"></span><span
                            class="path2"></span></i></a>
            </div>

            @include('partials.product-grid')
        </div>

        {{-- Quick links for admins --}}
        @can('admin')
            <div class="card card-flush">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title fw-bolder text-gray-900">Admin Actions</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('products.index') }}" class="card card-hover text-center py-5">
                                <div class="card-body">
                                    <i class="ki-duotone ki-basket fs-3x text-primary mb-3"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    <h4 class="card-title text-gray-900 fw-bolder">Manage Products</h4>
                                    <p class="text-muted fw-semibold mb-0">Create, edit, or remove products</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('categories.index') }}" class="card card-hover text-center py-5">
                                <div class="card-body">
                                    <i class="ki-duotone ki-tag fs-3x text-primary mb-3"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    <h4 class="card-title text-gray-900 fw-bolder">Manage Categories</h4>
                                    <p class="text-muted fw-semibold mb-0">Organize product categories</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        {{-- Super admin actions --}}
        @can('manage-admins')
            <div class="card card-flush bg-light-danger">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title fw-bolder text-danger">Super Admin</h3>
                </div>
                <div class="card-body pt-0">
                    <p class="text-muted fw-semibold mb-3">Manage system administrators and permissions.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-danger btn-sm">Manage Admins</a>
                </div>
            </div>
        @endcan
    </div>
@endsection
