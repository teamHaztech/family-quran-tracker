<x-app-layout>
    <x-slot name="title">Activity Log</x-slot>

    @php
        $icons = [
            'auth.login' => '🔓', 'auth.logout' => '🔒',
            'reading.created' => '✍️', 'reading.finished' => '⏱️', 'reading.edited' => '📝',
            'user.created' => '➕', 'user.updated' => '✏️', 'user.deleted' => '🗑️',
            'user.disabled' => '🚫', 'user.enabled' => '✅', 'user.password_reset' => '🔑',
            'password.changed' => '🔑', 'settings.updated' => '⚙️', 'report.exported' => '📤',
            'profile.avatar_updated' => '🖼️',
        ];
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h2 class="text-2xl font-extrabold">Activity Log</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">A record of everything happening in your family tracker.</p>
        </div>

        <form method="GET" class="card p-3 flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search descriptions…" class="form-input-field flex-1">
            <select name="action" class="form-input-field sm:w-52" onchange="this.form.submit()">
                <option value="">All actions</option>
                @foreach ($actions as $a)
                    <option value="{{ $a }}" @selected(request('action')===$a)>{{ $a }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Filter</button>
        </form>

        @if ($logs->isEmpty())
            <div class="card"><x-empty-state icon="📋" title="No activity yet" message="Actions will appear here as they happen." /></div>
        @else
            <div class="card divide-y divide-slate-50 dark:divide-slate-700/50">
                @foreach ($logs as $log)
                    <div class="p-4 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 grid place-items-center text-lg shrink-0">
                            {{ $icons[$log->action] ?? '•' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium">{{ $log->description ?? $log->action }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $log->user?->fullName() ?? 'System' }}
                                · {{ $log->created_at->diffForHumans() }}
                                @if ($log->ip_address) · {{ $log->ip_address }} @endif
                            </p>
                        </div>
                        <span class="pill-muted shrink-0">{{ $log->action }}</span>
                    </div>
                @endforeach
            </div>

            <div>{{ $logs->links() }}</div>
        @endif
    </div>
</x-app-layout>
