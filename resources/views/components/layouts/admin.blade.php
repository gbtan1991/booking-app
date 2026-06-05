<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — SwissBook</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-stone-100 text-stone-900 font-['Inter',sans-serif] h-full">

<div class="flex h-full min-h-screen">
    {{-- Sidebar --}}
    <aside class="hidden md:flex flex-col w-60 bg-stone-900 text-white shrink-0">
        <div class="h-16 flex items-center px-6 border-b border-stone-700">
            <span class="font-semibold tracking-tight">SwissBook Admin</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1">
            <a href="/admin" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-stone-800 text-white text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Bookings
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-stone-700">
            <a href="/" class="text-xs text-stone-400 hover:text-stone-200 transition-colors">← Public site</a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        {{-- Admin top bar --}}
        <header class="h-16 bg-white border-b border-stone-200 flex items-center px-4 sm:px-6 gap-4 shrink-0">
            <span class="md:hidden font-semibold text-stone-900">SwissBook Admin</span>
            <div class="ml-auto" id="notification-area"></div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-auto">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', (events) => {
            const event = events[0];
            const colors = { success: 'bg-green-600', warning: 'bg-amber-600', error: 'bg-red-600' };
            const el = document.createElement('div');
            el.className = `fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg text-white text-sm font-medium shadow-lg transition-all ${colors[event.type] ?? 'bg-stone-800'}`;
            el.textContent = event.message;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 3000);
        });
    });
</script>

</body>
</html>
