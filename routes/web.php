<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\AiCoachController;
use App\Http\Controllers\RitualController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TradeRitual Web Routes
|--------------------------------------------------------------------------
|
| Route groups:
| 1. Guest routes — Login/Register (redirect if already authenticated)
| 2. Authenticated routes — Dashboard, Profile
| 3. Ritual routes — Pre/Post Market Check-in
| 4. Trade routes — GATED by ritual.required middleware
|
*/

// ─────────────────────────────────────────────
// Landing
// ─────────────────────────────────────────────
Route::get('/', function () {
    if (session('supabase_access_token')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// ─────────────────────────────────────────────
// Authentication (Guest only)
// ─────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('supabase.auth');

// ─────────────────────────────────────────────
// Authenticated Routes
// ─────────────────────────────────────────────
Route::middleware(['supabase.auth'])->group(function () {

    // Dashboard — Main hub
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile & Accounts
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/accounts/switch', [AccountController::class, 'switchAccount'])->name('accounts.switch');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::post('/accounts/transactions', [App\Http\Controllers\AccountTransactionController::class, 'store'])->name('transactions.store');
    
    // Settings & Pairs
    Route::post('/settings/dashboard', [App\Http\Controllers\SettingController::class, 'updateDashboardSettings'])->name('settings.dashboard');
    Route::post('/settings/pairs', [App\Http\Controllers\SettingController::class, 'storePair'])->name('settings.pairs.store');
    Route::delete('/settings/pairs/{id}', [App\Http\Controllers\SettingController::class, 'destroyPair'])->name('settings.pairs.destroy');
    // Analytics Dashboard
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Performance Calendar
    Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');

    // AI Coach
    Route::get('/ai-coach', [AiCoachController::class, 'index'])->name('ai.index');
    Route::post('/ai-coach/generate', [AiCoachController::class, 'generate'])->name('ai.generate');

    // ─────────────────────────────────────────
    // Pre-Market & Post-Market Ritual
    // ─────────────────────────────────────────
    Route::get('/ritual', [RitualController::class, 'create'])->name('ritual.create');
    Route::post('/ritual', [RitualController::class, 'store'])->name('ritual.store');

    Route::get('/ritual/post-market', [RitualController::class, 'postMarket'])->name('ritual.post-market');
    Route::post('/ritual/post-market', [RitualController::class, 'storePostMarket'])->name('ritual.store-post-market');

    // ─────────────────────────────────────────
    // Trade Management (REQUIRES completed daily ritual)
    // ─────────────────────────────────────────
    Route::middleware(['ritual.required'])->group(function () {
        Route::get('/trades/create', [TradeController::class, 'create'])->name('trades.create');
        Route::post('/trades', [TradeController::class, 'store'])->name('trades.store');

        Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('trades.show');
        Route::get('/trades/{trade}/edit', [TradeController::class, 'edit'])->name('trades.edit');
        Route::put('/trades/{trade}', [TradeController::class, 'update'])->name('trades.update');
    });
});
