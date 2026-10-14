/**
 * sidebar-store.js — Alpine store para estado del sidebar
 */
export const sidebarStore = {
    collapsed: localStorage.getItem('sidebar-collapsed') === 'true',
    mobileOpen: false,

    toggle() {
        this.collapsed = !this.collapsed;
        localStorage.setItem('sidebar-collapsed', this.collapsed);
    },

    toggleMobile() {
        this.mobileOpen = !this.mobileOpen;
    },

    closeMobile() {
        this.mobileOpen = false;
    },
};
