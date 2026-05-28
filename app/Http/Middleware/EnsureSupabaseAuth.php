<?php

namespace App\Http\Middleware;

use App\Services\SupabaseAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureSupabaseAuth middleware
 *
 * Verifies the user has a valid Supabase session (access_token in session).
 * Attempts to refresh the token if expired. Redirects to login if invalid.
 */
class EnsureSupabaseAuth
{
    protected SupabaseAuthService $auth;

    public function __construct(SupabaseAuthService $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = session('supabase_access_token');

        if (!$accessToken) {
            return redirect()->route('login')
                ->with('error', 'Please log in to continue.');
        }

        // Verify the token is still valid by fetching user
        $user = $this->auth->getUser($accessToken);

        if (!$user) {
            // Try to refresh the token
            $refreshToken = session('supabase_refresh_token');

            if ($refreshToken) {
                $refreshResult = $this->auth->refreshToken($refreshToken);

                if ($refreshResult['success']) {
                    $data = $refreshResult['data'];
                    session([
                        'supabase_access_token' => $data['access_token'],
                        'supabase_refresh_token' => $data['refresh_token'],
                        'supabase_user' => $data['user'],
                    ]);

                    $userId = $data['user']['id'];
                    $profile = \App\Models\Profile::where('user_id', $userId)->first();

                    if (!$profile) {
                        return redirect()->route('login')->withErrors(['error' => 'Profile not found.']);
                    }

                    session(['profile_id' => $profile->id]);

                    if (!session()->has('active_account_id')) {
                        $account = \App\Models\TradingAccount::where('user_id', $profile->id)->first();
                        if ($account) {
                            session(['active_account_id' => $account->id]);
                        }
                    }

                    if (session('active_account_id')) {
                        $accounts = \App\Models\TradingAccount::where('user_id', $profile->id)->get();
                        $activeAccount = $accounts->firstWhere('id', session('active_account_id'));
                        
                        \Illuminate\Support\Facades\View::share('userAccounts', $accounts);
                        \Illuminate\Support\Facades\View::share('activeAccount', $activeAccount);
                    }

                    return $next($request);
                }
            }

            // Token expired and refresh failed
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Your session has expired. Please log in again.');
        }

        // Get user profile
        $userId = session('supabase_user')['id'];
        $profile = \App\Models\Profile::where('user_id', $userId)->first();

        if (!$profile) {
            // User authenticated but no profile (should not happen normally)
            return redirect()->route('login')->withErrors(['error' => 'Profile not found.']);
        }

        // Store profile_id in session for convenience
        session(['profile_id' => $profile->id]);

        // Select default active account if none selected
        if (!session()->has('active_account_id')) {
            $account = \App\Models\TradingAccount::where('user_id', $profile->id)->first();
            if ($account) {
                session(['active_account_id' => $account->id]);
            }
        }

        // Share user accounts with all views
        if (session('active_account_id')) {
            $accounts = \App\Models\TradingAccount::where('user_id', $profile->id)->get();
            $activeAccount = $accounts->firstWhere('id', session('active_account_id'));
            
            \Illuminate\Support\Facades\View::share('userAccounts', $accounts);
            \Illuminate\Support\Facades\View::share('activeAccount', $activeAccount);
        }

        return $next($request);
    }
}
