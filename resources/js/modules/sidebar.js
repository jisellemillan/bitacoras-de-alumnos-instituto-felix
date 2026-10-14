/** sidebar.js — Comportamiento responsive del sidebar */
export function initSidebar() {
    // Cerrar sidebar en mobile al hacer click fuera
    document.addEventListener('click', (e) => {
        const sidebar = document.querySelector('.sidebar');
        const hamburger = document.querySelector('.header-hamburger');
        if (!sidebar || !hamburger) return;

        if (window.innerWidth < 768 &&
            !sidebar.contains(e.target) &&
            !hamburger.contains(e.target)) {
            window.Alpine?.store('sidebar')?.closeMobile();
        }
    });

    // Cerrar en resize a desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            window.Alpine?.store('sidebar')?.closeMobile();
        }
    });
}
