<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceManager extends Component
{
    use WithPagination;

    #[Url] public string $search = '';

    // ── Panel state ───────────────────────────────────────────────────────

    public bool $showPanel = false;
    public ?int $editingId = null;

    // ── Form fields ───────────────────────────────────────────────────────

    public string $form_name        = '';
    public string $form_description = '';
    public string $form_price       = '';

    public function updatedSearch(): void { $this->resetPage(); }

    // ── Panel helpers ─────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showPanel = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $service = Service::findOrFail($id);

        $this->editingId        = $id;
        $this->form_name        = $service->name;
        $this->form_description = $service->description ?? '';
        $this->form_price       = (string) $service->price;

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
        $this->form_name        = '';
        $this->form_description = '';
        $this->form_price       = '';
        $this->editingId        = null;
    }

    // ── Save ─────────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'form_name'        => ['required', 'string', 'max:100'],
            'form_description' => ['nullable', 'string', 'max:1000'],
            'form_price'       => ['required', 'numeric', 'min:0', 'max:99999.99'],
        ]);

        $data = [
            'name'        => strip_tags($this->form_name),
            'description' => strip_tags($this->form_description),
            'price'       => (float) $this->form_price,
        ];

        if ($this->editingId) {
            Service::findOrFail($this->editingId)->update($data);
            $message = 'Service updated.';
        } else {
            Service::create($data);
            $message = 'Service created.';
        }

        $this->closePanel();
        $this->dispatch('notify', message: $message, type: 'success');
    }

    // ── Delete ────────────────────────────────────────────────────────────

    public function delete(int $id): void
    {
        Service::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Service deleted.', type: 'error');
    }

    // ── Render ────────────────────────────────────────────────────────────

    public function render()
    {
        $services = Service::query()
            ->when($this->search, fn ($q) => $q->where(fn ($i) =>
                $i->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            ))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.service-manager', compact('services'))
            ->layout('components.layouts.admin');
    }
}
