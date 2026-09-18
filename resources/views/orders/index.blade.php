@extends('layouts.metronic')

@section('page-title', request()->boolean('my_orders') ? __('store.orders') : __('store.all_orders'))

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
                    {{ __('store.filter_orders') }}
                </h3>
                @if (request()->hasAny(['customer', 'product_id', 'status', 'date_from', 'date_to', 'order_id']))
                    <div class="card-toolbar">
                        <span class="badge badge-light-primary fs-7">{{ __('store.filters_applied') }}</span>
                    </div>
                @endif
            </div>
            <div class="card-body pt-2">
                <form method="GET" action="{{ route('orders.index') }}">
                    @if (request()->boolean('my_orders'))
                        <input type="hidden" name="my_orders" value="1">
                    @endif
                    <div class="row g-4 align-items-end">
                        {{-- Customer Filter (Admin only, when viewing all orders) --}}
                        @if (auth()->user()?->isAdmin() && !request()->boolean('my_orders'))
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label
                                    class="form-label fw-bold fs-7 text-gray-700">{{ __('store.customer_name_id') }}</label>
                                <div class="position-relative">
                                    <input type="text" name="customer" class="form-control form-control-solid"
                                        placeholder="{{ __('store.search_customer_placeholder') }}"
                                        value="{{ request('customer') }}">
                                </div>
                            </div>
                        @endif

                        {{-- Product Filter --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label fw-bold fs-7 text-gray-700">{{ __('store.product') }}</label>
                            <select name="product_id" class="form-select form-select-solid">
                                <option value="">{{ __('store.all_products') }}</option>
                                @foreach ($products as $prod)
                                    <option value="{{ $prod->id }}"
                                        {{ (string) request('product_id') === (string) $prod->id ? 'selected' : '' }}>
                                        {{ $prod->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Filter --}}
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label fw-bold fs-7 text-gray-700">{{ __('store.order_status') }}</label>
                            <select name="status" class="form-select form-select-solid">
                                <option value="">{{ __('store.all_statuses') }}</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                    {{ __('store.pending') }}</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>
                                    {{ __('store.paid') }}</option>
                                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>
                                    {{ __('store.shipped') }}</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                    {{ __('store.completed') }}</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                    {{ __('store.cancelled') }}</option>
                            </select>
                        </div>

                        {{-- Date From --}}
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label fw-bold fs-7 text-gray-700">{{ __('store.date_from') }}</label>
                            <input type="date" name="date_from" class="form-control form-control-solid"
                                value="{{ request('date_from') }}">
                        </div>

                        {{-- Date To --}}
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label fw-bold fs-7 text-gray-700">{{ __('store.date_to') }}</label>
                            <input type="date" name="date_to" class="form-control form-control-solid"
                                value="{{ request('date_to') }}">
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-12 d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('orders.index', request()->boolean('my_orders') ? ['my_orders' => 1] : []) }}"
                                class="btn btn-light btn-active-light-primary">
                                {{ __('store.reset') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ki-duotone ki-magnifier fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('store.filter') }}
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
                    {{ request()->boolean('my_orders') ? __('store.orders') : __('store.orders_list') }}
                    <span
                        class="fs-6 text-gray-400 fw-normal ms-2">{{ __('store.orders_total_count', ['count' => $orders->total()]) }}</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                @if ($orders->isEmpty())
                    <div class="text-center py-12">
                        <i class="ki-duotone ki-basket fs-4x text-muted mb-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        <h4 class="text-gray-800 fw-bold">{{ __('store.no_orders_found') }}</h4>
                        <p class="text-muted fs-7">{{ __('store.try_adjusting_filters') }}</p>
                        @if (request()->hasAny(['customer', 'product_id', 'status', 'date_from', 'date_to', 'order_id']))
                            <a href="{{ route('orders.index', request()->boolean('my_orders') ? ['my_orders' => 1] : []) }}"
                                class="btn btn-sm btn-light-primary mt-2">
                                {{ __('store.clear_filters') }}
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-4">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th>{{ __('store.order_number') }}</th>
                                    @if (auth()->user()?->isAdmin() && !request()->boolean('my_orders'))
                                        <th>{{ __('store.customer') }}</th>
                                    @endif
                                    <th>{{ __('store.products') }}</th>
                                    <th>{{ __('store.total') }}</th>
                                    <th>{{ __('store.status') }}</th>
                                    <th>{{ __('store.placed_date') }}</th>
                                    <th class="text-end">{{ __('store.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.show', [$order] + (request()->boolean('my_orders') ? ['my_orders' => 1] : [])) }}"
                                                class="text-gray-800 text-hover-primary fw-bold">
                                                #{{ $order->id }}
                                            </a>
                                        </td>
                                        @if (auth()->user()?->isAdmin() && !request()->boolean('my_orders'))
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="text-gray-800 fw-bold">{{ $order->user->name ?? __('store.guest') }}</span>
                                                    <span class="text-muted fs-8">{{ $order->user->email ?? '' }}</span>
                                                </div>
                                            </td>
                                        @endif
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @foreach ($order->items as $item)
                                                    <span class="text-gray-700 fs-7">
                                                        {{ $item->product->name ?? __('store.product_number', ['id' => $item->product_id]) }}
                                                        <span
                                                            class="badge badge-light fs-9 ms-1">x{{ $item->quantity }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold text-gray-900">${{ number_format($order->total, 2) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-light-{{ match ($order->status) {
                                                    'pending' => 'warning',
                                                    'paid' => 'info',
                                                    'shipped' => 'primary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                    default => 'secondary',
                                                } }} fw-bold">
                                                {{ match ($order->status) {
                                                    'pending' => __('store.pending'),
                                                    'paid' => __('store.paid'),
                                                    'shipped' => __('store.shipped'),
                                                    'completed' => __('store.completed'),
                                                    'cancelled' => __('store.cancelled'),
                                                    default => ucfirst($order->status),
                                                } }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-gray-700 fs-7">{{ $order->created_at->format('M d, Y - h:i A') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('orders.show', [$order] + (request()->boolean('my_orders') ? ['my_orders' => 1] : [])) }}"
                                                class="btn btn-sm btn-light-primary">
                                                {{ __('store.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
                        <div class="fs-7 text-gray-600">
                            {{ __('store.showing_entries', ['first' => $orders->firstItem() ?? 0, 'last' => $orders->lastItem() ?? 0, 'total' => $orders->total()]) }}
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
