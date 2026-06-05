<?php

namespace App\Livewire;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BookingWizard extends Component
{
    // ── Steps: 1=Service  2=Date  3=Time  4=Details  5=Done ─────────────
    public int $step = 1;

    // Step 1
    #[Url(as: 'service')]
    public string $selectedService = '';

    // Step 2
    public string $selectedDate = '';

    // Step 3
    public string $selectedSlot = '';

    // Step 4
    #[Validate('required|string|max:100')]
    public string $name  = '';

    #[Validate('required|email|max:150')]
    public string $email = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|string|max:500')]
    public string $notes = '';

    // ── Constants ─────────────────────────────────────────────────────────

    public const SLOTS = [
        '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
        '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
        '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
        '17:00',
    ];

    // Simple list used in dropdowns / validation
    public const SERVICES = [
        'General Consultation',
        'Strategy Session',
        'Quick Review',
    ];

    // Full data used in the service-picker UI
    public const SERVICE_DETAILS = [
        [
            'name'        => 'General Consultation',
            'duration'    => '60 min',
            'price'       => 'CHF 200',
            'description' => 'Expert advice tailored to your specific business needs and challenges.',
            'icon'        => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        ],
        [
            'name'        => 'Strategy Session',
            'duration'    => '90 min',
            'price'       => 'CHF 350',
            'description' => 'Deep-dive analysis and actionable long-term strategic planning.',
            'icon'        => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        ],
        [
            'name'        => 'Quick Review',
            'duration'    => '30 min',
            'price'       => 'CHF 120',
            'description' => 'A focused audit of a single process, document, or deliverable.',
            'icon'        => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        ],
    ];

    // ── Mount ─────────────────────────────────────────────────────────────

    public function mount(): void
    {
        // If a valid service was passed via URL (?service=...), skip to step 2
        if ($this->selectedService && in_array($this->selectedService, self::SERVICES, true)) {
            $this->step = 2;
        }

        // Pre-fill contact fields for logged-in customers
        if ($user = auth()->user()) {
            $this->name  = $user->name;
            $this->email = $user->email;
        }
    }

    // ── Computed ──────────────────────────────────────────────────────────

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
                'month'   => $day->format('M Y'),
                'weekend' => $day->dayOfWeek === 6,
            ];
        }
        return $days;
    }

    #[Computed]
    public function availableSlots(): array
    {
        if (! $this->selectedDate) {
            return [];
        }
        $booked = Booking::bookedSlotsForDate($this->selectedDate);
        return array_values(
            array_filter(self::SLOTS, fn ($s) => ! in_array($s, $booked, true))
        );
    }

    #[Computed]
    public function selectedServiceDetail(): ?array
    {
        foreach (self::SERVICE_DETAILS as $detail) {
            if ($detail['name'] === $this->selectedService) {
                return $detail;
            }
        }
        return null;
    }

    // ── Step actions ──────────────────────────────────────────────────────

    public function selectService(string $service): void
    {
        if (! in_array($service, self::SERVICES, true)) {
            return;
        }
        $this->selectedService = $service;
        $this->step = 2;
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

    public function goToStep(int $target): void
    {
        $this->resetErrorBag();

        // Validate the current step before advancing
        match (true) {
            $target > 1 && ! $this->selectedService => $this->stepError('selectedService', 'Please select a service.'),
            $target > 2 && ! $this->selectedDate    => $this->stepError('selectedDate', 'Please select a date.'),
            $target > 3 && ! $this->selectedSlot    => $this->stepError('selectedSlot', 'Please choose a time slot.'),
            default => null,
        };

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $this->step = $target;
    }

    private function stepError(string $field, string $message): void
    {
        $this->addError($field, $message);
    }

    // ── Submit ────────────────────────────────────────────────────────────

    public function submitBooking(): void
    {
        $rateLimitKey = 'booking:' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError('rate_limit', "Too many requests. Please try again in {$seconds} seconds.");
            return;
        }

        $this->validate();

        $extra = Validator::make(
            [
                'service'      => $this->selectedService,
                'booking_date' => $this->selectedDate,
                'time_slot'    => $this->selectedSlot,
            ],
            [
                'service'      => ['required', 'string', 'in:' . implode(',', self::SERVICES)],
                'booking_date' => ['required', 'date', 'after:today'],
                'time_slot'    => ['required', 'string', 'in:' . implode(',', self::SLOTS)],
            ]
        );

        if ($extra->fails()) {
            $this->addError('selectedDate', 'Invalid booking data. Please start again.');
            $this->step = 1;
            return;
        }

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
                    'service'      => $this->selectedService,
                    'notes'        => strip_tags($this->notes ?? ''),
                    'status'       => 'pending',
                ]);
            });
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            $this->step = 3;
            return;
        }

        RateLimiter::hit($rateLimitKey, 600);
        $this->step = 5;
    }

    public function render()
    {
        return view('livewire.booking-wizard');
    }
}
