<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="theme-color" content="#111827">
    <title>@yield('title', 'TradeRitual')</title>

    {{-- PWA Setup --}}
    <link rel="manifest" href="/manifest.json" crossorigin="use-credentials">
    <link rel="apple-touch-icon" href="/icons/icon.svg">
    <meta name="description" content="TradeRitual - Master your trading discipline through psychology-first journaling.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN (for rapid dev — will be replaced with Vite build for production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#e879f9',
                            400: '#d946ef',
                            500: '#a855f7',
                            600: '#9333ea',
                            700: '#7e22ce',
                            800: '#6b21a8',
                            900: '#581c87',
                        },
                        surface: {
                            DEFAULT: '#111827',
                            50:  '#1f2937',
                            100: '#1a2332',
                            200: '#151d2b',
                            300: '#0f1623',
                        },
                    },
                },
            },
        }
    </script>

    <!-- Alpine.js for lightweight reactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Smooth transitions globally */
        * { -webkit-tap-highlight-color: transparent; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #111827;
            color: #f3f4f6;
            overscroll-behavior: none;
        }

        /* Glassmorphism utility */
        .glass {
            background: rgba(31, 41, 55, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Glow effects */
        .glow-purple { box-shadow: 0 0 20px rgba(168, 85, 247, 0.15); }
        .glow-green  { box-shadow: 0 0 20px rgba(34, 197, 94, 0.15); }
        .glow-red    { box-shadow: 0 0 20px rgba(239, 68, 68, 0.15); }

        /* Input focus ring */
        input:focus, select:focus, textarea:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.4);
        }

        /* Smooth page transition */
        .page-enter {
            animation: fadeInUp 0.3s ease-out forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Pulse animation for streak fire */
        .pulse-fire {
            animation: pulseFire 2s ease-in-out infinite;
        }
        @keyframes pulseFire {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.1); }
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-surface antialiased text-gray-100">
    <!-- Mobile App Wrapper -->
    <div id="app" class="max-w-md mx-auto min-h-screen relative flex flex-col bg-gray-900 overflow-x-hidden">
        
        <main class="flex-1 flex flex-col pb-20">
            @yield('content')
        </main>

        @if(session('supabase_access_token'))
        <!-- Bottom Navigation Bar -->
        <nav class="fixed bottom-0 left-0 right-0 w-full max-w-md mx-auto glass border-t border-gray-800 z-50">
            <div class="flex justify-around items-center h-16 px-4">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full text-gray-400 hover:text-purple-400 transition-colors {{ request()->routeIs('dashboard') ? 'text-purple-400' : '' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] font-medium">Home</span>
                </a>
                <a href="{{ route('analytics.index') }}" class="flex flex-col items-center justify-center w-full text-gray-400 hover:text-purple-400 transition-colors {{ request()->routeIs('analytics.index') ? 'text-purple-400' : '' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-[10px] font-medium">Analytics</span>
                </a>
                <a href="{{ route('performance.index') }}" class="flex flex-col items-center justify-center w-full text-gray-400 hover:text-purple-400 transition-colors {{ request()->routeIs('performance.index') ? 'text-purple-400' : '' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-[10px] font-medium">Calendar</span>
                </a>
                <a href="{{ route('ai.index') }}" class="flex flex-col items-center justify-center w-full text-gray-400 hover:text-purple-400 transition-colors {{ request()->routeIs('ai.index') ? 'text-purple-400' : '' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-[10px] font-medium">AI Coach</span>
                </a>
                <a href="{{ route('profile.index') }}" class="flex flex-col items-center justify-center w-full {{ request()->routeIs('profile.*') ? 'text-purple-400' : 'text-gray-400 hover:text-purple-400' }} transition-colors">
                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] font-medium">Profile</span>
                </a>
            </div>
        </nav>
        @endif
    </div>

    @stack('scripts')
    
    {{-- Service Worker Registration for PWA --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('ServiceWorker registered:', registration.scope);
                }).catch(err => {
                    console.log('ServiceWorker registration failed:', err);
                });
            });
        }
    </script>
</body>
</html>
