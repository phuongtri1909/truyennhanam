import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/frontend/css/styles.css',
                'resources/assets/frontend/js/script.js',
                'resources/assets/frontend/css/information.css',
                'resources/assets/frontend/css/advanced-search.css',
                'resources/assets/frontend/js/advanced-search.js',
                'resources/assets/admin/css/dashboard.css',
                'resources/assets/admin/css/responsive.css',
                'resources/assets/admin/css/components.css',
                'resources/assets/admin/js/dashboard.min.js',
                'resources/assets/admin/css/styles-admin.css',
                'resources/assets/frontend/css/styles-site.css'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'truyennhanam.local',
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: 'truyennhanam.local',
            protocol: 'http',
            port: 5173,
        },
    },
});
