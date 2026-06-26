<x-app-layout>
    <x-slot name="title">Members</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold">Family Members</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Manage who can track their Quran reading.</p>
            </div>
            <a href="{{ route('admin.members.create') }}" class="btn-primary self-start">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Member
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" class="card p-3 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or phone…"
                       class="form-input-field !pl-10">
            </div>
            <select name="status" class="form-input-field sm:w-44" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status')==='active')>Active</option>
                <option value="disabled" @selected(request('status')==='disabled')>Disabled</option>
            </select>
            <button class="btn-secondary">Search</button>
        </form>

        {{-- Reset-password flash (shows generated password once) --}}
        @if (session('success') && str_contains(session('success'), 'New password'))
            <div class="card border-l-4 !border-l-gold-500 px-4 py-3 text-sm font-mono">{{ session('success') }}</div>
        @endif

        {{-- Member grid --}}
        @if ($members->isEmpty())
            <div class="card">
                <x-empty-state icon="👨‍👩‍👧‍👦" title="No members yet"
                               message="Add your first family member to start tracking their Quran reading.">
                    <x-slot name="action">
                        <a href="{{ route('admin.members.create') }}" class="btn-primary">Add Member</a>
                    </x-slot>
                </x-empty-state>
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($members as $member)
                    <div class="card card-hover p-5">
                        <div class="flex items-start gap-3">
                            <img src="{{ $member->photoUrl() }}" class="w-14 h-14 rounded-2xl object-cover" alt="">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold truncate">{{ $member->fullName() }}</h3>
                                    @if ($member->isActive())
                                        <span class="pill-success">Active</span>
                                    @else
                                        <span class="pill-danger">Disabled</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $member->email }}</p>
                                @if ($member->phone)
                                    <p class="text-xs text-slate-400">{{ $member->phone }}</p>
                                @endif
                                @if ($member->profile?->current_surah || $member->profile?->current_ayah)
                                    <p class="text-xs text-brand-600 dark:text-brand-400 mt-0.5 truncate">
                                        📖 {{ $member->profile->current_surah }}@if($member->profile->current_ayah) · Ayah {{ $member->profile->current_ayah }}@endif
                                    </p>
                                @endif
                            </div>

                            {{-- Actions dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open=!open" class="w-8 h-8 rounded-lg grid place-items-center hover:bg-slate-100 dark:hover:bg-slate-700">
                                    <svg class="w-5 h-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8a2 2 0 100-4 2 2 0 000 4zm0 2a2 2 0 100 4 2 2 0 000-4zm0 6a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                </button>
                                <div x-show="open" x-cloak @click.outside="open=false" x-transition
                                     class="absolute right-0 mt-1 w-44 card p-1.5 z-20 text-sm">
                                    <a href="{{ route('admin.members.show', $member) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">View progress</a>
                                    <a href="{{ route('admin.members.edit', $member) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">Edit</a>

                                    @if ($member->isActive())
                                        <form method="POST" action="{{ route('admin.members.disable', $member) }}">
                                            @csrf @method('PATCH')
                                            <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">Disable</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.members.enable', $member) }}">
                                            @csrf @method('PATCH')
                                            <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">Enable</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.members.reset-password', $member) }}"
                                          onsubmit="return confirm('Generate a new password for {{ $member->fullName() }}?')">
                                        @csrf @method('PATCH')
                                        <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">Reset password</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.members.destroy', $member) }}"
                                          onsubmit="return confirm('Delete {{ $member->fullName() }}? This can be restored later.')">
                                        @csrf @method('DELETE')
                                        <button class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-900/40 py-2">
                                <p class="text-lg font-extrabold text-brand-600">{{ $member->readingSessions()->sum('pages_read') }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">Pages</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-900/40 py-2">
                                <p class="text-lg font-extrabold text-brand-600">{{ $member->readingSessions()->count() }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">Sessions</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-900/40 py-2">
                                <p class="text-lg font-extrabold text-brand-600">{{ $member->badges()->count() }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">Badges</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>{{ $members->links() }}</div>
        @endif
    </div>
</x-app-layout>
