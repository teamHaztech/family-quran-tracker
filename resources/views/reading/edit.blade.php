<x-app-layout>
    <x-slot name="title">Edit Reading</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <a href="{{ route('reading.index') }}" class="text-sm text-slate-500 hover:text-brand-600 inline-flex items-center gap-1">← Back to history</a>

        <div class="card p-6 sm:p-8">
            <h2 class="text-xl font-extrabold mb-1">Edit Reading Session</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ $session->date->format('l, M d, Y') }}</p>

            {{-- Delete form (separate, referenced by the button below to avoid nesting) --}}
            <form method="POST" action="{{ route('reading.destroy', $session) }}" id="delete-form"
                  onsubmit="return confirm('Delete this reading session?')">
                @csrf @method('DELETE')
            </form>

            <form method="POST" action="{{ route('reading.update', $session) }}" class="space-y-5">
                @csrf @method('PUT')
                @include('reading._fields')

                <div class="flex justify-between items-center pt-2">
                    <button type="submit" form="delete-form" class="btn-ghost !text-red-600">Delete</button>
                    <div class="flex gap-3">
                        <a href="{{ route('reading.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
