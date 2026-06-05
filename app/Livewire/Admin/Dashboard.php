<?php

namespace App\Livewire\Admin;

use App\Models\Book;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    #[Url] public string $search     = '';
    #[Url] public string $dateFilter = '';
    #[Url] public string $tab        = 'current'; // current | completed | cancelled

    public function updatedSearch(): void { $this->resetPage(); }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    // ── Stats ─────────────────────────────────────────────────────────────

    #[Computed]
    public function counts(): array
    {
        return [
            'current'   => Book::current()->count(),
            'completed' => Book::completed()->count(),
            'cancelled' => Book::cancelled()->count(),
            'today'     => Book::forDate(now()->toDateString())->where('status', 'current')->count(),
        ];
    }

    // ── Actions ───────────────────────────────────────────────────────────

    public function markCompleted(int $id): void
    {
        Book::findOrFail($id)->update(['status' => 'completed']);
        unset($this->counts);
        $this->dispatch('notify', message: 'Marked as completed.', type: 'success');
    }

    public function markCancelled(int $id): void
    {
        Book::findOrFail($id)->update(['status' => 'cancelled']);
        unset($this->counts);
        $this->dispatch('notify', message: 'Booking cancelled.', type: 'warning');
    }

    public function markCurrent(int $id): void
    {
        Book::findOrFail($id)->update(['status' => 'current']);
        unset($this->counts);
        $this->dispatch('notify', message: 'Restored to current.', type: 'success');
    }

    public function delete(int $id): void
    {
        Book::findOrFail($id)->delete();
        unset($this->counts);
        $this->dispatch('notify', message: 'Booking deleted.', type: 'error');
    }

    // ── Render ────────────────────────────────────────────────────────────

    public function render()
    {
        $bookings = Book::query()
            ->where('status', $this->tab)
            ->when($this->dateFilter, fn ($q) => $q->where('date', $this->dateFilter))
            ->when($this->search, fn ($q) => $q->where(fn ($i) =>
                $i->where('customer_name',  'like', "%{$this->search}%")
                  ->orWhere('customer_email', 'like', "%{$this->search}%")
                  ->orWhere('service',        'like', "%{$this->search}%")
            ))
            ->orderBy('date')
            ->orderBy('time')
            ->paginate(12);

        return view('livewire.admin.dashboard', compact('bookings'))
            ->layout('components.layouts.admin');
    }
}
