<x-app-layout>
    <x-slot name="title">My Profile</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Avatar + identity --}}
        <div class="card p-6 flex flex-col sm:flex-row items-center gap-5"
             x-data="{ preview: '{{ $user->photoUrl() }}' }">
            <img :src="preview" class="w-24 h-24 rounded-3xl object-cover shadow-soft" alt="">
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-xl font-extrabold">{{ $user->fullName() }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $user->isAdmin() ? 'Family Leader' : 'Member' }} · joined {{ optional($user->date_joined)->format('M Y') }}</p>

                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="mt-3 flex items-center gap-2 justify-center sm:justify-start">
                    @csrf @method('PATCH')
                    <input type="file" name="photo" accept="image/*" required
                           @change="const f=$event.target.files[0]; if(f) preview=URL.createObjectURL(f)"
                           class="block text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-700 file:font-semibold">
                    <button class="btn-secondary !py-1.5 !px-3 text-xs">Upload</button>
                </form>
                @if (session('status') === 'avatar-updated')
                    <p class="text-xs text-brand-600 mt-1" x-data x-init="setTimeout(() => $el.remove(), 2500)">Photo updated ✓</p>
                @endif
            </div>
        </div>

        {{-- Profile information --}}
        <div class="card p-6">
            <h3 class="font-bold mb-1">Profile Information</h3>
            <p class="text-sm text-slate-400 mb-5">Update your name and contact details.</p>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf @method('PATCH')
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="form-input-field">
                        @error('first_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-input-field">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input-field">
                        @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input-field">
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="btn-primary">Save</button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-brand-600" x-data x-init="setTimeout(() => $el.remove(), 2500)">Saved ✓</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Change password --}}
        <div class="card p-6">
            <h3 class="font-bold mb-1">Change Password</h3>
            <p class="text-sm text-slate-400 mb-5">Use a long, random password to stay secure.</p>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" autocomplete="current-password" class="form-input-field">
                    @error('current_password', 'updatePassword')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" autocomplete="new-password" class="form-input-field">
                        @error('password', 'updatePassword')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="form-input-field">
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="btn-primary">Update Password</button>
                    @if (session('status') === 'password-updated')
                        <p class="text-sm text-brand-600" x-data x-init="setTimeout(() => $el.remove(), 2500)">Updated ✓</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Danger zone (members only) --}}
        @unless ($user->isAdmin())
            <div class="card p-6 border border-red-100 dark:border-red-900/40" x-data="{ open: false }">
                <h3 class="font-bold mb-1 text-red-600">Delete Account</h3>
                <p class="text-sm text-slate-400 mb-4">Once deleted, your reading history is removed. This cannot be undone.</p>
                <button @click="open = true" class="btn-danger">Delete Account</button>

                <div x-show="open" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-black/40 backdrop-blur-sm p-4" @keydown.escape.window="open=false">
                    <div class="card p-6 max-w-md w-full">
                        <h3 class="font-bold text-lg">Are you sure?</h3>
                        <p class="text-sm text-slate-400 mt-1 mb-4">Enter your password to confirm account deletion.</p>
                        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                            @csrf @method('DELETE')
                            <input type="password" name="password" placeholder="Password" class="form-input-field">
                            @error('password', 'userDeletion')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="open=false" class="btn-secondary">Cancel</button>
                                <button class="btn-danger">Delete Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endunless
    </div>
</x-app-layout>
