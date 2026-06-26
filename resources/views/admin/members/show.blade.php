<x-app-layout>
    <x-slot name="title">{{ $member->fullName() }}</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <a href="{{ route('admin.members.index') }}" class="text-sm text-slate-500 hover:text-brand-600 inline-flex items-center gap-1">← Back to members</a>

        {{-- Profile header --}}
        <div class="card p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            <img src="{{ $member->photoUrl() }}" class="w-20 h-20 rounded-3xl object-cover" alt="">
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-extrabold">{{ $member->fullName() }}</h2>
                    @if ($member->isActive()) <span class="pill-success">Active</span> @else <span class="pill-danger">Disabled</span> @endif
                </div>
                <p class="text-sm text-slate-500">{{ $member->email }} @if($member->phone) · {{ $member->phone }} @endif</p>
                <p class="text-xs text-slate-400 mt-0.5">Joined {{ optional($member->date_joined)->format('M d, Y') }}</p>
                @if ($member->profile?->current_surah || $member->profile?->current_ayah)
                    <p class="mt-2">
                        <span class="pill-success">
                            📖 Currently on
                            @if ($member->profile->current_surah) Surah {{ $member->profile->current_surah }} @endif
                            @if ($member->profile->current_ayah) · Ayah {{ $member->profile->current_ayah }} @endif
                        </span>
                    </p>
                @endif
            </div>
            <a href="{{ route('admin.members.edit', $member) }}" class="btn-secondary self-start">Edit</a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Pages" :value="$memberStats['total_pages']" icon="📖" accent="brand" />
            <x-stat-card label="Reading Time" :value="$memberStats['total_minutes']" suffix="minutes" icon="⏱️" accent="blue" />
            <x-stat-card label="Current Streak" :value="$memberStats['current_streak']" suffix="days" icon="🔥" accent="gold" />
            <x-stat-card label="Sessions" :value="$memberStats['total_sessions']" icon="📚" accent="violet" />
        </div>

        {{-- Recent sessions --}}
        <div class="card p-6">
            <h3 class="font-bold mb-4">Recent Reading Sessions</h3>
            @if ($recent->isEmpty())
                <x-empty-state icon="📭" title="No sessions yet" message="This member hasn't logged any reading." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left text-slate-400 border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="py-2 font-medium">Date</th>
                                <th class="py-2 font-medium">Surah</th>
                                <th class="py-2 font-medium">Pages</th>
                                <th class="py-2 font-medium">Minutes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                            @foreach ($recent as $s)
                                <tr>
                                    <td class="py-2.5">{{ $s->date->format('M d, Y') }}</td>
                                    <td class="py-2.5">{{ $s->surah ?? '—' }}</td>
                                    <td class="py-2.5 font-semibold text-brand-600">{{ $s->pages_read }}</td>
                                    <td class="py-2.5">{{ $s->minutes_read }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
