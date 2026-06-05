<?php

namespace App\Livewire\Admin;

use App\Livewire\BookingWizard;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BookingDashboard extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────
    #[Url] public string $statusFilter = 'all';
    #[Url] public string $dateFilter   = '';
    #[Url] public string $search       = '';

    // ── Slide-over panel state ────────────────────────────────────────────
    public bool    $showPanel  = false;
    public ?int    $editingId  = null; // null = creating new

    // ── Form fields ───────────────────────────────────────────────────────
    public string $form_name         = '';
    public string $form_email        = '';
    public string $form_phone        = '';
    public string $form_booking_date = '';
    public string $form_time_slot    = '';
    public string $form_service      = 'General Consultation';
    public string $form_status       = 'pending';
    public string $form_notes        = '';

    // ── Stats ─────────────────────────────────────────────────────────────
    #[Computed]
    public function stats(): array
    {
        return [
            'pending'   => Booking::pending()->count(),
            'approved'  => Booking::approved()->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'today'     => Booking::forDate(now()->toDateString())
                               ->whereIn('status', ['pending', 'approved'])->count(),
        ];
    }

    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedSearch(): void       { $this->resetPage(); }

    // ── Panel helpers ─────────────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId  = null;
        $this->showPanel  = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $booking = Booking::findOrFail($id);

        $this->editingId          = $id;
        $this->form_name          = $booking->name;
        $this->form_email         = $booking->email;
        $this->form_phone         = $booking->phone;
        $this->form_booking_date  = $booking->booking_date->format('Y-m-d');
        $this->form_time_slot     = $booking->time_slot;
        $this->form_service       = $booking->service;
        $this->form_status        = $booking->status;
        $this->form_notes         = $booking->notes ?? '';

        $this->showPanel = true;
        $this->resetErrorBag();
    }

    public function closePanel(): void
    {
        $this->showPanel = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->form_name         = '';
        $this->form_email        = '';
        $this->form_phone        = '';
        $this->form_booking_date = '';
        $this->form_time_slot    = '';
        $this->form_service      = 'General Consultation';
        $this->form_status       = 'pending';
        $this->form_notes        = '';
        $this->editingId         = null;
    }

    // ── Save (create or update) ───────────────────────────────────────────
    public function save(): void
    {
        $rules = [
            'form_name'         => ['required', 'string', 'max:100'],
            'form_email'        => ['required', 'email', 'max:150'],
            'form_phone'        => ['required', 'string', 'max:20'],
            'form_booking_date' => ['required', 'date'],
            'form_time_slot'    => ['required', 'string', 'in:' . implode(',', BookingWizard::SLOTS)],
            'form_service'      => ['required', 'string', 'in:' . implode(',', BookingWizard::SERVICES)],
            'form_status'       => ['required', 'in:pending,approved,cancelled'],
            'form_notes'        => ['nullable', 'string', 'max:500'],
        ];

        $validated = $this->validate($rules);

        // Check for slot conflicts (exclude self when editing)
        $conflict = Booking::where('booking_date', $this->form_booking_date)
            ->where('time_slot', $this->form_time_slot)
            ->whereIn('status', ['pending', 'approved'])
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($conflict) {
            $this->addError('form_time_slot', 'This slot is already booked on that date.');
            return;
        }

        $data = [
            'name'         => strip_tags($validated['form_name']),
            'email'        => $validated['form_email'],
            'phone'        => strip_tags($validated['form_phone']),
            'booking_date' => $validated['form_booking_date'],
            'time_slot'    => $validated['form_time_slot'],
            'service'      => $validated['form_service'],
            'status'       => $validated['form_status'],
            'notes'        => strip_tags($validated['form_notes'] ?? ''),
        ];

        if ($this->editingId) {
            Booking::findOrFail($this->editingId)->update($data);
            $message = 'Booking updated.';
        } else {
            Booking::create($data);
            $message = 'Booking created.';
        }

        unset($this->stats);
        $this->closePanel();
        $this->dispatch('notify', message: $message, type: 'success');
    }

    // ── Status actions ────────────────────────────────────────────────────
    public function approve(int $id): void
    {
        Booking::findOrFail($id)->update(['status' => 'approved']);
        unset($this->stats);
        $this->dispatch('notify', message: 'Booking approved.', type: 'success');
    }

    public function cancel(int $id): void
    {
        Booking::findOrFail($id)->update(['status' => 'cancelled']);
        unset($this->stats);
        $this->dispatch('notify', message: 'Booking cancelled.', type: 'warning');
    }

    public function delete(int $id): void
    {
        Booking::findOrFail($id)->delete();
        unset($this->stats);
        $this->dispatch('notify', message: 'Booking deleted.', type: 'error');
    }

    // ── Render ────────────────────────────────────────────────────────────
    public function render()
    {
        $bookings = Booking::query()
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFilter, fn ($q) => $q->where('booking_date', $this->dateFilter))
            ->when($this->search, fn ($q) => $q->where(fn ($i) =>
                $i->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
            ))
            ->orderBy('booking_date')
            ->orderBy('time_slot')
            ->paginate(15);

        return view('livewire.admin.booking-dashboard', compact('bookings'))
            ->layout('components.layouts.admin');
    }
}

