<?php

namespace App\Http\Controllers;

use App\Models\DailyRitual;
use App\Models\Profile;
use Illuminate\Http\Request;

/**
 * RitualController
 *
 * Handles the Pre-Market & Post-Market Check-in flow.
 * This is the gate that must be passed before trading.
 */
class RitualController extends Controller
{
    /**
     * Show the Pre-Market Ritual form.
     */
    public function create(Request $request)
    {
        $supabaseUserId = session('supabase_user_id');
        $profile = Profile::where('user_id', $supabaseUserId)->first();

        // If today's ritual already exists, redirect to dashboard
        $existingRitual = DailyRitual::where('user_id', $profile->id)
            ->where('date', now()->toDateString())
            ->first();

        if ($existingRitual) {
            return redirect()->route('dashboard')
                ->with('info', 'You already completed today\'s ritual. Time to trade!');
        }

        return view('ritual.create', compact('profile'));
    }

    /**
     * Store the Pre-Market Ritual.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sleep_hours' => 'required|integer|min:0|max:24',
            'pre_mood' => 'required|integer|min:1|max:5',
            'max_loss_limit' => 'required|numeric|min:0',
        ]);

        $supabaseUserId = session('supabase_user_id');
        $profile = Profile::where('user_id', $supabaseUserId)->firstOrFail();

        // Prevent duplicate ritual for today
        $existing = DailyRitual::where('user_id', $profile->id)
            ->where('date', now()->toDateString())
            ->first();

        if ($existing) {
            return redirect()->route('dashboard')
                ->with('info', 'Ritual already completed for today.');
        }

        // Gamification: Calculate Discipline Score from previous ritual
        $previousRitual = \App\Models\DailyRitual::where('user_id', $profile->id)
            ->where('date', '<', now()->toDateString())
            ->orderBy('date', 'desc')
            ->first();

        $pointsToAdd = 0;
        $gamificationMessage = '';

        if ($previousRitual) {
            // Check if they followed their plan
            if ($previousRitual->followed_plan === true) {
                $pointsToAdd += 1;
            }

            // Check if they hit max loss limit and stopped trading
            $trades = \App\Models\Trade::where('ritual_id', $previousRitual->id)
                ->orderBy('created_at', 'asc')
                ->get();

            $runningPnl = 0;
            $hitLimitTime = null;
            $tradedAfterLimit = false;

            foreach ($trades as $trade) {
                if ($hitLimitTime && $trade->created_at > $hitLimitTime) {
                    $tradedAfterLimit = true;
                    break;
                }

                if ($trade->pnl !== null) {
                    $runningPnl += $trade->pnl;
                }

                // If running PnL hits max loss limit
                if ($runningPnl < 0 && abs($runningPnl) >= $previousRitual->max_loss_limit) {
                    if (!$hitLimitTime) {
                        $hitLimitTime = $trade->updated_at;
                    }
                }
            }

            if ($hitLimitTime && !$tradedAfterLimit) {
                $pointsToAdd += 1; // Reward for walking away after hitting limit
                $gamificationMessage = " You walked away when you hit your loss limit yesterday. True discipline!";
            }
        }

        if ($pointsToAdd > 0) {
            $profile->increment('discipline_score', $pointsToAdd);
            session()->flash('gamification_alert', "+{$pointsToAdd} Discipline Score!{$gamificationMessage}");
        }

        DailyRitual::create([
            'user_id' => $profile->id,
            'date' => now()->toDateString(),
            'sleep_hours' => $validated['sleep_hours'],
            'pre_mood' => $validated['pre_mood'],
            'max_loss_limit' => $validated['max_loss_limit'],
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Pre-Market Ritual complete. You\'re ready to trade! 🔥');
    }

    /**
     * Show the Post-Market Review form.
     */
    public function postMarket(Request $request)
    {
        $supabaseUserId = session('supabase_user_id');
        $profile = Profile::where('user_id', $supabaseUserId)->firstOrFail();

        $todayRitual = DailyRitual::where('user_id', $profile->id)
            ->where('date', now()->toDateString())
            ->first();

        if (!$todayRitual) {
            return redirect()->route('ritual.create');
        }

        return view('ritual.post-market', compact('profile', 'todayRitual'));
    }

    /**
     * Store the Post-Market Review.
     */
    public function storePostMarket(Request $request)
    {
        $validated = $request->validate([
            'post_mood' => 'required|integer|min:1|max:5',
            'followed_plan' => 'required|boolean',
            'daily_notes' => 'nullable|string|max:2000',
        ]);

        $supabaseUserId = session('supabase_user_id');
        $profile = Profile::where('user_id', $supabaseUserId)->firstOrFail();

        $todayRitual = DailyRitual::where('user_id', $profile->id)
            ->where('date', now()->toDateString())
            ->firstOrFail();

        $todayRitual->update($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Post-Market Review saved. See you tomorrow! 💪');
    }
}
