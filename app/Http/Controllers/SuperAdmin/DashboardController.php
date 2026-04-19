<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\SystemConfig;
use App\Models\Tenant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants'    => Tenant::count(),
            'active_tenants'   => Tenant::where('status', 'active')->count(),
            'suspended_tenants'=> Tenant::where('status', 'suspended')->count(),
            'total_plans'      => Plan::count(),
            'total_users'      => User::withoutGlobalScopes()->whereIn('role', ['network_admin', 'client'])->count(),
            'platform_version' => SystemConfig::get('current_version', 'v2'),
        ];

        $recentTenants = Tenant::with('plan', 'owner')->latest()->take(8)->get();
        $plans         = Plan::withCount('tenants')->get();

        return view('superadmin.dashboard', compact('stats', 'recentTenants', 'plans'));
    }
}
