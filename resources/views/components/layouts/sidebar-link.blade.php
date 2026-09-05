@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl bg-brand-600 text-white shadow-sm transition'
            : 'flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-brand-900 hover:bg-brand-100 hover:text-brand-950 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
