import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: 'sodierp.local', // <<< O MAIS IMPORTANTE
        port: 5173,
        cors: true, // <<< resolve CORS
        
        hmr: {
            host: 'sodierp.local',
            protocol: 'ws',
            port: 5173,
        }
    },

    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
