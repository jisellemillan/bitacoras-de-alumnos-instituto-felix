/**
 * toast-store.js — Alpine store para notificaciones toast
 */
export const toastStore = {
    items: [],
    _nextId: 0,

    add({ type = 'success', title = '', message = '', duration = 4000, undoAction = null }) {
        const id = ++this._nextId;
        this.items.push({ id, type, title, message, duration, undoAction });

        if (duration > 0) {
            setTimeout(() => this.remove(id), duration);
        }
    },

    remove(id) {
        this.items = this.items.filter(t => t.id !== id);
    },

    success(title, message = '', opts = {}) {
        this.add({ type: 'success', title, message, ...opts });
    },

    error(title, message = '', opts = {}) {
        this.add({ type: 'error', title, message, duration: 6000, ...opts });
    },

    warning(title, message = '', opts = {}) {
        this.add({ type: 'warning', title, message, ...opts });
    },

    info(title, message = '', opts = {}) {
        this.add({ type: 'info', title, message, ...opts });
    },
};
