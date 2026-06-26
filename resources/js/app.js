import './bootstrap';

import Alpine from 'alpinejs';
import {
    Chart,
    LineController, BarController, DoughnutController,
    LineElement, BarElement, PointElement, ArcElement,
    CategoryScale, LinearScale, Filler, Tooltip, Legend,
} from 'chart.js';

/* ------------------------------------------------------------------ *
 |  Chart.js — register only what we use (keeps the bundle small)
 * ------------------------------------------------------------------ */
Chart.register(
    LineController, BarController, DoughnutController,
    LineElement, BarElement, PointElement, ArcElement,
    CategoryScale, LinearScale, Filler, Tooltip, Legend,
);
Chart.defaults.font.family = "'Figtree', ui-sans-serif, system-ui, sans-serif";
Chart.defaults.color = '#64748b';
window.Chart = Chart;

const BRAND = '#059669';

/* Build a soft vertical gradient for line charts */
function gradient(ctx) {
    const g = ctx.createLinearGradient(0, 0, 0, 240);
    g.addColorStop(0, 'rgba(5, 150, 105, 0.28)');
    g.addColorStop(1, 'rgba(5, 150, 105, 0.01)');
    return g;
}

/* Public helper used by Blade views to render the standard chart types */
window.renderChart = function (id, type, labels, data, opts = {}) {
    const el = document.getElementById(id);
    if (!el) return;
    const ctx = el.getContext('2d');

    const baseDataset = {
        label: opts.label || '',
        data,
        borderColor: opts.color || BRAND,
        backgroundColor:
            type === 'line'
                ? gradient(ctx)
                : type === 'doughnut'
                ? ['#059669', '#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#fbbf24', '#f59e0b']
                : opts.color || BRAND,
        borderWidth: type === 'line' ? 3 : 0,
        borderRadius: type === 'bar' ? 8 : 0,
        fill: type === 'line',
        tension: 0.4,
        pointRadius: 0,
        pointHoverRadius: 5,
        pointBackgroundColor: BRAND,
        maxBarThickness: 38,
    };

    return new Chart(ctx, {
        type,
        data: { labels, datasets: [baseDataset] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: type === 'doughnut' ? '70%' : undefined,
            plugins: {
                legend: { display: type === 'doughnut' && opts.legend !== false, position: 'bottom' },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 10,
                    titleFont: { weight: '600' },
                },
            },
            scales:
                type === 'doughnut'
                    ? {}
                    : {
                          x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } },
                          y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { precision: 0 } },
                      },
        },
    });
};

/* ------------------------------------------------------------------ *
 |  Alpine components
 * ------------------------------------------------------------------ */

// Animated number counter
Alpine.data('counter', (target = 0, duration = 900) => ({
    value: 0,
    init() {
        const start = performance.now();
        const animate = (now) => {
            const p = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
            this.value = Math.round(eased * target);
            if (p < 1) requestAnimationFrame(animate);
        };
        requestAnimationFrame(animate);
    },
}));

// Reading timer (Method 2 — built-in reading mode)
Alpine.data('readingTimer', () => ({
    running: false,
    seconds: 0,
    startedAt: null,
    endedAt: null,
    interval: null,
    showSave: false,
    start() {
        this.running = true;
        this.startedAt = new Date();
        this.interval = setInterval(() => this.seconds++, 1000);
    },
    stop() {
        this.running = false;
        this.endedAt = new Date();
        clearInterval(this.interval);
        this.showSave = true;
    },
    reset() {
        this.running = false;
        this.seconds = 0;
        this.startedAt = null;
        this.endedAt = null;
        this.showSave = false;
        clearInterval(this.interval);
    },
    get display() {
        const h = String(Math.floor(this.seconds / 3600)).padStart(2, '0');
        const m = String(Math.floor((this.seconds % 3600) / 60)).padStart(2, '0');
        const s = String(this.seconds % 60).padStart(2, '0');
        return `${h}:${m}:${s}`;
    },
    get minutes() {
        return Math.max(1, Math.round(this.seconds / 60));
    },
    iso(d) {
        if (!d) return '';
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    },
}));

window.Alpine = Alpine;
Alpine.start();
