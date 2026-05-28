<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Trade;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $supabaseUserId = session('supabase_user_id');
        $profile = Profile::where('user_id', $supabaseUserId)->firstOrFail();

        // Get all closed trades for the active account
        $closedTrades = Trade::where('user_id', $profile->id)
            ->where('account_id', session('active_account_id'))
            ->whereNotNull('exit_price')
            ->orderBy('created_at', 'asc')
            ->get(['id', 'pnl', 'created_at']);

        // Prepare data for Alpine.js
        $tradesData = $closedTrades->map(function ($trade) {
            return [
                'id' => $trade->id,
                'pnl' => (float) $trade->pnl,
                'date' => \Carbon\Carbon::parse($trade->created_at)->format('Y-m-d'),
            ];
        });

        // Group by date for quick calendar box rendering
        $dailySummary = [];
        foreach ($tradesData as $trade) {
            $date = $trade['date'];
            if (!isset($dailySummary[$date])) {
                $dailySummary[$date] = 0;
            }
            $dailySummary[$date] += $trade['pnl'];
        }

        return view('performance.index', [
            'tradesJson' => json_encode($tradesData),
            'dailyJson' => json_encode($dailySummary),
        ]);
    }
}
