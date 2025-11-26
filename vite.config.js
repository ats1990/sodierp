import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // 💡 CONFIGURAÇÃO DE SERVIDOR PARA WAMP/WINDOWS
    server: {
        // CRUCIAL: '0.0.0.0' força o Vite a se ligar a todos os IPs,
        // garantindo que ele seja acessível via sodierp.local.
        host: '0.0.0.0', 
        
        // Mantém a instrução correta para o navegador e Laravel
        // sobre onde buscar os assets do HMR.
        hmr: {
            host: 'sodierp.local', 
            protocol: 'ws',
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