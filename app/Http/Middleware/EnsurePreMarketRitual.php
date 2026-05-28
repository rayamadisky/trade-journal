<?php

namespace App\Http\Middleware;

use App\Models\DailyRitual;
use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsurePreMarketRitual middleware
 *
 * The CORE business logic gate of TradeRitual.
 * Users CANNOT access trade-related routes unless they have
 * a daily_rituals record for today's date.
 *
 * If no ritual exists, they are redirected to the Pre-Market Check-in form.
 */
class EnsurePreMarketRitual
{
    public function handle(Request $request, Closure $next): Response
    {
        $supabaseUserId = session('supabase_user_id');

        if (!$supabaseUserId) {
            return redirect()->route('login');
        }

        // Find the user's profile
        $profile = Profile::where('user_id', $supabaseUserId)->first();

        if (!$profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found. Please contact support.');
        }

        // Check if today's ritual exists
        $todayRitual = DailyRitual::where('user_id', $profile->id)
            ->where('date', now()->toDateString())
            ->first();

        if (!$todayRitual) {
            return redirect()->route('ritual.create')
                ->with('warning', 'Complete your Pre-Market Ritual before trading.');
        }

        // Store ritual and profile in request for downstream controllers
        $request->merge([
            '_profile' => $profile,
            '_ritual' => $todayRitual,
        ]);

        return $next($request);
    }
}
