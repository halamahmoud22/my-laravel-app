@extends('layouts.metronic')

@section('page-title', __('store.order_hash', ['id' => $order->id]))

@section('content')
    <div class="card mb-5">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">{{ __('store.order_hash', ['id' => $order->id]) }}</h4>
                <span
                    class="text-muted">{{ __('user.created', ['date' => $order->created_at->translatedFormat('M , d, Y')]) }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span
                    class="badge badge-light-{{ match ($order->status) {
                        'pending' => 'warning',
                        'paid' => 'info',
                        'shipped' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    } }} fs-6">
                    {{ match ($order->status) {
                        'pending' => __('store.pending'),
                        'paid' => __('store.paid'),
                        'shipped' => __('store.shipped'),
                        'completed' => __('store.completed'),
                        'cancelled' => __('store.cancelled'),
                        default => ucfirst($order->status),
                    } }}
                </span>
                <a href="{{ route('orders.index', request()->boolean('my_orders') ? ['my_orders' => 1] : []) }}"
                    class="btn btn-sm btn-light">
                    {{ request()->boolean('my_orders') ? __('store.back_to_my_orders') : (auth()->user()?->isAdmin() ? __('store.view_all_orders') : __('store.back_to_orders')) }}
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>{{ __('store.product') }}</th>
                        <th>{{ __('store.price') }}</th>
                        <th>{{ __('store.quantity') }}</th>
                        <th>{{ __('store.subtotal') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-5">
                <h4 class="mb-0">{{ __('store.total') }}: ${{ number_format($order->total, 2) }}</h4>
                @can('create', [\App\Models\Payment::class, $order])
                    @if ($order->status === 'pending')
                        <form action="{{ route('payments.store', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">{{ __('store.pay_now') }}</button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>
@endsection
