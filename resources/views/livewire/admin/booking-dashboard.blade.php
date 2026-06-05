<div x-data="{ confirmId: null, confirmAction: null }">

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach ([
            ['Pending', $this->stats['pending'], 'text-amber-700 bg-amber-50 border-amber-200'],
            ['Approved', $this->stats['approved'], 'text-green-700 bg-green-50 border-green-200'],
            ['Cancelled', $this->stats['cancelled'], 'text-red-700 bg-red-50 border-red-200'],
            ["Today's Bookings", $this->stats['today'], 'text-stone-700 bg-stone-50 border-stone-200'],
        ] as [$label, $value, $classes])
        <div class="bg-white border rounded-2xl p-5 {{ $classes }}">
            <p class="text-xs font-semibold uppercase tracking-wide opacity-70">{{ $label }}</p>
            <p class="text-3xl font-bold mt-1">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Filters ── --}}
    <div class="bg-white border border-stone-200 rounded-2xl p-4 mb-6 flex flex-wrap gap-3 items-center">
        <div class="flex-1 min-w-[160px]">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search name or email…"
                class="w-full text-sm border border-stone-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-stone-900 transition"
            >
        </div>
        <div class="flex gap-2 flex-wrap">
            @foreach (['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'cancelled' => 'Cancelled'] as $val => $label)
            <button
                wire:click="$set('statusFilter', '{{ $val }}')"
                @class([
                    'px-3 py-2 rounded-lg text-xs font-semibold transition-all',
                    'bg-stone-900 text-white' => $statusFilter === $val,
                    'bg-stone-100 text-stone-600 hover:bg-stone-200' => $statusFilter !== $val,
                ])
            >{{ $label }}</button>
            @endforeach
        </div>
        <div>
            <input
                type="date"
                wire:model.live="dateFilter"
                class="text-sm border border-stone-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-stone-900 transition"
            >
        </div>
        @if ($dateFilter)
        <button wire:click="$set('dateFilter', '')" class="text-xs text-stone-400 hover:text-stone-700">Clear date ×</button>
        @endif
    </div>

    {{-- ── Table ── --}}
    <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide">Client</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden sm:table-cell">Date & Time</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden md:table-cell">Phone</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($bookings as $booking)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-medium text-stone-900">{{ $booking->name }}</p>
                            <p class="text-xs text-stone-400 mt-0.5">{{ $booking->email }}</p>
                        </td>
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <p class="font-medium text-stone-700">{{ $booking->booking_date->format('j M Y') }}</p>
                            <p class="text-xs text-stone-400 mt-0.5">{{ $booking->time_slot }}</p>
                        </td>
                        <td class="px-5 py-4 text-stone-600 hidden md:table-cell">{{ $booking->phone }}</td>
                        <td class="px-5 py-4">
                            <span @class([
                                'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold',
                                'bg-amber-100 text-amber-800' => $booking->status === 'pending',
                                'bg-green-100 text-green-800' => $booking->status === 'approved',
                                'bg-red-100 text-red-800' => $booking->status === 'cancelled',
                            ])>
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2"
                                 x-data="{ open: false }"
                            >
                                <div class="relative">
                                    <button @click="open = !open"
                                            class="p-1.5 rounded-lg hover:bg-stone-100 transition-colors text-stone-400">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="absolute right-0 mt-1 w-36 bg-white border border-stone-200 rounded-xl shadow-lg z-10 py-1">
                                        @if ($booking->status !== 'approved')
                                        <button wire:click="approve({{ $booking->id }})" @click="open = false"
                                                class="w-full text-left px-3 py-2 text-xs text-green-700 hover:bg-green-50 transition-colors">
                                            ✓ Approve
                                        </button>
                                        @endif
                                        @if ($booking->status !== 'cancelled')
                                        <button wire:click="cancel({{ $booking->id }})" @click="open = false"
                                                class="w-full text-left px-3 py-2 text-xs text-amber-700 hover:bg-amber-50 transition-colors">
                                            ✕ Cancel
                                        </button>
                                        @endif
                                        <button wire:click="delete({{ $booking->id }})" @click="open = false"
                                                wire:confirm="Permanently delete this booking?"
                                                class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors">
                                            🗑 Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-16 text-stone-400">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm font-medium">No bookings found</p>
                            <p class="text-xs mt-1">Adjust your filters or wait for new bookings.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings->hasPages())
        <div class="px-5 py-4 border-t border-stone-100">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

</div>
