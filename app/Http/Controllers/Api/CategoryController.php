<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: "/api/categories",
        summary: "List all categories (paginated)",
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(
                name: "search",
                description: "Search category by name",
                in: "query",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "page",
                description: "Page number",
                in: "query",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "A paginated list of categories"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        if (!$request->filled('search')) {
            if ($request->boolean('all')) {
                $categories = Cache::remember('api_all_categories', now()->addHours(24), function () {
                    return Category::withCount('products')->get();
                });
                return response()->json($categories);
            }

            $page = $request->get('page', 1);
            $categories = Cache::remember("api_categories_page_{$page}", now()->addHours(12), function () {
                return Category::withCount('products')->paginate(10);
            });
            return response()->json($categories);
        }

        $query = Category::withCount('products')->where('name', 'like', '%' . $request->search . '%');
        $categories = $request->boolean('all') ? $query->get() : $query->paginate(10);
        return response()->json($categories);
    }

    #[OA\Get(
        path: "/api/categories/{id}",
        summary: "Show a single category",
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "The requested category with products"),
            new OA\Response(response: 404, description: "Category not found"),
        ]
    )]
    public function show(Category $category): JsonResponse
    {
        $categoryData = Cache::remember("api_category_{$category->id}", now()->addHours(6), function () use ($category) {
            return $category->load('products');
        });

        return response()->json($categoryData);
    }

    #[OA\Post(
        path: "/api/categories",
        summary: "Create a new category",
        tags: ["Categories"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Electronics"),
                    new OA\Property(property: "slug", type: "string", example: "electronics"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Category created successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    #[OA\Put(
        path: "/api/categories/{id}",
        summary: "Update an existing category",
        tags: ["Categories"],
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
                    new OA\Property(property: "name", type: "string", example: "Home Appliances"),
                    new OA\Property(property: "slug", type: "string", example: "home-appliances"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Category updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Category not found"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:categories,slug,' . $category->id,
        ]);

        if (isset($validated['name']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ], 200);
    }

    #[OA\Delete(
        path: "/api/categories/{id}",
        summary: "Delete a category",
        tags: ["Categories"],
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
            new OA\Response(response: 200, description: "Category deleted successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Category not found"),
        ]
    )]
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
