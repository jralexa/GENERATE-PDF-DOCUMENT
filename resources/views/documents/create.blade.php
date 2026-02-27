<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
             <div class="flex flex-wrap items-center gap-2 text-sm">
                 <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                    {{ __('Create Document') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Fill in the required information below.') }}
                </p>
            </div>
                <a href="{{ route('documents.index') }}" class="ml-auto text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                {{ __('Back to List') }}
            </a>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-5">
                <form method="POST" action="{{ route('documents.store') }}">
                    @include('documents.partials.form', [
                        'method' => 'POST',
                        'document' => $document,
                    ])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
