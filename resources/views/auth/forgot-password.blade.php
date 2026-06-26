<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold">Reset password</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Enter your email and we'll send you a link to choose a new password.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 text-sm px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-input-field" placeholder="you@example.com">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary w-full !py-3">Email reset link</button>
    </form>

    <a href="{{ route('login') }}" class="block text-center text-sm font-medium text-brand-600 hover:text-brand-700 mt-6">
        ← Back to sign in
    </a>
</x-guest-layout>
