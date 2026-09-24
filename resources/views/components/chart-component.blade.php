<div class="relative w-full" style="height: {{ $height }}px">
    @if (count($chartData))
        <canvas id="{{ $chartId }}" role="img" aria-label="{{ $chartTitle }}" data-chart='@json($config)'></canvas>
    @else
        <p class="absolute inset-0 grid place-items-center text-sm text-body-subtle">No data yet</p>
    @endif
</div>

{{ $slot }}
