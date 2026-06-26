<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold">Choose a new password</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Make it strong and memorable.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                   autocomplete="username" class="form-input-field">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="form-label">New password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="form-input-field">
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   autocomplete="new-password" class="form-input-field">
        </div>

        <button type="submit" class="btn-primary w-full !py-3">Reset password</button>
    </form>
</x-guest-layout>
