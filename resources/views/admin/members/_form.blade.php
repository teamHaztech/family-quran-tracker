@php $member = $member ?? null; @endphp

@if ($errors->any())
    <div class="rounded-xl bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-sm px-4 py-3">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="grid sm:grid-cols-2 gap-5">
    {{-- Photo --}}
    <div class="sm:col-span-2 flex items-center gap-4" x-data="{ preview: '{{ $member?->photoUrl() }}' }">
        <img :src="preview" class="w-20 h-20 rounded-2xl object-cover bg-slate-100" alt="">
        <div>
            <label class="form-label">Profile Photo</label>
            <input type="file" name="photo" accept="image/*"
                   @change="const f=$event.target.files[0]; if(f) preview=URL.createObjectURL(f)"
                   class="block text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-brand-50 file:text-brand-700 file:font-semibold hover:file:bg-brand-100">
            <p class="text-xs text-slate-400 mt-1">JPG, PNG or WEBP · max 2MB</p>
        </div>
    </div>

    <div>
        <label class="form-label">First Name *</label>
        <input type="text" name="first_name" value="{{ old('first_name', $member?->first_name) }}" required class="form-input-field">
    </div>
    <div>
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" value="{{ old('last_name', $member?->last_name) }}" class="form-input-field">
    </div>
    <div>
        <label class="form-label">Email *</label>
        <input type="email" name="email" value="{{ old('email', $member?->email) }}" required class="form-input-field">
    </div>
    <div>
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $member?->phone) }}" class="form-input-field" placeholder="+91 …">
    </div>
    <div>
        <label class="form-label">Date Joined</label>
        <input type="date" name="date_joined" value="{{ old('date_joined', optional($member?->date_joined)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-input-field">
    </div>

    {{-- Current reading position --}}
    <div>
        <label class="form-label">Currently on — Surah</label>
        <input type="text" name="current_surah" list="surah-list"
               value="{{ old('current_surah', $member?->profile?->current_surah) }}"
               class="form-input-field" placeholder="e.g. Al-Baqarah">
    </div>
    <div>
        <label class="form-label">Currently on — Ayah</label>
        <input type="number" name="current_ayah" min="1" max="286"
               value="{{ old('current_ayah', $member?->profile?->current_ayah) }}"
               class="form-input-field" placeholder="e.g. 255">
    </div>

    @if ($member)
        <div>
            <label class="form-label">Daily Goal (pages)</label>
            <input type="number" name="daily_goal_pages" min="1" max="604"
                   value="{{ old('daily_goal_pages', $member->profile?->daily_goal_pages) }}"
                   class="form-input-field" placeholder="Uses family default">
        </div>
    @else
        <div>
            <label class="form-label">Password *</label>
            <input type="password" name="password" required class="form-input-field" placeholder="Min 8 characters">
        </div>
        <div>
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="password_confirmation" required class="form-input-field">
        </div>
    @endif
</div>

<datalist id="surah-list">
    @foreach (['Al-Fatihah','Al-Baqarah','Aal-i-Imran','An-Nisa','Al-Maidah','Al-Anam','Al-Araf','Al-Anfal','At-Tawbah','Yunus','Hud','Yusuf','Ar-Rad','Ibrahim','Al-Hijr','An-Nahl','Al-Isra','Al-Kahf','Maryam','Ta-Ha','Ya-Sin','Ar-Rahman','Al-Waqiah','Al-Mulk','An-Naba','Al-Ikhlas','Al-Falaq','An-Nas'] as $s)
        <option value="{{ $s }}">
    @endforeach
</datalist>
