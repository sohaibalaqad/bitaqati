<?php
namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller {
    private function getAdminId(): int {
        return User::where('role','admin')->value('id') ?? 1;
    }

    public function index() {
        $adminId = $this->getAdminId();
        $messages = ChatMessage::where(function($q) use ($adminId) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $adminId);
        })->orWhere(function($q) use ($adminId) {
            $q->where('sender_id', $adminId)->where('receiver_id', auth()->id());
        })->orderBy('created_at')->get();

        // Mark admin messages as read
        ChatMessage::where('sender_id', $adminId)->where('receiver_id', auth()->id())->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('client.chat', compact('messages'));
    }

    public function send(Request $request) {
        $request->validate(['message' => 'required|string|max:1000']);

        $adminId = $this->getAdminId();
        $msg = ChatMessage::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $adminId,
            'message'     => $request->message,
        ]);

        // Notify admin of new client message
        \App\Models\Notification::send(
            $adminId,
            'message',
            'رسالة جديدة من ' . auth()->user()->name,
            \Str::limit($request->message, 60),
            '/admin/chat/' . auth()->id()
        );

        return response()->json(['success' => true, 'message' => [
            'id' => $msg->id, 'message' => $msg->message,
            'created_at' => $msg->created_at->format('H:i'),
            'is_mine' => true,
        ]]);
    }

    public function poll(Request $request) {
        $adminId = $this->getAdminId();
        $lastId  = (int) $request->get('last_id', 0);
        $messages = ChatMessage::where('sender_id', $adminId)
            ->where('receiver_id', auth()->id())
            ->where('id', '>', $lastId)
            ->orderBy('created_at')->get(['id','message','created_at']);

        if ($messages->isNotEmpty()) {
            ChatMessage::where('sender_id', $adminId)->where('receiver_id', auth()->id())
                ->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
        }
        return response()->json(['messages' => $messages->map(fn($m) => [
            'id' => $m->id, 'message' => $m->message,
            'created_at' => $m->created_at->format('H:i'), 'is_mine' => false,
        ])]);
    }
}
