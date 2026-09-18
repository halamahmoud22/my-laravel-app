@extends('layouts.metronic')

@section('page-title', __('store.cart'))

@section('content')
    <div class="card">
        <div class="card-body">

            @if ($cartItems->isEmpty())
                <div class="text-center text-muted py-10">
                    {{ __('store.cart_empty') }}
                    <a href="{{ route('products.index') }}" class="d-block mt-3">{{ __('store.browse_products') }}</a>
                </div>
            @else
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('store.product') }}</th>
                            <th>{{ __('store.price') }}</th>
                            <th>{{ __('store.quantity') }}</th>
                            <th>{{ __('store.subtotal') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>${{ number_format($item->product->price, 2) }}</td>
                                <td style="max-width: 100px;">
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                            class="form-control form-control-sm" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>${{ number_format($item->quantity * $item->product->price, 2) }}</td>
                                <td>
                                    <form action="{{ route('cart.remove', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-5">
                    <h4 class="mb-0">{{ __('store.total') }}: ${{ number_format($total, 2) }}</h4>
                    <div class="d-flex gap-3">
                        <a href="{{ route('home') }}" class="btn btn-light">{{ __('store.continue_shopping') }}</a>
                        <form action="{{ route('checkout.store') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">{{ __('store.checkout') }}</button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
