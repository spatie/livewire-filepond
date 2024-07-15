import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    build: {
        sourcemap: true,
        manifest: false,
        rollupOptions: {
            output: {
                entryFileNames: `[name].js`,
                chunkFileNames: `[name].js`,
                assetFileNames: `[name].[ext]`
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/js/filepond.js',
            ],
            refresh: true,
            publicDirectory: 'resources',
            buildDirectory: 'dist',
        }),
    ],
})
