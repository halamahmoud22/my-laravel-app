@extends('layouts.metronic')

@section('page-title', 'Dashboard')

@section('content')
<div class="row g-5 g-xl-8">
    <div class="col-xl-4">
        <div class="card bg-primary hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <div class="text-white fw-bolder fs-2 mb-2 mt-5">{{ $productCount }}</div>
                <div class="fw-bold text-white">{{ __('store.total_products') }}</div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card bg-dark hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <div class="text-gray-100 fw-bolder fs-2 mb-2 mt-5">{{ $categoryCount }}</div>
                <div class="fw-bold text-gray-100">{{ __('store.total_categories') }}</div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card bg-body-white hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <div class="text-gray-900 fw-bolder fs-2 mb-2 mt-5">{{ $orderCount }}</div>
                <div class="fw-bold text-gray-400">{{ __('store.total_orders') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-5 g-xl-8 mt-1">
    <div class="col-xl-12">
        <div class="card card-xl-stretch mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder text-dark">{{ __('store.recent_orders') }}</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder text-muted">
                                <th>{{ __('store.order_number') }}</th>
                                <th>{{ __('store.customer') }}</th>
                                <th>{{ __('store.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td class="text-dark fw-bolder">#{{ $order->id }}</td>
                                    <td>{{ $order->user->name ?? __('store.guest') }}</td>
                                    <td class="text-muted">{{ __('user.created', ['date' => $order->created_at->translatedFormat('M , d, Y')]) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">{{ __('store.no_orders_yet') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
