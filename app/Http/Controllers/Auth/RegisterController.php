<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Services\SupabaseAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * RegisterController
 *
 * Handles new user registration via Supabase Auth.
 * After successful signup, creates a local profile record and
 * auto-logs the user in.
 */
class RegisterController extends Controller
{
    protected SupabaseAuthService $auth;

    public function __construct(SupabaseAuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:30',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Register with Supabase Auth
        $result = $this->auth->signUp(
            $request->input('email'),
            $request->input('password'),
            ['username' => $request->input('username')]
        );

        if (!$result['success']) {
            return back()
                ->withInput($request->only('username', 'email'))
                ->withErrors(['email' => $result['error']]);
        }

        $data = $result['data'];

        // Check if Supabase returned tokens (auto-confirm enabled)
        // or if email confirmation is required
        if (isset($data['access_token'])) {
            // Auto-confirm mode: user is immediately authenticated
            $userId = $data['user']['id'];

            // Create local profile (the Supabase trigger also does this,
            // but we ensure it exists on the Laravel side too)
            Profile::firstOrCreate(
                ['user_id' => $userId],
                [
                    'username' => $request->input('username'),
                    'discipline_score' => 0,
                    'default_max_loss' => 0,
                ]
            );

            // Store session
            session([
                'supabase_access_token' => $data['access_token'],
                'supabase_refresh_token' => $data['refresh_token'],
                'supabase_user_id' => $userId,
            ]);

            Log::info('User registered and logged in', ['user_id' => $userId]);

            return redirect()->route('dashboard');
        }

        // Email confirmation required
        return redirect()->route('login')
            ->with('status', 'Registration successful! Please check your email to confirm your account.');
    }
}
