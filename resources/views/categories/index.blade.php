@extends('layouts.metronic')

@section('content')
    <div class="app-main flex-column flex-row-fluid">
        <div class="d-flex flex-column flex-column-fluid">
            <!-- Header -->
            <div id="kt_app_header" class="app-header">
                <div class="app-container container-xxl d-flex align-items-center">
                    <div class="d-flex align-items-center flex-grow-1">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            {{ __('store.categories') }}
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div class="app-container container-xxl">
                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger mb-6">
                            <div class="alert-text">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Add Category Button -->
                    @can('admin')
                        <div class="mb-6">
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" />
                                        <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </span>
                                {{ __('store.add_category') }}
                            </a>
                        </div>
                    @endcan

                    <!-- Categories Table Card -->
                    <div class="card card-flush">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">
                                <div class="card-title">
                                    <form action="{{ route('categories.index') }}" method="GET"
                                        class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="17.0365" y="15.1395" width="8.15546"
                                                    height="8.15546" rx="2" transform="rotate(45 17.0365 15.1395)"
                                                    fill="black" />
                                                <path
                                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19Z"
                                                    fill="black" />
                                            </svg>
                                        </span>
                                        <input type="text" name="search" value="{{ $search ?? '' }}"
                                            class="form-control form-control-solid w-250px ps-15"
                                            placeholder="{{ __('store.search') }} {{ __('store.categories') }}..." />
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <table class="table table-row-dashed table-row-gray-300 gy-7" id="kt_table_categories">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td>
                                                <a href="{{ route('products.index', ['category' => $category->id]) }}"
                                                    class="btn btn-light-primary btn-sm fw-bold">
                                                    {{ $category->name }}
                                                </a>
                                            </td>
                                            <td class="text-end">
                                                @can('admin')
                                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                                        class="btn btn-light-primary btn-sm me-2">
                                                        <i class="ki-duotone ki-pencil fs-5 me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        {{ __('user.edit') }}
                                                    </a>
                                                    <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-light-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this category?')">
                                                            <i class="ki-duotone ki-trash fs-5 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            {{ __('user.delete') }}
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-gray-500 py-10">
                                                {{ __('store.no_categories') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
