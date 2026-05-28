@extends('layouts.app')

@section('title', 'Edit Profile - TradeRitual')

@section('content')
<div class="px-4 pt-8 pb-24 space-y-6 page-enter">
    
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('profile.index') }}" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-white">Edit Profile</h1>
    </div>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-400 rounded-xl bg-green-500/10 border border-green-500/20">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 mb-4 text-sm text-red-400 rounded-xl bg-red-500/10 border border-red-500/20">
            {{ session('error') }}
        </div>
    @endif

    {{-- General Settings Form --}}
    <div class="space-y-3">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider pl-1">General Settings</h3>
        <form method="POST" action="{{ route('profile.update') }}" class="glass p-5 rounded-2xl border border-gray-800 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="username" class="block text-sm font-medium text-gray-400 mb-1">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username', $profile->username) }}" required
                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                @error('username')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="default_max_loss" class="block text-sm font-medium text-gray-400 mb-1">Default Max Loss ($)</label>
                <input type="number" step="0.01" name="default_max_loss" id="default_max_loss" value="{{ old('default_max_loss', $profile->default_max_loss) }}" required
                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                <p class="text-xs text-gray-500 mt-1">This will be used as a default target when planning new trades.</p>
                @error('default_max_loss')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 transition">
                Save Profile
            </button>
        </form>
    </div>

    {{-- Security / Password Form --}}
    <div class="space-y-3 pt-4">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider pl-1">Security</h3>
        <form method="POST" action="{{ route('profile.password') }}" class="glass p-5 rounded-2xl border border-gray-800 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="password" class="block text-sm font-medium text-gray-400 mb-1">New Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-400 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
            </div>

            <button type="submit" class="w-full py-3 glass border border-purple-500/50 text-purple-400 font-bold rounded-xl hover:bg-purple-500/10 transition">
                Change Password
            </button>
        </form>
    </div>

</div>
@endsection
