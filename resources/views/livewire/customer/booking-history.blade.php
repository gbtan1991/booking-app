<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="mb-8">
        <p class="text-xs font-semibold tracking-[0.2em] uppercase text-stone-400 mb-1">Customer Dashboard</p>
        <h1 class="text-3xl font-bold text-stone-900">Hello, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-stone-500 mt-1">Your upcoming and past appointments.</p>
    </div>

    {{-- CTA --}}
    <div class="mb-10">
        <a href="/#booking"
           class="inline-flex items-center gap-2 bg-stone-900 text-white px-5 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Book a new appointment
        </a>
    </div>

    {{-- Upcoming --}}
    <section class="mb-10">
        <h2 class="text-sm font-semibold uppercase tracking-widest text-stone-400 mb-4">Upcoming</h2>

        @forelse ($this->upcomingBookings as $booking)
        <div class="bg-white border border-stone-200 rounded-2xl p-5 mb-3 flex items-start sm:items-center justify-between gap-4 flex-col sm:flex-row">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-stone-100 rounded-xl flex flex-col items-center justify-center shrink-0">
                    <span class="text-xs font-semibold text-stone-500 uppercase">{{ $booking->booking_date->format('M') }}</span>
                    <span class="text-lg font-bold text-stone-900 leading-tight">{{ $booking->booking_date->format('j') }}</span>
                </div>
                <div>
                    <p class="font-semibold text-stone-900">{{ $booking->service }}</p>
                    <p class="text-sm text-stone-500">
                        {{ $booking->booking_date->format('l, j F Y') }} at {{ $booking->time_slot }}
                    </p>
                </div>
            </div>
            <span @class([
                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shrink-0',
                'bg-amber-100 text-amber-800' => $booking->status === 'pending',
                'bg-green-100 text-green-800' => $booking->status === 'approved',
            ])>
                {{ ucfirst($booking->status) }}
            </span>
        </div>
        @empty
        <div class="bg-white border border-stone-200 rounded-2xl p-8 text-center text-stone-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm font-medium">No upcoming appointments.</p>
            <p class="text-xs mt-1">Book one above — it takes 30 seconds.</p>
        </div>
        @endforelse
    </section>

    {{-- Past / Cancelled --}}
    <section>
        <h2 class="text-sm font-semibold uppercase tracking-widest text-stone-400 mb-4">History</h2>

        @forelse ($this->pastBookings as $booking)
        <div class="bg-white border border-stone-100 rounded-2xl p-5 mb-3 flex items-start sm:items-center justify-between gap-4 flex-col sm:flex-row opacity-70">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-stone-50 rounded-xl flex flex-col items-center justify-center shrink-0">
                    <span class="text-xs font-semibold text-stone-400 uppercase">{{ $booking->booking_date->format('M') }}</span>
                    <span class="text-lg font-bold text-stone-400 leading-tight">{{ $booking->booking_date->format('j') }}</span>
                </div>
                <div>
                    <p class="font-medium text-stone-700">{{ $booking->service }}</p>
                    <p class="text-sm text-stone-400">
                        {{ $booking->booking_date->format('j F Y') }} at {{ $booking->time_slot }}
                    </p>
                </div>
            </div>
            <span @class([
                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shrink-0',
                'bg-stone-100 text-stone-500' => $booking->status !== 'cancelled',
                'bg-red-100 text-red-600' => $booking->status === 'cancelled',
            ])>
                {{ ucfirst($booking->status) }}
            </span>
        </div>
        @empty
        <p class="text-sm text-stone-400 text-center py-6">No past appointments yet.</p>
        @endforelse

        @if ($this->pastBookings->hasPages())
        <div class="mt-4">{{ $this->pastBookings->links() }}</div>
        @endif
    </section>

</div>
