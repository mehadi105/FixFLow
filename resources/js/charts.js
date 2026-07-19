import {
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    DoughnutController,
    ArcElement,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

Chart.register(
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    DoughnutController,
    ArcElement,
    Tooltip,
    Legend,
    Filler,
);

const indigo = '#6366f1';
const indigoSoft = 'rgba(99, 102, 241, 0.85)';
const palette = [
    '#f59e0b',
    '#0ea5e9',
    '#8b5cf6',
    '#d946ef',
    '#6366f1',
    '#10b981',
    '#f43f5e',
];

function parseJson(value, fallback) {
    if (! value) {
        return fallback;
    }

    try {
        return JSON.parse(value);
    } catch {
        return fallback;
    }
}

function createBarChart(canvas, labels, values, options = {}) {
    const isCurrency = options.currency === true;

    return new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: options.label || 'Count',
                data: values,
                backgroundColor: indigoSoft,
                borderColor: indigo,
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
                maxBarThickness: 48,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label(context) {
                            const value = context.parsed.y ?? 0;
                            return isCurrency
                                ? ` $${Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                                : ` ${value}`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 12 } },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#64748b',
                        font: { size: 12 },
                        precision: 0,
                        callback(value) {
                            return isCurrency ? `$${value}` : value;
                        },
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.25)',
                    },
                },
            },
        },
    });
}

function createDoughnutChart(canvas, labels, values) {
    return new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: labels.map((_, index) => palette[index % palette.length]),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        boxHeight: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        color: '#475569',
                        font: { size: 12 },
                        padding: 14,
                    },
                },
                tooltip: {
                    callbacks: {
                        label(context) {
                            const total = context.dataset.data.reduce((sum, n) => sum + Number(n), 0) || 1;
                            const value = Number(context.parsed) || 0;
                            const percent = Math.round((value / total) * 100);
                            return ` ${context.label}: ${value} (${percent}%)`;
                        },
                    },
                },
            },
        },
    });
}

document.querySelectorAll('[data-chart]').forEach((canvas) => {
    const type = canvas.dataset.chart;
    const labels = parseJson(canvas.dataset.labels, []);
    const values = parseJson(canvas.dataset.values, []);

    if (! labels.length || ! values.length) {
        return;
    }

    if (type === 'bar') {
        createBarChart(canvas, labels, values, {
            label: canvas.dataset.label || 'Count',
            currency: canvas.dataset.currency === 'true',
        });
        return;
    }

    if (type === 'doughnut') {
        createDoughnutChart(canvas, labels, values);
    }
});
