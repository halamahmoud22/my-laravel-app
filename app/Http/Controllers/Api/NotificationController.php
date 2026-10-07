<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

// Reusable shape of one notification (used by the responses below)
#[OA\Schema(
    schema: 'Notification',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'type', type: 'string', example: 'App\\Notifications\\OrderPlacedNotification'),
        new OA\Property(property: 'data', type: 'object', example: ['order_id' => 12, 'message' => 'New order placed']),
        new OA\Property(property: 'read_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class NotificationController extends Controller
{
    #[OA\Get(
        path: '/api/notifications',
        summary: 'List my notifications (paginated, 15 per page)',
        security: [['bearerAuth' => []]],          // requires the Bearer token
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'current_page', type: 'integer'),
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Notification')),
                    new OA\Property(property: 'total', type: 'integer'),
                ]
            )),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->paginate(15);

        return response()->json($notifications);
    }

    #[OA\Get(
        path: '/api/notifications/unread-count',
        summary: 'Count my unread notifications',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        responses: [
            new OA\Response(response: 200, description: 'Unread count', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'unread_count', type: 'integer', example: 3)]
            )),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json(['unread_count' => $count]);
    }

    #[OA\Patch(
        path: '/api/notifications/{id}/read',
        summary: 'Mark one notification as read',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Marked as read', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Notification marked as read.'),
                    new OA\Property(property: 'notification', ref: '#/components/schemas/Notification'),
                ]
            )),
            new OA\Response(response: 404, description: 'Not found (or not yours)'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read.',
            'notification' => $notification,
        ]);
    }

    #[OA\Post(
        path: '/api/notifications/mark-all-read',
        summary: 'Mark all my unread notifications as read',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        responses: [
            new OA\Response(response: 200, description: 'All marked as read', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'message', type: 'string', example: 'All notifications marked as read.')]
            )),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    #[OA\Delete(
        path: '/api/notifications/{id}',
        summary: 'Delete one notification',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'message', type: 'string', example: 'Notification deleted successfully.')]
            )),
            new OA\Response(response: 404, description: 'Not found (or not yours)'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully.'], 200);
    }
}
