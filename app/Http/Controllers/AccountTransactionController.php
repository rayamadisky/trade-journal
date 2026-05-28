<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\TradingAccount;
use Illuminate\Http\Request;

class AccountTransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:trading_accounts,id',
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        $account = TradingAccount::findOrFail($request->input('account_id'));
        
        // Basic security check to ensure this account belongs to the active user profile
        $user = session('supabase_user_id');
        $profileId = session('profile_id');
        if (!$profileId) {
            $profileId = \App\Models\Profile::where('user_id', $user)->value('id');
        }
        
        if ($account->user_id !== $profileId) {
            abort(403, 'Unauthorized action.');
        }

        AccountTransaction::create([
            'account_id' => $account->id,
            'type' => $request->input('type'),
            'amount' => $request->input('amount'),
            'notes' => $request->input('notes'),
        ]);

        $action = ucfirst($request->input('type'));
        return redirect()->back()->with('success', "{$action} of $" . number_format($request->input('amount'), 2) . " successful! 💸");
    }
}
