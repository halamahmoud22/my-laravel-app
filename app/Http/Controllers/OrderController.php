<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Events\OrderPlaced;

class OrderController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the user's orders.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);
        $query = Order::latest();

        $user = auth()->user();
        $isAdmin = $user && in_array($user->role, ['admin', 'super_admin']);

        if (! $isAdmin || $request->boolean('my_orders')) {
            $query->where('user_id', auth()->id()); // customers or admin viewing personal orders: scoped to their own
        }
        // admins viewing all orders: no scope added, see everything
        // --- Filters ---
        if ($request->filled('order_id')) {
            $query->where('id', $request->order_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($isAdmin && ! $request->boolean('my_orders') && $request->filled('customer')) {
            $customer = $request->customer;
            $query->whereHas('user', function ($q) use ($customer) {
                $q->where('name', 'like', "%{$customer}%");
                if (is_numeric($customer)) {
                    $q->orWhere('id', $customer);
                }
            });
        }
        // Filter by Product (by ID or Name)
        if ($request->filled('product_id')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        } elseif ($request->filled('product')) {
            $productName = $request->product;
            $query->whereHas('items.product', function ($q) use ($productName) {
                $q->where('name', 'like', "%{$productName}%");
            });
        }
        // Filter by Date or Date Range
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        $orders = $query->with(['user', 'items.product'])->paginate(10)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('orders.index', compact('orders', 'products'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.product');
        return view('orders.show', compact('order'));
    }

    /**
     * Store a newly created order from cart.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Order::class);

        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);

        $order = Order::create([
            'user_id' => auth()->id(),
            'total' => $total,
            'status' => 'pending',
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        Cart::where('user_id', auth()->id())->delete();

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }
        Cart::where('user_id', auth()->id())->delete();
        // 4. dispatch event — NEW
        event(new OrderPlaced($order));
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'order_placed',
            'description' => "Order #{$order->id} placed",
        ]);

        // 5. respond to the browser (this happens instantly, doesn't wait for step 4's email)
        return redirect()->route('orders.show', $order)->with('success', 'Order placed!');
    }
}
