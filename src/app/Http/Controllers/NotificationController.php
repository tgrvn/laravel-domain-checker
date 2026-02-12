<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $notifications = $this->notificationService->getForUser($request->user());
        $unreadCount = $this->notificationService->unreadCount($request->user());

        return response()->json([
            'notifications' => $notifications->items(),
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead(DatabaseNotification $notification): JsonResponse
    {
        $this->notificationService->markAsRead($notification);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $this->notificationService->markAllAsRead($request->user());

        return response()->json(['success' => true]);
    }
}
