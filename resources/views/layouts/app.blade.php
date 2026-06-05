<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Swiss Book') }} — {{ $title ?? 'Premium Booking' }}</title>
    <meta name="description" content="Book your appointment with precision and ease.">

    {{-- Preconnect for performance --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-stone-50 text-stone-900 font-['Inter',sans-serif] min-h-screen">

    {{-- Navigation --}}
    <header class="fixed top-0 inset-x-0 z-50 bg-white/90 backdrop-blur-md border-b border-stone-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <span class="w-7 h-7 bg-stone-900 rounded-sm flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <span class="font-semibold tracking-tight text-stone-900">SwissBook</span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-stone-600">
                <a href="/#how-it-works" class="hover:text-stone-900 transition-colors">How it works</a>
                <a href="/#services" class="hover:text-stone-900 transition-colors">Services</a>
                <a href="/#booking" class="hover:text-stone-900 transition-colors">Book now</a>
                <a href="/admin" class="text-stone-400 hover:text-stone-600 transition-colors text-xs">Admin</a>
            </nav>
            <a href="/#booking" class="md:hidden text-sm font-medium bg-stone-900 text-white px-4 py-2 rounded-full">
                Book
            </a>
        </div>
    </header>

    <main class="pt-16">
        {{ $slot }}
    </main>

    <footer class="mt-24 border-t border-stone-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div>
                    <p class="font-semibold text-stone-900">SwissBook</p>
                    <p class="text-sm text-stone-500 mt-1">Precision scheduling for discerning businesses.</p>
                </div>
                <p class="text-xs text-stone-400">© {{ date('Y') }} SwissBook. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
