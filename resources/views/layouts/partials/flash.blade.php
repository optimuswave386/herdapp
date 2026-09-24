{{-- Flash messages. Only the `success`, `error` and `warning` keys are shown; the
     Breeze `status` key is left alone because its values are message codes. --}}
@php
    $flashes = [
        'success' => ['text-fg-success-strong bg-success-soft border-success-subtle', 'M5 13l4 4L19 7'],
        'warning' => ['text-fg-warning bg-warning-soft border-warning-subtle', 'M12 9v4m0 4h.01M10.3 3.9L2.4 17.5A2 2 0 004.1 20.5h15.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z'],
        'error'   => ['text-fg-danger-strong bg-danger-soft border-danger-subtle', 'M6 18L18 6M6 6l12 12'],
    ];
@endphp
@foreach ($flashes as $key => [$classes, $icon])
    @if (session()->has($key))
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8" x-data="{ show: true }" x-show="show" x-cloak>
            <div class="flex items-start gap-3 rounded-lg border p-4 text-sm {{ $classes }}" role="alert">
                <svg class="mt-0.5 size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                <p class="flex-1 font-medium">{{ session($key) }}</p>
                <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Dismiss">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif
@endforeach
