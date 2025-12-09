@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#1a90d6] text-start text-base font-medium text-[#1a90d6] bg-sky-50 focus:outline-none focus:text-[#0f6ea5] focus:bg-sky-100 focus:border-[#1a90d6] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:text-slate-900 focus:bg-slate-50 focus:border-slate-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
