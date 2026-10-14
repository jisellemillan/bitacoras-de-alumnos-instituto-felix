import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            // Punto de entrada CSS: main.css (con todos los @import)
            // Punto de entrada JS: app.js (con todos los módulos)
            input: ['resources/css/main.css', 'resources/js/app.js'],
            refresh: [
                'resources/views/**',
                'app/Livewire/**',
                'app/View/Components/**',
                'routes/**',
            ],
        }),
    ],
    // Tailwind 4 maneja @import nativamente via el plugin oficial
    // No se necesita postcss-import adicional
});
