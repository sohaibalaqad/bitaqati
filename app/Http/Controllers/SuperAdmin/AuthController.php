<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS  = 10;
    private const LOCKOUT_MINS  = 15;

    public function showLogin()
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $key = 'superadmin_lockout_' . sha1($request->ip());

        // Reject locked-out IPs before even touching the DB
        $attempts = Cache::get($key, 0);
        if ($attempts >= self::MAX_ATTEMPTS) {
            $ttl = Cache::getStore()->connection
                ? null
                : self::LOCKOUT_MINS;  // fallback
            return back()->withErrors([
                'email' => 'تم تجاوز الحد المسموح من المحاولات. يرجى المحاولة بعد ' . self::LOCKOUT_MINS . ' دقيقة.',
            ]);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if (auth()->user()->isSuperAdmin()) {
                Cache::forget($key);   // clear lockout counter on success
                $request->session()->regenerate();
                return redirect()->route('superadmin.dashboard');
            }
            Auth::logout();
        }

        // Increment failure counter — auto-expires after LOCKOUT_MINS
        Cache::put($key, $attempts + 1, now()->addMinutes(self::LOCKOUT_MINS));

        $remaining = self::MAX_ATTEMPTS - ($attempts + 1);
        $msg = $remaining > 0
            ? "بيانات الدخول غير صحيحة. تبقّى {$remaining} محاولة قبل الإغلاق المؤقت."
            : 'تم تجاوز الحد المسموح من المحاولات. يرجى المحاولة بعد ' . self::LOCKOUT_MINS . ' دقيقة.';

        return back()->withErrors(['email' => $msg]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('superadmin.login');
    }
}
