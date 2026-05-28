@extends('layouts.app')

@section('title', 'TradeRitual — Login')

@section('content')
<div class="flex-1 flex flex-col justify-center px-6 py-12 page-enter">

    {{-- Logo & Brand --}}
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 mb-4 glow-purple">
            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white tracking-tight">TradeRitual</h1>
        <p class="text-gray-400 text-sm mt-1">Master your discipline. Own your edge.</p>
    </div>

    {{-- Status Message --}}
    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm text-center">
            {{ session('status') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center">
            {{ session('error') }}
        </div>
    @endif

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5" id="login-form">
        @csrf

        {{-- Email --}}
        <div>
            <label for="login-email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
            <input
                type="email"
                id="login-email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                placeholder="trader@example.com"
                class="w-full px-4 py-3.5 bg-gray-800/60 border border-gray-700/50 rounded-xl text-white placeholder-gray-500 text-sm transition-all duration-200 hover:border-gray-600 focus:border-purple-500"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="login-password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
            <div x-data="{ show: false }" class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    id="login-password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full px-4 py-3.5 bg-gray-800/60 border border-gray-700/50 rounded-xl text-white placeholder-gray-500 text-sm transition-all duration-200 hover:border-gray-600 focus:border-purple-500 pr-12"
                >
                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors">
                    <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            id="login-submit"
            class="w-full py-3.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-semibold rounded-xl transition-all duration-300 transform active:scale-[0.98] shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30"
        >
            Sign In
        </button>
    </form>

    {{-- Register Link --}}
    <p class="text-center text-sm text-gray-500 mt-8">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-medium transition-colors">
            Create one
        </a>
    </p>
</div>
@endsection
