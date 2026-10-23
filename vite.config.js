import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights:,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0', // Permet à Vite d'écouter sur toutes les adresses IP du réseau local
        port: 5173,      // Port standard pour le serveur de développement Vite
        hmr: {
            host: '192.168.1.100', // REMPLACER par l'adresse IP fixe réelle du serveur
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
