<?php

namespace App\Notifications;

use App\Models\Domain;
use Illuminate\Notifications\Notification;

class DomainUpNotification extends Notification
{
    public function __construct(
        private readonly Domain $domain,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'domain_id' => $this->domain->id,
            'domain' => $this->domain->domain,
            'message' => "Домен {$this->domain->domain} снова доступен",
        ];
    }
}
