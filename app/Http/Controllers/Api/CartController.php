<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    #[OA\Get(
        path: "/api/cart",
        summary: "Get current authenticated user's cart",
        tags: ["Cart"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Cart items with total"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $cartItems = Cart::where('user_id', $userId)
            ->with('product')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->product ? $item->product->price : 0);
        });

        return response()->json([
            'data' => $cartItems,
            'total' => round((float) $total, 2),
            'count' => $cartItems->sum('quantity'),
        ], 200);
    }

    #[OA\Post(
        path: "/api/cart",
        summary: "Add a product to cart",
        tags: ["Cart"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["product_id"],
                properties: [
                    new OA\Property(property: "product_id", type: "integer", example: 1),
                    new OA\Property(property: "quantity", type: "integer", example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Item added to cart"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $userId = $request->user()->id;
        $productId = $validated['product_id'];
        $quantity = $validated['quantity'] ?? 1;

        $cart = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $quantity);
            $cart->refresh();
        } else {
            $cart = Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart successfully',
            'data' => $cart->load('product'),
        ], 201);
    }



    #[OA\Put(
        path: "/api/cart/{id}",
        summary: "Update quantity of a cart item",
        tags: ["Cart"],
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
                required: ["quantity"],
                properties: [
                    new OA\Property(property: "quantity", type: "integer", example: 3),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Cart item updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Cart item not found"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function update(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'message' => 'Cart updated successfully',
            'data' => $cart->load('product'),
        ], 200);
    }

    #[OA\Delete(
        path: "/api/cart/{id}",
        summary: "Remove an item from cart",
        tags: ["Cart"],
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
            new OA\Response(response: 200, description: "Item removed from cart successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Cart item not found"),
        ]
    )]
    public function destroy(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        return response()->json([
            'message' => 'Item removed from cart successfully',
        ], 200);
    }

    #[OA\Delete(
        path: "/api/cart",
        summary: "Clear all items from current user's cart",
        tags: ["Cart"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Cart cleared successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function clear(Request $request): JsonResponse
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Cart cleared successfully',
        ], 200);
    }
}
