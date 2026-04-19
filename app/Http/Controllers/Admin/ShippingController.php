<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RechargeRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    public function index()
    {
        $requests = RechargeRequest::with('user')->latest()->get();
        return view('admin.shipping', compact('requests'));
    }

    public function approve($id)
    {
        DB::beginTransaction();
        try {
            // Lock the row — prevents double-approval if two admins click simultaneously
            $rechargeRequest = RechargeRequest::pending()
                ->lockForUpdate()
                ->findOrFail($id);

            $user = $rechargeRequest->user()->lockForUpdate()->first();

            $user->increment('balance', $rechargeRequest->amount);

            Transaction::create([
                'user_id' => $user->id,
                'type'    => 'deposit',
                'amount'  => $rechargeRequest->amount,
                'note'    => 'شحن رصيد - طلب #' . $rechargeRequest->id,
            ]);

            $rechargeRequest->update([
                'status'     => 'approved',
                'handled_by' => auth()->id(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Recharge approve failed', [
                'request_id' => $id,
                'admin_id'   => auth()->id(),
                'error'      => $e->getMessage(),
            ]);
            return back()->with('error', 'حدث خطأ أثناء معالجة الطلب، يرجى المحاولة مجدداً');
        }

        return back()->with('success', "تم قبول طلب الشحن وإضافة {$rechargeRequest->amount} ₪ لحساب {$user->name}");
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'nullable|string|max:500',
        ]);

        $rechargeRequest = RechargeRequest::pending()->findOrFail($id);

        $rechargeRequest->update([
            'status'        => 'rejected',
            'reject_reason' => $request->reject_reason,
            'handled_by'    => auth()->id(),
        ]);

        return back()->with('success', 'تم رفض طلب الشحن');
    }
}
