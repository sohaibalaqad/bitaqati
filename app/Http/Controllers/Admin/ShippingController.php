<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RechargeRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $requests = RechargeRequest::with('user')->latest()->get();
        return view('admin.shipping', compact('requests'));
    }

    public function approve($id)
    {
        $rechargeRequest = RechargeRequest::pending()->findOrFail($id);
        $user = $rechargeRequest->user;

        $user->increment('balance', $rechargeRequest->amount);

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $rechargeRequest->amount,
            'note' => 'شحن رصيد - طلب #' . $rechargeRequest->id,
        ]);

        $rechargeRequest->update([
            'status' => 'approved',
            'handled_by' => auth()->id(),
        ]);

        return back()->with('success', "تم قبول طلب الشحن وإضافة {$rechargeRequest->amount} ₪ لحساب {$user->name}");
    }

    public function reject(Request $request, $id)
    {
        $rechargeRequest = RechargeRequest::pending()->findOrFail($id);

        $rechargeRequest->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
            'handled_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم رفض طلب الشحن');
    }
}
