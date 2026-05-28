@extends('layouts.app')

@section('title', 'TradeRitual — Dashboard')

@section('content')
<div class="px-4 pt-6 pb-6 space-y-6 page-enter">

    {{-- Top Bar: Profile & Account Switcher --}}
    <div class="flex justify-between items-start">
        <div class="flex items-center gap-2">
            <div>
                <h2 class="text-gray-400 text-sm font-medium">Welcome back,</h2>
                <div class="flex items-center gap-2">
                    <p class="text-white font-bold text-lg">{{ $profile->username }}</p>
                    <button @click="$dispatch('open-dashboard-settings')" class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col items-end gap-2">
            {{-- Account Switcher Dropdown (Kanan Atas) --}}
            <div x-data="{ open: false }" class="relative">
                @if(isset($userAccounts) && $userAccounts->count() > 0)
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-xl text-xs font-bold text-white hover:bg-gray-700 transition">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        {{ $activeAccount->name }}
                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    
                    <div x-show="open" x-transition style="display: none;" class="absolute top-full right-0 mt-2 w-48 bg-gray-800 border border-gray-700 rounded-xl shadow-xl z-50 py-1">
                        <p class="px-3 py-1 text-[10px] uppercase font-bold text-gray-500">Switch Account</p>
                        @foreach($userAccounts as $acc)
                            <form method="POST" action="{{ route('accounts.switch') }}">
                                @csrf
                                <input type="hidden" name="account_id" value="{{ $acc->id }}">
                                <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition flex items-center justify-between">
                                    {{ $acc->name }}
                                    @if($activeAccount && $activeAccount->id === $acc->id)
                                        <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                        <div class="border-t border-gray-700 mt-1 pt-1">
                            <button @click="$dispatch('open-add-account')" class="w-full text-left px-3 py-2 text-sm text-purple-400 hover:bg-gray-700 font-bold transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Add New Account
                            </button>
                        </div>
                    </div>
                @else
                    <button @click="$dispatch('open-add-account')" class="flex items-center gap-2 bg-purple-600 border border-purple-500 px-3 py-1.5 rounded-xl text-xs font-bold text-white hover:bg-purple-500 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Account
                    </button>
                @endif
            </div>

            {{-- Add Account Modal (Alpine.js) --}}
            <div x-data="{ open: false }" @open-add-account.window="open = true" class="relative z-50">
                <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/80 backdrop-blur-sm" style="display: none;"></div>
                <div x-show="open" x-transition.scale.80 style="display: none;" class="fixed inset-0 flex items-center justify-center p-4">
                    <div @click.away="open = false" class="bg-gray-900 border border-gray-700 rounded-3xl p-6 w-full max-w-sm shadow-2xl relative">
                        <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                        <h3 class="text-xl font-bold text-white mb-4">Add Trading Account</h3>
                        <form method="POST" action="{{ route('accounts.store') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Account Name</label>
                                <input type="text" name="name" required placeholder="e.g. Apex 50k, FTMO, Main" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Type</label>
                                    <select name="type" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                        <option value="Real">Real</option>
                                        <option value="Prop Firm">Prop Firm</option>
                                        <option value="Demo">Demo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Initial Balance</label>
                                    <input type="number" name="balance" required min="0" step="0.01" placeholder="0.00" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                </div>
                            </div>
                            <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl transition-colors mt-2">Create Account</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Discipline Score --}}
            <div class="flex items-center gap-2 bg-gray-800/80 px-3 py-1.5 rounded-full border border-gray-700/50 glow-purple">
                <svg class="w-4 h-4 text-purple-500 pulse-fire" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                </svg>
                <span class="text-white font-bold text-sm">{{ $profile->discipline_score }}</span>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('gamification_alert'))
    <div class="p-4 rounded-2xl bg-purple-500/20 border border-purple-500/40 text-purple-300 text-sm glow-purple flex items-center gap-3 font-bold mb-4">
        <span class="text-2xl pulse-fire">🔥</span>
        {{ session('gamification_alert') }}
    </div>
    @endif

    @if(session('success'))
    <div class="p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('info'))
    <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm">
        {{ session('info') }}
    </div>
    @endif

    @if(session('loss_limit_alert'))
    <div class="p-5 rounded-2xl bg-red-500/10 border border-red-500/30 glow-red">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <h3 class="text-red-500 font-bold text-lg">Daily Loss Limit Reached!</h3>
                <p class="text-red-300/80 text-sm mt-1">You've hit your max loss of {{ $activeAccount->currency ?? 'USD' }} {{ number_format($todayRitual->max_loss_limit, 2) }}. Walk away from the screen now to protect your capital and earn discipline points.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Hero Section: Ritual Check OR Daily Summary --}}
    @if(!$todayRitual)
        <div class="glass rounded-3xl p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-amber-500/20 rounded-full blur-2xl"></div>
            <h3 class="text-xl font-bold text-white mb-2 relative z-10">Start Your Day Right</h3>
            <p class="text-gray-400 text-sm mb-6 relative z-10">Complete your pre-market ritual to unlock trading for today.</p>
            <a href="{{ route('ritual.create') }}" class="block w-full text-center py-3.5 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-xl transition-colors relative z-10 shadow-lg shadow-amber-500/20">
                Complete Pre-Market Ritual
            </a>
        </div>
    @else
        <div class="glass rounded-3xl p-5 border {{ $lossLimitHit ? 'border-red-500/30' : 'border-gray-800' }}" x-data="{ openTx: false, txType: 'deposit' }">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Account Balance</p>
                    <div class="flex items-center gap-3">
                        <h3 class="text-3xl font-bold text-white">
                            {{ $activeAccount->currency ?? 'USD' }} {{ number_format($currentBalance, 2) }}
                        </h3>
                        <div class="flex items-center gap-1">
                            <button @click="openTx = true; txType = 'deposit'" class="w-7 h-7 rounded-full bg-green-500/20 text-green-400 flex items-center justify-center hover:bg-green-500/40 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            </button>
                            <button @click="openTx = true; txType = 'withdrawal'" class="w-7 h-7 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center hover:bg-red-500/40 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Today's PnL</p>
                    <p class="text-lg font-bold {{ $dailyPnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $dailyPnl >= 0 ? '+' : '' }}{{ $activeAccount->currency ?? 'USD' }} {{ number_format(abs($dailyPnl), 2) }}
                    </p>
                </div>
            </div>

            {{-- Transaction Modal --}}
            <div x-show="openTx" style="display: none;" class="relative z-50">
                <div x-transition.opacity class="fixed inset-0 bg-black/80 backdrop-blur-sm"></div>
                <div x-transition.scale.80 class="fixed inset-0 flex items-center justify-center p-4">
                    <div @click.away="openTx = false" class="bg-gray-900 border border-gray-700 rounded-3xl p-6 w-full max-w-sm shadow-2xl relative">
                        <button @click="openTx = false" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                        <h3 class="text-xl font-bold text-white mb-4" x-text="txType === 'deposit' ? 'Deposit Funds' : 'Withdraw Funds'"></h3>
                        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="account_id" value="{{ $activeAccount->id ?? '' }}">
                            <input type="hidden" name="type" x-model="txType">
                            <div>
                                <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Amount ($)</label>
                                <input type="number" name="amount" required min="0.01" step="0.01" placeholder="0.00" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Notes (Optional)</label>
                                <input type="text" name="notes" placeholder="e.g. Prop firm reset, Bank transfer" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            </div>
                            <button type="submit" class="w-full py-3 text-white font-bold rounded-xl transition-colors mt-2"
                                :class="txType === 'deposit' ? 'bg-green-600 hover:bg-green-500' : 'bg-red-600 hover:bg-red-500'"
                                x-text="txType === 'deposit' ? 'Confirm Deposit' : 'Confirm Withdrawal'">
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-end mb-2">
                <span class="text-gray-400 text-xs font-medium uppercase">Daily Limit Status</span>
                <span class="text-gray-400 text-xs font-medium">Max: {{ $activeAccount->currency ?? 'USD' }} {{ number_format($todayRitual->max_loss_limit, 2) }}</span>
            </div>

            {{-- Progress Bar for Loss --}}
            @if($dailyPnl < 0)
                @php
                    $lossPercentage = min(100, (abs($dailyPnl) / $todayRitual->max_loss_limit) * 100);
                    $barColor = $lossPercentage > 80 ? 'bg-red-500' : 'bg-amber-500';
                @endphp
                <div class="w-full bg-gray-800 rounded-full h-2 mb-2">
                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500" style="width: {{ $lossPercentage }}%"></div>
                </div>
                @if($lossPercentage >= 100)
                    <p class="text-red-400 text-xs font-medium text-center mt-1">Trading disabled. Limit reached.</p>
                @else
                    <p class="text-gray-400 text-xs text-right mt-1">{{ number_format($lossPercentage, 0) }}% to limit</p>
                @endif
            @else
                <div class="w-full bg-gray-800 rounded-full h-2 mb-2">
                    <div class="bg-green-500 h-2 rounded-full w-full"></div>
                </div>
                <p class="text-green-400 text-xs text-right mt-1">In profit</p>
            @endif
        </div>

        {{-- Primary Action Button --}}
        @if(!$lossLimitHit)
            @if(isset($activeAccount))
                <a href="{{ route('trades.create') }}" class="flex items-center justify-center gap-2 w-full py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-2xl shadow-lg shadow-purple-500/20 active:scale-[0.98] transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Log New Trade
                </a>
            @else
                <button @click="$dispatch('open-add-account')" class="flex items-center justify-center gap-2 w-full py-4 bg-gray-800 border-2 border-dashed border-gray-600 text-gray-300 font-bold rounded-2xl active:scale-[0.98] transition-transform hover:bg-gray-700 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Trading Account to Start
                </button>
            @endif
        @endif
    @endif

    {{-- Equity Growth Chart --}}
    @if(isset($equityData) && count($equityData) > 0)
    <div class="glass rounded-3xl p-5 border border-gray-800" x-data="equityChart()">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-white font-bold">Equity Growth</h3>
            
            {{-- Time Filters --}}
            <div class="flex items-center gap-2">
                <div class="flex bg-gray-800 rounded-lg p-1">
                    <template x-for="filter in ['1W', '1M', '3M', 'All', 'Custom']">
                        <button @click="setFilter(filter)" 
                            class="px-3 py-1 text-[10px] font-bold rounded-md transition-colors"
                            :class="activeFilter === filter ? 'bg-purple-600 text-white shadow' : 'text-gray-400 hover:text-white'">
                            <span x-text="filter"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Custom Date Range Picker --}}
        <div x-show="activeFilter === 'Custom'" x-transition class="flex gap-2 mb-4 bg-gray-800/50 p-3 rounded-xl border border-gray-700/50">
            <div class="flex-1">
                <label class="text-[10px] text-gray-400 uppercase font-bold px-1">Start Date</label>
                <input type="date" x-model="customStart" @change="applyCustomFilter" class="w-full bg-gray-900 border border-gray-700 text-xs text-white rounded-lg px-2 py-1.5 focus:border-purple-500">
            </div>
            <div class="flex-1">
                <label class="text-[10px] text-gray-400 uppercase font-bold px-1">End Date</label>
                <input type="date" x-model="customEnd" @change="applyCustomFilter" class="w-full bg-gray-900 border border-gray-700 text-xs text-white rounded-lg px-2 py-1.5 focus:border-purple-500">
            </div>
        </div>

        <div class="relative h-48 w-full">
            <canvas id="equityChartCanvas"></canvas>
        </div>
    </div>
    @endif

    {{-- Floating Trades Section --}}
    @if($todayRitual && $openTrades->count() > 0)
        <div>
            <div class="flex justify-between items-end mb-4">
                <h3 class="text-lg font-bold text-white">Open Positions</h3>
                <span class="bg-purple-500/20 text-purple-400 text-xs font-bold px-2 py-1 rounded-lg">{{ $openTrades->count() }} ACTIVE</span>
            </div>
            
            <div class="space-y-3">
                @foreach($openTrades as $trade)
                <a href="{{ route('trades.edit', $trade) }}" class="block glass rounded-2xl p-4 hover:border-gray-700 transition-colors">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $trade->direction === 'Long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ strtoupper($trade->direction) }}
                            </span>
                            <span class="text-white font-bold">{{ $trade->pair }}</span>
                        </div>
                        <span class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($trade->created_at)->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-gray-500 text-xs">Entry Price</p>
                            <p class="text-gray-300 font-medium">{{ $trade->entry_price }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500 text-xs">Lot Size</p>
                            <p class="text-gray-300 font-medium">{{ $trade->lot_size }}</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-800 flex justify-center">
                        <span class="text-purple-400 text-xs font-medium flex items-center gap-1">
                            Close Position 
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Closed Trades Summary (Optional/Small) --}}
    @if($todayRitual && $todayTrades->whereNotNull('exit_price')->count() > 0)
        <div>
            <h3 class="text-gray-400 text-sm font-medium mb-3">Today's Closed Trades</h3>
            <div class="space-y-2">
                @foreach($todayTrades->whereNotNull('exit_price') as $trade)
                <div class="glass rounded-xl p-3 flex justify-between items-center opacity-80">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $trade->pnl >= 0 ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <div>
                            <p class="text-white text-sm font-medium">{{ $trade->pair }}</p>
                            <p class="text-gray-500 text-xs">{{ $trade->direction }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-sm {{ $trade->pnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ $trade->pnl >= 0 ? '+' : '' }}{{ $activeAccount->currency ?? 'USD' }} {{ number_format(abs($trade->pnl), 2) }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Post-Market Review Button --}}
    @if($todayRitual && $todayRitual->post_mood === null)
    <div class="pt-4 border-t border-gray-800">
        <a href="{{ route('ritual.post-market') }}" class="flex items-center justify-center gap-2 w-full py-4 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold rounded-2xl transition-colors border border-gray-700">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Complete Post-Market Review
        </a>
    </div>
    @endif

</div>

@include('dashboard.settings-modal')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('equityChart', () => ({
        activeFilter: 'All',
        customStart: '',
        customEnd: '',
        chartInstance: null,
        fullData: @json($equityData ?? []),
        
        init() {
            if (this.fullData.length === 0) return;
            this.$nextTick(() => {
                this.renderChart(this.fullData);
            });
        },
        
        setFilter(filter) {
            this.activeFilter = filter;
            if (this.fullData.length === 0) return;
            
            if (filter === 'Custom') {
                return; // Wait for applyCustomFilter
            }

            let filteredData = [...this.fullData];
            const now = new Date();
            
            if (filter === '1W') {
                const oneWeekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                filteredData = this.fullData.filter(d => new Date(d.x) >= oneWeekAgo);
            } else if (filter === '1M') {
                const oneMonthAgo = new Date();
                oneMonthAgo.setMonth(now.getMonth() - 1);
                filteredData = this.fullData.filter(d => new Date(d.x) >= oneMonthAgo);
            } else if (filter === '3M') {
                const threeMonthsAgo = new Date();
                threeMonthsAgo.setMonth(now.getMonth() - 3);
                filteredData = this.fullData.filter(d => new Date(d.x) >= threeMonthsAgo);
            }
            
            // If filtering results in empty array (e.g., account created today), just show all
            if(filteredData.length === 0) filteredData = this.fullData;

            this.updateChart(filteredData);
        },

        applyCustomFilter() {
            if (this.fullData.length === 0 || this.activeFilter !== 'Custom') return;
            if (!this.customStart || !this.customEnd) return;

            const start = new Date(this.customStart);
            const end = new Date(this.customEnd);
            // Include end of day
            end.setHours(23, 59, 59, 999);

            let filteredData = this.fullData.filter(d => {
                const dDate = new Date(d.x);
                return dDate >= start && dDate <= end;
            });

            if(filteredData.length === 0) filteredData = this.fullData;

            this.updateChart(filteredData);
        },
        
        renderChart(data) {
            const ctx = document.getElementById('equityChartCanvas').getContext('2d');
            
            // Create gradient for fill
            let gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(168, 85, 247, 0.4)'); // Purple-500
            gradient.addColorStop(1, 'rgba(168, 85, 247, 0.0)');

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.x),
                    datasets: [{
                        label: 'Equity',
                        data: data.map(d => d.y),
                        borderColor: '#a855f7', // Purple-500
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#d946ef', // Fuchsia-400
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: true,
                        tension: 0.4 // Smooth curve
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(17, 24, 39, 0.9)', // Gray-900
                            titleColor: '#9ca3af', // Gray-400
                            bodyColor: '#fff',
                            borderColor: '#374151', // Gray-700
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false // Hide X axis labels for cleaner look
                        },
                        y: {
                            display: false, // Hide Y axis
                            min: Math.min(...data.map(d => d.y)) * 0.99, // Add some padding below
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        },

        updateChart(data) {
            if(!this.chartInstance) return;
            this.chartInstance.data.labels = data.map(d => d.x);
            this.chartInstance.data.datasets[0].data = data.map(d => d.y);
            this.chartInstance.options.scales.y.min = Math.min(...data.map(d => d.y)) * 0.99;
            this.chartInstance.update();
        }
    }));
});
</script>
@endpush
@endsection
