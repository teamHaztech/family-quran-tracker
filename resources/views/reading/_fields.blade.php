@php $session = $session ?? null; @endphp

@if ($errors->any())
    <div class="rounded-xl bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-sm px-4 py-3">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<div x-data="{
        start: {{ old('start_page', $session?->start_page) ?: 'null' }},
        end: {{ old('end_page', $session?->end_page) ?: 'null' }},
        manualPages: {{ old('pages_read', $session?->pages_read) ?: 'null' }},
        get pages() {
            if (this.start && this.end && this.end >= this.start) return (this.end - this.start) + 1;
            return this.manualPages ?? 0;
        }
     }" class="space-y-5">

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">Date *</label>
            <input type="date" name="date" max="{{ now()->format('Y-m-d') }}"
                   value="{{ old('date', optional($session?->date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                   required class="form-input-field">
        </div>
        <div>
            <label class="form-label">Surah</label>
            <input type="text" name="surah" value="{{ old('surah', $session?->surah ?? request('surah')) }}"
                   class="form-input-field" placeholder="e.g. Al-Baqarah" list="surah-list">
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div>
            <label class="form-label">Start Page</label>
            <input type="number" name="start_page" min="1" max="604" x-model.number="start"
                   value="{{ old('start_page', $session?->start_page) }}" class="form-input-field">
        </div>
        <div>
            <label class="form-label">End Page</label>
            <input type="number" name="end_page" min="1" max="604" x-model.number="end"
                   value="{{ old('end_page', $session?->end_page) }}" class="form-input-field">
        </div>
        <div>
            <label class="form-label">Pages Read</label>
            <input type="number" name="pages_read" min="0" max="604" x-model.number="manualPages"
                   :placeholder="pages" class="form-input-field bg-brand-50/50 dark:bg-brand-900/10 font-semibold">
            <p class="text-[11px] text-slate-400 mt-1">Auto: <span x-text="pages" class="font-semibold text-brand-600"></span></p>
        </div>
        <div>
            <label class="form-label">Juz</label>
            <input type="number" name="juz" min="1" max="30" value="{{ old('juz', $session?->juz) }}" class="form-input-field">
        </div>
    </div>

    <div>
        <label class="form-label">Time Spent (minutes)</label>
        <input type="number" name="minutes_read" min="0" max="1440"
               value="{{ old('minutes_read', $session?->minutes_read) }}" class="form-input-field sm:w-1/2" placeholder="e.g. 25">
    </div>

    <div>
        <label class="form-label">Notes</label>
        <textarea name="notes" rows="3" class="form-input-field" placeholder="Reflections, tajweed notes, etc. (optional)">{{ old('notes', $session?->notes) }}</textarea>
    </div>
</div>

<datalist id="surah-list">
    @foreach (['Al-Fatihah','Al-Baqarah','Aal-i-Imran','An-Nisa','Al-Maidah','Al-Anam','Al-Araf','Al-Anfal','At-Tawbah','Yunus','Hud','Yusuf','Ar-Rad','Ibrahim','Al-Hijr','An-Nahl','Al-Isra','Al-Kahf','Maryam','Ta-Ha','Ya-Sin','Ar-Rahman','Al-Waqiah','Al-Mulk','An-Naba','Al-Ikhlas','Al-Falaq','An-Nas'] as $s)
        <option value="{{ $s }}">
    @endforeach
</datalist>
