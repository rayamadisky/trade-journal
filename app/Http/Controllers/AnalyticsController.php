<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Trade;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $supabaseUserId = session('supabase_user_id');
        $profile = Profile::where('user_id', $supabaseUserId)->firstOrFail();

        // Get all CLOSED trades for the active account
        $closedTrades = Trade::where('user_id', $profile->id)
            ->where('account_id', session('active_account_id'))
            ->whereNotNull('exit_price')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalTrades = $closedTrades->count();
        $totalPnl = $closedTrades->sum('pnl');

        // Win Rate
        $winningTrades = $closedTrades->where('pnl', '>', 0)->count();
        $winRate = $totalTrades > 0 ? ($winningTrades / $totalTrades) * 100 : 0;

        // Average RR (Risk-to-Reward Ratio) = Avg Win $ / Avg Loss $
        $avgWin = $closedTrades->where('pnl', '>', 0)->avg('pnl') ?? 0;
        $avgLoss = abs($closedTrades->where('pnl', '<', 0)->avg('pnl') ?? 0);
        $avgRR = $avgLoss > 0 ? $avgWin / $avgLoss : ($avgWin > 0 ? $avgWin : 0);

        // Tag Performance Analysis
        $tagStats = [];

        foreach ($closedTrades as $trade) {
            $tags = $trade->tags ?? [];
            if (empty($tags)) {
                $tags = ['Untagged'];
            } else {
                sort($tags); // Ensure ['A', 'B'] is treated the same as ['B', 'A']
            }
            
            $tagComboKey = implode('|', $tags);

            if (!isset($tagStats[$tagComboKey])) {
                $tagStats[$tagComboKey] = [
                    'tags' => $tags,
                    'total' => 0,
                    'wins' => 0,
                    'pnl' => 0,
                ];
            }

            $tagStats[$tagComboKey]['total']++;
            $tagStats[$tagComboKey]['pnl'] += $trade->pnl;
            if ($trade->pnl > 0) {
                $tagStats[$tagComboKey]['wins']++;
            }
        }

        // Calculate win rate per tag and sort by PnL
        foreach ($tagStats as &$stat) {
            $stat['win_rate'] = ($stat['wins'] / $stat['total']) * 100;
        }
        usort($tagStats, fn($a, $b) => $b['pnl'] <=> $a['pnl']);

        // ─────────────────────────────────────────
        // ADVANCED RATIOS
        // ─────────────────────────────────────────

        // 1. Profit Factor (Gross Profit / Gross Loss)
        $grossProfit = $closedTrades->where('pnl', '>', 0)->sum('pnl');
        $grossLoss = abs($closedTrades->where('pnl', '<', 0)->sum('pnl'));
        $profitFactor = $grossLoss > 0 ? ($grossProfit / $grossLoss) : ($grossProfit > 0 ? 99 : 0);

        // 2. Expectancy (Expected Payoff)
        $lossRate = $totalTrades > 0 ? 100 - $winRate : 0;
        $expectancy = (($winRate / 100) * $avgWin) - (($lossRate / 100) * $avgLoss);

        // 3. Largest Win & Loss
        $largestWin = $closedTrades->max('pnl') ?? 0;
        $largestLoss = $closedTrades->min('pnl') ?? 0;

        // 4. Holding Times (Avg Win vs Avg Loss Duration)
        $winDurations = [];
        $lossDurations = [];
        
        $peakEquity = 0;
        $currentEquity = 0;
        $maxDrawdown = 0;

        // Ensure chronological order for Drawdown calculation
        $chronologicalTrades = $closedTrades->sortBy('created_at');

        foreach ($chronologicalTrades as $trade) {
            // Drawdown calculation
            $currentEquity += $trade->pnl;
            if ($currentEquity > $peakEquity) {
                $peakEquity = $currentEquity;
            }
            
            $drawdown = $peakEquity - $currentEquity;
            if ($drawdown > $maxDrawdown) {
                $maxDrawdown = $drawdown;
            }

            // Holding Time Calculation (minutes)
            if ($trade->updated_at && $trade->created_at) {
                $duration = $trade->created_at->diffInMinutes($trade->updated_at);
                if ($trade->pnl > 0) {
                    $winDurations[] = $duration;
                } else if ($trade->pnl < 0) {
                    $lossDurations[] = $duration;
                }
            }
        }

        $avgWinMinutes = count($winDurations) > 0 ? collect($winDurations)->average() : 0;
        $avgLossMinutes = count($lossDurations) > 0 ? collect($lossDurations)->average() : 0;

        // 5. Recovery Factor
        $recoveryFactor = $maxDrawdown > 0 ? ($totalPnl / $maxDrawdown) : ($totalPnl > 0 ? 99 : 0);

        return view('analytics.index', compact(
            'profile',
            'closedTrades',
            'totalTrades',
            'totalPnl',
            'winRate',
            'avgRR',
            'tagStats',
            'profitFactor',
            'expectancy',
            'largestWin',
            'largestLoss',
            'maxDrawdown',
            'recoveryFactor',
            'avgWinMinutes',
            'avgLossMinutes'
        ));
    }
}
