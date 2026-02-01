import imagemin from 'imagemin';
import imageminMozjpeg from 'imagemin-mozjpeg';
import imageminPngquant from 'imagemin-pngquant';
import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT_DIR = path.resolve(__dirname, '../source/public/img');

async function getFiles(dir) {
    const dirents = await fs.readdir(dir, { withFileTypes: true });
    const files = await Promise.all(dirents.map((dirent) => {
        const res = path.resolve(dir, dirent.name);
        return dirent.isDirectory() ? getFiles(res) : res;
    }));
    return files.flat();
}

async function optimize() {
    console.log('Scanning files...');
    try {
        const allFiles = await getFiles(ROOT_DIR);
        const images = allFiles.filter(f => /\.(png|jpg|jpeg)$/i.test(f));

        console.log(`Found ${images.length} images. Starting optimization...`);

        let savedBytes = 0;
        let processedCount = 0;
        let errorCount = 0;

        const MIN_SAVINGS_PERCENT = 10; // Only save if we reduce size by at least 10%

        for (const file of images) {
            try {
                const originalBuffer = await fs.readFile(file);
                const optimizedBuffer = await imagemin.buffer(originalBuffer, {
                    plugins: [
                        imageminMozjpeg({ quality: 75 }),
                        imageminPngquant({
                            quality: [0.6, 0.8]
                        })
                    ]
                });

                const savings = originalBuffer.length - optimizedBuffer.length;
                const savingsPercent = (savings / originalBuffer.length) * 100;

                if (savingsPercent >= MIN_SAVINGS_PERCENT) {
                    await fs.writeFile(file, optimizedBuffer);
                    savedBytes += savings;
                    process.stdout.write(`\nOptimized: ${path.relative(ROOT_DIR, file)} (-${savingsPercent.toFixed(1)}% / -${(savings / 1024).toFixed(2)} KB)`);
                } else {
                    // console.log(`Skipped (already optimized): ${path.relative(ROOT_DIR, file)}`);
                }
            } catch (err) {
                console.error(`Error processing ${file}:`, err);
                errorCount++;
            }
            process.stdout.write(`\rProcessed ${++processedCount}/${images.length} images...`);
        }

        console.log('\nOptimization complete.');
        console.log(`Total space saved: ${(savedBytes / 1024 / 1024).toFixed(2)} MB`);
        if (errorCount > 0) {
            console.log(`Errors encountered: ${errorCount}`);
        }
    } catch (err) {
        console.error("Fatal error:", err);
    }
}

optimize();
