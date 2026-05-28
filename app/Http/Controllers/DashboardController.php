<?php

namespace App\Http\Controllers;

use App\Models\DailyRitual;
use App\Models\Profile;
use App\Models\Trade;
use Illuminate\Http\Request;

/**
 * DashboardController
 *
 * The main hub of TradeRitual. Shows:
 * - Discipline Score with streak fire
 * - Today's max loss limit vs current PnL
 * - Pre-Market Ritual status (CTA if not completed)
 * - List of today's floating trades
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $supabaseUserId = session('supabase_user_id');

        $profile = Profile::where('user_id', $supabaseUserId)->first();

        if (!$profile) {
            // Auto-create profile if it doesn't exist yet
            $profile = Profile::create([
                'user_id' => $supabaseUserId,
                'username' => session('supabase_user.email', 'Trader'),
                'discipline_score' => 0,
                'default_max_loss' => 0,
            ]);
        }

        // Get today's ritual (if exists)
        $todayRitual = DailyRitual::where('user_id', $profile->id)
            ->where('date', now()->toDateString())
            ->first();

        // Get today's trades
        $todayTrades = collect();
        $dailyPnl = 0;
        $openTrades = collect();

        if ($todayRitual) {
            // Get trades for the ACTIVE account for the UI list
            $activeAccountId = session('active_account_id');
            $todayTrades = Trade::where('ritual_id', $todayRitual->id)
                ->where('account_id', $activeAccountId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate PnL for the ACTIVE account
            $dailyPnl = $todayTrades->whereNotNull('pnl')->sum('pnl');

            // Get open/floating trades for the ACTIVE account
            $openTrades = $todayTrades->whereNull('exit_price');
            
            // Calculate GLOBAL PnL to check if max loss limit is hit globally
            $globalPnl = Trade::where('ritual_id', $todayRitual->id)
                ->whereNotNull('pnl')
                ->sum('pnl');
        }

        // Check if daily loss limit has been hit globally
        $lossLimitHit = false;
        if ($todayRitual && isset($globalPnl) && $globalPnl < 0) {
            $lossLimitHit = abs($globalPnl) >= $todayRitual->max_loss_limit;
        }

        // Calculate Current Account Balance
        $activeAccountId = session('active_account_id');
        $activeAccount = \App\Models\TradingAccount::find($activeAccountId);
        
        $totalAccountPnl = Trade::where('account_id', $activeAccountId)
            ->whereNotNull('exit_price')
            ->sum('pnl');
            
        $deposits = \App\Models\AccountTransaction::where('account_id', $activeAccountId)
            ->where('type', 'deposit')
            ->sum('amount');
            
        $withdrawals = \App\Models\AccountTransaction::where('account_id', $activeAccountId)
            ->where('type', 'withdrawal')
            ->sum('amount');
            
        $currentBalance = $activeAccount ? ($activeAccount->balance + $deposits - $withdrawals + $totalAccountPnl) : 0;

        // Generate Equity Chart Data
        $equityData = [];
        if ($activeAccount) {
            $runningBalance = (float) $activeAccount->balance;
            
            // Get all trades and transactions chronological
            $trades = Trade::where('account_id', $activeAccountId)->whereNotNull('exit_price')
                ->selectRaw("DATE(created_at) as date, SUM(pnl) as net_amount")
                ->groupByRaw("DATE(created_at)");
                
            $transactions = \App\Models\AccountTransaction::where('account_id', $activeAccountId)
                ->selectRaw("DATE(created_at) as date, SUM(CASE WHEN type='deposit' THEN amount ELSE -amount END) as net_amount")
                ->groupByRaw("DATE(created_at)");

            $events = $trades->union($transactions)->orderBy('date')->get();

            // Aggregate by date
            $dailyAggregates = [];
            foreach ($events as $event) {
                $dateStr = $event->date;
                if (!isset($dailyAggregates[$dateStr])) {
                    $dailyAggregates[$dateStr] = 0;
                }
                $dailyAggregates[$dateStr] += (float) $event->net_amount;
            }

            // Start with a point for the account creation date (initial balance)
            $equityData[] = [
                'x' => $activeAccount->created_at->format('Y-m-d'),
                'y' => $runningBalance
            ];

            foreach ($dailyAggregates as $date => $net) {
                $runningBalance += $net;
                $equityData[] = [
                    'x' => $date,
                    'y' => $runningBalance
                ];
            }
        }

        return view('dashboard', compact(
            'profile',
            'todayRitual',
            'todayTrades',
            'dailyPnl',
            'openTrades',
            'lossLimitHit',
            'currentBalance',
            'activeAccount',
            'equityData'
        ));
    }
}
