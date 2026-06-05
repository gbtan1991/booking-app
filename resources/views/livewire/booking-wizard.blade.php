{{--
    BookingWizard — 4 steps + confirmation
    Uses server-side @if for step rendering (no x-show/entangle) so Livewire
    DOM morphing cannot break step visibility.
--}}
<div class="w-full">

    {{-- ══════════════════════════ PROGRESS BAR ══════════════════════════ --}}
    @if ($step < 5)
    @php $steps = ['Service', 'Date', 'Time', 'Details']; @endphp
    <div class="flex items-center gap-2 mt-10 mb-10 max-w-lg mx-auto px-2">
        @foreach ($steps as $i => $label)
            @php $num = $i + 1; @endphp
            <div class="flex flex-col items-center gap-1 flex-shrink-0">
                <div @class([
                    'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all',
                    'bg-stone-900 border-stone-900 text-white' => $step === $num,
                    'bg-green-500 border-green-500 text-white' => $step > $num,
                    'bg-white border-stone-200 text-stone-400' => $step < $num,
                ])>
                    @if ($step > $num)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{ $num }}
                    @endif
                </div>
                <span @class([
                    'text-[10px] font-semibold uppercase tracking-wider hidden sm:block',
                    'text-stone-900' => $step === $num,
                    'text-green-600' => $step > $num,
                    'text-stone-300' => $step < $num,
                ])>{{ $label }}</span>
            </div>
            @if ($i < 3)
            <div @class([
                'flex-1 h-0.5 mb-4 rounded-full transition-all duration-500',
                'bg-green-400' => $step > $num,
                'bg-stone-200' => $step <= $num,
            ])></div>
            @endif
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════ STEP 1 — SERVICE ══════════════════════════ --}}
    @if ($step === 1)
    <div class="max-w-4xl mx-auto px-2">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-stone-900">What can we help you with?</h2>
            <p class="text-stone-500 text-sm mt-3">Choose the service that best fits your needs.</p>
        </div>

        @if ($this->services->isEmpty())
        <div class="text-center py-16 text-stone-400">
            <svg class="w-10 h-10 mx-auto mb-3 text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
            </svg>
            <p class="text-sm font-semibold">No services available yet.</p>
            <p class="text-xs mt-1">Please check back soon.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($this->services as $svc)
            <button
                type="button"
                wire:click="selectService('{{ $svc->name }}')"
                wire:key="svc-{{ $svc->id }}"
                wire:loading.class="opacity-50 cursor-wait"
                class="group text-left bg-white border-2 rounded-2xl p-6 transition-all duration-150 hover:border-stone-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-stone-900 focus:ring-offset-2 {{ $selectedService === $svc->name ? 'border-stone-900 shadow-md' : 'border-stone-200' }}"
            >
                <div class="w-10 h-10 {{ $selectedService === $svc->name ? 'bg-stone-900' : 'bg-stone-100 group-hover:bg-stone-900' }} rounded-xl flex items-center justify-center mb-4 transition-colors">
                    <svg class="w-5 h-5 {{ $selectedService === $svc->name ? 'text-white' : 'text-stone-600 group-hover:text-white' }} transition-colors"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-semibold text-stone-900 text-sm leading-snug">{{ $svc->name }}</h3>
                    <span class="shrink-0 text-xs font-bold text-stone-900 bg-stone-100 px-2 py-0.5 rounded-full">
                        {{ $svc->formatted_price }}
                    </span>
                </div>
                @if ($svc->description)
                <p class="text-xs text-stone-500 leading-relaxed">{{ $svc->description }}</p>
                @endif
            </button>
            @endforeach
        </div>
        @endif

        @error('selectedService')
        <p class="mt-4 text-sm text-red-500 text-center">{{ $message }}</p>
        @enderror
    </div>

    {{-- ══════════════════════════ STEP 2 — DATE ══════════════════════════ --}}
    @elseif ($step === 2)
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">

            @if ($this->selectedServiceModel)
            <div class="px-6 pt-6 pb-0">
                <div class="inline-flex items-center gap-2 bg-stone-50 border border-stone-100 rounded-xl px-3 py-2 text-xs text-stone-600 mb-5">
                    <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                    <span class="font-semibold text-stone-900">{{ $this->selectedServiceModel->name }}</span>
                    <span class="text-stone-400">·</span>
                    <span class="font-semibold">{{ $this->selectedServiceModel->formatted_price }}</span>
                </div>
            </div>
            @endif

            <div class="p-6 sm:p-8 pt-2">
                <h3 class="text-lg font-bold text-stone-900 mb-1">Pick a date</h3>
                <p class="text-sm text-stone-500 mb-6">Next 30 weekdays — Sundays excluded</p>

                @php
                    $grouped = collect($this->calendarDays)->groupBy(fn ($d) => $d['month']);
                @endphp

                @foreach ($grouped as $month => $days)
                <div class="mb-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-3">{{ $month }}</p>
                    {{-- 7-col calendar-week grid: Mon→Sun header row, then date cells --}}
                    <div class="grid grid-cols-7 gap-1.5">
                        @foreach (['M','T','W','T','F','S','S'] as $h)
                        <div class="text-center text-[10px] font-bold text-stone-300 uppercase pb-1">{{ $h }}</div>
                        @endforeach

                        @php
                            // Pad the first week so Monday = col 1
                            $firstDay = \Carbon\Carbon::parse($days->first()['value'])->dayOfWeekIso; // 1=Mon…7=Sun
                            $padCols  = $firstDay - 1;
                        @endphp
                        @for ($p = 0; $p < $padCols; $p++)
                        <div></div>
                        @endfor

                        @foreach ($days as $day)
                        <button
                            type="button"
                            wire:click="selectDate('{{ $day['value'] }}')"
                            wire:key="day-{{ $day['value'] }}"
                            @class([
                                'flex flex-col items-center gap-0.5 py-2 rounded-xl border text-center transition-all duration-100 cursor-pointer focus:outline-none focus:ring-2 focus:ring-stone-900 focus:ring-offset-1 w-full',
                                'border-stone-900 bg-stone-900 text-white shadow-md' => $selectedDate === $day['value'],
                                'border-stone-200 text-stone-700 hover:border-stone-400 hover:bg-stone-50' => $selectedDate !== $day['value'] && !$day['weekend'],
                                'border-stone-100 text-stone-400 hover:border-stone-200' => $selectedDate !== $day['value'] && $day['weekend'],
                            ])
                        >
                            <span class="text-sm font-bold leading-tight">{{ $day['day'] }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach

                @error('selectedDate')
                <p class="mt-2 text-sm text-red-500 flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="px-6 sm:px-8 pb-6 sm:pb-8 border-t border-stone-100 pt-4 flex justify-between items-center">
                <button type="button" wire:click="goToStep(1)"
                        class="text-sm font-semibold text-stone-500 hover:text-stone-900 transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                    </svg>
                    Back
                </button>
                <button
                    type="button"
                    wire:click="goToStep(3)"
                    {{ $selectedDate ? '' : 'disabled' }}
                    class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-2.5 rounded-full text-sm font-semibold hover:bg-stone-700 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                >
                    Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════ STEP 3 — TIME ══════════════════════════ --}}
    @elseif ($step === 3)
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-bold text-stone-900 mb-1">Choose a time</h3>
                <p class="text-sm text-stone-500 mb-6">
                    Available slots for
                    <span class="font-semibold text-stone-800">
                        {{ \Carbon\Carbon::parse($selectedDate)->format('l, j F Y') }}
                    </span>
                </p>

                @if (count($this->availableTimes) > 0)
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($this->availableTimes as $slot)
                    <button
                        type="button"
                        wire:click="selectTime('{{ $slot }}')"
                        wire:key="slot-{{ $slot }}"
                        @class([
                            'py-3 rounded-xl border text-sm font-semibold transition-all duration-100 focus:outline-none focus:ring-2 focus:ring-stone-900 focus:ring-offset-1',
                            'border-stone-900 bg-stone-900 text-white shadow-md' => $selectedTime === $slot,
                            'border-stone-200 text-stone-700 hover:border-stone-900 hover:bg-stone-50' => $selectedTime !== $slot,
                        ])
                    >{{ $slot }}</button>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-10 h-10 mx-auto mb-3 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-semibold text-stone-500">Fully booked</p>
                    <p class="text-xs text-stone-400 mt-1">No slots left for this date.</p>
                    <button type="button" wire:click="goToStep(2)"
                            class="mt-4 text-sm font-semibold text-stone-900 underline underline-offset-2">
                        Choose another date
                    </button>
                </div>
                @endif

                @error('selectedTime')
                <p class="mt-4 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="px-6 sm:px-8 pb-6 sm:pb-8 border-t border-stone-100 pt-4 flex justify-between items-center">
                <button type="button" wire:click="goToStep(2)"
                        class="text-sm font-semibold text-stone-500 hover:text-stone-900 transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                    </svg>
                    Back
                </button>
                <button
                    type="button"
                    wire:click="goToStep(4)"
                    {{ $selectedTime ? '' : 'disabled' }}
                    class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-2.5 rounded-full text-sm font-semibold hover:bg-stone-700 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                >
                    Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════ STEP 4 — DETAILS ══════════════════════════ --}}
    @elseif ($step === 4)
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8">

                {{-- Booking summary pill --}}
                <div class="flex flex-wrap gap-2 mb-7 p-4 bg-stone-50 rounded-xl border border-stone-100">
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-stone-700 bg-white border border-stone-200 rounded-lg px-3 py-1.5">
                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                        </svg>
                        {{ $selectedService }}
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-stone-700 bg-white border border-stone-200 rounded-lg px-3 py-1.5">
                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($selectedDate)->format('j M Y') }}
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-stone-700 bg-white border border-stone-200 rounded-lg px-3 py-1.5">
                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $selectedTime }}
                    </div>
                </div>

                <h3 class="text-lg font-bold text-stone-900 mb-5">Your details</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-600 mb-1.5 uppercase tracking-wider">Full Name</label>
                        <input type="text" wire:model="customer_name" placeholder="Marie Dupont" autocomplete="name"
                               class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300">
                        @error('customer_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-600 mb-1.5 uppercase tracking-wider">Email Address</label>
                        <input type="email" wire:model="customer_email" placeholder="marie@example.com" autocomplete="email"
                               class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300">
                        @error('customer_email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-600 mb-1.5 uppercase tracking-wider">Phone Number</label>
                        <input type="tel" wire:model="customer_telephone" placeholder="+41 79 123 45 67" autocomplete="tel"
                               class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300">
                        @error('customer_telephone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-600 mb-1.5 uppercase tracking-wider">
                            Notes <span class="font-normal text-stone-400 normal-case tracking-normal">(optional)</span>
                        </label>
                        <textarea wire:model="customer_notes" rows="3"
                                  placeholder="Anything helpful to know before your appointment…"
                                  class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300 resize-none"></textarea>
                        @error('customer_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

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
                <button type="button" wire:click="goToStep(3)"
                        class="text-sm font-semibold text-stone-500 hover:text-stone-900 transition-colors flex items-center gap-1.5">
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
                    <span wire:loading.remove wire:target="submitBooking">Confirm Booking</span>
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
    </div>

    {{-- ══════════════════════════ STEP 5 — SUCCESS ══════════════════════════ --}}
    @elseif ($step === 5)
    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">

            {{-- Green top accent --}}
            <div class="h-1.5 bg-gradient-to-r from-green-400 to-emerald-500"></div>

            <div class="p-8 sm:p-10 text-center">

                {{-- Animated checkmark --}}
                <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 ring-8 ring-green-50">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-stone-900 mb-2">Booking Confirmed!</h2>
                <p class="text-stone-500 text-sm leading-relaxed mb-8">
                    Thank you, <span class="font-semibold text-stone-700">{{ $customer_name }}</span>.<br>
                    We look forward to seeing you. A confirmation will be sent to<br>
                    <span class="font-semibold text-stone-700">{{ $customer_email }}</span>.
                </p>

                {{-- Booking summary card --}}
                <div class="bg-stone-50 border border-stone-100 rounded-2xl p-5 text-left space-y-3 mb-8">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-stone-400 font-medium">Service</span>
                        <span class="font-semibold text-stone-900">{{ $selectedService }}</span>
                    </div>
                    <div class="border-t border-stone-100"></div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-stone-400 font-medium">Date</span>
                        <span class="font-semibold text-stone-900">{{ \Carbon\Carbon::parse($selectedDate)->format('l, j F Y') }}</span>
                    </div>
                    <div class="border-t border-stone-100"></div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-stone-400 font-medium">Time</span>
                        <span class="font-semibold text-stone-900">{{ $selectedTime }}</span>
                    </div>
                    <div class="border-t border-stone-100"></div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-stone-400 font-medium">Status</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Confirmed
                        </span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center justify-center gap-2 border border-stone-200 text-stone-700 px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-50 transition-all">
                        Book another
                    </a>
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center justify-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-stone-700 transition-all">
                        Back to home
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
