@extends('layouts.app')

@section('title', 'TradeRitual — Log Trade')

@section('content')
<div class="px-4 py-6 page-enter" x-data="tradeForm()">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold text-white">Log New Trade</h1>
            <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Account: <span class="font-bold text-white">{{ $activeAccount->name }}</span>
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </a>
    </div>

    <form action="{{ route('trades.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Direction Toggle (Hidden Input mapped by Alpine) --}}
        <input type="hidden" name="direction" x-model="direction">
        <div class="flex gap-4">
            <button type="button" 
                @click="direction = 'Long'" 
                class="flex-1 py-4 rounded-2xl font-bold text-lg transition-all border-2"
                :class="direction === 'Long' ? 'bg-green-500/20 text-green-400 border-green-500 glow-green' : 'bg-gray-800 border-transparent text-gray-500'">
                LONG
            </button>
            <button type="button" 
                @click="direction = 'Short'" 
                class="flex-1 py-4 rounded-2xl font-bold text-lg transition-all border-2"
                :class="direction === 'Short' ? 'bg-red-500/20 text-red-400 border-red-500 glow-red' : 'bg-gray-800 border-transparent text-gray-500'">
                SHORT
            </button>
        </div>

        {{-- Instrument --}}
        <div class="glass p-5 rounded-2xl space-y-4">
            <div>
                <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Instrument Pair</label>
                <div class="relative">
                    @if(isset($tradingPairs) && $tradingPairs->count() > 0)
                        <select name="pair" required class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 font-bold focus:border-purple-500 focus:ring-1 focus:ring-purple-500 uppercase transition-colors">
                            <option value="" disabled selected>Select a Pair</option>
                            @foreach($tradingPairs as $tp)
                                <option value="{{ $tp->symbol }}" {{ old('pair') == $tp->symbol ? 'selected' : '' }}>{{ $tp->symbol }}</option>
                            @endforeach
                        </select>
                    @else
                        <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs">
                            You haven't added any Trading Pairs yet. Please add them in the Dashboard Settings (⚙️) before logging a trade.
                        </div>
                    @endif
                </div>
                @error('pair') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Numbers Grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Entry Price</label>
                    <input type="number" step="0.00001" name="entry_price" required placeholder="0.00" value="{{ old('entry_price') }}"
                        class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-3 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
                    >
                    @error('entry_price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Lot Size</label>
                    <input type="number" step="0.01" name="lot_size" required placeholder="1.00" value="{{ old('lot_size') }}"
                        class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-3 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
                    >
                    @error('lot_size') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-red-400 text-xs font-bold uppercase tracking-wider mb-2">Stop Loss</label>
                    <input type="number" step="0.00001" name="stop_loss" required placeholder="0.00" value="{{ old('stop_loss') }}"
                        class="w-full bg-red-900/20 border border-red-500/30 text-white rounded-xl px-3 py-3 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                    >
                    @error('stop_loss') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-green-400 text-xs font-bold uppercase tracking-wider mb-2">Take Profit</label>
                    <input type="number" step="0.00001" name="take_profit" required placeholder="0.00" value="{{ old('take_profit') }}"
                        class="w-full bg-green-900/20 border border-green-500/30 text-white rounded-xl px-3 py-3 focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-colors"
                    >
                    @error('take_profit') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Tactical Tags (JSONB) --}}
        <div class="glass p-5 rounded-2xl">
            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-3">Tactical Tags</label>
            <div class="flex flex-wrap gap-2">
                <template x-for="tag in availableTags" :key="tag">
                    <button type="button" 
                        @click="toggleTag(tag)"
                        class="px-3 py-1.5 rounded-full text-xs font-bold transition-all border"
                        :class="selectedTags.includes(tag) ? 'bg-purple-500/20 text-purple-300 border-purple-500' : 'bg-gray-800 text-gray-400 border-transparent hover:bg-gray-700'">
                        <span x-text="tag"></span>
                    </button>
                </template>
            </div>
            
            {{-- Hidden inputs for selected tags --}}
            <template x-for="tag in selectedTags">
                <input type="hidden" name="tags[]" :value="tag">
            </template>
        </div>

        {{-- Visual Evidence (Screenshot) --}}
        <div class="glass p-5 rounded-2xl">
            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-3">Visual Evidence (Chart)</label>
            
            <div class="relative group cursor-pointer" x-data="{ fileName: '' }">
                <input type="file" name="screenshot_entry" id="screenshot_entry" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="fileName = $event.target.files[0]?.name">
                <div class="border-2 border-dashed border-gray-700 hover:border-purple-500 rounded-xl p-8 text-center transition-colors bg-gray-900">
                    <svg class="mx-auto h-8 w-8 text-gray-500 group-hover:text-purple-500 transition-colors mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span x-show="!fileName" class="text-sm text-gray-400">Tap to upload screenshot</span>
                    <span x-show="fileName" x-text="fileName" class="text-sm text-purple-400 font-medium break-all"></span>
                </div>
            </div>
            @error('screenshot_entry') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <div class="pb-8">
            <button type="submit" class="w-full py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold text-lg rounded-2xl shadow-lg shadow-purple-500/20 active:scale-[0.98] transition-transform">
                Execute Trade
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('tradeForm', () => ({
            direction: '{{ old('direction', 'Long') }}',
            availableTags: ['Liquidity Sweep', 'Imbalance', 'Order Block', 'BOS / CHoCH', 'News Event', 'Killzone'],
            selectedTags: [],
            
            init() {
                // Initialize old tags if validation failed
                let oldTags = @json(old('tags', []));
                if(oldTags && oldTags.length > 0) {
                    this.selectedTags = oldTags;
                }
            },
            
            toggleTag(tag) {
                if (this.selectedTags.includes(tag)) {
                    this.selectedTags = this.selectedTags.filter(t => t !== tag);
                } else {
                    this.selectedTags.push(tag);
                }
            }
        }))
    })
</script>
@endpush
@endsection
