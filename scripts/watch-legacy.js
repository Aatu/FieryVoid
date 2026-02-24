const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');

/*
  Legacy Bundle Watcher
  ---------------------
  Watches 'source/public/client' and rebuilds legacy bundles when files change.
  Uses native fs.watch to avoid external dependencies.
  Includes simple debounce handling.
*/

const rootDir = path.resolve(__dirname, '..');
const clientDir = path.join(rootDir, 'source/public/client');
const gamePhp = path.join(rootDir, 'source/public/game.php');
const gameLobbyPhp = path.join(rootDir, 'source/public/gamelobby.php');
const bundleScript = path.join(rootDir, 'scripts/bundle-legacy.js');

let isBuilding = false;
let buildTimeout = null;
const DEBOUNCE_MS = 500;

function rebuildBundles() {
    if (isBuilding) return;

    console.log('\x1b[36m%s\x1b[0m', '>> File change detected. Rebuilding bundles...');
    isBuilding = true;

    exec(`node "${bundleScript}"`, (error, stdout, stderr) => {
        isBuilding = false;

        if (error) {
            console.error('\x1b[31m%s\x1b[0m', `Build error: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`Build stderr: ${stderr}`);
        }

        // Output the result (filtered for brevity if needed)
        console.log(stdout.trim());
        console.log('\x1b[32m%s\x1b[0m', `>> Build complete. Watching for changes...`);
    });
}

function triggerBuild() {
    // Clear existing timer to debounce rapid changes
    if (buildTimeout) {
        clearTimeout(buildTimeout);
    }

    buildTimeout = setTimeout(() => {
        rebuildBundles();
    }, DEBOUNCE_MS);
}

// Watch function
function startWatch() {
    console.log('\x1b[33m%s\x1b[0m', `Starting Legacy Bundle Watcher...`);
    console.log(`Watching: ${clientDir}`);
    console.log(`Watching: ${gamePhp}`);
    console.log(`Watching: ${gameLobbyPhp}`);

    // Watch Client Directory (Recursive)
    try {
        fs.watch(clientDir, { recursive: true }, (eventType, filename) => {
            if (filename) {
                // Ignore the bundle files themselves to prevent loops
                if (filename.includes('legacy.bundle.js')) return;

                // console.log(`Change detected in ${filename} (${eventType})`);
                triggerBuild();
            }
        });
    } catch (e) {
        console.error("Failed to watch client directory:", e);
    }

    // Watch PHP files (Non-recursive)
    [gamePhp, gameLobbyPhp].forEach(file => {
        fs.watch(file, (eventType, filename) => {
            triggerBuild();
        });
    });

    // Initial build to be safe
    rebuildBundles();
}

startWatch();
