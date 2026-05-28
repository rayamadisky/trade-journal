@extends('layouts.app')

@section('title', 'Profile - TradeRitual')

@section('content')
<div class="px-4 pt-8 pb-24 space-y-6 page-enter">
    
    {{-- Header --}}
    <div class="text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-800 border border-gray-700 rounded-full mb-3 shadow-lg">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <h1 class="text-2xl font-bold text-white">My Profile</h1>
        <p class="text-gray-400 text-sm mt-1">Trading Account Settings</p>
    </div>

    {{-- Stats / Info --}}
    <div class="glass p-5 rounded-2xl border border-gray-800 space-y-4">
        <div>
            <p class="text-xs text-gray-500 mb-1">Email</p>
            <p class="text-sm font-medium text-white">{{ session('supabase_user_email') ?? 'Trader' }}</p>
        </div>
        <hr class="border-gray-800">
        <div>
            <p class="text-xs text-gray-500 mb-1">Subscription Plan</p>
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-white">Pro Member</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-500/20 text-green-400">ACTIVE</span>
            </div>
        </div>
    </div>

    {{-- App Settings --}}
    <div class="space-y-3">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider pl-1">Settings</h3>
        <div class="glass rounded-2xl border border-gray-800 divide-y divide-gray-800">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </div>
                    <span class="text-sm text-gray-300 font-medium">Dark Mode</span>
                </div>
                <!-- Toggle switch active state -->
                <div class="w-11 h-6 bg-purple-600 rounded-full flex items-center px-1">
                    <div class="w-4 h-4 bg-white rounded-full translate-x-5"></div>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-4 opacity-50 cursor-not-allowed">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-500/10 flex items-center justify-center text-yellow-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <span class="text-sm text-gray-300 font-medium">Push Notifications</span>
                </div>
                <div class="w-11 h-6 bg-gray-700 rounded-full flex items-center px-1">
                    <div class="w-4 h-4 bg-gray-400 rounded-full"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Logout --}}
    <div class="pt-6">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full py-3 px-4 glass border border-red-500/30 text-red-400 rounded-xl font-bold hover:bg-red-500/10 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Sign Out
            </button>
        </form>
    </div>
</div>
@endsection
