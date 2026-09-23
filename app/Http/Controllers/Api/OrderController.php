<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;
use App\Jobs\SendOrderConfirmation;

class OrderController extends Controller
{
    #[OA\Get(
        path: "/api/orders",
        summary: "List orders (paginated)",
        tags: ["Orders"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "status",
                description: "Filter by order status (pending, processing, completed, cancelled)",
                in: "query",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "order_id",
                description: "Filter by specific order ID",
                in: "query",
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "page",
                description: "Page number",
                in: "query",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "A paginated list of orders"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['admin', 'super_admin']);

        $query = Order::latest();

        if (!$isAdmin || $request->boolean('my_orders')) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_id')) {
            $query->where('id', $request->order_id);
        }

        $orders = $query->with(['items.product', 'user:id,name,email'])->paginate(10);

        return response()->json($orders, 200);
    }

    #[OA\Get(
        path: "/api/orders/{id}",
        summary: "Show details of an order",
        tags: ["Orders"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Order details"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Order not found"),
        ]
    )]
    public function show(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['admin', 'super_admin']);

        if ($order->user_id !== $user->id && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load(['items.product', 'payment', 'user:id,name,email']), 200);
    }

    #[OA\Post(
        path: "/api/orders",
        summary: "Place a new order from current cart",
        tags: ["Orders"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 201, description: "Order created successfully"),
            new OA\Response(response: 400, description: "Cart is empty"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
    // 1. validate + build the order
        $user = $request->user();

        $cartItems = Cart::where('user_id', $user->id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.',
            ], 400);
        }

        $order = DB::transaction(function () use ($user, $cartItems) {
            $total = $cartItems->sum(function ($item) {
                return $item->quantity * ($item->product ? $item->product->price : 0);
            });

            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'pending',
            ]);
        // 2. copy cart items -> order_items (snapshot)
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product ? $item->product->price : 0,
                ]);
            }
        // 3. clear the cart
            Cart::where('user_id', $user->id)->delete();
        // 4. dispatch the background job — NEW
            SendOrderConfirmation::dispatch($order);
            // Log this order to the
            // 'logs' database connection (separate DB from orders/carts)
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'order_placed',
                'description' => "Order #{$order->id} placed",
            ]);

            return $order;
        });

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order->load('items.product'),
        ], 201);
    }

    #[OA\Put(
        path: "/api/orders/{id}",
        summary: "Update order status",
        tags: ["Orders"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(property: "status", type: "string", example: "cancelled"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Order status updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Order not found"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function update(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,completed,cancelled',
        ]);

        $newStatus = $validated['status'];
        $isOwner = $order->user_id === $user->id;

        // Customer cancelling their own order — only allowed while still pending
        if ($newStatus === 'cancelled' && $isOwner && $order->status === 'pending') {
            $order->update(['status' => 'cancelled']);
            return response()->json(['message' => 'Order cancelled', 'data' => $order], 200);
        }

        // Admin cancelling any order, any time
        if ($newStatus === 'cancelled' && $user->can('orders.cancel-any')) {
            $order->update(['status' => 'cancelled']);
            return response()->json(['message' => 'Order cancelled', 'data' => $order], 200);
        }

        // Admin marking an order as shipped — must currently be paid
        if ($newStatus === 'shipped' && $user->can('orders.mark-shipped')) {
            if ($order->status !== 'paid') {
                return response()->json(['message' => 'Order must be paid before it can be shipped.'], 422);
            }
            $order->update(['status' => 'shipped']);
            return response()->json(['message' => 'Order marked as shipped', 'data' => $order], 200);
        }

        // Customer confirming receipt — must be theirs, and currently shipped
        if ($newStatus === 'completed' && $isOwner && $order->status === 'shipped') {
            $order->update(['status' => 'completed']);
            return response()->json(['message' => 'Order marked as completed', 'data' => $order], 200);
        }

        return response()->json(['message' => 'This status transition is not allowed.'], 403);
    }
}
