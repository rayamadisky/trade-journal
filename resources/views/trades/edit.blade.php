@extends('layouts.app')

@section('title', 'TradeRitual — Close Trade')

@section('content')
<div class="px-4 py-6 page-enter" x-data="closeTradeForm()">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold text-white">Close Position</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $trade->pair }} • {{ strtoupper($trade->direction) }}</p>
            <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Account: <span class="font-bold text-white">{{ $activeAccount->name }}</span>
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </a>
    </div>

    {{-- Trade Summary Card --}}
    <div class="glass p-5 rounded-2xl mb-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider font-bold mb-1">Entry Price</p>
                <p class="text-white font-medium">{{ $trade->entry_price }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider font-bold mb-1">Lot Size</p>
                <p class="text-white font-medium">{{ $trade->lot_size }}</p>
            </div>
        </div>
        @if(count($trade->tags ?? []) > 0)
        <div class="mt-4 pt-4 border-t border-gray-800">
            <div class="flex flex-wrap gap-2">
                @foreach($trade->tags as $tag)
                <span class="px-2 py-1 bg-gray-800 text-gray-300 text-[10px] font-bold rounded-lg">{{ $tag }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <form action="{{ route('trades.update', $trade) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Exit Details --}}
        <div class="glass p-5 rounded-2xl space-y-4">
            <div>
                <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Exit Price</label>
                <input type="number" step="0.00001" name="exit_price" required placeholder="0.00" value="{{ old('exit_price') }}"
                    class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
                >
                @error('exit_price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Net PnL ($)</label>
                <div class="relative">
                    <input type="number" step="0.01" name="pnl" x-model="pnl" required placeholder="0.00"
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 font-bold transition-colors"
                        :class="parseFloat(pnl) > 0 ? 'text-green-400 focus:border-green-500 focus:ring-1 focus:ring-green-500' : (parseFloat(pnl) < 0 ? 'text-red-400 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500')"
                    >
                </div>
                @error('pnl') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Reflection & Notes --}}
        <div class="glass p-5 rounded-2xl">
            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Trade Reflection</label>
            <textarea name="trade_notes" rows="3" placeholder="Did you follow your rules? What could be improved?"
                class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
            >{{ old('trade_notes', $trade->trade_notes) }}</textarea>
            @error('trade_notes') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Exit Screenshot --}}
        <div class="glass p-5 rounded-2xl">
            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-3">Exit Screenshot (Optional)</label>
            
            <div class="relative group cursor-pointer" x-data="{ fileName: '' }">
                <input type="file" name="screenshot_exit" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="fileName = $event.target.files[0]?.name">
                <div class="border-2 border-dashed border-gray-700 hover:border-purple-500 rounded-xl p-6 text-center transition-colors bg-gray-900">
                    <svg class="mx-auto h-6 w-6 text-gray-500 group-hover:text-purple-500 transition-colors mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span x-show="!fileName" class="text-xs text-gray-400">Upload exit chart</span>
                    <span x-show="fileName" x-text="fileName" class="text-xs text-purple-400 font-medium break-all"></span>
                </div>
            </div>
            @error('screenshot_exit') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <div class="pb-8">
            <button type="submit" class="w-full py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold text-lg rounded-2xl shadow-lg shadow-purple-500/20 active:scale-[0.98] transition-transform">
                Save & Close Trade
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('closeTradeForm', () => ({
            pnl: '{{ old('pnl', '') }}',
        }))
    })
</script>
@endpush
@endsection
