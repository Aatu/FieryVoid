const fs = require('fs');
const path = require('path');

const rootDir = path.resolve(__dirname, '..');

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

    console.log(`Writing bundle to ${outputPath}...`);
    fs.writeFileSync(outputPath, bundleContent);
    console.log(`${config.name} bundle created successfully! Size: ${(bundleContent.length / 1024).toFixed(2)} KB\n`);
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
