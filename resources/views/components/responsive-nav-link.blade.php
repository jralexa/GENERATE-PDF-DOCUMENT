@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-2 text-start text-base font-medium text-cyan-800 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-cyan-300 dark:border-cyan-900/70 dark:bg-cyan-900/30 dark:text-cyan-200'
            : 'block w-full rounded-lg border border-transparent px-3 py-2 text-start text-base font-medium text-slate-600 transition duration-150 ease-in-out hover:border-slate-200 hover:bg-slate-50 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300 dark:text-slate-300 dark:hover:border-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
