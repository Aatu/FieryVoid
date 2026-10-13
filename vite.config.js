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
        // Library mode leaves NODE_ENV for the consumer to set, and the 'process.env' polyfill
        // below makes it undefined - so without this line React and styled-components pick
        // their DEVELOPMENT builds at runtime and the bundle carries both builds.
        'process.env.NODE_ENV': JSON.stringify('production'),
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
