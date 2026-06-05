<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    #[Url] public string $search = '';

    // ── Panel state ───────────────────────────────────────────────────────

    public bool  $showPanel = false;
    public ?int  $editingId = null;

    // ── Form fields ───────────────────────────────────────────────────────

    public string $form_name        = '';
    public string $form_position    = '';
    public string $form_system_role = 'Administrator';
    public string $form_email       = '';
    public string $form_password    = '';

    // Common system roles for the dropdown
    public array $systemRoles = [
        'Administrator',
        'Super Admin',
        'Booking Manager',
        'Support Staff',
    ];

    public function updatedSearch(): void { $this->resetPage(); }

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
        $user = User::findOrFail($id);

        $this->editingId        = $id;
        $this->form_name        = $user->name;
        $this->form_position    = $user->position ?? '';
        $this->form_system_role = $user->system_role;
        $this->form_email       = $user->email;
        $this->form_password    = '';

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
        $this->form_position    = '';
        $this->form_system_role = 'Administrator';
        $this->form_email       = '';
        $this->form_password    = '';
        $this->editingId        = null;
    }

    // ── Save ─────────────────────────────────────────────────────────────

    public function save(): void
    {
        $isNew = $this->editingId === null;

        $rules = [
            'form_name'        => ['required', 'string', 'max:100'],
            'form_position'    => ['nullable', 'string', 'max:100'],
            'form_system_role' => ['required', 'string', 'max:100'],
            'form_email'       => [
                'required', 'email', 'max:150',
                $isNew
                    ? 'unique:users,email'
                    : "unique:users,email,{$this->editingId}",
            ],
            'form_password' => $isNew
                ? ['required', 'string', 'min:8']
                : ['nullable', 'string', 'min:8'],
        ];

        $this->validate($rules);

        $data = [
            'name'        => strip_tags($this->form_name),
            'position'    => strip_tags($this->form_position),
            'system_role' => strip_tags($this->form_system_role),
            'email'       => $this->form_email,
        ];

        if ($this->form_password !== '') {
            $data['password'] = Hash::make($this->form_password);
        }

        if ($isNew) {
            User::create($data);
            $message = 'Admin user created.';
        } else {
            User::findOrFail($this->editingId)->update($data);
            $message = 'Admin user updated.';
        }

        $this->closePanel();
        $this->dispatch('notify', message: $message, type: 'success');
    }

    // ── Delete ────────────────────────────────────────────────────────────

    public function delete(int $id): void
    {
        // Prevent self-deletion
        if ($id === auth()->id()) {
            $this->dispatch('notify', message: 'You cannot delete your own account.', type: 'error');
            return;
        }

        User::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Admin user deleted.', type: 'error');
    }

    // ── Render ────────────────────────────────────────────────────────────

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn ($q) => $q->where(fn ($i) =>
                $i->where('name',        'like', "%{$this->search}%")
                  ->orWhere('email',       'like', "%{$this->search}%")
                  ->orWhere('position',    'like', "%{$this->search}%")
                  ->orWhere('system_role', 'like', "%{$this->search}%")
            ))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.user-manager', compact('users'))
            ->layout('components.layouts.admin');
    }
}
