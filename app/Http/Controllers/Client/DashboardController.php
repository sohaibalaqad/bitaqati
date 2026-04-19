<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Package;
use App\Models\RechargeRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $packages        = Package::withCount(['cards as available_count' => fn($q) => $q->where('status', 'available')])->get();
        $myCards         = $user->cards()->with('package')->latest('sold_at')->get();
        $transactions    = $user->transactions()->latest()->get();
        $myRecharges     = $user->rechargeRequests()->latest()->get();

        $myCardsCount    = $myCards->count();
        $myTransCount    = $transactions->count();
        $myRechargeCount = $myRecharges->count();

        return view('client.dashboard', compact(
            'packages', 'myCards', 'transactions', 'myRecharges',
            'myCardsCount', 'myTransCount', 'myRechargeCount'
        ));
    }

    public function buyCard(Request $request)
    {
        $request->validate(['package_id' => 'required|integer|exists:packages,id']);

        $package = Package::findOrFail($request->package_id);

        try {
            DB::beginTransaction();

            // Lock the user row FIRST — prevents two concurrent requests from
            // both reading a sufficient balance and both successfully decrementing it
            $user = User::lockForUpdate()->findOrFail(auth()->id());

            if ($user->balance < $package->price) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'رصيدك غير كافٍ، قم بشحن رصيدك أولاً']);
            }

            // Lock the card row — prevents the same card from being sold twice
            $card = Card::available()
                ->where('package_id', $package->id)
                ->lockForUpdate()
                ->first();

            if (! $card) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'لا توجد بطاقات متاحة لهذه الباقة']);
            }

            $user->decrement('balance', $package->price);

            $card->update([
                'status'  => 'sold',
                'sold_to' => $user->id,
                'sold_at' => now(),
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type'    => 'purchase',
                'amount'  => $package->price,
                'note'    => 'شراء بطاقة - ' . $package->name,
            ]);

            Invoice::create([
                'user_id'    => $user->id,
                'package_id' => $package->id,
                'card_id'    => $card->id,
                'amount'     => $package->price,
                'status'     => 'paid',
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('buyCard failed', [
                'user_id'    => auth()->id(),
                'package_id' => $package->id,
                'error'      => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء العملية، حاول مجدداً']);
        }

        // ── Notifications (outside transaction — failure here is non-critical) ──
        Notification::send(
            $user->id, 'purchase',
            'تم شراء بطاقة بنجاح',
            'تم خصم ' . number_format($package->price, 2) . '₪ من رصيدك لشراء باقة ' . $package->name,
            '/'
        );

        $admin = User::where('role', 'network_admin')
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if ($admin) {
            Notification::send(
                $admin->id, 'purchase',
                'عملية شراء جديدة',
                $user->name . ' اشترى بطاقة من باقة ' . $package->name,
                '/admin/active-cards'
            );
        }

        return response()->json([
            'success'     => true,
            'new_balance' => (float) $user->fresh()->balance,
            'card'        => [
                'package'  => $package->name,
                'username' => $card->username,
                'password' => $card->password,
            ],
        ]);
    }

    public function submitRecharge(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        RechargeRequest::create([
            'user_id' => auth()->id(),
            'amount'  => $request->amount,
            'note'    => $request->note,
            'status'  => 'pending',
        ]);

        // Notify all network_admin users belonging to this tenant
        $clientName = auth()->user()->name;
        $tenantId   = auth()->user()->tenant_id;

        User::where('role', 'network_admin')
            ->where('tenant_id', $tenantId)
            ->pluck('id')
            ->each(function ($adminId) use ($clientName, $request) {
                Notification::send(
                    $adminId, 'recharge',
                    'طلب شحن رصيد جديد',
                    "{$clientName} طلب شحن " . number_format($request->amount, 2) . ' شيقل',
                    route('admin.shipping')
                );
            });

        return response()->json(['success' => true]);
    }

    public function trackCardUsage(Request $request)
    {
        $request->validate(['card_id' => 'required|integer']);

        $user = auth()->user();
        $card = Card::where('id', $request->card_id)
            ->where('sold_to', $user->id)
            ->first();

        if (! $card) {
            return response()->json(['success' => false, 'message' => 'البطاقة غير موجودة']);
        }

        if (! $card->is_used) {
            $card->update(['is_used' => true, 'first_used_at' => now()]);
        }

        $mikrotikUrl = \App\Models\Setting::get('mikrotik_url', '');

        return response()->json([
            'success'       => true,
            'mikrotik_url'  => $mikrotikUrl,
            'username'      => $card->username,
            'password'      => $card->password,
            'first_used_at' => $card->first_used_at?->format('Y-m-d H:i'),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'phone'        => "required|string|unique:users,phone,{$user->id}",
            'new_password' => 'nullable|min:8|confirmed',
        ], [
            'name.required'          => 'الاسم مطلوب',
            'phone.required'         => 'رقم الهاتف مطلوب',
            'phone.unique'           => 'رقم الهاتف مستخدم من قِبَل مستخدم آخر',
            'new_password.min'       => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->filled('new_password')) {
            if (! $request->filled('current_password') || ! Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'كلمة المرور الحالية غير صحيحة']);
            }
            $user->update(['password' => Hash::make($request->new_password)]);
        }

        return response()->json(['success' => true]);
    }
}
