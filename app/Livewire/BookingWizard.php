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
    // Wizard state
    public int $step = 1;
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

    #[Validate('nullable|string|max:500')]
    public string $notes = '';

    public string $service = 'General Consultation';

    public function mount(): void
    {
        // Pre-fill contact details if the customer is already logged in
        if ($user = auth()->user()) {
            $this->name  = $user->name;
            $this->email = $user->email;
        }
    }

    // All available slots the business offers
    protected array $allSlots = [
        '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
        '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
        '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
        '17:00',
    ];

    #[Computed]
    public function availableSlots(): array
    {
        if (! $this->selectedDate) {
            return [];
        }

        $booked = Booking::bookedSlotsForDate($this->selectedDate);

        return array_values(array_filter(
            $this->allSlots,
            fn ($slot) => ! in_array($slot, $booked, true)
        ));
    }

    #[Computed]
    public function calendarDays(): array
    {
        $today = now()->startOfDay();
        $days = [];

        for ($i = 1; $i <= 30; $i++) {
            $day = $today->copy()->addDays($i);
            // Skip Sundays (0) for Swiss-style business hours
            if ($day->dayOfWeek === 0) {
                continue;
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
        // Invalidate computed cache so slots refresh
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
        // Rate limiting: max 5 submissions per IP per 10 minutes
        $key = 'booking:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('rate_limit', "Too many requests. Try again in {$seconds} seconds.");
            return;
        }

        $this->validate();

        // Re-validate date/slot at submission time
        $validator = Validator::make(
            ['booking_date' => $this->selectedDate, 'time_slot' => $this->selectedSlot],
            [
                'booking_date' => ['required', 'date', 'after:today'],
                'time_slot'    => ['required', 'string', 'in:' . implode(',', $this->allSlots)],
            ]
        );

        if ($validator->fails()) {
            $this->addError('selectedDate', 'Invalid date or time slot selected.');
            $this->step = 1;
            return;
        }

        try {
            DB::transaction(function () {
                // Pessimistic lock: prevents race conditions on the same slot
                $conflict = Booking::where('booking_date', $this->selectedDate)
                    ->where('time_slot', $this->selectedSlot)
                    ->whereIn('status', ['pending', 'approved'])
                    ->lockForUpdate()
                    ->first();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'selectedSlot' => 'This slot was just taken. Please choose another.',
                    ]);
                }

                Booking::create([
                    'user_id'      => auth()->id(), // null for guests
                    'name'         => strip_tags($this->name),
                    'email'        => $this->email,
                    'phone'        => $this->phone,
                    'booking_date' => $this->selectedDate,
                    'time_slot'    => $this->selectedSlot,
                    'service'      => $this->service,
                    'notes'        => strip_tags($this->notes),
                    'status'       => 'pending',
                ]);
            });
        } catch (ValidationException $e) {
            $this->addError('selectedSlot', $e->getMessage());
            $this->step = 2;
            return;
        }

        RateLimiter::hit($key, 600);

        $this->completed = true;
        $this->step = 4;
    }

    public function render()
    {
        return view('livewire.booking-wizard');
    }
}

