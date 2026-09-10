@extends('layouts.metronic')

@section('page-title', 'Order #' . $order->id)

@section('content')
<div class="card mb-5">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Order #{{ $order->id }}</h4>
            <span class="text-muted">Placed {{ $order->created_at->format('M d, Y') }}</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge badge-light-{{ match($order->status) {
                'pending' => 'warning',
                'paid' => 'info',
                'shipped' => 'primary',
                'completed' => 'success',
                'cancelled' => 'danger',
            } }} fs-6">
                {{ ucfirst($order->status) }}
            </span>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-light">View All Orders</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
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
            <h4 class="mb-0">Total: ${{ number_format($order->total, 2) }}</h4>

            @if($order->status === 'pending')
                <form action="{{ route('payments.store', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Pay Now</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
