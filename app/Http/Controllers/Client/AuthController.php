<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && !Auth::user()->isAdmin()) {
            return redirect()->route('client.dashboard');
        }
        return view('client.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['phone' => $request->phone, 'password' => $request->password, 'role' => 'client'], $request->boolean('remember'))) {
            if (!Auth::user()->isActive()) {
                Auth::logout();
                return back()->withErrors(['phone' => 'حسابك معطّل، تواصل مع الإدارة']);
            }
            $request->session()->regenerate();
            return redirect()->intended(route('client.dashboard'));
        }

        return back()->withErrors(['phone' => 'رقم الجوال أو كلمة المرور غير صحيحة'])->onlyInput('phone');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('client.login');
    }
}
