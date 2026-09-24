// Renders every <canvas data-chart="{...}"> on the page.
// Data comes from the server (see resources/views/components/chart-component.blade.php).
import {
    Chart,
    LineController, BarController, DoughnutController, PieController,
    CategoryScale, LinearScale,
    PointElement, LineElement, BarElement, ArcElement,
    Tooltip, Legend, Filler,
} from 'chart.js';

Chart.register(
    LineController, BarController, DoughnutController, PieController,
    CategoryScale, LinearScale,
    PointElement, LineElement, BarElement, ArcElement,
    Tooltip, Legend, Filler,
);

const PALETTE = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6', '#f97316'];

Chart.defaults.font.family = "'Figtree', ui-sans-serif, system-ui, sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#64748b';

const withAlpha = (color, alpha) => (/^#[0-9a-f]{6}$/i.test(color) ? color + alpha : color);

function configFor(cfg) {
    const type = cfg.type || 'bar';
    const circular = type === 'doughnut' || type === 'pie';
    const colors = cfg.colors && cfg.colors.length ? cfg.colors : PALETTE;
    const prefix = cfg.prefix || '';
    const fmt = (v) => prefix + Number(v).toLocaleString(undefined, { maximumFractionDigits: 2 });
    const dataset = { label: cfg.label || '', data: cfg.data || [] };

    if (circular) {
        dataset.backgroundColor = dataset.data.map((_, i) => colors[i % colors.length]);
        dataset.borderColor = '#ffffff';
        dataset.borderWidth = 2;
    } else if (type === 'line') {
        dataset.borderColor = colors[0];
        dataset.backgroundColor = withAlpha(colors[0], '1f');
        dataset.fill = true;
        dataset.tension = 0.35;
        dataset.borderWidth = 2;
        dataset.pointRadius = 3;
        dataset.pointBackgroundColor = '#ffffff';
        dataset.pointBorderColor = colors[0];
        dataset.pointBorderWidth = 2;
    } else {
        dataset.backgroundColor = colors[0];
        dataset.borderRadius = 6;
        dataset.maxBarThickness = 36;
    }

    return {
        type,
        data: { labels: cfg.labels || [], datasets: [dataset] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: type === 'doughnut' ? '68%' : undefined,
            plugins: {
                legend: { display: circular, position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 16 } },
                tooltip: {
                    backgroundColor: '#0f172a', padding: 10, cornerRadius: 8, displayColors: circular,
                    callbacks: {
                        label: (c) => ` ${circular ? c.label : c.dataset.label}: ${fmt(circular ? c.parsed : c.parsed.y)}`,
                    },
                },
            },
            scales: circular ? {} : {
                x: { grid: { display: false }, border: { display: false } },
                y: { beginAtZero: true, ticks: { precision: 0, callback: (v) => fmt(v) }, grid: { color: '#f1f5f9' }, border: { display: false } },
            },
        },
    };
}

export function initCharts() {
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        if (canvas.dataset.ready) return;
        canvas.dataset.ready = '1';
        try {
            new Chart(canvas, configFor(JSON.parse(canvas.dataset.chart)));
        } catch (e) {
            console.error('Chart failed to render', e);
        }
    });
}
