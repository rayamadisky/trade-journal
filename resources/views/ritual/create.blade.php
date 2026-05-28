@extends('layouts.app')

@section('title', 'TradeRitual — Pre-Market Ritual')

@section('content')
<div class="px-4 py-8 page-enter">
    
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-500/20 text-amber-500 mb-4 glow-purple">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Pre-Market Check-in</h1>
        <p class="text-gray-400 text-sm px-4">Your discipline starts here. Honest inputs equal better data.</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('ritual.store') }}" method="POST" class="space-y-6" x-data="{ mood: null }">
        @csrf

        {{-- Question 1: Sleep --}}
        <div class="glass p-5 rounded-2xl">
            <label for="sleep_hours" class="block text-white font-medium mb-1">How many hours did you sleep?</label>
            <p class="text-gray-500 text-xs mb-4">Lack of sleep kills your edge.</p>
            
            <div class="relative">
                <input 
                    type="number" 
                    name="sleep_hours" 
                    id="sleep_hours" 
                    min="0" max="24" required
                    placeholder="e.g. 7"
                    class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 text-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                >
                <div class="absolute right-4 top-3.5 text-gray-500 font-medium">hrs</div>
            </div>
            @error('sleep_hours')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Question 2: Mood --}}
        <div class="glass p-5 rounded-2xl">
            <label class="block text-white font-medium mb-1">How is your mental focus/mood?</label>
            <p class="text-gray-500 text-xs mb-4">1 = Frustrated/Tired, 5 = Peak Focus</p>
            
            <div class="flex justify-between items-center gap-2">
                @foreach([
                    1 => ['emoji' => '😫', 'color' => 'bg-red-500/20 text-red-500 border-red-500/50'],
                    2 => ['emoji' => '😮‍💨', 'color' => 'bg-orange-500/20 text-orange-500 border-orange-500/50'],
                    3 => ['emoji' => '😐', 'color' => 'bg-yellow-500/20 text-yellow-500 border-yellow-500/50'],
                    4 => ['emoji' => '🙂', 'color' => 'bg-green-400/20 text-green-400 border-green-400/50'],
                    5 => ['emoji' => '🔥', 'color' => 'bg-emerald-500/20 text-emerald-500 border-emerald-500/50']
                ] as $val => $data)
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="pre_mood" value="{{ $val }}" class="hidden" @click="mood = {{ $val }}" required>
                        <div 
                            class="flex flex-col items-center justify-center p-3 rounded-xl border border-gray-700 transition-all"
                            :class="mood === {{ $val }} ? '{{ $data['color'] }} scale-110 shadow-lg' : 'bg-gray-800/50 text-gray-500 hover:bg-gray-700'"
                        >
                            <span class="text-2xl">{{ $data['emoji'] }}</span>
                            <span class="text-[10px] mt-1 font-bold" x-show="mood === {{ $val }}">{{ $val }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('pre_mood')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Question 3: Max Loss --}}
        <div class="glass p-5 rounded-2xl">
            <label for="max_loss_limit" class="block text-white font-medium mb-1">Daily Max Loss Limit</label>
            <p class="text-gray-500 text-xs mb-4">Set your hard stop for today. Don't lie to yourself.</p>
            
            <div class="relative">
                <div class="absolute left-4 top-3.5 text-gray-500 font-medium">$</div>
                <input 
                    type="number" 
                    step="0.01"
                    name="max_loss_limit" 
                    id="max_loss_limit" 
                    min="0" required
                    value="{{ old('max_loss_limit', $profile->default_max_loss) }}"
                    class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl pl-8 pr-4 py-3 text-lg font-bold text-red-400 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                >
            </div>
            @error('max_loss_limit')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="pt-4 pb-8">
            <button type="submit" class="w-full py-4 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold text-lg rounded-2xl shadow-lg shadow-amber-500/20 active:scale-[0.98] transition-transform">
                I'm Ready to Trade
            </button>
        </div>

    </form>
</div>
@endsection
