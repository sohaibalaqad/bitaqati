<?php

namespace App\Http\Middleware;

use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsClient
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->isClient()) {
            return redirect()->route('client.login');
        }

        // Ensure client belongs to the current tenant
        $tenantId = app(TenantContext::class)->id();
        if ($tenantId && (int) auth()->user()->tenant_id !== (int) $tenantId) {
            auth()->logout();
            return redirect()->route('client.login')
                             ->withErrors(['phone' => 'غير مصرح لك بالدخول على هذه الشبكة']);
        }

        if (! auth()->user()->isActive()) {
            auth()->logout();
            return redirect()->route('client.login')
                             ->withErrors(['phone' => 'حسابك معطّل، تواصل مع المشرف']);
        }

        return $next($request);
    }
}
