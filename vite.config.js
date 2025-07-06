import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/main.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
            '~': resolve(__dirname, 'resources'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'vue-router', 'pinia'],
                    ui: ['@headlessui/vue', '@heroicons/vue'],
                    charts: ['chart.js', 'vue-chartjs'],
                    utils: ['axios', 'date-fns', 'lodash-es']
                }
            }
        },
        chunkSizeWarningLimit: 1000
    },
    server: {
        host: '0.0.0.0',
        https: false,
        hmr: {
            host: 'localhost',
        },
    },
});
