<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class BookingDashboard extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = 'all';

    #[Url]
    public string $dateFilter = '';

    #[Url]
    public string $search = '';

    public ?int $confirmActionId = null;
    public string $confirmAction = '';

    #[Computed]
    public function stats(): array
    {
        return [
            'pending'   => Booking::pending()->count(),
            'approved'  => Booking::approved()->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'today'     => Booking::forDate(now()->toDateString())->whereIn('status', ['pending', 'approved'])->count(),
        ];
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function confirmAction(int $id, string $action): void
    {
        $this->confirmActionId = $id;
        $this->confirmAction = $action;
    }

    public function cancelConfirm(): void
    {
        $this->confirmActionId = null;
        $this->confirmAction = '';
    }

    public function approve(int $id): void
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'approved']);
        unset($this->stats);
        $this->cancelConfirm();
        $this->dispatch('notify', message: 'Booking approved.', type: 'success');
    }

    public function cancel(int $id): void
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'cancelled']);
        unset($this->stats);
        $this->cancelConfirm();
        $this->dispatch('notify', message: 'Booking cancelled.', type: 'warning');
    }

    public function delete(int $id): void
    {
        Booking::findOrFail($id)->delete();
        unset($this->stats);
        $this->cancelConfirm();
        $this->dispatch('notify', message: 'Booking deleted.', type: 'error');
    }

    public function render()
    {
        $query = Booking::query()
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFilter, fn ($q) => $q->where('booking_date', $this->dateFilter))
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('booking_date')
            ->orderBy('time_slot');

        return view('livewire.admin.booking-dashboard', [
            'bookings' => $query->paginate(15),
        ])->layout('components.layouts.admin');
    }
}

