<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotificationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int    $userId;
    public string $type;
    public string $title;
    public ?string $message;
    public ?string $url;
    public int    $unreadCount;

    public function __construct(int $userId, string $type, string $title, ?string $message, ?string $url, int $unreadCount)
    {
        $this->userId      = $userId;
        $this->type        = $type;
        $this->title       = $title;
        $this->message     = $message;
        $this->url         = $url;
        $this->unreadCount = $unreadCount;
    }

    /**
     * Private channel per user: notifications.{userId}
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('notifications.' . $this->userId)];
    }

    public function broadcastAs(): string
    {
        return 'new-notification';
    }

    public function broadcastWith(): array
    {
        return [
            'type'         => $this->type,
            'title'        => $this->title,
            'message'      => $this->message,
            'url'          => $this->url,
            'unread_count' => $this->unreadCount,
        ];
    }
}
