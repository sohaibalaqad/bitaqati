<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isNetworkAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Attempt without a role filter — role is validated after
        if (! Auth::attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->boolean('remember')
        )) {
            return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة'])->onlyInput('email');
        }

        // Block clients and super_admin from entering the network-admin panel
        if (! Auth::user()->isNetworkAdmin()) {
            Auth::logout();
            return back()->withErrors(['email' => 'ليس لديك صلاحية الوصول لهذه اللوحة'])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
