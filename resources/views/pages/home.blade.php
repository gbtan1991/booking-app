<x-layouts.app title="Premium Business Booking">

    {{-- ══════════════ HERO ══════════════ --}}
    <section class="relative bg-white overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-36">
            <div class="max-w-2xl">
                <p class="text-xs font-bold tracking-[0.2em] uppercase text-stone-400 mb-5">Precision · Reliability · Excellence</p>
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold leading-[1.06] tracking-tight text-stone-900">
                    Your time,<br>
                    <span class="text-stone-300">perfectly</span><br>
                    scheduled.
                </h1>
                <p class="mt-6 text-lg text-stone-500 max-w-lg leading-relaxed">
                    Book a premium consultation in seconds. Swiss precision meets modern convenience — no phone calls, no waiting.
                </p>
                <div class="mt-10 flex flex-wrap gap-3">
                    <a href="{{ route('book') }}"
                       class="inline-flex items-center gap-2 bg-stone-900 text-white px-7 py-3.5 rounded-full text-sm font-semibold hover:bg-stone-700 transition-colors shadow-sm">
                        Book an appointment
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="#how-it-works"
                       class="inline-flex items-center gap-2 text-stone-600 px-7 py-3.5 text-sm font-semibold hover:text-stone-900 transition-colors border border-stone-200 rounded-full hover:border-stone-400">
                        How it works
                    </a>
                </div>
            </div>
        </div>
        {{-- Decorative grid --}}
        <div class="absolute right-0 top-0 h-full w-1/2 hidden lg:block pointer-events-none" aria-hidden="true">
            <div class="h-full w-full opacity-[0.025]"
                 style="background-image: repeating-linear-gradient(0deg,#000 0,#000 1px,transparent 0,transparent 50%),repeating-linear-gradient(90deg,#000 0,#000 1px,transparent 0,transparent 50%); background-size: 44px 44px;"></div>
        </div>
    </section>

    {{-- ══════════════ HOW IT WORKS ══════════════ --}}
    <section id="how-it-works" class="bg-stone-50 py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-bold tracking-[0.2em] uppercase text-stone-400 mb-3">Process</p>
            <h2 class="text-3xl font-bold text-stone-900 mb-14">Four steps. Done.</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ([
                    ['01', 'Pick a service',  'Choose from our curated list of consulting services.'],
                    ['02', 'Choose a date',   'Browse 30 available weekdays on a clean calendar.'],
                    ['03', 'Select a time',   'Real-time slots. Locked the moment you confirm.'],
                    ['04', 'Confirm & done',  'Enter your details and receive instant confirmation.'],
                ] as [$num, $title, $desc])
                <div class="flex gap-4">
                    <span class="text-2xl font-black text-stone-200 shrink-0 leading-tight pt-0.5">{{ $num }}</span>
                    <div>
                        <h3 class="font-semibold text-stone-900 mb-1 text-sm">{{ $title }}</h3>
                        <p class="text-sm text-stone-500 leading-relaxed">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════ SERVICES ══════════════ --}}
    <section id="services" class="bg-white py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-14">
                <div>
                    <p class="text-xs font-bold tracking-[0.2em] uppercase text-stone-400 mb-3">Services</p>
                    <h2 class="text-3xl font-bold text-stone-900">What we offer</h2>
                </div>
                <a href="{{ route('book') }}"
                   class="shrink-0 inline-flex items-center gap-2 text-sm font-semibold text-stone-900 border-b-2 border-stone-900 pb-0.5 hover:text-stone-600 hover:border-stone-600 transition-colors">
                    Book now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach (\App\Livewire\BookingWizard::SERVICE_DETAILS as $svc)
                <div class="group border border-stone-200 rounded-2xl p-7 hover:border-stone-900 hover:shadow-lg transition-all duration-200 flex flex-col">
                    <div class="w-10 h-10 bg-stone-100 group-hover:bg-stone-900 rounded-xl flex items-center justify-center mb-5 transition-colors">
                        <svg class="w-5 h-5 text-stone-600 group-hover:text-white transition-colors"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $svc['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <h3 class="font-semibold text-stone-900">{{ $svc['name'] }}</h3>
                        <span class="shrink-0 text-xs font-bold text-stone-900 bg-stone-100 px-2.5 py-1 rounded-full">{{ $svc['price'] }}</span>
                    </div>
                    <p class="text-sm text-stone-500 leading-relaxed mb-5 flex-1">{{ $svc['description'] }}</p>
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-stone-100">
                        <span class="flex items-center gap-1 text-xs text-stone-400 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $svc['duration'] }}
                        </span>
                        <a href="{{ route('book') }}?service={{ urlencode($svc['name']) }}"
                           class="text-xs font-bold text-stone-900 hover:text-stone-600 transition-colors underline underline-offset-2">
                            Book this →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════ CTA BANNER ══════════════ --}}
    <section class="bg-stone-900 py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Ready to get started?</h2>
            <p class="text-stone-400 mb-8 text-base">
                Booking takes less than two minutes. Slots fill fast.
            </p>
            <a href="{{ route('book') }}"
               class="inline-flex items-center gap-2 bg-white text-stone-900 px-8 py-4 rounded-full text-sm font-bold hover:bg-stone-100 transition-colors shadow-sm">
                Reserve your slot
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </section>

</x-layouts.app>
