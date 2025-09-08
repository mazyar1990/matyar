import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    base: '/', 
    server: {  
        host: 'matyar.test', // Ensure this is set correctly  
        port: 3000, // Default port  
        strictPort: true, // Optional: stops if the port is in use
        cors: {
            origin: "https://matyar.test"
        }
    },
});
