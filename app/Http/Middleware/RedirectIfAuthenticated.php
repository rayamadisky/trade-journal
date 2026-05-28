<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RedirectIfAuthenticated middleware
 *
 * Custom "guest" middleware for Supabase auth.
 * Redirects to dashboard if user already has a valid session.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('supabase_access_token')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
