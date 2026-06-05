<div
    x-data="{
        step: @entangle('step'),
        animating: false,
        transition(callback) {
            this.animating = true;
            setTimeout(() => { callback(); this.animating = false; }, 250);
        }
    }"
    class="w-full max-w-2xl mx-auto"
>
    {{-- ── Progress indicator ── --}}
    @if ($step < 4)
    <div class="mb-8">
        <div class="flex items-center justify-between gap-2">
            @foreach (['Date', 'Time', 'Details'] as $i => $label)
            <div class="flex-1 flex flex-col items-center gap-2">
                <div @class([
                    'w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold transition-all duration-300',
                    'bg-stone-900 text-white shadow-md' => $step == $i + 1,
                    'bg-stone-200 text-stone-500' => $step != $i + 1 && $step <= $i + 1,
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
                    'text-xs font-medium transition-colors',
                    'text-stone-900' => $step == $i + 1,
                    'text-stone-400' => $step != $i + 1,
                ])>{{ $label }}</span>
            </div>
            @if ($i < 2)
            <div @class([
                'flex-1 h-px transition-colors duration-500 mb-5',
                'bg-green-400' => $step > $i + 1,
                'bg-stone-200' => $step <= $i + 1,
            ])></div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Card shell ── --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">

        {{-- ── Step 1: Date Picker ── --}}
        <div
            x-show="step === 1"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
        >
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-stone-900 mb-1">Select a date</h3>
                <p class="text-sm text-stone-500 mb-6">Next 30 available weekdays (Sundays excluded)</p>

                {{-- Date grid --}}
                <div class="grid grid-cols-5 sm:grid-cols-7 gap-2">
                    @foreach ($this->calendarDays as $day)
                    <button
                        wire:click="selectDate('{{ $day['value'] }}')"
                        @class([
                            'flex flex-col items-center gap-0.5 py-3 px-1 rounded-xl border text-center transition-all duration-150 hover:border-stone-400 hover:bg-stone-50 cursor-pointer',
                            'border-stone-900 bg-stone-900 text-white shadow-md' => $selectedDate === $day['value'],
                            'border-stone-200 text-stone-700' => $selectedDate !== $day['value'] && !$day['weekend'],
                            'border-stone-100 text-stone-400' => $selectedDate !== $day['value'] && $day['weekend'],
                        ])
                    >
                        <span class="text-[10px] font-medium uppercase tracking-wide opacity-70">{{ $day['label'] }}</span>
                        <span class="text-base font-bold">{{ $day['day'] }}</span>
                        <span class="text-[10px] opacity-60">{{ $day['month'] }}</span>
                    </button>
                    @endforeach
                </div>

                @error('selectedDate')
                <p class="mt-3 text-sm text-red-500 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 110 18A9 9 0 0112 3z"/>
                    </svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="px-6 sm:px-8 pb-6 sm:pb-8 flex justify-end">
                <button
                    wire:click="goToStep(2)"
                    @disabled(!$selectedDate)
                    class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                >
                    Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Step 2: Time Slot ── --}}
        <div
            x-show="step === 2"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
        >
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-stone-900 mb-1">Choose a time</h3>
                <p class="text-sm text-stone-500 mb-6">
                    Available slots for
                    <span class="font-medium text-stone-700">
                        {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l, j F Y') : '' }}
                    </span>
                </p>

                <div wire:loading.class="opacity-50" wire:target="selectDate">
                    @if (count($this->availableSlots) > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach ($this->availableSlots as $slot)
                        <button
                            wire:click="selectSlot('{{ $slot }}')"
                            @class([
                                'py-3 rounded-xl border text-sm font-semibold transition-all duration-150 hover:border-stone-400',
                                'border-stone-900 bg-stone-900 text-white shadow-md' => $selectedSlot === $slot,
                                'border-stone-200 text-stone-700 hover:bg-stone-50' => $selectedSlot !== $slot,
                            ])
                        >
                            {{ $slot }}
                        </button>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12 text-stone-400">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium">No slots available for this date.</p>
                        <p class="text-xs mt-1">Please select another day.</p>
                    </div>
                    @endif
                </div>

                @error('selectedSlot')
                <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="px-6 sm:px-8 pb-6 sm:pb-8 flex justify-between">
                <button wire:click="goToStep(1)" class="text-sm text-stone-500 hover:text-stone-900 transition-colors font-medium">
                    ← Back
                </button>
                <button
                    wire:click="goToStep(3)"
                    @disabled(!$selectedSlot)
                    class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                >
                    Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Step 3: Contact Details ── --}}
        <div
            x-show="step === 3"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
        >
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-stone-900 mb-1">Your details</h3>
                <p class="text-sm text-stone-500 mb-6">
                    Booking
                    <span class="font-medium text-stone-700">{{ $selectedSlot }}</span>
                    on
                    <span class="font-medium text-stone-700">
                        {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('j F Y') : '' }}
                    </span>
                </p>

                <div class="space-y-4">
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
                        <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Email Address</label>
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
                        <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Phone Number</label>
                        <input
                            type="tel"
                            wire:model="phone"
                            placeholder="+41 79 123 45 67"
                            autocomplete="tel"
                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition placeholder-stone-300"
                        >
                        @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes (optional) --}}
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

                    {{-- Rate limit error --}}
                    @error('rate_limit')
                    <div class="flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 110 18A9 9 0 0112 3z"/>
                        </svg>
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="px-6 sm:px-8 pb-6 sm:pb-8 flex justify-between items-center">
                <button wire:click="goToStep(2)" class="text-sm text-stone-500 hover:text-stone-900 transition-colors font-medium">
                    ← Back
                </button>
                <button
                    wire:click="submitBooking"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 transition-all"
                >
                    <span wire:loading.remove wire:target="submitBooking">Confirm Booking</span>
                    <span wire:loading wire:target="submitBooking" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Processing…
                    </span>
                </button>
            </div>
        </div>

        {{-- ── Step 4: Success ── --}}
        <div
            x-show="step === 4"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="p-8 sm:p-12 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-stone-900 mb-2">Booking Confirmed</h3>
                <p class="text-stone-500 mb-1">
                    <span class="font-medium text-stone-700">{{ $selectedSlot }}</span> —
                    {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l, j F Y') : '' }}
                </p>
                <p class="text-sm text-stone-400 mb-8">A confirmation has been recorded. We look forward to seeing you.</p>
                <a href="/" class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 transition-all">
                    Back to home
                </a>
            </div>
        </div>

    </div>
</div>
