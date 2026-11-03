import { defineConfig, transformWithOxc } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

// FV's React files are .js but contain JSX. Vite 8 picks the parser from the file extension and
// reads .js as plain JavaScript (esbuild's old `loader: "jsx"` has no equivalent), so this compiles
// their JSX first - everything after it sees ordinary JavaScript.
const jsxInJs = {
    name: 'fv:jsx-in-js',
    enforce: 'pre',
    transform: {
        filter: { id: /source\/.*\.js$/ },
        async handler(code, id) {
            const result = await transformWithOxc(code, id, { lang: 'jsx', jsx: { runtime: 'automatic' } });
            return { code: result.code, map: result.map };
        }
    }
};

export default defineConfig({
    plugins: [jsxInJs, react()],
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
            entry: path.resolve(import.meta.dirname, 'source/public/client/UI/reactJs/UI.js'),
            name: 'UI',
            fileName: () => 'UI.bundle.js',
            formats: ['umd']
        },
        rolldownOptions: {
            // Ensure specific external dependencies are bundled or treated as external
            // React should be bundled, jQuery is likely global
            external: ['jquery'],
            output: {
                globals: {
                    jquery: 'jQuery'
                },
                // Rollup always opened the bundle with "use strict"; Rolldown only does when the
                // source has the directive, which ES modules don't - without this the UI runs sloppy.
                strict: true
            }
        }
    }
});
