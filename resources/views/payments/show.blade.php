@extends('layouts.metronic')

@section('title', 'Payment ' . $payment->transaction_id)

@section('content')
<div class="row g-6 g-xl-9">
    <div class="col-xl-8">
        <div class="card card-flush">
            <div class="card-header py-5 border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bolder text-gray-900">{{ __('store.payment_details') }}</h3>
                    @if ($payment->status === 'completed')
                        <span class="badge badge-success">{{ __('store.completed') }}</span>
                    @elseif ($payment->status === 'pending')
                        <span class="badge badge-warning">{{ __('store.pending') }}</span>
                    @else
                        <span class="badge badge-danger">{{ __('store.failed') }}</span>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-6">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <span class="text-muted fw-semibold">{{ __('store.transaction_id') }}</span>
                            <span class="d-block text-gray-900 fw-bold fs-5">{{ $payment->transaction_id }}</span>
                        </div>
                        <div>
                            <span class="text-muted fw-semibold">{{ __('store.method') }}</span>
                            <span class="d-block text-gray-900 fw-bold fs-5">{{ $payment->payment_method ? ucfirst($payment->payment_method) : __('store.not_available') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <span class="text-muted fw-semibold">{{ __('store.amount') }}</span>
                            <span class="d-block text-primary fw-bold fs-5">${{ number_format((float) $payment->amount, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-muted fw-semibold">{{ __('store.date') }}</span>
                            <span class="d-block text-gray-900 fw-bold fs-5">{{ $payment->created_at->format('F d, Y g:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Order --}}
        <div class="card card-flush mt-6">
            <div class="card-header py-5 border-0">
                <h3 class="card-title fw-bolder text-gray-900">{{ __('store.related_order') }}</h3>
            </div>

            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-semibold">{{ __('store.order_number') }}</span>
                        <a href="{{ route('orders.show', $payment->order) }}" class="text-primary text-hover-secondary fw-bold">
                            #{{ str_pad($payment->order->id, 6, '0', STR_PAD_LEFT) }}
                        </a>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-semibold">{{ __('store.order_total') }}</span>
                        <span class="text-gray-900 fw-bold">${{ number_format((float) $payment->order->total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-semibold">{{ __('store.order_status') }}</span>
                        @switch($payment->order->status)
                            @case('pending')
                                <span class="badge badge-warning">{{ __('store.pending') }}</span>
                                @break
                            @case('processing')
                                <span class="badge badge-info">{{ __('store.processing') }}</span>
                                @break
                            @case('completed')
                                <span class="badge badge-success">{{ __('store.completed') }}</span>
                                @break
                            @case('cancelled')
                                <span class="badge badge-danger">{{ __('store.cancelled') }}</span>
                                @break
                            @default
                                <span class="badge badge-secondary">{{ ucfirst($payment->order->status) }}</span>
                        @endswitch
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('orders.show', $payment->order) }}" class="btn btn-light btn-active-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-3 me-2"><span class="path1"></span><span class="path2"></span></i>
                    {{ __('store.view_full_order') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Receipt Card --}}
    <div class="col-xl-4">
        <div class="card card-flush">
            <div class="card-header py-5 border-0">
                <h3 class="card-title fw-bolder text-gray-900">{{ __('store.receipt') }}</h3>
            </div>

            <div class="card-body">
                <div class="mb-7">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-gray-600 fw-semibold">{{ __('store.order_number') }}:</span>
                        <span class="text-gray-900 fw-bold">#{{ str_pad($payment->order->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-gray-600 fw-semibold">{{ __('store.transaction_id') }}:</span>
                        <span class="text-gray-900 fw-bold">{{ $payment->transaction_id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-gray-600 fw-semibold">{{ __('store.date') }}:</span>
                        <span class="text-gray-900 fw-bold">{{ $payment->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-gray-600 fw-semibold">{{ __('store.method') }}:</span>
                        <span class="text-gray-900 fw-bold">{{ $payment->payment_method ? ucfirst($payment->payment_method) : __('store.not_available') }}</span>
                    </div>
                </div>

                <div class="border-top pt-7">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-gray-600 fw-semibold">{{ __('store.total_paid') }}:</span>
                        <span class="text-primary fw-bold fs-4">${{ number_format((float) $payment->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="card-footer pt-0 border-top-0">
                <a href="{{ route('orders.show', $payment->order) }}" class="btn btn-primary w-100">
                    {{ __('store.back_to_order') }}
                </a>
            </div>
        </div>

        {{-- Status Info --}}
        <div class="card card-flush mt-6 bg-light-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
            <div class="card-body py-5">
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-{{ $payment->status === 'completed' ? 'check-circle' : ($payment->status === 'pending' ? 'clock' : 'close-circle') }} fs-2hx me-3 text-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}"><span class="path1"></span><span class="path2"></span></i>
                    <div>
                        <span class="text-gray-900 fw-bold d-block">
                            @if ($payment->status === 'completed')
                                {{ __('store.payment_successful') }}
                            @elseif ($payment->status === 'pending')
                                {{ __('store.payment_pending') }}
                            @else
                                {{ __('store.payment_failed') }}
                            @endif
                        </span>
                        <span class="text-muted fw-semibold fs-7">
                            @if ($payment->status === 'completed')
                                {{ __('store.payment_successful_desc') }}
                            @elseif ($payment->status === 'pending')
                                {{ __('store.payment_pending_desc') }}
                            @else
                                {{ __('store.payment_failed_desc') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
