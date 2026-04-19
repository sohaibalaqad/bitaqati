<?php

namespace App\Models;

use App\Events\NewNotificationEvent;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'user_id', 'type', 'title', 'message', 'url', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create and broadcast a real-time notification to a specific user.
     * Works without tenant scope intentionally — notifications target a user directly.
     */
    public static function send(int $userId, string $type, string $title, ?string $message = null, ?string $url = null): void
    {
        static::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
        ]);

        // Keep only last 50 notifications per user (use withoutGlobalScopes to be safe)
        $ids = static::withoutGlobalScopes()->where('user_id', $userId)
                     ->orderByDesc('id')->pluck('id')->skip(50);
        if ($ids->isNotEmpty()) {
            static::withoutGlobalScopes()->whereIn('id', $ids)->delete();
        }

        // Count unread for badge
        $unreadCount = static::withoutGlobalScopes()
                             ->where('user_id', $userId)
                             ->where('is_read', false)
                             ->count();

        // Broadcast real-time via Laravel Reverb WebSocket
        try {
            event(new NewNotificationEvent($userId, $type, $title, $message, $url, $unreadCount));
        } catch (\Throwable $e) {
            \Log::debug('Reverb broadcast skipped: ' . $e->getMessage());
        }
    }
}
