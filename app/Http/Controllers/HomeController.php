<?php

namespace App\Http\Controllers;

use App\Models\HomepageSection;
use App\Models\Plan;

class HomeController extends Controller
{
    public function index()
    {
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

        // Load plans for the pricing section (eager, cached separately)
        $plans = \Illuminate\Support\Facades\Cache::remember('homepage_plans', 3600, function () {
            return Plan::where('is_active', true)
                ->orderBy('price')
                ->get();
        });

        return view('welcome', compact('sections', 'plans'));
    }
}
