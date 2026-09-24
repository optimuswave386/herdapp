{{-- One KPI card. Expects $kpi = [label, value, note, good, icon]. --}}
<div class="card p-5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-medium text-body-subtle">{{ $kpi['label'] }}</p>
            <p class="mt-1 truncate text-3xl font-bold tracking-tight text-heading">{{ $kpi['value'] }}</p>
        </div>
        <span class="kpi-icon">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $kpi['icon'] }}"/></svg>
        </span>
    </div>
    <p class="mt-3 text-xs font-medium {{ $kpi['good'] ? 'text-fg-success' : 'text-fg-danger' }}">{{ $kpi['note'] }}</p>
</div>
