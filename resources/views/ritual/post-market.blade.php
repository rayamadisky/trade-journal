@extends('layouts.app')

@section('title', 'TradeRitual — Post-Market Review')

@section('content')
<div class="px-4 py-8 page-enter" x-data="{ mood: null, followedPlan: null }">
    
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-500/20 text-purple-400 mb-4 glow-purple">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Post-Market Review</h1>
        <p class="text-gray-400 text-sm px-4">Reflect on your day. Be completely honest with yourself.</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('ritual.store-post-market') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Question 1: Mood --}}
        <div class="glass p-5 rounded-2xl">
            <label class="block text-white font-medium mb-1">How are you feeling after trading today?</label>
            <p class="text-gray-500 text-xs mb-4">1 = Tilted/Revenge Mode, 5 = Calm/Disciplined</p>
            
            <div class="flex justify-between items-center gap-2">
                @foreach([
                    1 => ['emoji' => '🤬', 'color' => 'bg-red-500/20 text-red-500 border-red-500/50'],
                    2 => ['emoji' => '😔', 'color' => 'bg-orange-500/20 text-orange-500 border-orange-500/50'],
                    3 => ['emoji' => '😐', 'color' => 'bg-yellow-500/20 text-yellow-500 border-yellow-500/50'],
                    4 => ['emoji' => '😌', 'color' => 'bg-green-400/20 text-green-400 border-green-400/50'],
                    5 => ['emoji' => '🧘‍♂️', 'color' => 'bg-emerald-500/20 text-emerald-500 border-emerald-500/50']
                ] as $val => $data)
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="post_mood" value="{{ $val }}" class="hidden" @click="mood = {{ $val }}" required>
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
            @error('post_mood')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Question 2: Followed Plan --}}
        <div class="glass p-5 rounded-2xl">
            <label class="block text-white font-medium mb-3">Did you follow your trading plan?</label>
            
            <input type="hidden" name="followed_plan" x-model="followedPlan">
            <div class="grid grid-cols-2 gap-4">
                <button type="button" 
                    @click="followedPlan = 1" 
                    class="py-4 rounded-xl font-bold transition-all border-2 flex flex-col items-center gap-2"
                    :class="followedPlan === 1 ? 'bg-green-500/20 text-green-400 border-green-500 glow-green' : 'bg-gray-800 border-transparent text-gray-500 hover:bg-gray-700'">
                    <span class="text-2xl">🛡️</span>
                    Yes
                </button>
                <button type="button" 
                    @click="followedPlan = 0" 
                    class="py-4 rounded-xl font-bold transition-all border-2 flex flex-col items-center gap-2"
                    :class="followedPlan === 0 ? 'bg-red-500/20 text-red-400 border-red-500 glow-red' : 'bg-gray-800 border-transparent text-gray-500 hover:bg-gray-700'">
                    <span class="text-2xl">🤡</span>
                    No
                </button>
            </div>
            @error('followed_plan')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Question 3: Daily Notes --}}
        <div class="glass p-5 rounded-2xl">
            <label class="block text-white font-medium mb-1">Journal Notes</label>
            <p class="text-gray-500 text-xs mb-4">What did you do well? What needs improvement tomorrow?</p>
            
            <textarea name="daily_notes" rows="4" placeholder="Be brutally honest..."
                class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
            >{{ old('daily_notes') }}</textarea>
            @error('daily_notes')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="pt-4 pb-8">
            <button type="submit" class="w-full py-4 bg-purple-600 hover:bg-purple-500 text-white font-bold text-lg rounded-2xl shadow-lg shadow-purple-500/20 active:scale-[0.98] transition-transform">
                Complete Day
            </button>
        </div>

    </form>
</div>
@endsection
