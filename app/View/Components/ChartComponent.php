<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

/**
 * <x-chart-component :chartTitle="..." :labels="[...]" :chartData="[...]" type="line" />
 *
 * Renders a <canvas data-chart="..."> that resources/js/charts.js picks up.
 * Chart.js itself comes from npm through Vite (no CDN, no inline scripts).
 * Only `chartTitle`, `labels` and `chartData` are needed; the rest is optional.
 */
class ChartComponent extends Component
{
    public string $chartId;
    public array $labels;
    public array $chartData;
    public array $config;

    public function __construct(
        public string $chartTitle = '',
        $labels = [],
        $chartData = [],
        public string $type = 'bar',      // bar | line | doughnut | pie
        ?string $chartId = null,
        ?array $colors = null,            // optional hex colours
        public int $height = 260,         // px
        public string $prefix = '',       // e.g. '$' for money
    ) {
        $this->labels = collect($labels)->values()->all();
        $this->chartData = collect($chartData)->values()->all();
        $this->chartId = $chartId ?: 'chart_'.Str::random(8);
        $this->config = [
            'type' => $type,
            'label' => $chartTitle,
            'labels' => $this->labels,
            'data' => $this->chartData,
            'colors' => $colors,
            'prefix' => $prefix,
        ];
    }

    public function render(): View|string
    {
        return view('components.chart-component');
    }
}
