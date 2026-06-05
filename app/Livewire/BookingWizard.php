<?php

namespace App\Livewire;

use App\Models\Book;
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
    // Steps: 1=Service  2=Date  3=Time  4=Details  5=Confirmed
    public int $step = 1;

    // Step 1
    #[Url(as: 'service')]
    public string $selectedService = '';

    // Step 2
    public string $selectedDate = '';

    // Step 3
    public string $selectedTime = '';

    // Step 4
    #[Validate('required|string|max:100')]
    public string $customer_name = '';

    #[Validate('required|email|max:150')]
    public string $customer_email = '';

    #[Validate('required|string|max:30')]
    public string $customer_telephone = '';

    #[Validate('nullable|string|max:500')]
    public string $customer_notes = '';

    // ── Constants ─────────────────────────────────────────────────────────

    public const TIME_SLOTS = [
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
        if ($this->selectedService && in_array($this->selectedService, self::SERVICES, true)) {
            $this->step = 2;
        }
    }

    // ── Computed properties ───────────────────────────────────────────────

    #[Computed]
    public function calendarDays(): array
    {
        $today = now()->startOfDay();
        $days  = [];
        for ($i = 1; $i <= 30; $i++) {
            $day = $today->copy()->addDays($i);
            if ($day->dayOfWeek === 0) {
                continue;
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
    public function availableTimes(): array
    {
        if (! $this->selectedDate) {
            return [];
        }
        $booked = Book::bookedTimesForDate($this->selectedDate);
        return array_values(
            array_filter(self::TIME_SLOTS, fn ($t) => ! in_array($t, $booked, true))
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

    // ── Step navigation ───────────────────────────────────────────────────

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
        $this->selectedTime = '';
        unset($this->availableTimes);
    }

    public function selectTime(string $time): void
    {
        if (in_array($time, self::TIME_SLOTS, true)) {
            $this->selectedTime = $time;
        }
    }

    public function goToStep(int $target): void
    {
        $this->resetErrorBag();

        match (true) {
            $target > 1 && ! $this->selectedService => $this->addError('selectedService', 'Please select a service.'),
            $target > 2 && ! $this->selectedDate    => $this->addError('selectedDate', 'Please select a date.'),
            $target > 3 && ! $this->selectedTime    => $this->addError('selectedTime', 'Please choose a time slot.'),
            default => null,
        };

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $this->step = $target;
    }

    // ── Submit ────────────────────────────────────────────────────────────

    public function submitBooking(): void
    {
        // Rate limit: 5 attempts per IP per 10 minutes
        $rateLimitKey = 'book:' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError('rate_limit', "Too many requests. Please try again in {$seconds} seconds.");
            return;
        }

        // Validate contact fields
        $this->validate();

        // Hard-validate service, date, time server-side (prevents tampered state)
        $extra = Validator::make(
            [
                'service' => $this->selectedService,
                'date'    => $this->selectedDate,
                'time'    => $this->selectedTime,
            ],
            [
                'service' => ['required', 'string', 'in:' . implode(',', self::SERVICES)],
                'date'    => ['required', 'date', 'after:today'],
                'time'    => ['required', 'string', 'in:' . implode(',', self::TIME_SLOTS)],
            ]
        );

        if ($extra->fails()) {
            $this->addError('selectedDate', 'Invalid booking data detected. Please start over.');
            $this->step = 1;
            return;
        }

        // Atomic slot reservation with pessimistic locking
        try {
            DB::transaction(function () {
                $conflict = Book::where('date', $this->selectedDate)
                    ->where('time', $this->selectedTime)
                    ->where('status', 'current')
                    ->lockForUpdate()
                    ->exists();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'selectedTime' => 'This time slot was just taken. Please choose another.',
                    ]);
                }

                Book::create([
                    'service'              => $this->selectedService,
                    'date'                 => $this->selectedDate,
                    'time'                 => $this->selectedTime,
                    'customer_name'        => strip_tags($this->customer_name),
                    'customer_email'       => $this->customer_email,
                    'customer_telephone'   => strip_tags($this->customer_telephone),
                    'customer_notes'       => strip_tags($this->customer_notes ?? ''),
                    'status'               => 'current',
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
