<div>
    {{-- ══════════════════════════ HEADER ══════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Bookings</h1>
            <p class="text-sm text-stone-500 mt-0.5">Manage all customer appointments</p>
        </div>
        <a href="{{ route('home') }}" target="_blank"
           class="inline-flex items-center gap-2 border border-stone-200 text-stone-700 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-stone-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Booking page
        </a>
    </div>

    {{-- ══════════════════════════ STATS CARDS ══════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-stone-200 p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">Current</p>
            <p class="text-3xl font-bold text-stone-900">{{ $this->counts['current'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">Today</p>
            <p class="text-3xl font-bold text-blue-600">{{ $this->counts['today'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">Completed</p>
            <p class="text-3xl font-bold text-green-600">{{ $this->counts['completed'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">Cancelled</p>
            <p class="text-3xl font-bold text-red-500">{{ $this->counts['cancelled'] }}</p>
        </div>
    </div>

    {{-- ══════════════════════════ FILTERS ══════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">

        {{-- Tab bar + search --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 pt-4 pb-0 border-b border-stone-100">
            <div class="flex gap-1">
                @foreach (['current' => 'Current', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
                <button
                    type="button"
                    wire:click="setTab('{{ $key }}')"
                    @class([
                        'px-4 py-2 text-sm font-semibold rounded-t-xl border-b-2 -mb-px transition-colors',
                        'border-stone-900 text-stone-900 bg-stone-50' => $tab === $key,
                        'border-transparent text-stone-400 hover:text-stone-700' => $tab !== $key,
                    ])
                >{{ $label }}</button>
                @endforeach
            </div>

            <div class="sm:ml-auto flex flex-col sm:flex-row gap-2 pb-3">
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search name, email, service…"
                    class="border border-stone-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 w-full sm:w-64 placeholder-stone-300"
                >
                <input
                    type="date"
                    wire:model.live="dateFilter"
                    class="border border-stone-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 text-stone-700"
                >
                @if ($dateFilter)
                <button type="button" wire:click="$set('dateFilter', '')"
                        class="text-xs font-semibold text-stone-400 hover:text-stone-700 px-2">Clear date</button>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════ TABLE ══════════════════════════ --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-bold text-stone-400 uppercase tracking-wider bg-stone-50 border-b border-stone-100">
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3 hidden md:table-cell">Service</th>
                        <th class="px-5 py-3">Date & Time</th>
                        <th class="px-5 py-3 hidden lg:table-cell">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse ($bookings as $booking)
                    <tr class="hover:bg-stone-50 transition-colors" wire:key="booking-{{ $booking->id }}">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-stone-900 leading-snug">{{ $booking->customer_name }}</p>
                            <p class="text-xs text-stone-400 mt-0.5">{{ $booking->customer_email }}</p>
                            @if ($booking->customer_telephone)
                            <p class="text-xs text-stone-400">{{ $booking->customer_telephone }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-stone-700 font-medium">{{ $booking->service }}</span>
                            @if ($booking->customer_notes)
                            <p class="text-xs text-stone-400 mt-0.5 max-w-xs truncate" title="{{ $booking->customer_notes }}">
                                {{ $booking->customer_notes }}
                            </p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-stone-800">{{ \Carbon\Carbon::parse($booking->date)->format('j M Y') }}</p>
                            <p class="text-xs text-stone-400 mt-0.5">{{ $booking->time }}</p>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $booking->status_color }}">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                @if ($booking->status === 'current')
                                    <button
                                        type="button"
                                        wire:click="markCompleted({{ $booking->id }})"
                                        wire:confirm="Mark this booking as completed?"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-50 text-green-700 hover:bg-green-100 transition-colors border border-green-100"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Done
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markCancelled({{ $booking->id }})"
                                        wire:confirm="Cancel this booking?"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-100"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Cancel
                                    </button>
                                @elseif ($booking->status === 'cancelled' || $booking->status === 'completed')
                                    <button
                                        type="button"
                                        wire:click="markCurrent({{ $booking->id }})"
                                        wire:confirm="Restore this booking to current?"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors border border-blue-100"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Restore
                                    </button>
                                @endif
                                <button
                                    type="button"
                                    wire:click="delete({{ $booking->id }})"
                                    wire:confirm="Permanently delete this booking?"
                                    class="p-1.5 rounded-lg text-stone-300 hover:text-red-500 hover:bg-red-50 transition-colors"
                                    title="Delete"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <svg class="w-10 h-10 mx-auto mb-3 text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-semibold text-stone-400">No bookings found</p>
                            <p class="text-xs text-stone-300 mt-1">Try adjusting your search or date filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($bookings->hasPages())
        <div class="px-5 py-4 border-t border-stone-100">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
