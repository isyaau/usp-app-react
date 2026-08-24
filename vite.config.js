import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/react.css',
                'resources/js/app.tsx',
            ],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        rollupOptions: {
            output: {
                // Pecah vendor agar tiap chunk < 500 kB & cache lebih efisien.
                manualChunks(id) {
                    if (! id.includes('node_modules')) {
                        return;
                    }
                    if (id.includes('react-dom') || /[\\/]react[\\/]/.test(id) || id.includes('scheduler')) {
                        return 'vendor-react';
                    }
                    if (id.includes('@inertiajs')) {
                        return 'vendor-inertia';
                    }
                    if (
                        id.includes('@radix-ui')
                        || id.includes('lucide-react')
                        || id.includes('class-variance-authority')
                        || id.includes('clsx')
                        || id.includes('tailwind-merge')
                    ) {
                        return 'vendor-ui';
                    }
                    return 'vendor';
                },
            },
        },
    },
});
