<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * LoginController
 *
 * Handles user authentication via Supabase Auth (GoTrue).
 * Stores access_token, refresh_token, and user data in the Laravel session.
 */
class LoginController extends Controller
{
    protected SupabaseAuthService $auth;

    public function __construct(SupabaseAuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $result = $this->auth->signIn(
            $request->input('email'),
            $request->input('password')
        );

        if (!$result['success']) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $result['error']]);
        }

        $data = $result['data'];

        // Store tokens and user info in session
        session([
            'supabase_access_token' => $data['access_token'],
            'supabase_refresh_token' => $data['refresh_token'],
            'supabase_user' => $data['user'],
            'supabase_user_id' => $data['user']['id'],
        ]);

        Log::info('User logged in', ['user_id' => $data['user']['id']]);

        return redirect()->route('dashboard');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $token = session('supabase_access_token');

        if ($token) {
            $this->auth->signOut($token);
        }

        $request->session()->flush();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
