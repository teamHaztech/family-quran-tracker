<x-app-layout>
    <x-slot name="title">Add Member</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <a href="{{ route('admin.members.index') }}" class="text-sm text-slate-500 hover:text-brand-600 inline-flex items-center gap-1">← Back to members</a>

        <div class="card p-6 sm:p-8">
            <h2 class="text-xl font-extrabold mb-1">New Family Member</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Create an account so they can log their reading.</p>

            <form method="POST" action="{{ route('admin.members.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @include('admin.members._form')

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.members.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Create Member</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
