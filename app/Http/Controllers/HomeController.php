<?php

namespace App\Http\Controllers;

use App\Models\HomepageSection;
use App\Models\Plan;

class HomeController extends Controller
{
    public function index()
    {
        // On a subdomain that doesn't match any active tenant → branded 404
        $appDomain = config('app.domain', '');
        $host      = request()->getHost();
        if ($appDomain && $host !== $appDomain && str_ends_with($host, '.' . $appDomain)) {
            $subdomain = str_replace('.' . $appDomain, '', $host);
            if (! \App\Models\Tenant::where('subdomain', $subdomain)->where('status', 'active')->exists()) {
                abort(404);
            }
        }

        // Redirect authenticated users to their appropriate dashboard
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'super_admin') {
                return redirect()->route('superadmin.dashboard');
            }
            if ($user->role === 'network_admin') {
                return redirect()->route('admin.dashboard');
            }
            if ($user->role === 'client') {
                return redirect()->route('client.dashboard');
            }
        }

        $sections = HomepageSection::active();

        // Load plans for the pricing section (cached as raw DB attributes)
        $rawPlans = \Illuminate\Support\Facades\Cache::remember('homepage_plans', 3600, function () {
            return Plan::where('is_active', true)
                ->orderBy('price')
                ->get()
                ->map(fn($m) => $m->getAttributes())
                ->all();
        });
        $plans = Plan::hydrate($rawPlans);

        return view('welcome', compact('sections', 'plans'));
    }
}
