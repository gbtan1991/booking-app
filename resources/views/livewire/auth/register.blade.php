<div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-stone-900">Create account</h1>
        <p class="text-sm text-stone-500 mt-1">Book appointments and track your history.</p>
    </div>

    <form wire:submit="register" class="space-y-5">

        <div>
            <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Full Name</label>
            <input
                type="text"
                wire:model="name"
                autocomplete="name"
                autofocus
                class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition placeholder-stone-300"
                placeholder="Marie Dupont"
            >
            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Email</label>
            <input
                type="email"
                wire:model="email"
                autocomplete="email"
                class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition placeholder-stone-300"
                placeholder="marie@example.com"
            >
            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Password</label>
            <input
                type="password"
                wire:model="password"
                autocomplete="new-password"
                class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition placeholder-stone-300"
                placeholder="Min. 8 characters"
            >
            @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-stone-600 mb-1.5 uppercase tracking-wide">Confirm Password</label>
            <input
                type="password"
                wire:model="password_confirmation"
                autocomplete="new-password"
                class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-transparent transition placeholder-stone-300"
                placeholder="Repeat password"
            >
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-60 cursor-wait"
            class="w-full bg-stone-900 text-white py-3 rounded-full text-sm font-semibold hover:bg-stone-700 transition-all"
        >
            <span wire:loading.remove wire:target="register">Create account</span>
            <span wire:loading wire:target="register" class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Creating…
            </span>
        </button>

    </form>

    <p class="mt-6 text-center text-sm text-stone-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-stone-900 hover:underline">Sign in</a>
    </p>

</div>
