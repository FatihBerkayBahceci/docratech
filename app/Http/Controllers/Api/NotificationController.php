<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index(Request $request): JsonResponse
    {
        // Simple notification implementation
        $notifications = collect([
            [
                'id' => 1,
                'title' => 'Welcome to DocraTech',
                'message' => 'Your account has been successfully created',
                'type' => 'success',
                'read_at' => null,
                'created_at' => now()->subHours(1),
            ],
            [
                'id' => 2,
                'title' => 'System Update',
                'message' => 'System maintenance scheduled for tonight',
                'type' => 'info',
                'read_at' => null,
                'created_at' => now()->subHours(3),
            ],
        ]);

        return response()->json([
            'data' => $notifications->take(20),
            'meta' => [
                'total' => $notifications->count(),
                'unread_count' => $notifications->whereNull('read_at')->count(),
            ]
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId): JsonResponse
    {
        return response()->json([
            'message' => 'Notification marked as read',
            'data' => ['id' => $notificationId, 'read_at' => now()]
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }
} 