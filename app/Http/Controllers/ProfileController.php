<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Services\SupabaseAuthService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected SupabaseAuthService $auth;

    public function __construct(SupabaseAuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Show the profile overview page.
     */
    public function index()
    {
        $profile = Profile::where('user_id', session('supabase_user_id'))->firstOrFail();
        return view('profile.index', compact('profile'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $profile = Profile::where('user_id', session('supabase_user_id'))->firstOrFail();
        return view('profile.edit', compact('profile'));
    }

    /**
     * Update the profile settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'default_max_loss' => 'required|numeric|min:0',
        ]);

        $profile = Profile::where('user_id', session('supabase_user_id'))->firstOrFail();
        $profile->update([
            'username' => $request->input('username'),
            'default_max_loss' => $request->input('default_max_loss'),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user password in Supabase.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $token = session('supabase_access_token');
        if (!$token) {
            return back()->with('error', 'Session expired. Please log in again.');
        }

        $result = $this->auth->updateUser($token, [
            'password' => $request->input('password')
        ]);

        if ($result['success']) {
            return redirect()->route('profile.edit')->with('success', 'Password updated successfully. You can now log in with your new password.');
        }

        return back()->with('error', $result['error'] ?? 'Failed to update password.');
    }
}
