<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">{{ __('Edit Document') }} #{{ $document->id }}</h2>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('Update details and save changes.') }}</p>
            </div>
            <a href="{{ route('documents.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl space-y-3 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/25 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-5">
                <form method="POST" action="{{ route('documents.update', $document) }}">
                    @include('documents.partials.form', [
                        'method' => 'PUT',
                        'document' => $document,
                    ])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
