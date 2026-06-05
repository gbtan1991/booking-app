<div
    x-data="{ step: @entangle('step') }"
    class="w-full max-w-2xl mx-auto"
>
    {{-- ── Progress indicator ── --}}
    @if ($step < 4)
    <div class="mb-8">
        <div class="flex items-center gap-2">
            @foreach (['Date', 'Time', 'Details'] as $i => $label)
            <div class="flex flex-col items-center gap-1.5 flex-1">
                <div @class([
                    'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300',
                    'bg-stone-900 text-white ring-4 ring-stone-900/20' => $step == $i + 1,
                    'bg-stone-200 text-stone-500' => $step < $i + 1,
                    'bg-green-500 text-white' => $step > $i + 1,
                ])>
                    @if ($step > $i + 1)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{ $i + 1 }}
                    @endif
                </div>
                <span @class([
                    'text-xs font-semibold',
                    'text-stone-900' => $step == $i + 1,
                    'text-stone-400' => $step != $i + 1,
                ])>{{ $label }}</span>
            </div>
            @if ($i < 2)
            <div @class([
                'flex-1 h-px mb-5 transition-all duration-500',
                'bg-green-400' => $step > $i + 1,
                'bg-stone-200' => $step <= $i + 1,
            ])></div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Card ── --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">

        {{-- ─────────────────── STEP 1: Date ─────────────────── --}}
        <div x-show="step === 1"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-stone-900 mb-1">Select a date</h3>
                <p class="text-sm text-stone-500 mb-6">Next 30 available weekdays — Sundays excluded</p>

                <div class="grid grid-cols-5 sm:grid-cols-7 gap-2">
                    @foreach ($this->calendarDays as $day)
                    <button
                        type="button"
                        wire:click="selectDate('{{ $day['value'] }}')"
                        wire:key="day-{{ $day['value'] }}"
                        @class([
                            'flex flex-col items-center gap-0.5 py-3 px-1 rounded-xl border text-center transition-all duration-150 cursor-pointer',
                            'border-stone-900 bg-stone-900 text-white shadow-md' => $selectedDate === $day['value'],
                            'border-stone-200 text-stone-700 hover:border-stone-400 hover:bg-stone-50' => $selectedDate !== $day['value'] && !$day['weekend'],
                            'border-stone-100 text-stone-400 hover:border-stone-300 hover:bg-stone-50' => $selectedDate !== $day['value'] && $day['weekend'],
                        ])
                    >
                        <span class="text-[10px] font-semibold uppercase tracking-wider leading-none opacity-60">{{ $day['label'] }}</span>
                        <span class="text-base font-bold leading-tight">{{ $day['day'] }}</span>
                        <span class="text-[10px] leading-none opacity-50">{{ $day['month'] }}</span>
                    </button>
                    @endforeach
                </div>

                @error('selectedDate')
                <p class="mt-4 text-sm text-red-500 flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="px-6 sm:px-8 pb-6 sm:pb-8 border-t border-stone-100 pt-4 flex justify-end">
                <button
                    type="button"
                    wire:click="goToStep(2)"
                    {{ $selectedDate ? '' : 'disabled' }}
                    class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                >
                    Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ─────────────────── STEP 2: Time ─────────────────── --}}
        <div x-show="step === 2"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-stone-900 mb-1">Choose a time</h3>
                <p class="text-sm text-stone-500 mb-6">
                    Available slots for
                    <span class="font-semibold text-stone-800">
                        {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l, j F Y') : '—' }}
                    </span>
                </p>

                <div wire:loading.class="opacity-40 pointer-events-none" wire:target="selectDate,goToStep">
                    @if (count($this->availableSlots) > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach ($this->availableSlots as $slot)
                        <button
                            type="button"
                            wire:click="selectSlot('{{ $slot }}')"
                            wire:key="slot-{{ $slot }}"
                            @class([
                                'py-3 px-2 rounded-xl border text-sm font-semibold transition-all duration-150',
                                'border-stone-900 bg-stone-900 text-white shadow-md scale-[1.02]' => $selectedSlot === $slot,
                                'border-stone-200 text-stone-700 hover:border-stone-400 hover:bg-stone-50' => $selectedSlot !== $slot,
                            ])
                        >{{ $slot }}</button>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12 text-stone-400">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-semibold">Fully booked</p>
                        <p class="text-xs mt-1">No slots left for this date. Please go back and pick another day.</p>
                    </div>
                    @endif
                </div>

                <div wire:loading wire:target="selectDate,goToStep" class="text-center py-8">
                    <svg class="animate-spin w-6 h-6 text-stone-400 mx-auto" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </div>

                @error('selectedSlot')
                <p class="mt-4 text-sm text-red-500 flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="px-6 sm:px-8 pb-6 sm:pb-8 border-t border-stone-100 pt-4 flex justify-between">
                <button type="button" wire:click="goToStep(1)"
                        class="text-sm font-semibold text-stone-500 hover:text-stone-900 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                    </svg>
                    Back
                </button>
                <button
                    type="button"
                    wire:click="goToStep(3)"
                    {{ $selectedSlot ? '' : 'disabled' }}
                    class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                >
                    Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ─────────────────── STEP 3: Details ─────────────────── --}}
        <div x-show="step === 3"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="p-6 sm:p-8">
                {{-- Booking summary pill --}}
                <div class="flex items-center gap-3 mb-6 p-3 bg-stone-50 rounded-xl border border-stone-100">
                    <div class="w-9 h-9 bg-stone-900 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-stone-900">
                            {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l, j F Y') : '' }}
                        </p>
                        <p class="text-xs text-stone-500">at {{ $selectedSlot }}</p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-stone-900 mb-5">Your details</h3>

                <div class="space-y-4">

                    {{-- Service --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Service</label>
                        <select
                            wire:model="service"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition bg-white"
                        >
                            @foreach (\App\Livewire\BookingWizard::SERVICES as $svc)
                            <option value="{{ $svc }}">{{ $svc }}</option>
                            @endforeach
                        </select>
                        @error('service') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Full Name</label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Marie Dupont"
                            autocomplete="name"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition placeholder-stone-300"
                        >
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Email</label>
                        <input
                            type="email"
                            wire:model="email"
                            placeholder="marie@example.com"
                            autocomplete="email"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition placeholder-stone-300"
                        >
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Phone</label>
                        <input
                            type="tel"
                            wire:model="phone"
                            placeholder="+41 79 123 45 67"
                            autocomplete="tel"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition placeholder-stone-300"
                        >
                        @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">
                            Notes <span class="font-normal text-stone-400 normal-case tracking-normal">(optional)</span>
                        </label>
                        <textarea
                            wire:model="notes"
                            rows="3"
                            placeholder="Anything we should know before your appointment…"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition placeholder-stone-300 resize-none"
                        ></textarea>
                        @error('notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Rate limit / general error --}}
                    @error('rate_limit')
                    <div class="flex items-start gap-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="px-6 sm:px-8 pb-6 sm:pb-8 border-t border-stone-100 pt-4 flex justify-between items-center">
                <button type="button" wire:click="goToStep(2)"
                        class="text-sm font-semibold text-stone-500 hover:text-stone-900 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                    </svg>
                    Back
                </button>
                <button
                    type="button"
                    wire:click="submitBooking"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 transition-all"
                >
                    <span wire:loading.remove wire:target="submitBooking">
                        Confirm Booking
                    </span>
                    <span wire:loading wire:target="submitBooking" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Saving…
                    </span>
                </button>
            </div>
        </div>

        {{-- ─────────────────── STEP 4: Success ─────────────────── --}}
        <div x-show="step === 4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="p-8 sm:p-12 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-stone-900 mb-2">Booking Received!</h3>
                <div class="inline-flex items-center gap-3 bg-stone-50 border border-stone-100 rounded-xl px-5 py-3 mb-6">
                    <div class="text-left">
                        <p class="text-sm font-semibold text-stone-900">{{ $service }}</p>
                        <p class="text-xs text-stone-500">
                            {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l, j F Y') : '' }}
                            at {{ $selectedSlot }}
                        </p>
                    </div>
                </div>
                <p class="text-sm text-stone-400 mb-8">We'll be in touch to confirm your appointment.</p>
                <div class="flex flex-wrap gap-3 justify-center">
                    @auth
                    <a href="{{ route('customer.dashboard') }}"
                       class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 transition-all">
                        View my bookings
                    </a>
                    @else
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 transition-all">
                        Create account to track
                    </a>
                    @endauth
                    <a href="/" class="inline-flex items-center gap-2 border border-stone-200 text-stone-700 px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-50 transition-all">
                        Back to home
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
