import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    plugins: [react()],
    esbuild: {
        loader: "jsx",
        include: /source\/.*\.js?$/,
        exclude: [],
    },
    define: {
        'process.env': {} // Polyfill for some libs that might expect it
    },
    build: {
        // Output to the same directory as the old watchify build
        outDir: 'source/public/client/UI/reactJs',
        emptyOutDir: false, // Don't delete other files in that directory
        lib: {
            entry: path.resolve(__dirname, 'source/public/client/UI/reactJs/UI.js'),
            name: 'UI',
            fileName: () => 'UI.bundle.js',
            formats: ['umd']
        },
        rollupOptions: {
            // Ensure specific external dependencies are bundled or treated as external
            // React should be bundled, jQuery is likely global
            external: ['jquery'],
            output: {
                globals: {
                    jquery: 'jQuery'
                }
            }
        },
        minify: 'esbuild'
    }
});
