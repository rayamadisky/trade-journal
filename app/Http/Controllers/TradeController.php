<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;

/**
 * TradeController
 *
 * Handles trade journal CRUD operations.
 * All routes using this controller are gated by the
 * 'ritual.required' middleware — ensuring a daily ritual exists.
 */
class TradeController extends Controller
{
    /**
     * Show the trade entry form.
     */
    public function create(Request $request)
    {
        $profile = $request->input('_profile');
        $ritual = $request->input('_ritual');

        return view('trades.create', compact('profile', 'ritual'));
    }

    /**
     * Store a new trade entry.
     */
    public function store(Request $request, SupabaseStorageService $storage)
    {
        $validated = $request->validate([
            'pair' => 'required|string|max:20',
            'direction' => 'required|in:Long,Short',
            'entry_price' => 'required|numeric|min:0',
            'stop_loss' => 'required|numeric|min:0',
            'take_profit' => 'required|numeric|min:0',
            'lot_size' => 'required|numeric|min:0.01',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'screenshot_entry' => 'nullable|image|max:5120', // 5MB max
            'trade_notes' => 'nullable|string|max:2000',
        ]);

        $profile = $request->input('_profile');
        $ritual = $request->input('_ritual');

        // Handle screenshot upload
        $screenshotUrl = null;
        if ($request->hasFile('screenshot_entry')) {
            $folder = "{$profile->id}/" . now()->toDateString();
            $result = $storage->upload($request->file('screenshot_entry'), $folder);
            if ($result['success']) {
                $screenshotUrl = $result['url'];
            }
        }

        Trade::create([
            'user_id' => $profile->id,
            'account_id' => session('active_account_id'),
            'ritual_id' => $ritual->id,
            'pair' => $validated['pair'],
            'direction' => $validated['direction'],
            'entry_price' => $validated['entry_price'],
            'stop_loss' => $validated['stop_loss'],
            'take_profit' => $validated['take_profit'],
            'lot_size' => $validated['lot_size'],
            'tags' => $validated['tags'] ?? [],
            'screenshot_entry' => $screenshotUrl,
            'trade_notes' => $validated['trade_notes'] ?? null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Trade logged successfully! 📊');
    }

    /**
     * Show the trade close/exit form.
     */
    public function edit(Request $request, Trade $trade)
    {
        $profile = $request->input('_profile');

        // Ensure the trade belongs to this user
        if ($trade->user_id !== $profile->id) {
            abort(403);
        }

        return view('trades.edit', compact('trade', 'profile'));
    }

    /**
     * Update trade with exit data (close the trade).
     */
    public function update(Request $request, Trade $trade, SupabaseStorageService $storage)
    {
        $profile = $request->input('_profile');

        if ($trade->user_id !== $profile->id) {
            abort(403);
        }

        $validated = $request->validate([
            'exit_price' => 'required|numeric|min:0',
            'pnl' => 'required|numeric',
            'screenshot_exit' => 'nullable|image|max:5120',
            'trade_notes' => 'nullable|string|max:2000',
        ]);

        // Handle exit screenshot upload
        $screenshotExitUrl = $trade->screenshot_exit;
        if ($request->hasFile('screenshot_exit')) {
            $folder = "{$profile->id}/" . now()->toDateString();
            $result = $storage->upload($request->file('screenshot_exit'), $folder);
            if ($result['success']) {
                $screenshotExitUrl = $result['url'];
            }
        }

        $trade->update([
            'exit_price' => $validated['exit_price'],
            'pnl' => $validated['pnl'],
            'screenshot_exit' => $screenshotExitUrl,
            'trade_notes' => $validated['trade_notes'] ?? $trade->trade_notes,
        ]);

        // Check if daily loss limit has been hit after this trade
        $ritual = $trade->ritual;
        $dailyPnl = Trade::where('ritual_id', $ritual->id)
            ->whereNotNull('pnl')
            ->sum('pnl');

        $lossLimitHit = ($dailyPnl < 0 && abs($dailyPnl) >= $ritual->max_loss_limit);

        if ($lossLimitHit) {
            return redirect()->route('dashboard')
                ->with('loss_limit_alert', true)
                ->with('daily_pnl', $dailyPnl);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Trade closed. Review your results! 📈');
    }

    /**
     * Show trade detail view.
     */
    public function show(Request $request, Trade $trade)
    {
        $profile = $request->input('_profile');

        if ($trade->user_id !== $profile->id) {
            abort(403);
        }

        return view('trades.show', compact('trade', 'profile'));
    }
}
