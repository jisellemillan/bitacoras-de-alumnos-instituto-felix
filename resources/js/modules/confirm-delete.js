/**
 * confirm-delete.js — Confirmación antes de eliminar
 * Los componentes Livewire llaman:
 *   $dispatch('confirm-delete', { message: '...', wireId: '...' })
 */
export function initConfirmDelete() {
    // Manejado por el componente Livewire ConfirmDeleteModal
    // Este módulo solo registra el atajo de teclado Escape para cancelar
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.dispatchEvent(new CustomEvent('confirm-delete-cancel'));
        }
    });
}
