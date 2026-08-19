<?php
/**
 * Universal Asset Optimizer for FieryVoid
 * Recursively converts PNG and JPG/JPEG files in public/img/ to WebP.
 *
 * See README "Image Optimiser". Run it from a browser on the live/test server after
 * uploading new artwork.
 *
 * 08.2026 HARDENING — why this file looks the way it does now.
 * This is the twin of generateStaticShipFileWeb.php, and it had none of that script's
 * protections while doing work that is far heavier per request. Four things were wrong:
 *
 *  1. $forceRebuild was hardcoded true, so every run re-encoded all ~2500 images even when
 *     not one had changed — ~50 chunk requests of pure waste. It is now opt-in via ?force=1
 *     and a normal run converts only images whose .webp is missing or older than the source.
 *  2. No memory_limit, no time limit and no Imagick resource limits. Imagick allocates in C,
 *     OUTSIDE php.ini's memory_limit, so PHP's own accounting never sees it — but LiteSpeed's
 *     lsphp Memory Hard Limit is checked against process RSS and does. On a persistent lsphp
 *     worker that bloat carries over between requests, which is the same mechanism that used
 *     to kill the ship generator (see its header comment). Capping Imagick explicitly is the
 *     only way to bound it, and RESOURCETYPE_THREAD matters most: ImageMagick's OpenMP will
 *     otherwise spawn a thread per core and multiply the footprint on a shared box.
 *  3. The whole 2500-entry directory tree was re-walked on EVERY chunk. The worklist is now
 *     built once and cached in the state file.
 *  4. A failed state write left the offset unmoved, so the client retried the identical chunk
 *     for ever. State-write failure is now a hard, reported error.
 */

// ─── Access gate ────────────────────────────────────────────────────────────────────────
// varconfig.php must be loaded at FILE scope before the gate runs — this script deliberately
// does not pull in global.php (it needs no DB, and global.php's HTML error handler would
// corrupt the JSON responses below), so it loads varconfig itself. MaintenanceGate must not
// do that include on its own; see the contract note in that file.
require_once dirname(__DIR__) . '/server/varconfig.php';
require_once dirname(__DIR__) . '/server/lib/MaintenanceGate.php';
MaintenanceGate::requireAccess('Image optimiser');

// ─── Resource limits ────────────────────────────────────────────────────────────────────
// Finite, never '-1', for exactly the reason spelled out in generateStaticShipFileWeb.php:
// with no limit PHP sails past LiteSpeed's lsphp Memory Hard Limit and the worker is killed
// mid-request, producing an opaque 503 instead of a readable PHP error.
ini_set('memory_limit', '512M');
set_time_limit(180); // per chunk, not for the whole run

// Configuration
$sourceDir = __DIR__ . '/img';
$quality = 80;
$chunkSize = 50;

// Opt-in full rebuild. Default false: re-encoding an image whose source has not changed
// produces a byte-identical result for a lot of CPU.
$forceRebuild = isset($_GET['force']) && $_GET['force'] !== '0';

if (!extension_loaded('imagick')) {
    die("Error: Imagick extension not loaded.");
}

// Bound ImageMagick's own allocator. These are C-level limits; without them a single
// oversized source image can take the worker's RSS past the host's hard limit on its own.
if (method_exists('Imagick', 'setResourceLimit')) {
    if (defined('Imagick::RESOURCETYPE_MEMORY')) Imagick::setResourceLimit(Imagick::RESOURCETYPE_MEMORY, 256 * 1024 * 1024);
    if (defined('Imagick::RESOURCETYPE_MAP'))    Imagick::setResourceLimit(Imagick::RESOURCETYPE_MAP,    512 * 1024 * 1024);
    if (defined('Imagick::RESOURCETYPE_DISK'))   Imagick::setResourceLimit(Imagick::RESOURCETYPE_DISK,  1024 * 1024 * 1024);
    // One thread. OpenMP fan-out multiplies RSS for no useful gain on a shared host.
    if (defined('Imagick::RESOURCETYPE_THREAD')) Imagick::setResourceLimit(Imagick::RESOURCETYPE_THREAD, 1);
}

// ─── State ──────────────────────────────────────────────────────────────────────────────
// Kept in the system temp dir, not the web root. The old location
// (public/optimization_state.json) was a publicly fetchable URL, and it sat inside the very
// tree being scanned. The prefix mirrors server_load_guard.php's path-based isolation so
// /game/ and /testInstance/ on the same account cannot share a state file.
$stateFile = sys_get_temp_dir() . '/fv_imgopt_' . substr(md5(__DIR__), 0, 8) . '.json';

$freshState = static function () {
    return ['offset' => 0, 'completed' => false, 'files' => null, 'force' => null,
            'converted' => 0, 'errors' => 0, 'lastError' => ''];
};

$state = $freshState();
if (file_exists($stateFile)) {
    $decoded = json_decode((string)file_get_contents($stateFile), true);
    if (is_array($decoded)) {
        $state = array_merge($state, $decoded);
    }
}

if (isset($_GET['reset'])) {
    @unlink($stateFile);
    $state = $freshState();
}

// ─── Action: Process Chunk ──────────────────────────────────────────────────────────────
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    header('Cache-Control: no-store');

    $fail = static function (string $msg) {
        http_response_code(500);
        echo json_encode(['error' => $msg]);
        exit;
    };

    // Build the worklist ONCE per run and cache it. A run that changed its force setting
    // mid-flight would be comparing against a list built under the other rule, so that
    // restarts too.
    if (!is_array($state['files']) || $state['force'] !== $forceRebuild) {
        $allFiles = [];
        try {
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS)
            );
            foreach ($it as $file) {
                if ($file->isDir()) continue;
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, ['png', 'jpg', 'jpeg'], true)) continue;

                $src = $file->getPathname();
                $dst = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $src);

                // Skip anything already up to date. This is what turns a routine run from
                // ~2500 conversions into however many images actually changed.
                if (!$forceRebuild && file_exists($dst) && filemtime($dst) >= $file->getMTime()) {
                    continue;
                }
                $allFiles[] = $src;
            }
        } catch (Exception $e) {
            $fail('Could not scan ' . $sourceDir . ': ' . $e->getMessage());
        }

        sort($allFiles);
        $state = ['offset' => 0, 'completed' => false, 'files' => $allFiles,
                  'force' => $forceRebuild, 'converted' => 0, 'errors' => 0, 'lastError' => ''];
    }

    $allFiles   = $state['files'];
    $totalFiles = count($allFiles);

    // Nothing to do. Reported as a clean finish rather than dividing by zero — the old code
    // computed a percentage against $totalFiles unconditionally, which is a PHP 8
    // DivisionByZeroError (a fatal, not a warning) the moment the worklist comes back empty.
    if ($totalFiles === 0) {
        @unlink($stateFile);
        echo json_encode(['offset' => 0, 'total' => 0, 'finished' => true, 'percent' => 100,
                          'converted' => 0, 'errors' => 0, 'lastError' => '',
                          'message' => 'Everything is already up to date.']);
        exit;
    }

    $slice = array_slice($allFiles, $state['offset'], $chunkSize);

    foreach ($slice as $src) {
        $dst = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $src);
        $im = null;
        try {
            $im = new Imagick($src);
            $im->setImageFormat('webp');
            $im->setImageCompressionQuality($quality);
            if ($im->getImageAlphaChannel()) {
                $im->setOption('webp:lossless', 'false');
            }
            $im->writeImage($dst);
            $state['converted']++;
        } catch (Exception $e) {
            // Previously swallowed in an empty catch, so an image that failed every single
            // run was indistinguishable from one that succeeded. Counted and surfaced now.
            $state['errors']++;
            $state['lastError'] = basename($src) . ': ' . $e->getMessage();
        } finally {
            if ($im instanceof Imagick) {
                $im->clear();
                $im->destroy();
            }
        }
    }

    $newOffset  = $state['offset'] + count($slice);
    $isFinished = ($newOffset >= $totalFiles);

    $state['offset']    = $newOffset;
    $state['completed'] = $isFinished;

    if ($isFinished) {
        @unlink($stateFile);
    } elseif (file_put_contents($stateFile, json_encode($state), LOCK_EX) === false) {
        // Hard stop. If the offset cannot be persisted the next request repeats this exact
        // chunk, and the client loops on it for ever — which is both useless work and the
        // sort of sustained request pattern that gets an IP throttled.
        $fail('Could not write state file ' . $stateFile . ' — offset cannot advance, aborting.');
    }

    echo json_encode([
        "offset"    => $newOffset,
        "total"     => $totalFiles,
        "finished"  => $isFinished,
        "percent"   => round(($newOffset / $totalFiles) * 100, 2),
        "converted" => $state['converted'],
        "errors"    => $state['errors'],
        "lastError" => $state['lastError'],
    ]);
    exit;
}

// ─── Action: Display UI ─────────────────────────────────────────────────────────────────
$resumeOffset = (is_array($state['files']) && empty($state['completed'])) ? (int)$state['offset'] : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>FieryVoid Optimizer Auto-Pilot</title>
    <style>
        body { font-family: sans-serif; background: #0a161c; color: #eee; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #162a33; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 440px; text-align: center; }
        .progress-container { background: #000; border-radius: 20px; height: 10px; margin: 25px 0; overflow: hidden; }
        .progress-bar { background: #4a90e2; height: 100%; width: 0%; transition: width 0.3s; }
        button { background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background: #c0392b; }
        #status { margin-bottom: 10px; font-size: 14px; color: #999; }
        #detail { margin-top: 12px; font-size: 12px; color: #7f8c8d; min-height: 2.4em; }
        .cooldown { color: #f1c40f !important; font-weight: bold; }
        .blocked { color: #e74c3c !important; font-weight: bold; }
        .mode { font-size: 12px; color: #7f8c8d; margin-top: 18px; line-height: 1.6; }
        .mode a { color: #4a90e2; }
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
        <div id="detail"></div>
        <br>
        <button id="stopBtn">STOP OPTIMIZER</button>
        <p><small>Closing this tab will pause the process.</small></p>
        <p class="mode">
            Mode: <strong><?php echo $forceRebuild ? 'FULL REBUILD (re-encoding every image)' : 'incremental (changed images only)'; ?></strong><br>
            <?php if ($forceRebuild): ?>
                <a href="?">switch to incremental</a>
            <?php else: ?>
                <a href="?force=1" onclick="return confirm('A full rebuild re-encodes every image and is much heavier on the server. Continue?')">force a full rebuild</a>
            <?php endif; ?>
            &nbsp;|&nbsp; <a href="?reset=1">reset progress</a>
            <?php if ($resumeOffset > 0): ?><br>Resuming from image <?php echo $resumeOffset; ?>.<?php endif; ?>
        </p>
    </div>

    <script>
        let running = true;

        // Backoff state. The old version treated every non-503 failure identically: throw,
        // then retry in 5s, for ever, with no cap. If the server or a WAF had started
        // refusing us (a 403 from LiteSpeed per-client throttling is the case that actually
        // happens here), that loop kept knocking indefinitely — which on most throttlers
        // extends the ban rather than waiting it out. Now: capped exponential backoff, and
        // we give up and say so rather than hammering a door that is being held shut.
        const BASE_BACKOFF_MS = 5000;
        const MAX_BACKOFF_MS = 60000;
        const MAX_CONSECUTIVE_FAILURES = 6;
        let consecutiveFailures = 0;

        const el = (id) => document.getElementById(id);

        el('stopBtn').onclick = () => {
            running = false;
            el('status').className = '';
            el('status').innerText = 'Paused.';
        };

        function backoffMs() {
            return Math.min(BASE_BACKOFF_MS * Math.pow(2, consecutiveFailures - 1), MAX_BACKOFF_MS);
        }

        function giveUp(reason) {
            running = false;
            el('status').className = 'blocked';
            el('status').innerText = 'Stopped: ' + reason;
            el('detail').innerText =
                'Gave up after ' + MAX_CONSECUTIVE_FAILURES + ' consecutive failures rather than '
                + 'keep retrying. If this was a 403, the block is above PHP (LiteSpeed per-client '
                + 'throttling or the host WAF) — leave it a few minutes before retrying, and do '
                + 'not leave this tab retrying in the background.';
        }

        function handleFailure(label, retryAfterMs) {
            consecutiveFailures++;
            if (consecutiveFailures >= MAX_CONSECUTIVE_FAILURES) {
                giveUp(label);
                return;
            }
            const waitMs = retryAfterMs || backoffMs();
            el('status').className = 'cooldown';
            el('status').innerText = label + ' — retrying in ' + Math.round(waitMs / 1000)
                + 's (attempt ' + consecutiveFailures + '/' + MAX_CONSECUTIVE_FAILURES + ')';
            setTimeout(processNext, waitMs);
        }

        async function processNext() {
            if (!running) return;

            try {
                const response = await fetch('?ajax=1<?php echo $forceRebuild ? '&force=1' : ''; ?>', { credentials: 'same-origin' });

                // 503 = our own load guard, or the server shedding load. It sends no
                // Retry-After, so the 10s default below is what actually applies in practice.
                if (response.status === 503) {
                    const retryAfter = parseInt(response.headers.get('Retry-After')) || 10;
                    handleFailure('Rate limited (503)', retryAfter * 1000);
                    return;
                }

                // 403/429 = something ABOVE the application refused us. Nothing in FieryVoid
                // emits a 403, so this is the web server, a WAF or a CDN, and hammering it is
                // the one guaranteed way to make it worse.
                if (response.status === 403 || response.status === 429) {
                    handleFailure('Blocked by the server (' + response.status + ')', null);
                    return;
                }

                if (!response.ok) {
                    handleFailure('Server error (' + response.status + ')', null);
                    return;
                }

                const data = await response.json();

                if (data.error) {
                    giveUp(data.error);
                    return;
                }

                consecutiveFailures = 0;
                el('status').className = '';
                el('bar').style.width = data.percent + '%';
                el('stats').innerText = data.offset + ' / ' + data.total;
                el('detail').innerText =
                    'converted ' + data.converted + ', errors ' + data.errors
                    + (data.lastError ? ' — last: ' + data.lastError : '');
                el('status').innerText = data.finished
                    ? (data.message || 'Complete!')
                    : 'Processing chunk...';

                if (!data.finished && running) {
                    setTimeout(processNext, 200);
                } else if (data.finished) {
                    el('bar').style.width = '100%';
                    running = false;
                }
            } catch (e) {
                console.error(e);
                handleFailure('Network or parse error', null);
            }
        }

        processNext();
    </script>
</body>
</html>
