<?php
namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller {
    public function index() {
        $tickets = Ticket::where('user_id', auth()->id())->with('replies')->latest()->get();
        return view('client.tickets', compact('tickets'));
    }

    public function store(Request $request) {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|string',
            'message'  => 'required|string|max:2000',
        ], [
            'title.required'   => 'عنوان التذكرة مطلوب',
            'message.required' => 'نص الرسالة مطلوب',
        ]);

        $ticket = Ticket::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'subject'     => $request->title,
            'category'    => $request->category,
            'description' => $request->message,
            'priority'    => 'medium',
            'status'      => 'open',
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $request->message,
            'is_admin'  => false,
        ]);

        // Notify admin of new ticket
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            \App\Models\Notification::send(
                $admin->id,
                'ticket',
                'تذكرة دعم جديدة من ' . auth()->user()->name,
                $request->title,
                '/admin/support/' . $ticket->id
            );
        }

        return back()->with('success', 'تم إرسال تذكرة الدعم بنجاح. سيرد عليك فريقنا قريباً.');
    }

    public function reply(Request $request, $id) {
        $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);
        $request->validate(['message' => 'required|string|max:2000']);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $request->message,
            'is_admin'  => false,
        ]);

        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'تم إرسال ردك بنجاح');
    }
}
