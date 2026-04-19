<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'client')->latest()->get();
        return view('admin.users', compact('users'));
    }

    public function profile($id)
    {
        $user = User::findOrFail($id);
        $cards = $user->cards()->with('package')->latest('sold_at')->get();
        $transactions = $user->transactions()->latest()->get();
        $invoices = $user->invoices()->with(['package', 'card'])->latest()->get();
        $rechargeRequests = $user->rechargeRequests()->latest()->get();
        return view('admin.user-profile', compact('user', 'cards', 'transactions', 'invoices', 'rechargeRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        User::create([
            'tenant_id' => auth()->user()->tenant_id, // explicit — never rely on trait alone
            'name'      => $request->name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'client',
            'balance'   => 0,
            'status'    => 'active',
        ]);

        return back()->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        return back()->with('success', 'تم تحديث حالة المستخدم');
    }

    public function addBalance(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);
        $user = User::findOrFail($id);
        $user->increment('balance', $request->amount);

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'note' => $request->note ?? 'إضافة رصيد من الإدارة',
        ]);

        return back()->with('success', "تم إضافة {$request->amount} ₪ لحساب {$user->name}");
    }

    public function withdrawBalance(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);
        $user = User::findOrFail($id);

        if ($request->amount > $user->balance) {
            return back()->with('error', 'المبلغ أكبر من الرصيد المتوفر');
        }

        $user->decrement('balance', $request->amount);

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'withdraw',
            'amount' => $request->amount,
            'note' => $request->note ?? 'سحب رصيد من الإدارة',
        ]);

        return back()->with('success', "تم سحب {$request->amount} ₪ من حساب {$user->name}");
    }

    public function updateSettings(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Handle toggle status from profile page
        if ($request->has('toggle_status')) {
            $user->status = $user->status === 'active' ? 'inactive' : 'active';
            $user->save();
            return back()->with('success', 'تم تحديث حالة المستخدم');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => "required|string|unique:users,phone,{$id}",
            'email' => "nullable|email|unique:users,email,{$id}",
        ]);

        $user->update($request->only(['name', 'phone', 'email']));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'تم تحديث بيانات المستخدم');
    }
}
