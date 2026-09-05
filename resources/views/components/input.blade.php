@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-amber-200 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm']) !!}>
