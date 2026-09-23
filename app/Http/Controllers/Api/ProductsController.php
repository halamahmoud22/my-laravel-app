<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

class ProductsController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "List all products (paginated)",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(
                name: "page",
                description: "Page number",
                in: "query",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "A paginated list of products"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $products = Cache::remember("api_products_page_{$page}", now()->addMinutes(15), function () {
            return Product::with('category')->paginate(10);
        });

        return response()->json($products);
    }

    #[OA\Get(
        path: "/api/products/{id}",
        summary: "Show a single product",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "The requested product"),
            new OA\Response(response: 404, description: "Product not found"),
        ]
    )]
    public function show(Product $product): JsonResponse
    {
        $productData = Cache::remember("api_product_{$product->id}", now()->addHours(6), function () use ($product) {
            return $product->load('category');
        });

        return response()->json($productData);
    }

    #[OA\Post(
        path: "/api/products",
        summary: "Create a new product",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "price", "category_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Wireless Mouse"),
                    new OA\Property(property: "price", type: "number", format: "float", example: 29.99),
                    new OA\Property(property: "category_id", type: "integer", example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Product created successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);
        $product = Product::create($validated);
        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    #[OA\Put(
        path: "/api/products/{id}",
        summary: "Update an existing product",
        tags: ["Products"],
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
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Wireless Mouse Pro"),
                    new OA\Property(property: "price", type: "number", format: "float", example: 34.99),
                    new OA\Property(property: "category_id", type: "integer", example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Product updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Product not found"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);
        $product->update($validated);
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
        ], 200);
    }

    #[OA\Delete(
        path: "/api/products/{id}",
        summary: "Delete a product",
        tags: ["Products"],
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
            new OA\Response(response: 200, description: "Product deleted successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Product not found"),
        ]
    )]
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
