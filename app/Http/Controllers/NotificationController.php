<?php
namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Http\Request;
class NotificationController extends Controller {
    public function index() {
        $notifications = Notification::where('user_id', auth()->id())->latest()->paginate(20);
        Notification::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['notifications' => $notifications]);
    }
    public function unreadCount() {
        $count = Notification::where('user_id', auth()->id())->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }
    public function markAllRead() {
        Notification::where('user_id', auth()->id())->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
