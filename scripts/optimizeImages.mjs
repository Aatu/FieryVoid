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

                if (optimizedBuffer.length < originalBuffer.length) {
                    await fs.writeFile(file, optimizedBuffer);
                    savedBytes += (originalBuffer.length - optimizedBuffer.length);
                    // console.log(`Optimized: ${path.relative(ROOT_DIR, file)} (-${((originalBuffer.length - optimizedBuffer.length) / 1024).toFixed(2)} KB)`);
                } else {
                    // console.log(`Skipped (no improvement): ${path.relative(ROOT_DIR, file)}`);
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
