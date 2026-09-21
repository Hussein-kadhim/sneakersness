import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/verkopers/verkopersoverzicht.css',
                'resources/css/tickets/ticketsoverzicht.css',
                'resources/css/contactpersonen/contactpersonenoverzicht.css',
                'resources/css/stands/standoverzicht.css',
                'resources/css/home/home.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
