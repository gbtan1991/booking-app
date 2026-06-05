<?php

namespace App\Livewire;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

class BookingWizard extends Component
{
    // Wizard step (1–4, where 4 = success)
    public int  $step      = 1;
    public bool $completed = false;

    // Step 1
    public string $selectedDate = '';

    // Step 2
    public string $selectedSlot = '';

    // Step 3
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|email|max:150')]
    public string $email = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('required|string')]
    public string $service = 'General Consultation';

    #[Validate('nullable|string|max:500')]
    public string $notes = '';

    // Kept as a const so it's available in every request context
    // including inside DB::transaction() closures
    public const SLOTS = [
        '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
        '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
        '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
        '17:00',
    ];

    public const SERVICES = [
        'General Consultation',
        'Strategy Session',
        'Quick Review',
    ];

    public function mount(): void
    {
        if ($user = auth()->user()) {
            $this->name  = $user->name;
            $this->email = $user->email;
        }
    }

    #[Computed]
    public function availableSlots(): array
    {
        if (! $this->selectedDate) {
            return [];
        }

        $booked = Booking::bookedSlotsForDate($this->selectedDate);

        return array_values(array_filter(
            self::SLOTS,
            fn ($slot) => ! in_array($slot, $booked, true)
        ));
    }

    #[Computed]
    public function calendarDays(): array
    {
        $today = now()->startOfDay();
        $days  = [];

        for ($i = 1; $i <= 30; $i++) {
            $day = $today->copy()->addDays($i);
            if ($day->dayOfWeek === 0) {
                continue; // skip Sundays
            }
            $days[] = [
                'value'   => $day->format('Y-m-d'),
                'label'   => $day->format('D'),
                'day'     => $day->format('j'),
                'month'   => $day->format('M'),
                'weekend' => $day->dayOfWeek === 6,
            ];
        }

        return $days;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->selectedSlot = '';
        unset($this->availableSlots);
    }

    public function selectSlot(string $slot): void
    {
        $this->selectedSlot = $slot;
    }

    public function goToStep(int $step): void
    {
        if ($step === 2 && ! $this->selectedDate) {
            $this->addError('selectedDate', 'Please select a date first.');
            return;
        }

        if ($step === 3 && ! $this->selectedSlot) {
            $this->addError('selectedSlot', 'Please select a time slot first.');
            return;
        }

        $this->resetErrorBag();
        $this->step = $step;
    }

    public function submitBooking(): void
    {
        // Rate limit: 5 submissions per IP per 10 minutes
        $rateLimitKey = 'booking:' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError('rate_limit', "Too many requests. Try again in {$seconds} seconds.");
            return;
        }

        // Validate contact fields
        $this->validate();

        // Hard-validate date + slot server-side (prevents tampered Livewire state)
        $extra = Validator::make(
            ['booking_date' => $this->selectedDate, 'time_slot' => $this->selectedSlot],
            [
                'booking_date' => ['required', 'date', 'after:today'],
                'time_slot'    => ['required', 'string', 'in:' . implode(',', self::SLOTS)],
            ]
        );

        if ($extra->fails()) {
            $this->addError('selectedDate', 'Invalid date or time slot. Please start again.');
            $this->step = 1;
            return;
        }

        // Atomically check slot availability and insert
        try {
            DB::transaction(function () {
                $conflict = Booking::where('booking_date', $this->selectedDate)
                    ->where('time_slot', $this->selectedSlot)
                    ->whereIn('status', ['pending', 'approved'])
                    ->lockForUpdate()
                    ->exists();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'selectedSlot' => 'This slot was just taken. Please choose another time.',
                    ]);
                }

                Booking::create([
                    'user_id'      => auth()->id(),
                    'name'         => strip_tags($this->name),
                    'email'        => $this->email,
                    'phone'        => strip_tags($this->phone),
                    'booking_date' => $this->selectedDate,
                    'time_slot'    => $this->selectedSlot,
                    'service'      => $this->service,
                    'notes'        => strip_tags($this->notes ?? ''),
                    'status'       => 'pending',
                ]);
            });
        } catch (ValidationException $e) {
            // Forward slot-conflict message back to the view
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            $this->step = 2;
            return;
        }

        RateLimiter::hit($rateLimitKey, 600);

        $this->completed = true;
        $this->step      = 4;
    }

    public function render()
    {
        return view('livewire.booking-wizard');
    }
}

