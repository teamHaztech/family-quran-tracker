<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold">Welcome back 👋</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Sign in to continue your reading journey.</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-4 rounded-xl bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 text-sm px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-sm px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   autocomplete="username" class="form-input-field" placeholder="you@example.com">
        </div>

        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="form-input-field" placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full !py-3">Sign in</button>
    </form>

    <p class="text-center text-xs text-slate-400 mt-6">
        Accounts are created by your Family Leader. <br>Contact them if you need access.
    </p>
</x-guest-layout>
