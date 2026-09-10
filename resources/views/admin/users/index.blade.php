@extends('layouts.metronic')

@section('title', 'Manage Admins')

@section('content')
<div class="card card-flush">
    <div class="card-header py-5 border-0">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="card-title fw-bolder text-gray-900">Admins</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="ki-duotone ki-plus fs-3 me-2"><span class="path1"></span><span class="path2"></span></i>
                Add Admin
            </a>
        </div>
    </div>

    @if ($admins->count())
        <table class="table table-striped table-row-bordered gy-5 gs-7">
            <thead>
                <tr class="fw-bold fs-6 text-muted border-bottom-2 border-gray-200">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                    <tr>
                        <td>
                            <span class="text-gray-900 fw-bold">{{ $admin->name }}</span>
                        </td>
                        <td>
                            <span class="text-muted fw-semibold">{{ $admin->email }}</span>
                        </td>
                        <td>
                            @if ($admin->role === 'super_admin')
                                <span class="badge badge-danger">Super Admin</span>
                            @else
                                <span class="badge badge-primary">Admin</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted fw-semibold">{{ $admin->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.edit', $admin) }}" class="btn btn-icon btn-light-primary btn-sm me-2">
                                <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                            @if (auth()->user()->id !== $admin->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" style="display:inline;" onsubmit="return confirm('Delete this admin?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-light-danger btn-sm">
                                        <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $admins->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="card-body text-center py-15">
            <i class="ki-duotone ki-user fs-5x text-muted mb-5"><span class="path1"></span><span class="path2"></span></i>
            <h3 class="text-gray-900 fw-bolder">No admins yet</h3>
            <p class="text-muted mb-5">Create your first admin user below.</p>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create Admin</a>
        </div>
    @endif
</div>
@endsection
