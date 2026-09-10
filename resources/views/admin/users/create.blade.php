@extends('layouts.metronic')

@section('title', 'Create Admin')

@section('content')
<div class="card card-flush">
    <div class="card-header py-5 border-0">
        <h3 class="card-title fw-bolder text-gray-900">Create Admin User</h3>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="card-body">
        @csrf

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="name" class="required form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                    placeholder="Full name" value="{{ old('name') }}" required />
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="email" class="required form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="Email address" value="{{ old('email') }}" required />
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="role" class="required form-label">Role</label>
                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="">Select a role</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @error('role')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="password" class="required form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Minimum 8 characters" required />
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-md-12">
                <label for="password_confirmation" class="required form-label">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    placeholder="Confirm password" required />
            </div>
        </div>

        <div class="d-flex gap-3 pt-5">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-active-light-primary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ki-duotone ki-check fs-3 me-2"><span class="path1"></span><span class="path2"></span></i>
                Create Admin
            </button>
        </div>
    </form>
</div>
@endsection
