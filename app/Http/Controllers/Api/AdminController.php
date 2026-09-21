<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

class AdminController extends Controller
{
    #[OA\Post(
        path: '/api/admins',
        summary: 'Create a new admin account',
        description: "Creates a user and assigns the 'admin' role. Requires the 'admins.create' permission (super_admin only).",
        tags: ['Admins'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Jane Admin'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'SecurePass123!'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'SecurePass123!'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Admin account created successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: "Forbidden - missing 'admins.create' permission"),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $admin = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign the 'admin' role via Spatie - not 'super_admin', which
        // should never be assignable through this endpoint
        $admin->assignRole('admin');

        return response()->json([
            'message' => 'Admin account created successfully',
            'data'    => $admin->only(['id', 'name', 'email']),
        ], 201);
    }

    #[OA\Delete(
        path: '/api/admins/{admin}',
        summary: 'Delete an admin account',
        description: "Requires the 'admins.delete' permission (super_admin only). Cannot be used to delete a super_admin account.",
        tags: ['Admins'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'admin',
                description: 'ID of the admin user to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 5)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Admin account deleted successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: "Forbidden - missing permission, or target is a super_admin"),
            new OA\Response(response: 404, description: 'User is not an admin'),
        ]
    )]
    public function destroy(User $admin): JsonResponse
    {
        // Guard against deleting a super_admin through this endpoint -
        // even if a super_admin somehow has 'admins.delete', this account
        // type should only ever be managed manually / via seeder
        if ($admin->hasRole('super_admin')) {
            return response()->json([
                'message' => 'Cannot delete a super_admin account.',
            ], 403);
        }

        if (!$admin->hasRole('admin')) {
            return response()->json([
                'message' => 'This user is not an admin.',
            ], 404);
        }

        $admin->delete();

        return response()->json([
            'message' => 'Admin account deleted successfully',
        ], 200);
    }
}
