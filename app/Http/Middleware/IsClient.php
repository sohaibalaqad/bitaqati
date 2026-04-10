<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsClient
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->isAdmin()) {
            return redirect()->route('client.login');
        }
        if (!auth()->user()->isActive()) {
            auth()->logout();
            return redirect()->route('client.login')->withErrors(['phone' => 'حسابك معطّل']);
        }
        return $next($request);
    }
}
