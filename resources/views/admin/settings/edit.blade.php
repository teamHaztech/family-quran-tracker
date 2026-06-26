<x-app-layout>
    <x-slot name="title">Settings</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h2 class="text-2xl font-extrabold">Family Settings</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Configure your family's tracker.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-sm px-4 py-3">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Family identity --}}
            <div class="card p-6 space-y-5">
                <h3 class="font-bold">Family Identity</h3>
                <div class="flex items-center gap-4" x-data="{ preview: '{{ $settings->family_logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings->family_logo) : '' }}' }">
                    <div class="w-16 h-16 rounded-2xl bg-brand-50 dark:bg-slate-700 grid place-items-center overflow-hidden">
                        <template x-if="preview"><img :src="preview" class="w-full h-full object-cover"></template>
                        <template x-if="!preview"><span class="text-2xl">☪</span></template>
                    </div>
                    <div>
                        <label class="form-label">Family Logo</label>
                        <input type="file" name="family_logo" accept="image/*"
                               @change="const f=$event.target.files[0]; if(f) preview=URL.createObjectURL(f)"
                               class="block text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-brand-50 file:text-brand-700 file:font-semibold">
                    </div>
                </div>
                <div>
                    <label class="form-label">Family Name *</label>
                    <input type="text" name="family_name" value="{{ old('family_name', $settings->family_name) }}" required class="form-input-field">
                </div>
            </div>

            {{-- Goals --}}
            <div class="card p-6 space-y-5">
                <h3 class="font-bold">Reading Goals</h3>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Daily Goal (pages)</label>
                        <input type="number" name="daily_goal_pages" min="1" max="604" value="{{ old('daily_goal_pages', $settings->daily_goal_pages) }}" required class="form-input-field">
                    </div>
                    <div>
                        <label class="form-label">Monthly Goal (pages)</label>
                        <input type="number" name="monthly_goal_pages" min="1" value="{{ old('monthly_goal_pages', $settings->monthly_goal_pages) }}" required class="form-input-field">
                    </div>
                </div>
            </div>

            {{-- Features --}}
            <div class="card p-6 space-y-4">
                <h3 class="font-bold">Features</h3>
                <label class="flex items-center justify-between cursor-pointer">
                    <span>
                        <span class="font-medium text-sm block">Enable Leaderboard</span>
                        <span class="text-xs text-slate-400">Show family ranking to members</span>
                    </span>
                    <input type="checkbox" name="enable_leaderboard" value="1" @checked($settings->enable_leaderboard)
                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-5 h-5">
                </label>
                <label class="flex items-center justify-between cursor-pointer">
                    <span>
                        <span class="font-medium text-sm block">Enable Badges</span>
                        <span class="text-xs text-slate-400">Award achievement badges</span>
                    </span>
                    <input type="checkbox" name="enable_badges" value="1" @checked($settings->enable_badges)
                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-5 h-5">
                </label>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-400">App version {{ config('quran.version') }}</span>
                <button type="submit" class="btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</x-app-layout>
