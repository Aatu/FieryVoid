// Build the tree-shaken THREE global shim — perf roadmap #5.
// See source/public/client/lib/three-global-shim.src.js for the why.
//
// esbuild bundles the shim entry (which imports ONLY the THREE symbols FV uses)
// into a single plain IIFE script. Tree-shaking drops every unused part of the
// engine, then minification shrinks the rest. Output is a classic <script> (NOT
// an ES module) so it runs before the non-module legacy bundle + MeshLine and
// installs window.THREE in time for them.
//
// Output replaces the role of the old vendored client/lib/three.min.js. Like the
// legacy bundles, it is a generated artifact regenerated per build.

const path = require('path');
const esbuild = require('esbuild');

const rootDir = path.resolve(__dirname, '..');
const entry = path.join(rootDir, 'source/public/client/lib/three-global-shim.src.js');
const outfile = path.join(rootDir, 'source/public/client/lib/three.shim.bundle.js');

esbuild.build({
    entryPoints: [entry],
    outfile,
    bundle: true,
    minify: true,
    treeShaking: true,
    // IIFE, not ESM: the legacy scripts are classic globals-scoped <script>s and
    // run synchronously in document order. A module would defer/scope differently
    // and window.THREE wouldn't be set in time.
    format: 'iife',
    // Match the runtime: r160 + the FX shaders rely on modern syntax. esnext keeps
    // working syntax intact (no downlevelling), same policy as bundle-legacy.js.
    target: 'esnext',
    legalComments: 'none',
}).then(() => {
    const fs = require('fs');
    const kb = (fs.statSync(outfile).size / 1024).toFixed(1);
    console.log(`three.shim.bundle.js built successfully! Size: ${kb} KB (was ~670 KB raw UMD)`);
}).catch((e) => {
    console.error('Failed to build three shim:', e.message);
    process.exit(1);
});
