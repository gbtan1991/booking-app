<?php

namespace App\Livewire\Customer;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class BookingHistory extends Component
{
    use WithPagination;

    #[Computed]
    public function upcomingBookings()
    {
        return auth()->user()
            ->bookings()
            ->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('booking_date')
            ->orderBy('time_slot')
            ->get();
    }

    #[Computed]
    public function pastBookings()
    {
        return auth()->user()
            ->bookings()
            ->where('booking_date', '<', now()->toDateString())
            ->orWhere('status', 'cancelled')
            ->orderByDesc('booking_date')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.customer.booking-history');
    }
}
