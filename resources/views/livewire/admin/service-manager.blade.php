<div>
    {{-- ══════════════════════════ HEADER ══════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Services</h1>
            <p class="text-sm text-stone-500 mt-0.5">Manage the services customers can book</p>
        </div>
        <button
            type="button"
            wire:click="openCreate"
            class="inline-flex items-center gap-2 bg-stone-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-stone-700 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add service
        </button>
    </div>

    {{-- ══════════════════════════ SEARCH ══════════════════════════ --}}
    <div class="mb-4">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="Search by name or description…"
            class="border border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 w-full sm:w-80 placeholder-stone-300 bg-white"
        >
    </div>

    {{-- ══════════════════════════ TABLE ══════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold text-stone-400 uppercase tracking-wider bg-stone-50 border-b border-stone-100">
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3 hidden md:table-cell">Description</th>
                    <th class="px-5 py-3">Price</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @forelse ($services as $service)
                <tr class="hover:bg-stone-50 transition-colors" wire:key="svc-{{ $service->id }}">
                    <td class="px-5 py-4 font-semibold text-stone-900">{{ $service->name }}</td>
                    <td class="px-5 py-4 hidden md:table-cell text-stone-500 max-w-sm">
                        <span class="line-clamp-2">{{ $service->description ?: '—' }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="font-semibold text-stone-900">{{ $service->formatted_price }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-1.5">
                            <button
                                type="button"
                                wire:click="openEdit({{ $service->id }})"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-stone-100 text-stone-700 hover:bg-stone-200 transition-colors"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                            <button
                                type="button"
                                wire:click="delete({{ $service->id }})"
                                wire:confirm="Delete '{{ $service->name }}'? This cannot be undone."
                                class="p-1.5 rounded-lg text-stone-300 hover:text-red-500 hover:bg-red-50 transition-colors"
                                title="Delete"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-16 text-center">
                        <svg class="w-10 h-10 mx-auto mb-3 text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm font-semibold text-stone-400">No services yet</p>
                        <p class="text-xs text-stone-300 mt-1">Click "Add service" to create your first one.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if ($services->hasPages())
        <div class="px-5 py-4 border-t border-stone-100">
            {{ $services->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════════════════ SLIDE-OVER PANEL ══════════════════════════ --}}
    @if ($showPanel)
    <div class="fixed inset-0 z-50 flex">
        <div class="absolute inset-0 bg-stone-900/40 backdrop-blur-sm" wire:click="closePanel"></div>

        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col">

            <div class="flex items-center justify-between px-6 py-5 border-b border-stone-100">
                <h2 class="text-base font-bold text-stone-900">
                    {{ $editingId ? 'Edit service' : 'Add service' }}
                </h2>
                <button type="button" wire:click="closePanel"
                        class="p-2 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-5">

                <div>
                    <label class="block text-xs font-bold text-stone-600 mb-1.5 uppercase tracking-wider">Service Name</label>
                    <input
                        type="text"
                        wire:model="form_name"
                        placeholder="e.g. General Consultation"
                        class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300"
                    >
                    @error('form_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-600 mb-1.5 uppercase tracking-wider">
                        Description <span class="font-normal text-stone-400 normal-case tracking-normal">(optional)</span>
                    </label>
                    <textarea
                        wire:model="form_description"
                        rows="4"
                        placeholder="Briefly describe what this service involves…"
                        class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300 resize-none"
                    ></textarea>
                    @error('form_description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-600 mb-1.5 uppercase tracking-wider">Price (CHF)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-stone-400">CHF</span>
                        <input
                            type="number"
                            wire:model="form_price"
                            placeholder="0.00"
                            min="0"
                            step="0.01"
                            class="w-full border border-stone-200 rounded-xl pl-14 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 transition placeholder-stone-300"
                        >
                    </div>
                    @error('form_price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

            </div>

            <div class="px-6 py-5 border-t border-stone-100 flex justify-end gap-3">
                <button type="button" wire:click="closePanel"
                        class="px-5 py-2.5 rounded-xl border border-stone-200 text-sm font-semibold text-stone-600 hover:bg-stone-50 transition-colors">
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    class="inline-flex items-center gap-2 bg-stone-900 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-stone-700 transition-colors"
                >
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Save changes' : 'Create service' }}
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Saving…
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
