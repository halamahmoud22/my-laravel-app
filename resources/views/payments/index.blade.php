@extends('layouts.metronic')

@section('title', 'Payments')

@section('content')
<div class="card card-flush">
    <div class="card-header py-5 border-0">
        <h3 class="card-title fw-bolder text-gray-900">Payment History</h3>
    </div>

    @if ($payments->count())
        <table class="table table-striped table-row-bordered gy-5 gs-7">
            <thead>
                <tr class="fw-bold fs-6 text-muted border-bottom-2 border-gray-200">
                    <th>Transaction ID</th>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>
                            <span class="text-gray-900 fw-bold">{{ $payment->transaction_id }}</span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $payment->order) }}" class="text-primary text-hover-secondary fw-bold">
                                #{{ str_pad($payment->order->id, 6, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                        <td>
                            <span class="text-gray-900 fw-bold">${{ number_format((float) $payment->amount, 2) }}</span>
                        </td>
                        <td>
                            @if ($payment->status === 'completed')
                                <span class="badge badge-success">Completed</span>
                            @elseif ($payment->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-danger">Failed</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted fw-semibold">{{ ucfirst($payment->payment_method ?? 'N/A') }}</span>
                        </td>
                        <td>
                            <span class="text-muted fw-semibold">{{ $payment->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('payments.show', $payment) }}" class="btn btn-icon btn-light-primary btn-sm">
                                <i class="ki-duotone ki-arrow-right fs-3"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $payments->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="card-body text-center py-15">
            <i class="ki-duotone ki-credit-cart fs-5x text-muted mb-5"><span class="path1"></span><span class="path2"></span></i>
            <h3 class="text-gray-900 fw-bolder">No payments yet</h3>
            <p class="text-muted mb-0">Payments will appear here once orders are placed and paid.</p>
        </div>
    @endif
</div>
@endsection
