@props(['active' => false])

@php
$classes = $active
    ? 'inline-flex items-center border-b-2 border-brand px-1 pt-1 text-sm font-semibold text-heading focus:outline-none'
    : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-body-subtle transition hover:border-gray-300 hover:text-heading focus:outline-none focus:text-heading';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
