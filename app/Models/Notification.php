<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Events\NewNotificationEvent;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'title', 'message', 'url', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create and broadcast a real-time notification to a specific user.
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

        // Keep only last 50 notifications per user
        $ids = static::where('user_id', $userId)->orderByDesc('id')->pluck('id')->skip(50);
        if ($ids->isNotEmpty()) static::whereIn('id', $ids)->delete();

        // Count unread for the badge
        $unreadCount = static::where('user_id', $userId)->where('is_read', false)->count();

        // 🔴 Broadcast real-time via Laravel Reverb WebSocket
        try {
            event(new NewNotificationEvent($userId, $type, $title, $message, $url, $unreadCount));
        } catch (\Throwable $e) {
            // Graceful fallback: if Reverb is not running, polling handles it
            \Log::debug('Reverb broadcast skipped: ' . $e->getMessage());
        }
    }
}
