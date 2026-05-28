<?php

namespace App\Http\Controllers;

use App\Models\TradingAccount;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Switch the active trading account.
     */
    public function switchAccount(Request $request)
    {
        $accountId = $request->input('account_id');
        $user = session('supabase_user_id'); // Or profile id

        if (!$accountId || !$user) {
            return redirect()->back()->with('error', 'Unable to switch account.');
        }

        // Verify the account belongs to the current user
        // Wait, we need to check if the account exists for this profile
        $profileId = session('profile_id');
        if (!$profileId) {
            // fallback
            $profileId = \App\Models\Profile::where('user_id', $user)->value('id');
        }

        $account = TradingAccount::where('id', $accountId)->where('user_id', $profileId)->first();

        if ($account) {
            session(['active_account_id' => $account->id]);
            return redirect()->back()->with('success', 'Switched to ' . $account->name);
        }

        return redirect()->back()->with('error', 'Account not found.');
    }

    /**
     * Store a newly created trading account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255', // e.g., Real, Demo, Prop
            'balance' => 'required|numeric|min:0',
        ]);

        $user = session('supabase_user_id');
        $profileId = session('profile_id');
        if (!$profileId) {
            $profileId = \App\Models\Profile::where('user_id', $user)->value('id');
        }

        if (!$profileId) {
            return redirect()->back()->with('error', 'Profile not found.');
        }

        $account = TradingAccount::create([
            'user_id' => $profileId,
            'name' => $request->input('name'),
            'type' => $request->input('type', 'Real'),
            'balance' => $request->input('balance', 0),
            'currency' => 'USD',
        ]);

        // Auto-switch to the newly created account
        session(['active_account_id' => $account->id]);

        return redirect()->back()->with('success', 'New trading account created! 🎉');
    }
}
