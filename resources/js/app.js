/**
 * app.js — Punto de entrada JS. Solo importa módulos.
 * Sin lógica de negocio aquí.
 */

// Bootstrap (Axios, CSRF token)
import './bootstrap.js';

// Alpine.js integration with Livewire 3
import { toastStore } from './alpine/toast-store.js';
import { sidebarStore } from './alpine/sidebar-store.js';

// Módulos de UI
import { initCharts } from './modules/charts.js';
import { initToasts } from './modules/toasts.js';
import { initSidebar } from './modules/sidebar.js';
import { initConfirmDelete } from './modules/confirm-delete.js';
import { initModals } from './modules/modals.js';

function setupAlpineStores(alpineInstance) {
    if (!alpineInstance) return;
    if (!alpineInstance.store('toasts')) {
        alpineInstance.store('toasts', toastStore);
    }
    if (!alpineInstance.store('sidebar')) {
        alpineInstance.store('sidebar', sidebarStore);
    }
}

// En Livewire 3, Alpine viene integrado globalmente
if (window.Alpine) {
    setupAlpineStores(window.Alpine);
} else {
    document.addEventListener('alpine:init', () => {
        setupAlpineStores(window.Alpine);
    });
}

// Inicializar módulos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initToasts();
    initConfirmDelete();
    initModals();
});

// Inicializar gráficas cuando Livewire actualice la página
document.addEventListener('livewire:navigated', () => {
    initCharts();
});

// Escuchar eventos Livewire globales para toasts
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (data) => {
        Alpine.store('toasts').add(data[0]);
    });
});
