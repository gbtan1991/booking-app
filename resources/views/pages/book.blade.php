<x-layouts.app title="Book an Appointment">

    <div class="min-h-[calc(100vh-4rem)] bg-stone-50">

        {{-- Page header --}}
        <div class="bg-white border-b border-stone-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-400 hover:text-stone-700 transition-colors mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                    </svg>
                    SwissBook
                </a>
                <h1 class="text-3xl font-bold text-stone-900">Book an appointment</h1>
                <p class="text-stone-500 text-sm mt-1.5">
                    Real-time availability — select your service, date, and time in under a minute.
                </p>
            </div>
        </div>

        {{-- Wizard --}}
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <livewire:booking-wizard />
        </div>

    </div>

</x-layouts.app>
