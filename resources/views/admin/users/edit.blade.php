@extends('layouts.metronic')

@section('title', 'Edit Admin')

@section('content')
<div class="card card-flush">
    <div class="card-header py-5 border-0">
        <h3 class="card-title fw-bolder text-gray-900">{{ __('user.edit_admin') }}</h3>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="card-body">
        @csrf
        @method('PUT')

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="name" class="required form-label">{{ __('user.name') }}</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                    placeholder="{{ __('user.full_name') }}" value="{{ old('name', $user->name) }}" required />
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="email" class="required form-label">{{ __('user.email') }}</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="{{ __('user.email_address') }}" value="{{ old('email', $user->email) }}" required />
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="role" class="required form-label">{{ __('user.role') }}</label>
                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>{{ __('user.admin') }}</option>
                    <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>{{ __('user.super_admin') }}</option>
                </select>
                @error('role')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="password" class="form-label">{{ __('user.password') }}</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ __('user.password_char') }}" />
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="password_confirmation" class="form-label">{{ __('user.confirm_password') }}</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    placeholder="{{ __('user.confirm_password') }}" />
            </div>
        </div>

        <div class="d-flex gap-3 pt-5">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-active-light-primary">
                {{ __('user.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ki-duotone ki-check fs-3 me-2"><span class="path1"></span><span class="path2"></span></i>
                {{ __('user.update_admin') }}
            </button>
        </div>
    </form>
</div>
@endsection
