<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SupabaseAuthService
 *
 * Handles all interactions with the Supabase Auth API (GoTrue).
 * Uses the REST API directly via Laravel's HTTP client.
 */
class SupabaseAuthService
{
    protected string $baseUrl;
    protected string $anonKey;
    protected string $serviceRoleKey;

    public function __construct()
    {
        $this->baseUrl = config('supabase.url');
        $this->anonKey = config('supabase.anon_key');
        $this->serviceRoleKey = config('supabase.service_role_key');
    }

    /**
     * Register a new user via Supabase Auth.
     *
     * @param string $email
     * @param string $password
     * @param array  $metadata  Optional user metadata (e.g. username)
     * @return array{success: bool, data?: array, error?: string}
     */
    public function signUp(string $email, string $password, array $metadata = []): array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'apikey' => $this->anonKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/auth/v1/signup", [
                'email' => $email,
                'password' => $password,
                'data' => $metadata,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            $error = $response->json('msg') ?? $response->json('error_description') ?? 'Registration failed.';
            Log::warning('Supabase signup failed', ['error' => $error, 'email' => $email]);

            return ['success' => false, 'error' => $error];
        } catch (\Exception $e) {
            Log::error('Supabase signup exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Connection error. Please try again.'];
        }
    }

    /**
     * Sign in with email and password via Supabase Auth.
     *
     * @param string $email
     * @param string $password
     * @return array{success: bool, data?: array, error?: string}
     */
    public function signIn(string $email, string $password): array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'apikey' => $this->anonKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/auth/v1/token?grant_type=password", [
                'email' => $email,
                'password' => $password,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            $error = $response->json('error_description') ?? $response->json('msg') ?? 'Invalid credentials.';
            Log::warning('Supabase signin failed', ['error' => $error, 'email' => $email]);

            return ['success' => false, 'error' => $error];
        } catch (\Exception $e) {
            Log::error('Supabase signin exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Connection error. Please try again.'];
        }
    }

    /**
     * Get the authenticated user's profile from Supabase using their access token.
     *
     * @param string $accessToken
     * @return array|null
     */
    public function getUser(string $accessToken): ?array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'apikey' => $this->anonKey,
                'Authorization' => "Bearer {$accessToken}",
            ])->get("{$this->baseUrl}/auth/v1/user");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Supabase getUser exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Sign out (invalidate the session on Supabase side).
     *
     * @param string $accessToken
     * @return bool
     */
    public function signOut(string $accessToken): bool
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'apikey' => $this->anonKey,
                'Authorization' => "Bearer {$accessToken}",
            ])->post("{$this->baseUrl}/auth/v1/logout");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Supabase signOut exception', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Refresh an expired access token using the refresh token.
     *
     * @param string $refreshToken
     * @return array{success: bool, data?: array, error?: string}
     */
    public function refreshToken(string $refreshToken): array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'apikey' => $this->anonKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/auth/v1/token?grant_type=refresh_token", [
                'refresh_token' => $refreshToken,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return ['success' => false, 'error' => 'Token refresh failed.'];
        } catch (\Exception $e) {
            Log::error('Supabase refreshToken exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Connection error.'];
        }
    }
}
