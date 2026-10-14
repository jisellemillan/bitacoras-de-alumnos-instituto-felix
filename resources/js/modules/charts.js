/**
 * charts.js — Gráficas Chart.js para el dashboard
 * Se llama al cargar y tras eventos Livewire
 */
import Chart from 'chart.js/auto';

// Paleta verde del Instituto Félix
const COLORS = {
    green: '#2E7D32',
    greenLight: '#4CAF50',
    greenSoft: '#C8E6C9',
    gold: '#E3A21A',
    blue: '#0277BD',
    red: '#C62828',
    gray: '#9CA3AF',
};

// Registro de instancias activas para destruirlas antes de re-crear
const chartInstances = new Map();

function destroyChart(id) {
    if (chartInstances.has(id)) {
        chartInstances.get(id).destroy();
        chartInstances.delete(id);
    }
}

/**
 * Gráfica de barras: distribución por grado
 */
export function renderGradeChart(canvasId, data) {
    destroyChart(canvasId);
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const chart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Expedientes activos',
                data: data.values,
                backgroundColor: [COLORS.green, COLORS.gold, COLORS.blue],
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} expedientes` } },
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#EEF2EE' } },
                x: { grid: { display: false } },
            },
        },
    });

    chartInstances.set(canvasId, chart);
}

/**
 * Gráfica de dona: distribución por área de necesidad
 */
export function renderNeedAreasChart(canvasId, data) {
    destroyChart(canvasId);
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const chart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: [
                    COLORS.green, COLORS.gold, COLORS.blue,
                    COLORS.red, '#9C27B0', COLORS.greenLight,
                ],
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 12, font: { size: 11 } },
                },
            },
        },
    });

    chartInstances.set(canvasId, chart);
}

/**
 * Gráfica de barras: distribución por ruta de ingreso (A/B/C)
 */
export function renderRouteChart(canvasId, data) {
    destroyChart(canvasId);
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const chart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Casos',
                data: data.values,
                backgroundColor: ['#7E57C2', COLORS.blue, COLORS.green],
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#EEF2EE' } },
                y: { grid: { display: false } },
            },
        },
    });

    chartInstances.set(canvasId, chart);
}

/**
 * Inicializa todas las gráficas del dashboard
 * Los datos se pasan via atributos data-* en el canvas
 */
export function initCharts() {
    document.querySelectorAll('[data-chart]').forEach(canvas => {
        const type = canvas.dataset.chart;
        try {
            const data = JSON.parse(canvas.dataset.chartData || '{}');
            switch (type) {
                case 'grade':      renderGradeChart(canvas.id, data);     break;
                case 'need-areas': renderNeedAreasChart(canvas.id, data); break;
                case 'route':      renderRouteChart(canvas.id, data);     break;
            }
        } catch (e) {
            console.warn('Error rendering chart:', canvas.id, e);
        }
    });
}
