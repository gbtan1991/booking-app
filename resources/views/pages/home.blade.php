<x-layouts.app title="Book Your Appointment">

    {{-- Hero --}}
    <section class="relative bg-white overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold tracking-[0.2em] uppercase text-stone-400 mb-6">Precision · Reliability · Excellence</p>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-[1.08] tracking-tight text-stone-900">
                    Your time,<br>
                    <span class="text-stone-400">perfectly</span> scheduled.
                </h1>
                <p class="mt-6 text-lg text-stone-600 max-w-xl leading-relaxed">
                    Book a premium consultation in seconds. Swiss precision meets modern convenience — no phone calls, no waiting.
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="#booking"
                       class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3.5 rounded-full text-sm font-semibold hover:bg-stone-700 transition-colors shadow-sm">
                        Book an appointment
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="#how-it-works" class="inline-flex items-center gap-2 text-stone-600 px-6 py-3.5 text-sm font-semibold hover:text-stone-900 transition-colors">
                        How it works
                    </a>
                </div>
            </div>
        </div>
        {{-- Decorative grid --}}
        <div class="absolute right-0 top-0 h-full w-1/2 hidden lg:block opacity-[0.03]" aria-hidden="true">
            <div class="h-full w-full"
                 style="background-image: repeating-linear-gradient(0deg,#000 0,#000 1px,transparent 0,transparent 50%),repeating-linear-gradient(90deg,#000 0,#000 1px,transparent 0,transparent 50%); background-size: 40px 40px;">
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="bg-stone-50 py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold tracking-[0.2em] uppercase text-stone-400 mb-3">Process</p>
            <h2 class="text-3xl font-bold text-stone-900 mb-12">Three steps. Done.</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                @foreach ([
                    ['01', 'Choose a date', 'Browse our 30-day calendar and pick any available weekday that suits you.'],
                    ['02', 'Pick a time slot', 'See real-time availability. Slots are locked the moment you confirm.'],
                    ['03', 'Confirm & done', 'Enter your details. Receive instant confirmation. Zero friction.'],
                ] as [$num, $title, $desc])
                <div class="flex gap-5">
                    <span class="text-2xl font-bold text-stone-200 shrink-0 leading-tight">{{ $num }}</span>
                    <div>
                        <h3 class="font-semibold text-stone-900 mb-1">{{ $title }}</h3>
                        <p class="text-sm text-stone-500 leading-relaxed">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section id="services" class="bg-white py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold tracking-[0.2em] uppercase text-stone-400 mb-3">Services</p>
            <h2 class="text-3xl font-bold text-stone-900 mb-12">What we offer</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ([
                    ['General Consultation', '60 min', 'Expert advice tailored to your specific business needs.'],
                    ['Strategy Session', '90 min', 'Deep-dive analysis and actionable strategic planning.'],
                    ['Quick Review', '30 min', 'A focused audit of a single process or deliverable.'],
                ] as [$name, $duration, $desc])
                <div class="group border border-stone-200 rounded-2xl p-6 hover:border-stone-400 hover:shadow-md transition-all duration-200">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="font-semibold text-stone-900">{{ $name }}</h3>
                        <span class="text-xs font-medium text-stone-400 bg-stone-100 px-2 py-1 rounded-full">{{ $duration }}</span>
                    </div>
                    <p class="text-sm text-stone-500 leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Booking Component --}}
    <section id="booking" class="bg-stone-50 py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-xs font-semibold tracking-[0.2em] uppercase text-stone-400 mb-3">Book Now</p>
                <h2 class="text-3xl font-bold text-stone-900">Reserve your slot</h2>
                <p class="mt-3 text-stone-500 text-sm">Real-time availability. Instant confirmation.</p>
            </div>
            <livewire:booking-wizard />
        </div>
    </section>

</x-layouts.app>
