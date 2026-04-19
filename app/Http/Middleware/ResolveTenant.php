<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host      = $request->getHost();
        $appDomain = config('app.domain', '');   // Set APP_DOMAIN=yourapp.com in .env

        // Determine subdomain: strip the app domain from the host
        // e.g. "network1.yourapp.com" → "network1"
        $subdomain = null;
        if ($appDomain && str_ends_with($host, '.' . $appDomain)) {
            $subdomain = str_replace('.' . $appDomain, '', $host);
        } elseif ($host === $appDomain || $host === 'localhost') {
            // Main domain or localhost — no tenant (super admin or landing)
            return $next($request);
        } else {
            // Local dev fallback: treat the full host as subdomain if no APP_DOMAIN set
            // e.g. "default.localhost" → subdomain = "default"
            $parts     = explode('.', $host);
            $subdomain = count($parts) > 1 ? $parts[0] : null;
        }

        if (! $subdomain) {
            return $next($request);
        }

        $tenant = Tenant::where('subdomain', $subdomain)
                        ->where('status', 'active')
                        ->first();

        if (! $tenant) {
            abort(404, 'الشبكة غير موجودة أو معطّلة');
        }

        // Set the tenant in the context singleton — all Eloquent queries now auto-filter
        app(TenantContext::class)->set($tenant);

        // Share with all Blade views
        view()->share('currentTenant', $tenant);

        return $next($request);
    }
}
