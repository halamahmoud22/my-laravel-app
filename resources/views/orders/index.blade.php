@extends('layouts.metronic')

@section('page-title', 'Orders')

@section('content')
<div class="d-flex flex-column gap-5">
    {{-- Filters Card --}}
    <div class="card card-flush">
        <div class="card-header pt-6">
            <h3 class="card-title fw-bolder text-gray-900">
                <i class="ki-duotone ki-filter fs-3 me-2 text-primary">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Filter Orders
            </h3>
            @if(request()->hasAny(['customer', 'product_id', 'status', 'date_from', 'date_to', 'order_id']))
                <div class="card-toolbar">
                    <span class="badge badge-light-primary fs-7">Filters Applied</span>
                </div>
            @endif
        </div>
        <div class="card-body pt-2">
            <form method="GET" action="{{ route('orders.index') }}">
                <div class="row g-4 align-items-end">
                    {{-- Customer Filter (Admin only) --}}
                    @can('admin')
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label fw-bold fs-7 text-gray-700">Customer Name / ID</label>
                            <div class="position-relative">
                                <input type="text"
                                       name="customer"
                                       class="form-control form-control-solid"
                                       placeholder="Search customer..."
                                       value="{{ request('customer') }}">
                            </div>
                        </div>
                    @endcan

                    {{-- Product Filter --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label fw-bold fs-7 text-gray-700">Product</label>
                        <select name="product_id" class="form-select form-select-solid">
                            <option value="">All Products</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}" {{ (string)request('product_id') === (string)$prod->id ? 'selected' : '' }}>
                                    {{ $prod->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-bold fs-7 text-gray-700">Order Status</label>
                        <select name="status" class="form-select form-select-solid">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-bold fs-7 text-gray-700">Date From</label>
                        <input type="date"
                               name="date_from"
                               class="form-control form-control-solid"
                               value="{{ request('date_from') }}">
                    </div>

                    {{-- Date To --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-bold fs-7 text-gray-700">Date To</label>
                        <input type="date"
                               name="date_to"
                               class="form-control form-control-solid"
                               value="{{ request('date_to') }}">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-12 d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-light btn-active-light-primary">
                            Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-duotone ki-magnifier fs-4 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Orders List Card --}}
    <div class="card card-flush">
        <div class="card-header pt-6">
            <h3 class="card-title fw-bolder text-gray-900">
                Orders List
                <span class="fs-6 text-gray-400 fw-normal ms-2">({{ $orders->total() }} total)</span>
            </h3>
        </div>
        <div class="card-body pt-0">
            @if($orders->isEmpty())
                <div class="text-center py-12">
                    <i class="ki-duotone ki-basket fs-4x text-muted mb-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    <h4 class="text-gray-800 fw-bold">No orders found</h4>
                    <p class="text-muted fs-7">Try adjusting your filters or search criteria.</p>
                    @if(request()->hasAny(['customer', 'product_id', 'status', 'date_from', 'date_to', 'order_id']))
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-light-primary mt-2">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-4">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>Order #</th>
                                @can('admin')
                                    <th>Customer</th>
                                @endcan
                                <th>Products</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Placed Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="text-gray-800 text-hover-primary fw-bold">
                                            #{{ $order->id }}
                                        </a>
                                    </td>
                                    @can('admin')
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold">{{ $order->user->name ?? 'Guest/Unknown' }}</span>
                                                <span class="text-muted fs-8">{{ $order->user->email ?? '' }}</span>
                                            </div>
                                        </td>
                                    @endcan
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($order->items as $item)
                                                <span class="text-gray-700 fs-7">
                                                    {{ $item->product->name ?? 'Product #' . $item->product_id }}
                                                    <span class="badge badge-light fs-9 ms-1">x{{ $item->quantity }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-gray-900">${{ number_format($order->total, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ match($order->status) {
                                            'pending' => 'warning',
                                            'paid' => 'info',
                                            'shipped' => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        } }} fw-bold">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-gray-700 fs-7">{{ $order->created_at->format('M d, Y - h:i A') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
                    <div class="fs-7 text-gray-600">
                        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries
                    </div>
                    <div>
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
