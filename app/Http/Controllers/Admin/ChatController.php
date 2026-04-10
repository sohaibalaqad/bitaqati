<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller {
    public function index() {
        $adminId = auth()->id();
        // Get all client IDs who have messages with admin
        $clientIds = ChatMessage::where(function($q) use ($adminId) {
            $q->where('sender_id', $adminId)->orWhere('receiver_id', $adminId);
        })->selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as client_id', [$adminId])
          ->distinct()->pluck('client_id');

        $clients = User::where('role','client')->whereIn('id', $clientIds)->get();
        $allClients = User::where('role','client')->get();
        return view('admin.chat', compact('clients','allClients'));
    }

    public function conversation($clientId) {
        $client = User::where('role','client')->findOrFail($clientId);
        $adminId = auth()->id();
        $messages = ChatMessage::where(function($q) use ($adminId, $clientId) {
            $q->where('sender_id', $adminId)->where('receiver_id', $clientId);
        })->orWhere(function($q) use ($adminId, $clientId) {
            $q->where('sender_id', $clientId)->where('receiver_id', $adminId);
        })->orderBy('created_at')->get();

        ChatMessage::where('sender_id', $clientId)->where('receiver_id', $adminId)->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['messages' => $messages->map(fn($m) => [
            'id' => $m->id, 'message' => $m->message,
            'created_at' => $m->created_at->format('H:i'),
            'is_mine' => $m->sender_id === $adminId,
        ]), 'client' => ['id'=>$client->id,'name'=>$client->name]]);
    }

    public function send(Request $request) {
        $request->validate(['client_id'=>'required|exists:users,id','message'=>'required|string|max:1000']);

        $msg = ChatMessage::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->client_id,
            'message'     => $request->message,
        ]);

        // Notify client of new message
        $admin = auth()->user();
        \App\Models\Notification::send(
            $request->client_id,
            'message',
            'رسالة جديدة من الإدارة',
            \Str::limit($request->message, 60),
            '/chat'
        );

        return response()->json(['success' => true, 'message' => [
            'id'=>$msg->id,'message'=>$msg->message,
            'created_at'=>$msg->created_at->format('H:i'),'is_mine'=>true,
        ]]);
    }

    public function poll(Request $request) {
        $clientId = (int) $request->get('client_id');
        $lastId   = (int) $request->get('last_id', 0);
        $adminId  = auth()->id();
        $messages = ChatMessage::where('sender_id', $clientId)
            ->where('receiver_id', $adminId)
            ->where('id', '>', $lastId)->orderBy('created_at')->get(['id','message','created_at']);
        return response()->json(['messages' => $messages->map(fn($m) => [
            'id'=>$m->id,'message'=>$m->message,
            'created_at'=>$m->created_at->format('H:i'),'is_mine'=>false,
        ])]);
    }
}
