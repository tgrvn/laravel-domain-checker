<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

readonly class NotificationService
{
    public function __construct(
        private NotificationRepository $notificationRepository,
    ) {}

    public function getForUser(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return $this->notificationRepository->getForUser($user, $perPage);
    }

    public function unreadCount(User $user): int
    {
        return $this->notificationRepository->unreadCount($user);
    }

    public function markAsRead(DatabaseNotification $notification): void
    {
        $this->notificationRepository->markAsRead($notification);
    }

    public function markAllAsRead(User $user): void
    {
        $this->notificationRepository->markAllAsRead($user);
    }
}
