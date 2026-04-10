<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user')->latest()->get();
        return view('admin.support', compact('tickets'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in-progress,resolved',
        ]);

        Ticket::findOrFail($id)->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث حالة التذكرة');
    }

    public function show($id)
    {
        $ticket = Ticket::with(['user', 'replies.user'])->findOrFail($id);
        return view('admin.ticket-detail', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|max:2000']);
        $ticket = Ticket::with('user')->findOrFail($id);

        \App\Models\TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $request->message,
            'is_admin'  => true,
        ]);

        // Update ticket status to in-progress if it was open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in-progress']);
        }

        // Notify the client
        if ($ticket->user) {
            \App\Models\Notification::send(
                $ticket->user->id,
                'reply',
                'رد جديد على تذكرتك',
                'تم الرد على تذكرة: ' . ($ticket->title ?? $ticket->subject),
                '/' . 'tickets'
            );
        }

        return back()->with('success', 'تم إرسال الرد بنجاح');
    }
}
