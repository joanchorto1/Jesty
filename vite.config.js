import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { existsSync } from 'node:fs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const xlsxEntry = ['xlsx.mjs', 'xlsx.js']
    .map((candidate) => path.resolve(__dirname, `node_modules/xlsx/${candidate}`))
    .find((candidate) => existsSync(candidate));

const aliases = {};

if (xlsxEntry) {
    aliases.xlsx = xlsxEntry;
}

export default defineConfig({
    resolve: {
        alias: aliases,
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],            refresh: true,
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
    optimizeDeps: {
        include: ['xlsx'],
    },
});
