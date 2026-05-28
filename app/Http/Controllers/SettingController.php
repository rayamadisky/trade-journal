<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\TradingAccount;
use App\Models\TradingPair;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Update default max loss for profile and currency for active account.
     */
    public function updateDashboardSettings(Request $request)
    {
        $request->validate([
            'default_max_loss' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        $userId = session('profile_id');
        if (!$userId) {
            return back()->with('error', 'Profile not found.');
        }

        // Update Profile Max Loss
        $profile = Profile::findOrFail($userId);
        $profile->update([
            'default_max_loss' => $request->input('default_max_loss'),
        ]);

        // Update Account Currency
        $accountId = session('active_account_id');
        if ($accountId) {
            $account = TradingAccount::where('id', $accountId)->where('user_id', $userId)->first();
            if ($account) {
                $account->update([
                    'currency' => strtoupper($request->input('currency')),
                ]);
            }
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Add a new trading pair.
     */
    public function storePair(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string|max:20',
        ]);

        $userId = session('profile_id');
        $symbol = strtoupper(trim($request->input('symbol')));

        // Check if already exists
        $exists = TradingPair::where('user_id', $userId)->where('symbol', $symbol)->exists();
        if ($exists) {
            return back()->with('error', 'Trading pair already exists.');
        }

        TradingPair::create([
            'user_id' => $userId,
            'symbol' => $symbol,
        ]);

        return back()->with('success', 'Trading pair added.');
    }

    /**
     * Delete a trading pair.
     */
    public function destroyPair($id)
    {
        $userId = session('profile_id');
        $pair = TradingPair::where('id', $id)->where('user_id', $userId)->firstOrFail();
        
        $pair->delete();

        return back()->with('success', 'Trading pair deleted.');
    }
}
