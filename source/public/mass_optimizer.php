<?php
/**
 * Universal Asset Optimizer for FieryVoid (Auto-Pilot Version)
 * Recursively converts PNG and JPG/JPEG files in public/img/ to WebP.
 */

// Configuration
$sourceDir = __DIR__ . '/img';
$quality = 80;
$chunkSize = 50; 
$forceRebuild = true; 

if (!extension_loaded('imagick')) {
    die("Error: Imagick extension not loaded.");
}

$stateFile = __DIR__ . '/optimization_state.json';
$state = file_exists($stateFile) ? json_decode(file_get_contents($stateFile), true) : ['offset' => 0, 'completed' => false];

if (isset($_GET['reset'])) {
    $state = ['offset' => 0, 'completed' => false];
    file_put_contents($stateFile, json_encode($state));
}

// Action: Process Chunk
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $allFiles = [];
    $it = new RecursiveDirectoryIterator($sourceDir);
    $it = new RecursiveIteratorIterator($it);

    foreach ($it as $file) {
        if ($file->isDir()) continue;
        $ext = strtolower($file->getExtension());
        if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
            $allFiles[] = $file->getPathname();
        }
    }

    sort($allFiles);
    $totalFiles = count($allFiles);
    $slice = array_slice($allFiles, $state['offset'], $chunkSize);
    $processedCount = 0;

    foreach ($slice as $src) {
        $dst = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $src);
        if ($forceRebuild || !file_exists($dst)) {
            try {
                $im = new Imagick($src);
                $im->setImageFormat('webp');
                $im->setImageCompressionQuality($quality);
                if ($im->getImageAlphaChannel()) {
                    $im->setOption('webp:lossless', 'false');
                }
                $im->writeImage($dst);
                $im->clear();
                $im->destroy();
            } catch (Exception $e) {}
        }
        $processedCount++;
    }

    $newOffset = $state['offset'] + count($slice);
    $isFinished = ($newOffset >= $totalFiles);

    $state = ['offset' => $newOffset, 'completed' => $isFinished];
    file_put_contents($stateFile, json_encode($state));

    if ($isFinished) @unlink($stateFile);

    echo json_encode([
        "offset" => $newOffset,
        "total" => $totalFiles,
        "finished" => $isFinished,
        "percent" => round(($newOffset / $totalFiles) * 100, 2)
    ]);
    exit;
}

// Action: Display UI
?>
<!DOCTYPE html>
<html>
<head>
    <title>FieryVoid Optimizer Auto-Pilot</title>
    <style>
        body { font-family: sans-serif; background: #0a161c; color: #eee; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: #162a33; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 400px; text-align: center; }
        .progress-container { background: #000; border-radius: 20px; height: 10px; margin: 25px 0; overflow: hidden; }
        .progress-bar { background: #4a90e2; height: 100%; width: 0%; transition: width 0.3s; }
        button { background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background: #c0392b; }
        #status { margin-bottom: 10px; font-size: 14px; color: #999; }
        .cooldown { color: #f1c40f !important; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Optimizing Assets...</h2>
        <div id="status">Starting...</div>
        <div class="progress-container">
            <div id="bar" class="progress-bar"></div>
        </div>
        <div id="stats">0 / 0</div>
        <br>
        <button id="stopBtn">STOP OPTIMIZER</button>
        <p><small>Closing this tab will pause the process.</small></p>
    </div>

    <script>
        let running = true;
        document.getElementById('stopBtn').onclick = () => { running = false; document.getElementById('status').innerText = 'Paused.'; };

        async function processNext() {
            if (!running) return;

            try {
                const response = await fetch('?ajax=1');
                
                if (response.status === 503) {
                    const retryAfter = parseInt(response.headers.get('Retry-After')) || 10;
                    document.getElementById('status').innerText = `Rate Limited! Cooldown for ${retryAfter}s...`;
                    document.getElementById('status').className = 'cooldown';
                    setTimeout(processNext, retryAfter * 1000);
                    return;
                }

                if (!response.ok) throw new Error('Server Error');

                const data = await response.json();
                document.getElementById('status').className = '';
                document.getElementById('bar').style.width = data.percent + '%';
                document.getElementById('stats').innerText = `${data.offset} / ${data.total}`;
                document.getElementById('status').innerText = data.finished ? 'Complete!' : 'Processing chunk...';

                if (!data.finished && running) {
                    setTimeout(processNext, 200); // 200ms delay for safety
                } else if (data.finished) {
                    alert('Optimization Complete!');
                }
            } catch (e) {
                console.error(e);
                document.getElementById('status').innerText = 'Error occurred. retrying in 5s...';
                setTimeout(processNext, 5000);
            }
        }

        processNext();
    </script>
</body>
</html>
