@props(['status'])

@php
    $classes = match ($status) {
        'paid'      => 'bg-brand-soft text-fg-brand-strong',
        'shipped'   => 'bg-brand-soft text-fg-brand-strong',
        'completed' => 'bg-success-soft text-fg-success-strong',
        'cancelled' => 'bg-danger-soft text-fg-danger-strong',
        default     => 'bg-warning-soft text-fg-warning', // pending
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold $classes"]) }}>{{ ucfirst($status) }}</span>
