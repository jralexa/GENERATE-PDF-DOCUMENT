<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-6xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div
                    class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/25 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-2 text-sm">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                        {{ __('Documents') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Create, manage, and download generated PDFs.') }}
                    </p>
                </div>
                <a href="{{ route('documents.create') }}"
                    class="ml-auto inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:bg-green-200 dark:text-green-900 dark:hover:bg-green-300">
                    {{ __('Create Document') }}
                </a>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th
                                    class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('Date') }}</th>
                                <th
                                    class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('Document No.') }}</th>
                                <th
                                    class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('Year') }}</th>
                                <th
                                    class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('Employee') }}</th>
                                <th
                                    class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documents as $document)
                                <tr class="border-b border-gray-100 last:border-0 dark:border-gray-700">
                                    <td class="whitespace-nowrap px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200">
                                        {{ $document->document_date?->format('M d, Y') }}</td>
                                    <td class="px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $document->document_no }}</td>
                                    <td class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $document->document_year }}</td>
                                    <td class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $document->employee_name }}</td>
                                    <td class="px-4 py-2.5 text-right text-sm">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('documents.show', $document) }}"
                                                class="rounded-md border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('View') }}</a>
                                            <a href="{{ route('documents.edit', $document) }}"
                                                class="rounded-md border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Edit') }}</a>
                                            <a href="{{ route('documents.pdf', $document) }}"
                                                class="rounded-md border border-indigo-200 px-2.5 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-500/40 dark:text-indigo-300 dark:hover:bg-indigo-500/10">{{ __('PDF') }}</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('No records yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-2.5 p-3 md:hidden">
                    @forelse ($documents as $document)
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $document->document_no }} / {{ $document->document_year }}</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $document->employee_name }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $document->document_date?->format('M d, Y') }}</p>
                            <div class="mt-2.5 flex items-center gap-2">
                                <a href="{{ route('documents.show', $document) }}"
                                    class="rounded-md border border-gray-200 px-2 py-1 text-xs font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200">{{ __('View') }}</a>
                                <a href="{{ route('documents.edit', $document) }}"
                                    class="rounded-md border border-gray-200 px-2 py-1 text-xs font-medium text-gray-700 dark:border-gray-600 dark:text-gray-200">{{ __('Edit') }}</a>
                                <a href="{{ route('documents.pdf', $document) }}"
                                    class="rounded-md border border-indigo-200 px-2 py-1 text-xs font-medium text-indigo-700 dark:border-indigo-500/40 dark:text-indigo-300">{{ __('PDF') }}</a>
                            </div>
                        </div>
                    @empty
                        <p
                            class="rounded-md border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            {{ __('No records yet.') }}
                        </p>
                    @endforelse
                </div>

                <div class="border-t border-gray-100 px-4 py-3 dark:border-gray-700">
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
