<?php
declare(strict_types=1);
// Optimised version: Bundles all ships into one file to avoid HTTP/2 request overload
// 12.2025 - Refactored to generate single shipsCombined.js
// 04.2026 - Added compactShipForStaticJson() to strip default/empty values (Optimisation #4)
// 09.2026 - Runs as a series of short AJAX steps driven by its own page (see "Why steps" below)
/**
 * generateStaticShipFileWeb.php
 * Generates the per-faction static ship JSON (+ pre-compressed .br) used by the Game Lobby.
 * Updated for PHP 8 + Apache + Brotli/Gzip
 */

//// ─── Output Compression ─────────────────────────────────────────────
// Compression is left ENTIRELY to global.php, which starts its own buffer and registers
// fv_compress_output() as a shutdown function.
//
// This file used to open an ob_gzhandler buffer HERE, i.e. BEFORE global.php, which made it
// the OUTER buffer — the opposite nesting to every other script that uses ob_gzhandler
// (gamedata.php, chatdata.php, playerChatInfo.php all open theirs AFTER global.php, so the
// gz buffer is the inner one and fv_compress_output correctly sees Content-Encoding already
// set and passes the body straight through).
//
// With the nesting inverted the response was compressed TWICE: at shutdown
// fv_compress_output() popped global.php's inner buffer, brotli-compressed it, sent
// 'Content-Encoding: br' plus an explicit Content-Length, and echoed the brotli bytes into
// the still-open ob_gzhandler buffer — which then gzipped those brotli bytes, overwrote the
// header with 'Content-Encoding: gzip', and left the now-wrong Content-Length in place. The
// client was told "gzip" for a body that gunzips to brotli, with a length that matches
// neither. Deleting the outer buffer restores the single, correct compression pass.

//// ─── Why steps (09.2026) ────────────────────────────────────────────
// Opening this URL no longer does the work in one request. It renders a small page, and that
// page drives the job as a series of AJAX steps against this same file: ~200 ship classes per
// step, then ~15 faction files per step, then the report. Same pattern as
// source/public/mass_optimizer.php.
//
// The single-request version kept producing an intermittent 503 after the 07.2026 memory fix.
// That 503 was not FieryVoid's: the only 503 in the app (server_load_guard.php) is sent after
// global.php has registered fv_compress_output(), so it always carries
// "Cache-Control: private, must-revalidate" and "text/html; charset=UTF-8". The failing
// response had neither, and said "server: nginx". So the layer above PHP (nginx in front,
// LiteSpeed/lsphp behind it) cut the request off, and the one-request shape was the worst
// possible one to hand it:
//   - Big. Measured 09.2026 in Docker with OPcache on (as live), 2582 ships: ~250MB process
//     RSS, while memory_get_peak_usage() — the old "Peak memory" line — said 83MB, because the
//     compiled classes live in OPcache shared memory, which the RSS counts and PHP's own
//     figure does not. It also grows with every ship added (the OPcache-off peak went from
//     168MB in 07.2026 to 212MB in 09.2026).
//   - Silent. global.php buffers the whole page, so not one byte left the server until the
//     run was over.
//   - Holding the session lock throughout (global.php's session_start() was never released),
//     so every other FV tab in the same browser queued behind it, each waiting request holding
//     a PHP worker of its own.
// Steps address all three. And a step that still gets a 503 is simply retried by the page:
// every step is idempotent because the SERVER owns the cursor and saves it only once that
// step's work is on disk. A refused step now costs one retry, not the whole run.

// ----------------------
// Resource Limits
// ----------------------
// NOTE: memory_limit is deliberately FINITE, not '-1'.
//
// This host runs LiteSpeed, whose persistent lsphp workers have their own Memory
// Hard Limit (configured in the LiteSpeed/panel config, NOT php.ini). With
// memory_limit='-1' PHP will happily balloon past that limit, at which point LSWS
// kills the worker mid-request and the browser gets an opaque 503. A finite limit
// makes PHP fail first, with a real, readable "allowed memory exhausted" error and
// a line number, instead of the process being SIGKILLed out from under us.
ini_set('memory_limit', '1024M');
set_time_limit(120); // per STEP, not for the whole run

// ----------------------
// Progress + memory log (survives a worker kill) — DISABLED by default
// ----------------------
// Diagnostic instrumentation from the 2026-07 intermittent-503 investigation. It is
// OFF; flip GEN_MEMORY_LOG to true to turn it back on. Leave it off in normal use —
// it writes a file on every deploy and we don't need it once the generator is healthy.
//
// Turn it ON if a step starts dying repeatedly. If LiteSpeed kills the lsphp worker on its
// memory hard limit, the response is discarded and any echo'd diagnostics are lost with it —
// so this appends progress to a file instead. After a failure, read
// source/public/static/generator-log.txt: the LAST line tells you which step was running and
// how much memory was in use when the process died, and each step's 'boot' rss= tells you
// whether the worker came in already bloated from an earlier request.
define('GEN_MEMORY_LOG', false);

$genLogFile = __DIR__ . '/source/public/static/generator-log.txt';
$genLogStart = microtime(true);

// A /proc/self/status figure in MB, or -1 off Linux. VmRSS is the process RSS as the OS sees
// it — the figure LiteSpeed's lsphp Memory Hard Limit is checked against, not php.ini's
// memory_limit and not memory_get_usage(), which only reports PHP's own arena and is reset
// per request.
$procStatusMb = static function (string $field): float {
    if (!@is_readable('/proc/self/status')) {
        return -1.0; // non-Linux (e.g. Windows dev box)
    }
    $status = @file_get_contents('/proc/self/status');
    if ($status !== false && preg_match('/^' . $field . ':\s+(\d+)\s+kB/mi', $status, $m)) {
        return ((float)$m[1]) / 1024.0; // kB -> MB
    }
    return -1.0;
};

$genLog = function (string $msg) use ($genLogFile, $genLogStart, $procStatusMb): void {
    if (!GEN_MEMORY_LOG) {
        return; // instrumentation disabled — all $genLog() call sites become no-ops
    }
    $rss = $procStatusMb('VmRSS');
    $line = sprintf(
        "[%s] +%05.1fs  rss=%s  cur=%6.1fMB  peak=%6.1fMB  %s\n",
        date('Y-m-d H:i:s'),
        microtime(true) - $genLogStart,
        $rss < 0 ? '   n/a  ' : sprintf('%6.1fMB', $rss),
        memory_get_usage(true) / 1048576,
        memory_get_peak_usage(true) / 1048576,
        $msg
    );
    file_put_contents($genLogFile, $line, FILE_APPEND | LOCK_EX);
};

$genLog('boot (before includes) ' . (isset($_GET['step']) ? 'step' : 'page'));

//// ─── Includes ──────────────────────────────────────────────────────
define('IN_STATIC_GENERATION', true);
require_once __DIR__ . '/source/public/global.php';

// Access gate. This URL is publicly reachable and starts a job that instantiates every ship
// in the game and rewrites ~180 files, so it must not be runnable by anyone who stumbles onto
// it. CLI is exempt inside requireAccess(), and the check runs straight after global.php
// because that is what loads varconfig.php (where the key lives). See MaintenanceGate for why
// it must not load varconfig itself. The page's own AJAX steps carry no key: the gate
// remembers the first, keyed page load in the session.
require_once __DIR__ . '/source/server/lib/MaintenanceGate.php';
MaintenanceGate::requireAccess('Static ship file generator');

// Nothing below reads or writes the session, and holding its lock would stall every other FV
// tab in this browser (lobby and game polls all session_start()) until this request ends.
// The gate has already stored its flag, and this is what persists it.
session_write_close();

// Compaction + brotli pre-compression now live in ONE shared place (ShipCompactor) so
// this generator and generateStaticShipFile.php can no longer drift apart. They already
// HAD drifted: eight keys were stripped on the dev box and kept live until 2026-08-01,
// so the live server shipped a bigger file to every visitor than the dev box produced.
// A comment cannot enforce that invariant; one shared file can.
//
// Explicit require_once: source/autoload.php is an autogenerated classmap ("do not edit").
require_once __DIR__ . '/source/server/lib/ShipCompactor.php';

//// ─── Config ────────────────────────────────────────────────────────
$jsonDir = __DIR__ . '/source/public/static/json';
if (!is_dir($jsonDir)) {
    mkdir($jsonDir, 0777, true);
}

$encodeFlags = JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE;

$shipsPerStep    = 200; // ship classes instantiated per request
$factionsPerStep = 15;  // faction files assembled + brotli'd per request

// Run state lives in the system temp dir (where the fragments always lived), one sub-directory
// per run. The prefix mirrors server_load_guard.php's path-based isolation, so /game/ and
// /testInstance/ on the same account can never share a run.
$runRoot = sys_get_temp_dir() . '/fv_shipgen_' . substr(md5(__DIR__), 0, 8);

function fvgen_removeDir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    foreach (scandir($dir) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $path = $dir . '/' . $entry;
        is_dir($path) ? fvgen_removeDir($path) : unlink($path);
    }
    rmdir($dir);
}

// Written to a temp name and renamed into place, so a worker killed mid-write leaves the
// previous (valid) state behind and the retried step starts from it.
function fvgen_saveState(string $stateFile, array $state): void
{
    $tmp = $stateFile . '.tmp';
    if (file_put_contents($tmp, json_encode($state, JSON_THROW_ON_ERROR)) === false || !rename($tmp, $stateFile)) {
        throw new RuntimeException('Could not write run state ' . $stateFile);
    }
}

// One fragment per faction per ship step, holding that step's comma-separated
// "phpclass":{...} entries for the faction. The step number is in the name, so a retried step
// overwrites exactly its own fragments and nothing else.
function fvgen_fragPath(string $runDir, string $faction, int $chunk): string
{
    return $runDir . '/' . md5($faction) . '.' . sprintf('%04d', $chunk) . '.frag';
}

//// ─── Page load: start a run and hand the page its driver ───────────
if (!isset($_GET['step'])) {
    // Opening (or reloading) the page STARTS a run. Any earlier run — finished, abandoned, or
    // still going in another tab — is discarded; that tab's next step gets a 409 and stops.
    if (is_dir($runRoot)) {
        foreach (glob($runRoot . '/*', GLOB_ONLYDIR) ?: [] as $oldRun) {
            fvgen_removeDir($oldRun);
        }
    } else {
        mkdir($runRoot, 0777, true);
    }

    // The class list is read ONCE and frozen into the run. It comes from readdir(), whose
    // order is the filesystem's, and that order decides the ship ids — so every step must work
    // through the same list rather than re-reading the directory.
    $names = ShipLoader::getShipClassnamesStatic();
    if (!$names) {
        exit("<b>Error:</b> No ships found.");
    }

    $token = bin2hex(random_bytes(8));
    $runDir = $runRoot . '/' . $token;
    mkdir($runDir, 0777, true);
    fvgen_saveState($runDir . '/state.json', [
        'names'     => array_values($names),
        'phase'     => 'ships',  // ships -> assemble -> report -> done
        'nextName'  => 0,        // index into names of the next class to build
        'nextId'    => 0,        // MUST mirror getAllShipsStatic()'s $count — it becomes the ship id
        'chunk'     => 0,        // ship-step counter; names that step's fragments
        'shipCount' => 0,
        'factions'  => [],       // [['name' => ..., 'chunks' => [int, ...]], ...] in first-seen order
        'assembled' => 0,        // factions whose JSON has been written
        'br'        => ['count' => 0, 'raw' => 0, 'comp' => 0,
                        'sampleRaw' => 0, 'sampleBr' => 0, 'sampleQ4' => 0, 'sampleName' => ''],
        'steps'     => 0,
        'peakPhpMb' => 0.0,
        'peakRssMb' => -1.0,     // stays -1 where /proc is unavailable
        'started'   => microtime(true),
        'report'    => '',
    ]);

    if (GEN_MEMORY_LOG) {
        file_put_contents($genLogFile, "\n=== generator run $token started (pid " . getmypid() . ") ===\n", FILE_APPEND | LOCK_EX);
    }
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Static Ship Generator</title>
    <style>
        body { font-family: sans-serif; background: #0a161c; color: #eee; margin: 0; padding: 24px; }
        .card { background: #162a33; padding: 24px 32px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); max-width: 640px; margin: 0 auto 24px; }
        h2 { margin-top: 0; }
        .progress-container { background: #000; border-radius: 20px; height: 10px; margin: 18px 0; overflow: hidden; }
        .progress-bar { background: #4a90e2; height: 100%; width: 0%; transition: width 0.3s; }
        #status { font-size: 15px; }
        #stats, #detail, .note { font-size: 13px; color: #9aa9b0; margin-top: 8px; }
        .cooldown { color: #f1c40f !important; font-weight: bold; }
        .blocked { color: #e74c3c !important; font-weight: bold; }
        .done { color: #2ecc71 !important; font-weight: bold; }
        #report { max-width: 900px; margin: 0 auto 24px; font-size: 14px; line-height: 1.5; }
        #report:empty { display: none; }
        #log { max-width: 900px; margin: 0 auto; font-size: 13px; line-height: 1.5; }
        #log .faction { margin-top: 10px; }
        #log .ships { color: #9aa9b0; }
        code { color: #f1c40f; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Generating static ship files</h2>
        <div id="status">Starting…</div>
        <div class="progress-container"><div id="bar" class="progress-bar"></div></div>
        <div id="stats"></div>
        <div id="detail"></div>
        <p class="note">Keep this tab open until it says finished. Reloading the page starts a fresh run.</p>
    </div>
    <div id="report"></div>
    <div id="log"></div>

    <script>
        const RUN = <?php echo json_encode($token); ?>;

        // Retry policy. Each step is idempotent (the server only advances its cursor once the
        // step's work is on disk), so a step the server refused or killed is safe to repeat.
        // Capped exponential backoff, then give up and say so rather than keep knocking — see
        // mass_optimizer.php for why hammering a refusing server makes things worse.
        const BASE_BACKOFF_MS = 3000;
        const MAX_BACKOFF_MS = 30000;
        const MAX_CONSECUTIVE_FAILURES = 6;
        let consecutiveFailures = 0;
        const retried = [];   // every failed attempt, reported at the end
        const startedAt = Date.now();

        const el = (id) => document.getElementById(id);
        const factionRows = {};

        function setStatus(text, cls) {
            el('status').className = cls || '';
            el('status').textContent = text;
        }

        function snippet(text) {
            return (text || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 300);
        }

        function giveUp(reason) {
            setStatus('Stopped: ' + reason, 'blocked');
            el('detail').textContent = 'Nothing further was changed. Files written by earlier steps '
                + 'are complete in themselves; reload the page to run the whole job again.';
        }

        function handleFailure(label, detail) {
            consecutiveFailures++;
            retried.push(label);
            if (consecutiveFailures >= MAX_CONSECUTIVE_FAILURES) {
                giveUp(label + ' — ' + MAX_CONSECUTIVE_FAILURES + ' times in a row');
                el('detail').textContent += (detail ? ' Last response: ' + detail : '');
                return;
            }
            const waitMs = Math.min(BASE_BACKOFF_MS * Math.pow(2, consecutiveFailures - 1), MAX_BACKOFF_MS);
            setStatus(label + ' — retrying this step in ' + Math.round(waitMs / 1000) + 's (attempt '
                + consecutiveFailures + '/' + MAX_CONSECUTIVE_FAILURES + ')', 'cooldown');
            el('detail').textContent = detail || '';
            setTimeout(runStep, waitMs);
        }

        function appendListing(listing) {
            for (const [faction, ships] of listing) {
                let row = factionRows[faction];
                if (!row) {
                    row = document.createElement('div');
                    row.className = 'faction';
                    const name = document.createElement('strong');
                    name.textContent = faction;
                    const list = document.createElement('div');
                    list.className = 'ships';
                    row.append(name, list);
                    el('log').append(row);
                    factionRows[faction] = row = { list: list, count: 0 };
                }
                row.list.textContent += (row.count ? ', ' : '') + ships.join(', ');
                row.count += ships.length;
            }
        }

        function showProgress(data) {
            let pct;
            if (data.phase === 'ships') {
                pct = 85 * data.nextName / data.totalNames;
                setStatus('Building ships…');
            } else if (data.phase === 'assemble') {
                pct = 85 + 14 * data.assembled / Math.max(1, data.totalFactions);
                setStatus('Writing faction files…');
            } else {
                pct = 99;
                setStatus('Finishing…');
            }
            el('bar').style.width = pct.toFixed(1) + '%';
            el('stats').textContent = data.shipCount + ' ships, ' + data.totalFactions + ' factions'
                + ' — faction files written: ' + data.assembled + ' / ' + data.totalFactions;
            el('detail').textContent = '';
        }

        async function runStep() {
            let response, text;
            try {
                response = await fetch('?step=1&run=' + encodeURIComponent(RUN),
                    { credentials: 'same-origin', cache: 'no-store' });
                text = await response.text();
            } catch (e) {
                handleFailure('Network error', e.message);
                return;
            }

            let data = null;
            try { data = JSON.parse(text); } catch (e) { /* not JSON: see below */ }

            // Our own refusals (superseded run, thrown PHP error) are not worth repeating.
            if (data && data.fatal) {
                giveUp(data.error);
                return;
            }
            // MaintenanceGate answers 404 when the session no longer holds the key.
            if (response.status === 404) {
                giveUp('access refused (404) — open the page again with ?key=');
                return;
            }

            // Anything else that is not a clean JSON answer — a 503/502/504 from the server
            // layer, or a PHP fatal such as memory exhaustion printed as HTML — is retried.
            if (!response.ok || !data) {
                handleFailure('HTTP ' + response.status + (data && data.error ? ' (' + data.error + ')' : ''),
                    data ? '' : snippet(text));
                return;
            }

            consecutiveFailures = 0;
            if (data.listing) appendListing(data.listing);

            if (data.phase === 'done') {
                el('bar').style.width = '100%';
                setStatus('Finished — ' + data.shipCount + ' ships generated.', 'done');
                el('stats').textContent = data.steps + ' steps in ' + Math.round((Date.now() - startedAt) / 1000) + 's'
                    + (retried.length ? ' — ' + retried.length + ' step attempt(s) had to be retried: ' + retried.join(', ')
                                      : ' — no step needed a retry.');
                el('detail').textContent = '';
                el('report').innerHTML = data.report;
                return;
            }

            showProgress(data);
            setTimeout(runStep, 150);
        }

        runStep();
    </script>
</body>
</html>
<?php
    exit;
}

//// ─── AJAX step ─────────────────────────────────────────────────────
header('Content-Type: application/json');
header('Cache-Control: no-store');

$respond = static function (int $code, array $body): void {
    http_response_code($code);
    echo json_encode($body, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
};

$token = (string)($_GET['run'] ?? '');
$runDir = $runRoot . '/' . $token;
$stateFile = $runDir . '/state.json';
if (!preg_match('/^[0-9a-f]{16}$/', $token) || !is_file($stateFile)) {
    $respond(409, ['fatal' => true, 'error' => 'this run is no longer current (the generator was '
        . 'opened again somewhere, or its temp files were cleared). Reload the page to start a new run.']);
}
$state = json_decode((string)file_get_contents($stateFile), true);
if (!is_array($state)) {
    $respond(500, ['fatal' => true, 'error' => 'run state is unreadable — reload the page to start a new run.']);
}
$genLog("step {$state['steps']} ({$state['phase']}) start");

$listing = null;
$phaseAtStart = $state['phase'];

try {
    //// ─── Phase 1: ships ────────────────────────────────────────────
    //
    // We deliberately do NOT call ShipLoader::getAllShipsStatic(null) here.
    //
    // That helper instantiates every ship class in the game (~2600 ships, each with its
    // full system list, beforeTurn(), enhancement options and notesFill()) and returns
    // them ALL at once — ~350MB in one call when measured in 07.2026. Nothing here needs all
    // the ships simultaneously, only one at a time, long enough to encode it. So we stream:
    // instantiate → encode → discard, spooling the compact JSON text to fragments on disk.
    //
    // This replicates getAllShipsStatic()'s semantics EXACTLY so the output is unchanged:
    //   - same class-name iteration order (getShipClassnamesStatic), frozen at page load
    //   - $count incremented once per existing class, and used as the ship id — carried
    //     from step to step in the run state
    //   - same construction args, beforeTurn() on every system, setEnhancementOptions(),
    //     notesFill()
    //   - factions keyed in first-seen order
    if ($state['phase'] === 'ships') {
        $names = $state['names'];
        $end = min(count($names), $state['nextName'] + $shipsPerStep);
        $chunk = $state['chunk'];
        $count = $state['nextId'];

        $factionIndex = []; // faction => index into $state['factions']
        foreach ($state['factions'] as $i => $f) {
            $factionIndex[$f['name']] = $i;
        }
        $fragHandles  = []; // faction => this step's fragment handle
        $factionFirst = []; // faction => is the next ship the first in this step's fragment?
        $listing      = []; // [[faction, [phpclass, ...]], ...] for the page
        $listingIndex = [];

        for ($i = $state['nextName']; $i < $end; $i++) {
            $name = $names[$i];
            if (!class_exists($name)) {
                continue;
            }
            $count++;

            $ship = new $name($count, 0, "", 0, 0, false, false, array());
            if (!($ship instanceof BaseShip)) {
                unset($ship);
                continue;
            }

            foreach ($ship->systems as $system) {
                $system->beforeTurn($ship, 0, 0);
            }
            Enhancements::setEnhancementOptions($ship); // enhancements (for fleet selection)
            $ship->notesFill();

            $faction = (string)$ship->faction;

            if (!isset($factionIndex[$faction])) {
                $factionIndex[$faction] = count($state['factions']);
                $state['factions'][] = ['name' => $faction, 'chunks' => []];
            }
            if (!isset($fragHandles[$faction])) {
                $fragHandles[$faction] = fopen(fvgen_fragPath($runDir, $faction, $chunk), 'wb');
                $factionFirst[$faction] = true;
                $state['factions'][$factionIndex[$faction]]['chunks'][] = $chunk;
            }

            // Write "phpclass":{...} — assembling the object map by hand, comma-separated.
            $shipJson = json_encode(ShipCompactor::compactShipObject($ship), $encodeFlags);
            fwrite(
                $fragHandles[$faction],
                ($factionFirst[$faction] ? '' : ',')
                . json_encode((string)$ship->phpclass, JSON_UNESCAPED_UNICODE)
                . ':' . $shipJson
            );
            $factionFirst[$faction] = false;
            $state['shipCount']++;

            if (!isset($listingIndex[$faction])) {
                $listingIndex[$faction] = count($listing);
                $listing[] = [$faction, []];
            }
            $listing[$listingIndex[$faction]][1][] = (string)$ship->phpclass;

            // Drop everything for this ship before moving to the next one: at no moment do we
            // hold more than a single ship's object graph.
            unset($ship, $shipJson, $system);
        }

        foreach ($fragHandles as $handle) {
            fclose($handle);
        }

        $state['nextName'] = $end;
        $state['nextId'] = $count;
        $state['chunk'] = $chunk + 1;
        if ($end >= count($names)) {
            if ($state['shipCount'] === 0) {
                throw new RuntimeException('No ships were generated.');
            }
            $state['phase'] = 'assemble';
        }

    //// ─── Phase 2: faction files ────────────────────────────────────
    // Each faction's fragments are copied through in step order (stream_copy_to_stream), so
    // even a large faction is never fully resident in PHP memory.
    //
    /* ─── shipsCombined.js generation DISABLED (08.2026) ─────────────────────────────────
     *
     * NOTHING LOADS IT. static/ships.php (the <script> tag that pulled it in) is included by
     * no page: both call sites were commented out in Dec 2025 —
     *     source/public/game.php:105       //include 'static/ships.php';
     *     source/public/gamelobby.php:128  //include 'static/ships.php';
     * A search for "shipsCombined" across every .php/.js/.html and the built bundles finds
     * only those two commented lines. The file was ~99MB, rebuilt on every deploy, and served
     * to nobody.
     *
     * WHAT REPLACED IT (both still produced):
     *   - Lobby : per-faction static/json/<faction>.json below, fetched on demand via
     *             gamelobbyloader.php with a ?v=<filemtime> cache-buster.
     *   - Game  : game.php builds window.staticShips INLINE per request from
     *             ShipLoader::getShipsByClass(), covering only the classes in that game.
     *
     * TO RESTORE: the last single-request version of this file still has the three blocks
     * marked [shipsCombined] (git show a03dd732d:generateStaticShipFileWeb.php), as does
     * generateStaticShipFile.php. In this stepped version the bundle would be truncated and
     * seeded in the first assemble step, appended to per faction (fopen 'ab') in each one,
     * and closed + pre-compressed in the report step. Then re-enable the include in game.php /
     * gamelobby.php.
     *
     * ALSO NEEDED IF RESTORING: source/public/.htaccess no longer has the rule that serves a
     * pre-compressed .js.br. It was removed in 08.2026 because retiring this bundle left it
     * with nothing to act on — a plain static .js is the only thing it ever matched, and there
     * are none being pre-compressed. Restoring the bundle without it means visitors get the
     * mod_deflate gzip copy (~5x bigger than the .br sitting next to it). The rule was:
     *
     *   <IfModule mod_rewrite.c>
     *     RewriteEngine On
     *     RewriteCond %{HTTP:Accept-Encoding} br
     *     RewriteCond %{REQUEST_FILENAME}.br -f
     *     RewriteRule ^(.+\.js)$ $1.br [QSA,L]
     *   </IfModule>
     *   <FilesMatch "\.js\.br$">
     *     ForceType application/javascript
     *     SetEnv no-gzip 1
     *     <IfModule mod_headers.c>
     *       Header set Content-Encoding br
     *       Header append Vary Accept-Encoding
     *       Header set X-LiteSpeed-No-Gzip "1"
     *     </IfModule>
     *   </FilesMatch>
     *
     * and the bundle cache rule needs to become <FilesMatch "\.bundle\.js(\.br)?$"> so a
     * rewritten .br keeps its immutable Cache-Control instead of falling back to 1 month.
     *
     * (None of this affects the per-faction JSON: gamelobbyloader.php serves that .br itself
     * in PHP and never depended on .htaccess.)
     */
    } elseif ($state['phase'] === 'assemble') {
        $factions = $state['factions'];
        $end = min(count($factions), $state['assembled'] + $factionsPerStep);
        $br = $state['br'];

        for ($i = $state['assembled']; $i < $end; $i++) {
            $factionName = $factions[$i]['name'];

            // Per-faction JSON for server-side caching (Game Lobby): {"Faction":{ ...fragments... }}
            $jsonPath   = $jsonDir . '/' . $factionName . '.json';
            $jsonHandle = fopen($jsonPath, 'wb');
            fwrite($jsonHandle, '{' . json_encode((string)$factionName, JSON_UNESCAPED_UNICODE) . ':{');
            foreach ($factions[$i]['chunks'] as $n => $chunk) {
                if ($n > 0) {
                    fwrite($jsonHandle, ',');
                }
                $in = fopen(fvgen_fragPath($runDir, $factionName, $chunk), 'rb');
                stream_copy_to_stream($in, $jsonHandle);
                fclose($in);
            }
            fwrite($jsonHandle, '}}');
            fclose($jsonHandle);

            // .json.br for gamelobbyloader.php; returns null (and writes nothing) without the
            // brotli extension. Totals are accumulated for the deploy report at the end —
            // this host has no shell access, so the generator's own output IS the diagnostics.
            $brPath = ShipCompactor::precompressBrotli($jsonPath);
            $br['raw'] += filesize($jsonPath);
            if ($brPath !== null) {
                $br['count']++;
                $br['comp'] += filesize($brPath);
                // Compress the ONE largest faction at quality 4 as a like-for-like baseline.
                //
                // q4 is NOT an arbitrary choice: it is exactly what this endpoint already did.
                // gamelobbyloader.php pulls in global.php, which registers fv_compress_output()
                // (compression_helper.php) as a shutdown function, and that calls
                // brotli_compress($content, 4) on every single request. So the "before" here is
                // runtime brotli q4 — NOT mod_deflate gzip, which never applied to this PHP
                // endpoint. Comparing against gzip would flatter the result by ~5x.
                //
                // Only one file, so it costs ~0.03s.
                if (filesize($jsonPath) > $br['sampleRaw']) {
                    $br['sampleRaw']  = filesize($jsonPath);
                    $br['sampleName'] = $factionName;
                    $br['sampleBr']   = filesize($brPath);
                    $br['sampleQ4']   = function_exists('brotli_compress')
                        ? strlen(brotli_compress(file_get_contents($jsonPath), 4))
                        : 0;
                }
            }
        }

        $state['br'] = $br;
        $state['assembled'] = $end;
        if ($end >= count($factions)) {
            $state['phase'] = 'report';
        }

    //// ─── Phase 3: report ───────────────────────────────────────────
    } elseif ($state['phase'] === 'report') {
        // Fragments are no longer needed. The state file stays (phase 'done', with the report
        // in it) so that a report request whose RESPONSE was lost can be answered again when
        // the page retries; the next page load clears the whole run directory.
        foreach (glob($runDir . '/*.frag') ?: [] as $frag) {
            unlink($frag);
        }

        $br = $state['br'];
        $factionCount = count($state['factions']);
        ob_start();

        //// ─── Brotli pre-compression report ──────────────────────────────
        // There is no shell on this shared host, so this page IS the diagnostics. It answers
        // two questions on every deploy:
        //   1. did pre-compression actually RUN (is the extension present, were .br written)?
        //   2. how much is it saving versus the runtime brotli q4 we had before?
        //
        // It canNOT tell you whether the server is SERVING the .br — that is entirely up to
        // gamelobbyloader.php's own .br branch, and is only observable from the client. Check it
        // in the browser: DevTools > Network > open a faction in the lobby > the
        // gamelobbyloader.php request > Response Headers > "content-encoding: br", and watch the
        // transferred size drop. See the note printed below.
        echo "<strong>Brotli pre-compression</strong>:<br/>\n";
        if ($br['count'] === 0) {
            $missingFn    = !function_exists('brotli_compress_init') || !function_exists('brotli_compress_add');
            $missingConst = !defined('BROTLI_TEXT') || !defined('BROTLI_PROCESS') || !defined('BROTLI_FINISH');
            if ($missingFn) {
                $why = 'the brotli extension\'s incremental API (brotli_compress_init/_add) is NOT available';
            } elseif ($missingConst) {
                $why = 'the functions exist but the BROTLI_* constants do not — this binding names them '
                     . 'differently, so tell whoever maintains this and the call can be adapted';
            } else {
                $why = 'the extension is present but no .br file was written — check directory permissions';
            }
            echo " &nbsp; - <strong>NOT active</strong>: $why.<br/>\n";
            echo " &nbsp; &nbsp; Faction JSON is still compressed on the fly by compression_helper.php "
               . "(brotli q4), exactly as before — nothing is broken, you are simply not getting the "
               . "extra ~26% saving or the per-request CPU back.<br/>\n";
            echo " &nbsp; &nbsp; brotli_compress(): " . (function_exists('brotli_compress') ? 'yes' : 'no')
               . " &nbsp; brotli_compress_init(): " . (function_exists('brotli_compress_init') ? 'yes' : 'no')
               . " &nbsp; brotli_compress_add(): " . (function_exists('brotli_compress_add') ? 'yes' : 'no')
               . " &nbsp; BROTLI_TEXT/PROCESS/FINISH: "
               . (defined('BROTLI_TEXT') ? 'yes' : 'no') . '/'
               . (defined('BROTLI_PROCESS') ? 'yes' : 'no') . '/'
               . (defined('BROTLI_FINISH') ? 'yes' : 'no')
               . "<br/>\n";
        } else {
            printf(" &nbsp; - <strong>ACTIVE</strong>: %d of %d faction files pre-compressed.<br/>\n",
                $br['count'], $factionCount);
            printf(" &nbsp; - total on disk: %.1f MB raw &rarr; <strong>%.2f MB</strong> as .br (%.0f:1)<br/>\n",
                $br['raw'] / 1048576, $br['comp'] / 1048576,
                $br['comp'] > 0 ? $br['raw'] / $br['comp'] : 0);
            if ($br['sampleQ4'] > 0) {
                printf(" &nbsp; - largest faction (%s, %.2f MB): was <strong>%s KB</strong> "
                     . "(runtime brotli q4, recompressed on EVERY request by compression_helper.php), "
                     . "now <strong>%s KB</strong> pre-built at q9 &mdash; <strong>%.0f%% smaller "
                     . "payload and zero per-request compression CPU</strong>.<br/>\n",
                    htmlspecialchars($br['sampleName']), $br['sampleRaw'] / 1048576,
                    number_format($br['sampleQ4'] / 1024, 1), number_format($br['sampleBr'] / 1024, 1),
                    $br['sampleBr'] > 0 ? 100 * (1 - $br['sampleBr'] / $br['sampleQ4']) : 0);
            }
            echo " &nbsp; <em>&rarr; This confirms the files were BUILT. To confirm they are being "
               . "SERVED, open the lobby with DevTools &gt; Network, click a faction, and check the "
               . "gamelobbyloader.php response has &nbsp;<code>content-encoding: br</code>.</em><br/>\n";
        }

        //// ─── Flush server-side caches ───────────────────────────────────
        // This generator is run (in the browser) on every deploy, so it's the natural
        // place to flush server caches — belt-and-suspenders alongside the deploy-
        // versioned cache prefix in Manager::getCachePrefix(). On shared Apache hosting
        // (mod_php / single FPM pool) both caches below are one shared segment across all
        // workers, so a single browser hit reaches every live entry.

        // APCu = cached DATA (the per-game gamedata JSON). Validated only against each
        // game's last_update timestamp, so without a flush it can survive a patch and
        // keep serving old-shape data to clients running the new bundle until the game
        // is next touched or the 1-hour TTL expires.
        if (function_exists('apcu_clear_cache')) {
            $apcuCleared = apcu_clear_cache();
            echo $apcuCleared
                ? "<br/>APCu cache cleared.<br/>\n"
                : "<br/><strong>Warning:</strong> apcu_clear_cache() returned false.<br/>\n";
        } else {
            echo "<br/>APCu not available — cache clear skipped.<br/>\n";
        }

        // OPcache = cached compiled PHP BYTECODE. Independent of APCu.
        //
        // We deliberately DO NOT call opcache_reset() here.
        //
        // This host runs LiteSpeed (SAPI: litespeed) with opcache.validate_timestamps=On
        // and revalidate_freq=30s. That means freshly-uploaded .php files are auto-detected
        // and lazily recompiled per-file within the revalidate window — no manual reset is
        // needed to pick up a patch.
        //
        // A blanket opcache_reset() from inside a live web request on LiteSpeed empties the
        // entire shared-SHM bytecode cache (~2500 scripts) at once, forcing the lsphp worker
        // pool to recompile the whole codebase simultaneously. That recompile storm can
        // exhaust the pool / trip a worker limit, and LiteSpeed responds with an intermittent
        // 503 to whichever request lands in the window (fixed by an lsphp pool restart, e.g.
        // re-saving PHP settings in the host panel). Since validate_timestamps already gives
        // us correct pickup for free, the reset is pure downside here and is intentionally
        // omitted. If instant (sub-30s) pickup is ever required, lower revalidate_freq at the
        // host level instead of resetting from a web request.
        echo "<br/>OPcache reset skipped by design (validate_timestamps=On handles pickup; "
           . "reset would risk a LiteSpeed recompile-storm 503).<br/>\n";

        // --- Original blanket reset, disabled (kept for reference / quick restore). ---
        // Re-enable ONLY if validate_timestamps is turned Off at the host level, and be
        // aware it can cause the intermittent LiteSpeed 503 documented above.
        // if (function_exists('opcache_reset')) {
        //     $opcacheReset = opcache_reset();
        //     echo $opcacheReset
        //         ? "<br/>OPcache reset.<br/>\n"
        //         : "<br/><strong>Warning:</strong> opcache_reset() returned false (may be disabled or restricted).<br/>\n";
        // } else {
        //     echo "<br/>OPcache not available — reset skipped.<br/>\n";
        // }

        // ─── OPcache diagnostics ───
        // One-time-per-deploy readout so we can confirm empirically (rather than guess)
        // how this shared host runs OPcache. Key questions this answers:
        //   - enabled?            → is OPcache even active for web requests
        //   - restrict_api        → if set and doesn't match this script's path, the
        //                           reset above is silently refused
        //   - validate_timestamps → if On (with a short revalidate_freq), the host auto-
        //                           notices uploaded .php files and the reset is optional;
        //                           if Off, opcache_reset() is the ONLY way to pick up a patch
        //   - SAPI                → mod_php (reset reaches all workers) vs fpm-fcgi
        //                           (web-request reset may not propagate cleanly)
        echo "<br/><strong>OPcache diagnostics</strong> (SAPI: " . htmlspecialchars(PHP_SAPI) . "):<br/>\n";
        if (function_exists('opcache_get_status') && function_exists('opcache_get_configuration')) {
            // Pass false: we don't need the (large) per-script list, just the summary.
            $status = @opcache_get_status(false);
            $config = @opcache_get_configuration();

            if ($status === false || $config === false) {
                echo " &nbsp; - opcache_get_status()/get_configuration() returned false "
                   . "(API likely restricted via opcache.restrict_api).<br/>\n";
            } else {
                $d = $config['directives'] ?? [];
                $boolStr = function ($v) { return $v ? 'On' : 'Off'; };

                $enabled       = $status['opcache_enabled'] ?? false;
                $restrictApi   = $d['opcache.restrict_api'] ?? '';
                $validateTs    = $d['opcache.validate_timestamps'] ?? null;
                $revalidate    = $d['opcache.revalidate_freq'] ?? null;
                $cachedScripts = $status['opcache_statistics']['num_cached_scripts'] ?? 'n/a';

                echo " &nbsp; - opcache_enabled: " . $boolStr($enabled) . "<br/>\n";
                echo " &nbsp; - validate_timestamps: "
                   . ($validateTs === null ? 'n/a' : $boolStr($validateTs))
                   . " &nbsp; revalidate_freq: "
                   . ($revalidate === null ? 'n/a' : (int)$revalidate) . "s<br/>\n";
                echo " &nbsp; - restrict_api: "
                   . ($restrictApi === '' ? '(not set — reset allowed)' : htmlspecialchars($restrictApi))
                   . "<br/>\n";
                echo " &nbsp; - cached scripts: " . htmlspecialchars((string)$cachedScripts) . "<br/>\n";

                // Plain-language guidance based on what we found.
                if (!$enabled) {
                    echo " &nbsp; <em>→ OPcache is off for web requests; stale-bytecode is not a concern here.</em><br/>\n";
                } elseif ($validateTs === false) {
                    echo " &nbsp; <em>→ validate_timestamps is Off: the reset above is REQUIRED to pick up patched PHP.</em><br/>\n";
                } elseif ($validateTs === true) {
                    echo " &nbsp; <em>→ validate_timestamps is On: the host auto-detects uploaded files within revalidate_freq; the reset just makes it instant.</em><br/>\n";
                }
            }
        } else {
            echo " &nbsp; - opcache_get_status()/get_configuration() not available.<br/>\n";
        }

        //// ─── Memory report ─────────────────────────────────────────────
        // Both figures are the largest seen in any ONE step, since that is the unit the server
        // now has to tolerate. The RSS is the one to compare against the LiteSpeed lsphp
        // Memory Hard Limit: with OPcache on it runs ~3x the PHP figure, because the compiled
        // classes live in shared memory that PHP's own accounting leaves out. Read at the end
        // of the step, on a worker that may have served other requests first.
        $peakPhp = max($state['peakPhpMb'], memory_get_peak_usage(true) / 1048576);
        $peakRss = max($state['peakRssMb'], $procStatusMb('VmRSS'));
        echo "<br/><strong>Heaviest step:</strong> PHP " . number_format($peakPhp, 1) . " MB"
           . ", process RSS " . ($peakRss < 0 ? 'n/a' : number_format($peakRss, 1) . ' MB')
           . " &nbsp;(php.ini memory_limit: " . htmlspecialchars((string)ini_get('memory_limit')) . ")<br/>\n";
        echo " &nbsp; <em>Compare the RSS against the LiteSpeed lsphp Memory Hard Limit.</em><br/>\n";

        $state['report'] = ob_get_clean();
        $state['phase'] = 'done';
    }
    // phase 'done': nothing to do — the report is answered again from the saved state.

    if ($phaseAtStart !== 'done') {
        $state['steps']++;
        $state['peakPhpMb'] = max($state['peakPhpMb'], memory_get_peak_usage(true) / 1048576);
        $state['peakRssMb'] = max($state['peakRssMb'], $procStatusMb('VmRSS'));
        fvgen_saveState($stateFile, $state);
    }
} catch (Throwable $e) {
    $genLog('step failed: ' . $e->getMessage());
    $respond(500, ['fatal' => true, 'error' => get_class($e) . ': ' . $e->getMessage()
        . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')']);
}

$genLog("step {$state['steps']} done, phase now {$state['phase']}");

$respond(200, [
    'phase'         => $state['phase'],
    'nextName'      => $state['nextName'],
    'totalNames'    => count($state['names']),
    'shipCount'     => $state['shipCount'],
    'assembled'     => $state['assembled'],
    'totalFactions' => count($state['factions']),
    'steps'         => $state['steps'],
    'listing'       => $listing,
    'report'        => $state['phase'] === 'done' ? $state['report'] : null,
]);
