<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => "nullable|string|unique:users,phone,{$user->id}",
        ]);

        $user->update($request->only(['name', 'email', 'phone']));
        return back()->with('success', 'تم تحديث البيانات الشخصية');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'تم تغيير كلمة المرور');
    }
}
