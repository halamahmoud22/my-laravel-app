<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Admin: list all payments.
     */
    public function index()
    {
        $this->authorize('viewAny', Payment::class);
        $payments = Payment::latest()->paginate(10);
        return view('payments.index', compact('payments'));
    }

    /**
     * View a single payment.
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        return view('payments.show', compact('payment'));
    }

    /**
     * Process payment for an order (simulated - no real gateway yet).
     */
    public function store(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        $this->authorize('create', [Payment::class, $order]);   // ← changed

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total,
            'payment_method' => 'card',
            'status' => 'completed',
            'transaction_id' => 'txn_' . uniqid(),
        ]);

        $order->update(['status' => 'paid']);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Payment successful.');
    }
}
