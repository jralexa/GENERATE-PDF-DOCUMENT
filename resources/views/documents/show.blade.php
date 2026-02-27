<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">{{ __('Document') }} #{{ $document->id }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('View document details and download the generated PDF.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('documents.edit', $document) }}" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('documents.pdf', $document) }}" class="inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white">
                    {{ __('Download PDF') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <dl class="grid grid-cols-1 gap-0 md:grid-cols-2">
                    <div class="border-b border-gray-100 p-4 dark:border-gray-700">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Document Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $document->document_date?->format('M d, Y') }}</dd>
                    </div>
                    <div class="border-b border-gray-100 p-4 dark:border-gray-700">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Document No') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $document->document_no }}</dd>
                    </div>
                    <div class="border-b border-gray-100 p-4 dark:border-gray-700">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Document Year') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $document->document_year }}</dd>
                    </div>
                    <div class="border-b border-gray-100 p-4 dark:border-gray-700">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Employee Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $document->employee_name }}</dd>
                    </div>
                    <div class="border-b border-gray-100 p-4 dark:border-gray-700">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Position') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $document->position }}</dd>
                    </div>
                    <div class="border-b border-gray-100 p-4 dark:border-gray-700">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Assignment Station') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $document->assignment_station }}</dd>
                    </div>
                    <div class="p-4 md:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Conforme Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $document->conforme_name }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
