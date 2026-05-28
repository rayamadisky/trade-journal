@extends('layouts.app')

@section('title', 'TradeRitual — AI Coach')

@section('content')
<div class="px-4 py-6 pb-24 page-enter">
    
    <div class="flex items-center gap-3 mb-6">
        <div class="p-3 bg-purple-500/20 rounded-2xl">
            <svg class="w-8 h-8 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-white">AI Coach</h1>
            <p class="text-gray-400 text-xs">Powered by Google Gemini</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 rounded-2xl bg-green-500/20 border border-green-500/40 text-green-300 text-sm glow-green">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-6 rounded-2xl bg-red-500/20 border border-red-500/40 text-red-300 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Generate Button --}}
    <form action="{{ route('ai.generate') }}" method="POST" x-data="{ loading: false }" @submit="loading = true" class="mb-8">
        @csrf
        <button type="submit" class="w-full relative group overflow-hidden rounded-2xl p-1" :disabled="loading">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 rounded-2xl opacity-70 group-hover:opacity-100 animate-pulse transition-opacity"></div>
            <div class="relative bg-gray-900 rounded-xl px-6 py-4 flex items-center justify-center gap-2">
                <svg x-show="!loading" class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                
                {{-- Spinner --}}
                <svg x-show="loading" style="display: none;" class="animate-spin -ml-1 mr-3 h-5 w-5 text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>

                <span class="font-bold text-white" x-text="loading ? 'Analyzing your brain...' : 'Analyze Last 7 Days'"></span>
            </div>
        </button>
    </form>

    {{-- Insights Feed --}}
    <div>
        <h3 class="text-lg font-bold text-white mb-4">Past Insights</h3>
        
        @if($insights->isEmpty())
            <div class="glass p-8 rounded-3xl text-center">
                <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <p class="text-gray-400 text-sm">No insights generated yet. Hit the button above to get your first AI psychological review!</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($insights as $insight)
                    <div class="glass p-6 rounded-3xl border border-purple-500/20 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 blur-3xl rounded-full pointer-events-none"></div>
                        
                        <div class="flex items-center gap-2 mb-4 border-b border-gray-800 pb-3">
                            <span class="text-xs font-bold text-purple-400 bg-purple-500/10 px-2 py-1 rounded">Google Gemini AI</span>
                            <span class="text-xs text-gray-500">{{ $insight->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <div class="prose prose-invert prose-sm max-w-none prose-p:leading-relaxed prose-headings:text-purple-300 prose-a:text-purple-400 prose-strong:text-white">
                            {!! Str::markdown($insight->insight_text) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
