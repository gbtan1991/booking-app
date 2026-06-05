<div x-data="{ panel: @entangle('showPanel') }">

    {{-- ════════════════════════════════════════
         PAGE HEADER
    ════════════════════════════════════════ --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-stone-900">Bookings</h1>
            <p class="text-sm text-stone-500 mt-0.5">Manage all client appointments</p>
        </div>
        <button
            wire:click="openCreate"
            class="inline-flex items-center gap-2 bg-stone-900 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-stone-700 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Booking
        </button>
    </div>

    {{-- ════════════════════════════════════════
         STATS
    ════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['Pending',         $this->stats['pending'],   'text-amber-700',  'bg-amber-50',  'border-amber-200'],
            ['Approved',        $this->stats['approved'],  'text-green-700',  'bg-green-50',  'border-green-200'],
            ['Cancelled',       $this->stats['cancelled'], 'text-red-700',    'bg-red-50',    'border-red-200'],
            ["Today's Slots",   $this->stats['today'],     'text-stone-700',  'bg-stone-50',  'border-stone-200'],
        ] as [$label, $value, $text, $bg, $border])
        <div class="rounded-2xl border p-5 {{ $bg }} {{ $border }}">
            <p class="text-xs font-semibold uppercase tracking-wide {{ $text }} opacity-70">{{ $label }}</p>
            <p class="text-3xl font-bold mt-1 {{ $text }}">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    {{-- ════════════════════════════════════════
         FILTERS
    ════════════════════════════════════════ --}}
    <div class="bg-white border border-stone-200 rounded-2xl p-4 mb-5 flex flex-wrap gap-3 items-center">
        <div class="flex-1 min-w-[180px] relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search name, email, phone…"
                class="w-full text-sm border border-stone-200 rounded-xl pl-9 pr-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-stone-900 transition"
            >
        </div>

        <div class="flex gap-1.5 flex-wrap">
            @foreach (['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'cancelled' => 'Cancelled'] as $val => $lbl)
            <button
                wire:click="$set('statusFilter', '{{ $val }}')"
                @class([
                    'px-3 py-2 rounded-lg text-xs font-semibold transition-all',
                    'bg-stone-900 text-white shadow-sm' => $statusFilter === $val,
                    'bg-stone-100 text-stone-600 hover:bg-stone-200' => $statusFilter !== $val,
                ])
            >{{ $lbl }}</button>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <input
                type="date"
                wire:model.live="dateFilter"
                class="text-sm border border-stone-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-stone-900 transition"
            >
            @if ($dateFilter)
            <button wire:click="$set('dateFilter', '')"
                    class="text-xs text-stone-400 hover:text-red-500 transition-colors font-medium">✕</button>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════
         TABLE
    ════════════════════════════════════════ --}}
    <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50/60">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider">Client</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider hidden sm:table-cell">Date & Time</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider hidden lg:table-cell">Service</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider hidden md:table-cell">Phone</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-stone-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($bookings as $booking)
                    <tr class="hover:bg-stone-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-stone-900 leading-tight">{{ $booking->name }}</p>
                            <p class="text-xs text-stone-400 mt-0.5">{{ $booking->email }}</p>
                        </td>
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <p class="font-medium text-stone-800">{{ $booking->booking_date->format('j M Y') }}</p>
                            <p class="text-xs text-stone-400 mt-0.5">{{ $booking->time_slot }}</p>
                        </td>
                        <td class="px-5 py-4 text-stone-600 text-xs hidden lg:table-cell">{{ $booking->service }}</td>
                        <td class="px-5 py-4 text-stone-500 text-xs hidden md:table-cell">{{ $booking->phone }}</td>
                        <td class="px-5 py-4">
                            <span @class([
                                'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold',
                                'bg-amber-100 text-amber-800' => $booking->status === 'pending',
                                'bg-green-100 text-green-800' => $booking->status === 'approved',
                                'bg-red-100   text-red-800'   => $booking->status === 'cancelled',
                            ])>{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                 x-data="{ open: false }">
                                {{-- Edit --}}
                                <button
                                    wire:click="openEdit({{ $booking->id }})"
                                    title="Edit"
                                    class="p-1.5 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                {{-- More --}}
                                <div class="relative">
                                    <button @click="open = !open"
                                            class="p-1.5 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="5"  cy="12" r="1.5"/>
                                            <circle cx="12" cy="12" r="1.5"/>
                                            <circle cx="19" cy="12" r="1.5"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="absolute right-0 mt-1 w-36 bg-white border border-stone-200 rounded-xl shadow-lg z-20 py-1 text-xs">
                                        @if ($booking->status !== 'approved')
                                        <button wire:click="approve({{ $booking->id }})" @click="open=false"
                                                class="w-full text-left px-3 py-2 text-green-700 hover:bg-green-50 font-medium transition-colors">
                                            ✓ Approve
                                        </button>
                                        @endif
                                        @if ($booking->status !== 'cancelled')
                                        <button wire:click="cancel({{ $booking->id }})" @click="open=false"
                                                class="w-full text-left px-3 py-2 text-amber-700 hover:bg-amber-50 font-medium transition-colors">
                                            ✕ Cancel
                                        </button>
                                        @endif
                                        <div class="my-1 border-t border-stone-100"></div>
                                        <button
                                            wire:click="delete({{ $booking->id }})"
                                            wire:confirm="Permanently delete this booking?"
                                            @click="open=false"
                                            class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 font-medium transition-colors">
                                            🗑 Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-16 text-stone-400">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm font-semibold">No bookings found</p>
                            <p class="text-xs mt-1 text-stone-300">Adjust your filters or create one manually.</p>
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

    {{-- ════════════════════════════════════════
         SLIDE-OVER PANEL (Create / Edit)
    ════════════════════════════════════════ --}}

    {{-- Backdrop --}}
    <div
        x-show="panel"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$wire.closePanel()"
        class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm z-30"
        x-cloak
    ></div>

    {{-- Panel --}}
    <div
        x-show="panel"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 w-full sm:w-[480px] bg-white shadow-2xl z-40 flex flex-col"
        x-cloak
    >
        {{-- Panel header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-stone-100 shrink-0">
            <div>
                <h2 class="text-lg font-bold text-stone-900">
                    {{ $editingId ? 'Edit Booking' : 'New Booking' }}
                </h2>
                <p class="text-xs text-stone-400 mt-0.5">
                    {{ $editingId ? 'Update the booking details below.' : 'Manually create a booking for a client.' }}
                </p>
            </div>
            <button wire:click="closePanel"
                    class="p-2 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Panel body --}}
        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-5">

            {{-- Client details --}}
            <fieldset class="space-y-4">
                <legend class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-3">Client Details</legend>

                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Full Name</label>
                    <input type="text" wire:model="form_name" placeholder="Marie Dupont"
                           class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300">
                    @error('form_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" wire:model="form_email" placeholder="marie@example.com"
                           class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300">
                    @error('form_email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Phone</label>
                    <input type="tel" wire:model="form_phone" placeholder="+41 79 123 45 67"
                           class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300">
                    @error('form_phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </fieldset>

            <div class="border-t border-stone-100"></div>

            {{-- Appointment details --}}
            <fieldset class="space-y-4">
                <legend class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-3">Appointment</legend>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Date</label>
                        <input type="date" wire:model="form_booking_date"
                               min="{{ now()->addDay()->format('Y-m-d') }}"
                               class="w-full border border-stone-200 rounded-xl px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition">
                        @error('form_booking_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Time Slot</label>
                        <select wire:model="form_time_slot"
                                class="w-full border border-stone-200 rounded-xl px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition bg-white">
                            <option value="">— pick time —</option>
                            @foreach (\App\Livewire\BookingWizard::SLOTS as $slot)
                            <option value="{{ $slot }}">{{ $slot }}</option>
                            @endforeach
                        </select>
                        @error('form_time_slot') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Service</label>
                    <select wire:model="form_service"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition bg-white">
                        @foreach (\App\Livewire\BookingWizard::SERVICES as $svc)
                        <option value="{{ $svc }}">{{ $svc }}</option>
                        @endforeach
                    </select>
                    @error('form_service') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Status</label>
                    <select wire:model="form_status"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition bg-white">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('form_status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">
                        Notes <span class="font-normal text-stone-400 normal-case tracking-normal">(optional)</span>
                    </label>
                    <textarea wire:model="form_notes" rows="3"
                              placeholder="Internal notes or client requests…"
                              class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300 resize-none"></textarea>
                    @error('form_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </fieldset>
        </div>

        {{-- Panel footer --}}
        <div class="shrink-0 px-6 py-4 border-t border-stone-100 flex justify-between items-center bg-stone-50/50">
            <button wire:click="closePanel"
                    class="text-sm font-semibold text-stone-500 hover:text-stone-900 transition-colors">
                Cancel
            </button>
            <button
                wire:click="save"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                class="inline-flex items-center gap-2 bg-stone-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-stone-700 transition-all"
            >
                <span wire:loading.remove wire:target="save">
                    {{ $editingId ? 'Save Changes' : 'Create Booking' }}
                </span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Saving…
                </span>
            </button>
        </div>
    </div>

</div>
