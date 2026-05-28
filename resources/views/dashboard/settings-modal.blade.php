{{-- Dashboard Settings Modal (Alpine.js) --}}
<div x-data="{ 
    open: false,
    tab: 'general',
}" @open-dashboard-settings.window="open = true" class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/80 backdrop-blur-sm" style="display: none;"></div>
    <div x-show="open" x-transition.scale.80 style="display: none;" class="fixed inset-0 flex items-center justify-center p-4">
        <div @click.away="open = false" class="bg-gray-900 border border-gray-700 rounded-3xl w-full max-w-lg shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center bg-gray-800/50">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                </h3>
                <button @click="open = false" class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Tabs --}}
            <div class="flex border-b border-gray-800">
                <button @click="tab = 'general'" :class="tab === 'general' ? 'text-purple-400 border-b-2 border-purple-500 bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50'" class="flex-1 py-3 text-sm font-bold transition">General</button>
                <button @click="tab = 'pairs'" :class="tab === 'pairs' ? 'text-purple-400 border-b-2 border-purple-500 bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800/50'" class="flex-1 py-3 text-sm font-bold transition">Trading Pairs</button>
            </div>

            <div class="p-6 overflow-y-auto">
                {{-- General Tab --}}
                <div x-show="tab === 'general'" x-transition>
                    <form method="POST" action="{{ route('settings.dashboard') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Default Max Loss ($)</label>
                            <input type="number" name="default_max_loss" value="{{ $profile->default_max_loss }}" required min="0" step="0.01" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            <p class="text-xs text-gray-500 mt-1">This applies when starting a new Daily Ritual.</p>
                        </div>

                        <div>
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Active Account Currency</label>
                            @if(isset($activeAccount))
                                <select name="currency" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                    <option value="USD" {{ $activeAccount->currency === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ $activeAccount->currency === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ $activeAccount->currency === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                    <option value="IDR" {{ $activeAccount->currency === 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                                    <option value="JPY" {{ $activeAccount->currency === 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                                    <option value="AUD" {{ $activeAccount->currency === 'AUD' ? 'selected' : '' }}>AUD ($)</option>
                                    <option value="CAD" {{ $activeAccount->currency === 'CAD' ? 'selected' : '' }}>CAD ($)</option>
                                </select>
                            @else
                                <p class="text-sm text-gray-500">No active account selected.</p>
                            @endif
                        </div>

                        <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl transition-colors mt-2">Save General Settings</button>
                    </form>
                </div>

                {{-- Pairs Tab --}}
                <div x-show="tab === 'pairs'" x-transition style="display: none;">
                    <form method="POST" action="{{ route('settings.pairs.store') }}" class="mb-6">
                        @csrf
                        <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Add New Pair</label>
                        <div class="flex gap-2">
                            <input type="text" name="symbol" required placeholder="e.g., XAUUSD, NQ, BTC" class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 uppercase">
                            <button type="submit" class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-purple-400 font-bold rounded-xl border border-gray-700 transition">Add</button>
                        </div>
                    </form>

                    <div>
                        <h4 class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-3">Your Saved Pairs</h4>
                        @if(isset($tradingPairs) && $tradingPairs->count() > 0)
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($tradingPairs as $pair)
                                    <div class="flex items-center justify-between bg-gray-800 border border-gray-700 rounded-xl px-3 py-2 group">
                                        <span class="text-sm font-bold text-white">{{ $pair->symbol }}</span>
                                        <form method="POST" action="{{ route('settings.pairs.destroy', $pair->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-400 opacity-0 group-hover:opacity-100 transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 border-2 border-dashed border-gray-800 rounded-2xl">
                                <p class="text-sm text-gray-500">No trading pairs added yet.</p>
                                <p class="text-xs text-gray-600 mt-1">Add your pairs above so you can easily select them when logging trades.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
