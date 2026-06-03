const fs = require('fs');
const path = require('path');
const esbuild = require('esbuild');

const rootDir = path.resolve(__dirname, '..');

// Minify the concatenated bundle in one pass. We minify the whole blob (not
// per-file) because these legacy scripts share one global scope and reference
// each other across file boundaries — minifying files individually would be
// unsafe.
//
// minifyIdentifiers is deliberately OFF. The legacy files are non-strict,
// global-scoped scripts where one file's top-level `function foo(){}` / `var
// foo` can be referenced by name from another file, and several do `window.Foo
// = Foo`. Renaming top-level identifiers across the blob is technically
// consistent within one scope, but it's not worth the risk for the size it
// saves — in this hand-written code whitespace + comments dominate, so
// whitespace/syntax minification alone captures the large majority of the win
// with essentially zero behavioral risk. (Can revisit enabling it once
// validated in-game.)
//
// target: esnext so we never downlevel working syntax into something subtly
// different — this is purely whitespace + comment + dead-code removal.
//
// On any failure we fall back to the raw concatenation so a single syntax quirk
// in one source file can never break a deploy.
function minifyBundle(code, name) {
    // Escape hatch: the watcher (watch-legacy.js) sets FV_NO_MINIFY=1 so that
    // local dev iteration keeps readable, line-numbered bundles. Production
    // `yarn build` does not set it, so deploys are always minified.
    if (process.env.FV_NO_MINIFY === '1') {
        return code;
    }
    try {
        const result = esbuild.transformSync(code, {
            minifyWhitespace: true,
            minifySyntax: true,
            minifyIdentifiers: false,
            target: 'esnext',
            legalComments: 'none'
        });
        return result.code;
    } catch (e) {
        console.warn(`Warning: minification failed for ${name} (${e.message}). Falling back to un-minified bundle.`);
        return code;
    }
}

// Main execution
const bundles = [
    {
        name: 'game.php',
        sourceFile: 'source/public/game.php',
        outputFile: 'source/public/client/game.legacy.bundle.js',
        excluded: [
            'client/lib/three.min.js',
            'client/lib/THREE.MeshLine.js',
            'client/UI/reactJs/UI.bundle.js',
            'static/ships.js',
            'client/game.legacy.bundle.js'
        ]
    },
    {
        name: 'gamelobby.php',
        sourceFile: 'source/public/gamelobby.php',
        outputFile: 'source/public/client/gamelobby.legacy.bundle.js',
        excluded: [
            'static/ships.js',
            'client/gamelobby.legacy.bundle.js',
            'client/lib/jquery-ui-1.8.15.custom.min.js' // It's in the top section
        ]
    }
];

function createBundle(config) {
    const sourcePath = path.join(rootDir, config.sourceFile);
    const outputPath = path.join(rootDir, config.outputFile);

    console.log(`Reading ${config.name}...`);
    if (!fs.existsSync(sourcePath)) {
        console.error(`Error: ${config.name} not found at ${sourcePath}`);
        return;
    }

    const content = fs.readFileSync(sourcePath, 'utf8');

    // Extract sources using the helper
    const sources = extractScriptSources(content, config.excluded);
    console.log(`Found ${sources.length} scripts to bundle for ${config.name}.`);

    let bundleContent = '';

    sources.forEach(src => {
        const fullPath = path.join(rootDir, 'source/public', src);
        try {
            if (fs.existsSync(fullPath)) {
                const fileContent = fs.readFileSync(fullPath, 'utf8');
                bundleContent += `\n/* Source: ${src} */\n`;
                bundleContent += fileContent + ';\n';
            } else {
                console.warn(`Warning: File not found: ${fullPath}`);
            }
        } catch (e) {
            console.error(`Error reading ${fullPath}: ${e.message}`);
        }
    });

    const rawKb = bundleContent.length / 1024;
    const minified = minifyBundle(bundleContent, config.name);
    const minKb = minified.length / 1024;
    const saved = rawKb > 0 ? (100 * (1 - minKb / rawKb)).toFixed(1) : '0';

    console.log(`Writing bundle to ${outputPath}...`);
    fs.writeFileSync(outputPath, minified);
    console.log(`${config.name} bundle created successfully! Size: ${minKb.toFixed(2)} KB (was ${rawKb.toFixed(2)} KB, -${saved}%)\n`);
}

// Modify extractScriptSources to accept excluded list
function extractScriptSources(content, excluded) {
    const sources = [];
    const scriptRegex = /<script(?: defer)? src="([^"]+)"><\/script>/;

    const lines = content.split('\n');
    lines.forEach(line => {
        line = line.trim();
        if (line.startsWith('<!--') || line.startsWith('//')) return; // Skip comments

        const lineMatch = line.match(scriptRegex);
        if (lineMatch) {
            let src = lineMatch[1];
            // Skip excluded files, external URLs, and dynamic PHP tags
            if (!excluded.includes(src) && !src.startsWith('https://') && !src.includes('<?php')) {
                sources.push(src);
            }
        }
    });
    return sources;
}

bundles.forEach(config => createBundle(config));
