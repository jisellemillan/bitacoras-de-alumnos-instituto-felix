/** toasts.js — Inicialización y auto-dismiss */
export function initToasts() {
    // Los toasts son manejados por Alpine store + Livewire events en app.js
    // Esta función registra el listener para tecla Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.Alpine?.store('toasts')?.items.forEach(t => {
                window.Alpine.store('toasts').remove(t.id);
            });
        }
    });
}
