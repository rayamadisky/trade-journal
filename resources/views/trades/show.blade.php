@extends('layouts.app')

@section('title', 'TradeRitual — Trade Detail')

@section('content')
<div class="px-4 py-6 page-enter pb-20">
    
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back
        </a>
        <span class="px-3 py-1 rounded text-xs font-bold {{ $trade->direction === 'Long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
            {{ strtoupper($trade->direction) }}
        </span>
    </div>

    {{-- Header --}}
    <div class="glass p-6 rounded-3xl mb-6 relative overflow-hidden">
        @if($trade->pnl !== null)
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full blur-3xl opacity-20 {{ $trade->pnl >= 0 ? 'bg-green-500' : 'bg-red-500' }}"></div>
        @endif
        
        <h1 class="text-3xl font-bold text-white mb-1">{{ $trade->pair }}</h1>
        <p class="text-gray-400 text-sm mb-4">{{ \Carbon\Carbon::parse($trade->created_at)->format('M d, Y • H:i') }}</p>
        
        @if($trade->pnl !== null)
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider font-bold mb-1">Net PnL</p>
                <h2 class="text-4xl font-bold {{ $trade->pnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $trade->pnl >= 0 ? '+' : '' }}${{ number_format($trade->pnl, 2) }}
                </h2>
            </div>
        @else
            <div class="inline-block px-3 py-1 bg-amber-500/20 text-amber-500 text-sm font-bold rounded-lg border border-amber-500/30">
                ACTIVE POSITION
            </div>
        @endif
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="glass p-4 rounded-2xl">
            <p class="text-gray-500 text-xs uppercase tracking-wider font-bold mb-1">Entry Price</p>
            <p class="text-white font-medium">{{ $trade->entry_price }}</p>
        </div>
        <div class="glass p-4 rounded-2xl">
            <p class="text-gray-500 text-xs uppercase tracking-wider font-bold mb-1">Lot Size</p>
            <p class="text-white font-medium">{{ $trade->lot_size }}</p>
        </div>
        <div class="glass p-4 rounded-2xl">
            <p class="text-gray-500 text-xs uppercase tracking-wider font-bold mb-1">Stop Loss</p>
            <p class="text-red-400 font-medium">{{ $trade->stop_loss }}</p>
        </div>
        <div class="glass p-4 rounded-2xl">
            <p class="text-gray-500 text-xs uppercase tracking-wider font-bold mb-1">Take Profit</p>
            <p class="text-green-400 font-medium">{{ $trade->take_profit }}</p>
        </div>
        @if($trade->exit_price)
        <div class="glass p-4 rounded-2xl col-span-2">
            <p class="text-gray-500 text-xs uppercase tracking-wider font-bold mb-1">Exit Price</p>
            <p class="text-white font-medium text-xl">{{ $trade->exit_price }}</p>
        </div>
        @endif
    </div>

    {{-- Tags --}}
    @if(count($trade->tags ?? []) > 0)
    <div class="mb-6">
        <h3 class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-3">Tactical Tags</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($trade->tags as $tag)
            <span class="px-3 py-1.5 bg-purple-500/20 text-purple-300 border border-purple-500/50 text-xs font-bold rounded-full">
                {{ $tag }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Notes --}}
    @if($trade->trade_notes)
    <div class="glass p-5 rounded-2xl mb-6">
        <h3 class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-3">Reflection</h3>
        <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">{{ $trade->trade_notes }}</p>
    </div>
    @endif

    {{-- Screenshots --}}
    @if($trade->screenshot_entry || $trade->screenshot_exit)
    <div class="space-y-4">
        <h3 class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-3">Visual Evidence</h3>
        
        @if($trade->screenshot_entry)
        <div>
            <p class="text-gray-500 text-xs mb-2">Entry Chart</p>
            <img src="{{ $trade->screenshot_entry }}" alt="Entry Screenshot" class="w-full rounded-2xl border border-gray-800 shadow-lg">
        </div>
        @endif

        @if($trade->screenshot_exit)
        <div class="mt-4">
            <p class="text-gray-500 text-xs mb-2">Exit Chart</p>
            <img src="{{ $trade->screenshot_exit }}" alt="Exit Screenshot" class="w-full rounded-2xl border border-gray-800 shadow-lg">
        </div>
        @endif
    </div>
    @endif

    @if(!$trade->exit_price)
    <div class="mt-8">
        <a href="{{ route('trades.edit', $trade) }}" class="block w-full text-center py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold text-lg rounded-2xl shadow-lg shadow-purple-500/20 active:scale-[0.98] transition-transform">
            Close Position
        </a>
    </div>
    @endif

</div>
@endsection
