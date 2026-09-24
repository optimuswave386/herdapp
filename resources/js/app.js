import 'flowbite';

import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Chart.js is only downloaded on pages that actually contain a chart.
if (document.querySelector('canvas[data-chart]')) {
    import('./charts').then((m) => m.initCharts());
}
