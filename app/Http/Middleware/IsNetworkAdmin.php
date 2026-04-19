<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsNetworkAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->isNetworkAdmin()) {
            return redirect()->route('admin.login');
        }

        $tenantId = app(TenantContext::class)->id();

        // Ensure this admin belongs to the current tenant
        if ($tenantId && (int) auth()->user()->tenant_id !== (int) $tenantId) {
            auth()->logout();
            return redirect()->route('admin.login')
                             ->withErrors(['email' => 'غير مصرح لك بالدخول على هذه الشبكة']);
        }

        // Block access if the tenant is still pending approval
        $tenant = $tenantId ? Tenant::find($tenantId) : null;
        if ($tenant && $tenant->status === 'pending') {
            return redirect()->route('admin.pending');
        }

        // Block access if the tenant is suspended
        if ($tenant && $tenant->status === 'suspended') {
            auth()->logout();
            return redirect()->route('admin.login')
                             ->withErrors(['email' => 'تم تعليق هذه الشبكة، تواصل مع الدعم الفني']);
        }

        return $next($request);
    }
}
