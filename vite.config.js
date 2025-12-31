import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            'resources/mix/plugins.scss',
            'resources/mix/plugins.js'
        }),
    ],
});
