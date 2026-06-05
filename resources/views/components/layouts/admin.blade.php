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
    <aside class="hidden md:flex flex-col w-64 bg-stone-900 text-white shrink-0">
        <div class="h-16 flex items-center px-6 border-b border-stone-700">
            <span class="font-semibold tracking-tight">SwissBook Admin</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1">
            @php $path = request()->path(); @endphp
            <a href="/admin"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $path === 'admin' ? 'bg-stone-800 text-white' : 'text-stone-400 hover:bg-stone-800 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Dashboard
            </a>
            <a href="/admin/services"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ str_starts_with($path, 'admin/services') ? 'bg-stone-800 text-white' : 'text-stone-400 hover:bg-stone-800 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Services
            </a>
            <a href="/admin/users"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ str_starts_with($path, 'admin/users') ? 'bg-stone-800 text-white' : 'text-stone-400 hover:bg-stone-800 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Users
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
            <div class="ml-auto flex items-center gap-4">
                <span class="text-sm text-stone-500 hidden sm:block">{{ auth()->user()?->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-stone-400 hover:text-stone-700 transition-colors">
                        Log out
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-6 sm:p-8 lg:p-10 overflow-auto">
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
