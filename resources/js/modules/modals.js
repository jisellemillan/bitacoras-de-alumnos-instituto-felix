/**
 * modals.js — Control de modales y bloqueo de scroll de fondo
 */

export function initModals() {
    let isLocked = false;
    let originalOverflow = '';
    let originalPaddingRight = '';

    const checkModalState = () => {
        const hasOpenModal = !!document.querySelector('.modal-backdrop, .slide-over-backdrop');

        if (hasOpenModal && !isLocked) {
            originalOverflow = document.body.style.overflow;
            originalPaddingRight = document.body.style.paddingRight;

            // Evitar salto de barra de scroll
            const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
            if (scrollBarWidth > 0) {
                document.body.style.paddingRight = `${scrollBarWidth}px`;
            }

            document.body.style.overflow = 'hidden';
            isLocked = true;
        } else if (!hasOpenModal && isLocked) {
            document.body.style.overflow = originalOverflow;
            document.body.style.paddingRight = originalPaddingRight;
            isLocked = false;
        }
    };

    // Observar mutaciones en el DOM para detectar modales de Livewire / Alpine
    const observer = new MutationObserver(() => {
        checkModalState();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class', 'style'],
    });

    // Restaurar si el usuario navega
    document.addEventListener('livewire:navigated', () => {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        isLocked = false;
        checkModalState();
    });

    checkModalState();
}
