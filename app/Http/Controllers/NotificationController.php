<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // withoutGlobalScopes() — Notification has BelongsToTenant; we query by
        // user_id which already scopes correctly, so bypass the tenant scope here
        // to avoid a double-WHERE that could drop rows when tenant context is unset.
        $notifications = Notification::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        Notification::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['notifications' => $notifications]);
    }

    public function unreadCount()
    {
        $count = Notification::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markAllRead()
    {
        Notification::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
