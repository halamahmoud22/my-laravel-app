@extends('layouts.metronic')
@section('content')
<div class="app-main flex-column flex-row-fluid">
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Header -->
        <div id="kt_app_header" class="app-header">
            <div class="app-container container-xxl d-flex align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ __('user.create') }} {{ __('store.category') }}
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
                            <div class="alert alert-danger mb-6">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.categories.store') }}" method="POST">
                            @csrf

                            <div class="fv-row mb-10">
                                <label class="required form-label">{{ __('user.name') }}</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ __('store.enter_category_name') }}">
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('categories.index') }}" class="btn btn-light me-3">{{ __('user.cancel') }}</a>
                                <button type="submit" class="btn btn-primary">{{ __('user.create') }} {{ __('store.category') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
