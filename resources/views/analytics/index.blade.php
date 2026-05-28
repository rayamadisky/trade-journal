@extends('layouts.app')

@section('title', 'TradeRitual — Analytics')

@section('content')
<div class="px-4 pt-6 pb-24 space-y-6 page-enter" x-data="{ showGlossary: false }">
    
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Analytics</h1>
            <p class="text-gray-400 text-sm">{{ $totalTrades }} closed trades</p>
        </div>
    </div>

    {{-- Top Stats Grid --}}
    <div class="grid grid-cols-2 gap-4">
        {{-- Total PnL --}}
        <div class="glass p-5 rounded-3xl col-span-2 relative overflow-hidden border {{ $totalPnl >= 0 ? 'border-green-500/20' : 'border-red-500/20' }}">
            @if($totalPnl != 0)
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full blur-2xl opacity-20 {{ $totalPnl >= 0 ? 'bg-green-500' : 'bg-red-500' }}"></div>
            @endif
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Net PnL</p>
            <h2 class="text-4xl font-bold {{ $totalPnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                {{ $totalPnl >= 0 ? '+' : '' }}{{ $activeAccount->currency ?? 'USD' }} {{ number_format(abs($totalPnl), 2) }}
            </h2>
        </div>

        {{-- Win Rate --}}
        <div class="glass p-5 rounded-2xl flex flex-col justify-between">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Win Rate</p>
            <div class="flex items-end gap-2">
                <h3 class="text-3xl font-bold text-white">{{ number_format($winRate, 0) }}%</h3>
            </div>
            
            {{-- Mini progress bar --}}
            <div class="w-full bg-gray-800 rounded-full h-1.5 mt-3">
                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $winRate }}%"></div>
            </div>
        </div>

        {{-- Average RR --}}
        <div class="glass p-5 rounded-2xl flex flex-col justify-between">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Average RR</p>
            <div class="flex items-end gap-2">
                <h3 class="text-3xl font-bold text-white">1:{{ number_format($avgRR, 2) }}</h3>
            </div>
            <p class="text-[10px] text-gray-500 mt-2 font-medium">Avg Win / Avg Loss</p>
        </div>
    </div>

    {{-- Advanced Ratios Grid --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <h3 class="text-lg font-bold text-white">Advanced Ratios</h3>
            <button @click="showGlossary = true" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-3">
            {{-- Profit Factor --}}
            <div class="glass p-4 rounded-2xl flex flex-col justify-between">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-1">Profit Factor</p>
                <h3 class="text-xl font-bold text-white">{{ number_format($profitFactor, 2) }}</h3>
            </div>
            {{-- Expectancy --}}
            <div class="glass p-4 rounded-2xl flex flex-col justify-between">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-1">Expectancy</p>
                <h3 class="text-xl font-bold {{ $expectancy >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ $expectancy >= 0 ? '+' : '' }}{{ $activeAccount->currency ?? 'USD' }} {{ number_format(abs($expectancy), 2) }}</h3>
            </div>
            {{-- Max Drawdown --}}
            <div class="glass p-4 rounded-2xl flex flex-col justify-between">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-1">Max Drawdown</p>
                <h3 class="text-xl font-bold text-red-400">-{{ $activeAccount->currency ?? 'USD' }} {{ number_format($maxDrawdown, 2) }}</h3>
            </div>
            {{-- Recovery Factor --}}
            <div class="glass p-4 rounded-2xl flex flex-col justify-between">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-1">Recovery Factor</p>
                <h3 class="text-xl font-bold text-white">{{ number_format($recoveryFactor, 2) }}</h3>
            </div>
            {{-- Avg Win Time --}}
            <div class="glass p-4 rounded-2xl flex flex-col justify-between">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-1">Avg Win Hold</p>
                <h3 class="text-sm font-bold text-green-400">{{ $avgWinMinutes < 60 ? round($avgWinMinutes).'m' : floor($avgWinMinutes/60).'h '.round($avgWinMinutes%60).'m' }}</h3>
            </div>
            {{-- Avg Loss Time --}}
            <div class="glass p-4 rounded-2xl flex flex-col justify-between">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-1">Avg Loss Hold</p>
                <h3 class="text-sm font-bold text-red-400">{{ $avgLossMinutes < 60 ? round($avgLossMinutes).'m' : floor($avgLossMinutes/60).'h '.round($avgLossMinutes%60).'m' }}</h3>
            </div>
            {{-- Largest Win --}}
            <div class="glass p-4 rounded-2xl flex flex-col justify-between">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-1">Largest Win</p>
                <h3 class="text-sm font-bold text-green-400">+{{ $activeAccount->currency ?? 'USD' }} {{ number_format($largestWin, 2) }}</h3>
            </div>
            {{-- Largest Loss --}}
            <div class="glass p-4 rounded-2xl flex flex-col justify-between">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-1">Largest Loss</p>
                <h3 class="text-sm font-bold text-red-400">-{{ $activeAccount->currency ?? 'USD' }} {{ number_format(abs($largestLoss), 2) }}</h3>
            </div>
        </div>
    </div>

    {{-- Tactical Tag Performance --}}
    <div>
        <h3 class="text-lg font-bold text-white mb-4">Tag Performance</h3>
        @if(empty($tagStats))
            <div class="glass p-5 rounded-2xl text-center">
                <p class="text-gray-500 text-sm">No tags used in closed trades yet.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($tagStats as $stat)
                <div class="glass p-4 rounded-2xl flex justify-between items-center hover:border-gray-700 transition-colors">
                    <div>
                        <div class="flex flex-wrap items-center gap-1 mb-2">
                            @foreach($stat['tags'] as $t)
                                <span class="px-2 py-0.5 bg-purple-500/20 text-purple-400 text-[10px] font-bold rounded">{{ $t }}</span>
                            @endforeach
                            <span class="text-gray-500 text-xs ml-1">{{ $stat['total'] }} trades</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-16 bg-gray-800 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $stat['win_rate'] }}%"></div>
                            </div>
                            <span class="text-white text-xs font-bold">{{ number_format($stat['win_rate'], 0) }}% WR</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold {{ $stat['pnl'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ $stat['pnl'] >= 0 ? '+' : '' }}{{ $activeAccount->currency ?? 'USD' }} {{ number_format(abs($stat['pnl']), 2) }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Trade History --}}
    <div>
        <h3 class="text-lg font-bold text-white mb-4">Trade History</h3>
        @if($closedTrades->isEmpty())
            <div class="glass p-5 rounded-2xl text-center">
                <p class="text-gray-500 text-sm">No closed trades found.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($closedTrades as $trade)
                <a href="{{ route('trades.show', $trade) }}" class="block glass rounded-2xl p-4 hover:border-gray-700 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-1 rounded text-[10px] font-bold {{ $trade->direction === 'Long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ strtoupper($trade->direction) }}
                                </span>
                                <span class="text-white font-bold">{{ $trade->pair }}</span>
                            </div>
                            <span class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($trade->created_at)->format('M d, H:i') }}</span>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-lg {{ $trade->pnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $trade->pnl >= 0 ? '+' : '' }}{{ $activeAccount->currency ?? 'USD' }} {{ number_format(abs($trade->pnl), 2) }}
                            </p>
                        </div>
                    </div>
                    
                    @if(count($trade->tags ?? []) > 0)
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach(array_slice($trade->tags, 0, 3) as $tag)
                        <span class="text-[10px] text-gray-400 bg-gray-800 px-1.5 py-0.5 rounded">{{ $tag }}</span>
                        @endforeach
                        @if(count($trade->tags) > 3)
                        <span class="text-[10px] text-gray-500">+{{ count($trade->tags) - 3 }}</span>
                        @endif
                    </div>
                    @endif
                </a>
                @endforeach
            </div>
        @endif
    </div>
{{-- Glossary Modal --}}
<div x-show="showGlossary" x-transition.opacity class="fixed inset-0 z-50 flex items-end justify-center bg-black/80 backdrop-blur-sm sm:items-center" style="display: none;">
    <div @click.outside="showGlossary = false" class="w-full max-w-md bg-gray-900 border border-gray-800 rounded-t-3xl sm:rounded-3xl p-6 h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-white">Metrics Glossary</h2>
            <button @click="showGlossary = false" class="text-gray-500 hover:text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="space-y-6 pb-6">
            <div>
                <h4 class="text-purple-400 font-bold mb-1">Profit Factor</h4>
                <p class="text-gray-400 text-sm">Gross Profit divided by Gross Loss. A value above 1.0 means you are profitable. A value above 1.5 indicates a highly consistent and robust trading system.</p>
            </div>
            <div>
                <h4 class="text-purple-400 font-bold mb-1">Expectancy</h4>
                <p class="text-gray-400 text-sm">The average expected return per trade based on your historical Win Rate, Average Win, and Average Loss. A positive expectancy proves you have a mathematical edge.</p>
            </div>
            <div>
                <h4 class="text-purple-400 font-bold mb-1">Max Drawdown</h4>
                <p class="text-gray-400 text-sm">The maximum observed loss from a peak in your equity curve. It measures the biggest drop your account has suffered before recovering.</p>
            </div>
            <div>
                <h4 class="text-purple-400 font-bold mb-1">Recovery Factor</h4>
                <p class="text-gray-400 text-sm">Total Net Profit divided by Max Drawdown. It indicates how well your system recovers from its worst losses. Values > 2.0 are generally considered good.</p>
            </div>
            <div>
                <h4 class="text-purple-400 font-bold mb-1">Avg Hold Time</h4>
                <p class="text-gray-400 text-sm">The average duration you hold a position. Psychologically, profitable traders tend to cut losses quickly and hold winners longer. Compare your Win Hold vs Loss Hold to check for bad habits!</p>
            </div>
            <div>
                <h4 class="text-purple-400 font-bold mb-1">Average RR (Risk-Reward)</h4>
                <p class="text-gray-400 text-sm">Average Win Size divided by Average Loss Size. Represents how much you make when you win compared to how much you lose when you lose.</p>
            </div>
        </div>
        </div>
    </div>
</div>

</div>
@endsection
