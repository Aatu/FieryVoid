<?php 
ob_start();

// Force GZIP compression as these JSON payloads can be massive (e.g. 5MB+)
ini_set('zlib.output_compression', 'On');
// Allow the script more time to dynamically compile a faction if the cache is completely cold
set_time_limit(60);

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$playerid = $_SESSION['user'] ?? null;
session_write_close();

if (!$playerid) {
    http_response_code(401);
    if(ob_get_length()) ob_clean();
    echo json_encode(['error' => 'Not logged in.']);
    exit;
}

$input = [];
// Accept JSON POST body if present
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Try reading JSON input if Content-Type matches OR if $_POST is empty (fallback)
    if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false || empty($_POST)) {
        $jsonInput = file_get_contents('php://input');
        if ($jsonInput) {
            $decoded = json_decode($jsonInput, true);
            if (is_array($decoded)) {
                $input = $decoded;
            }
        }
    }
}

// Retrieve faction parameter from either GET or POST (POST preferred)
$factionRequest = $input['faction'] ?? $_POST['faction'] ?? $_GET['faction'] ?? null;

if (!$factionRequest) {
    http_response_code(400);
    if(ob_get_length()) ob_clean();
    echo json_encode(['error' => 'Missing faction parameter']);
    exit;
}

try {
    //Changed how staticships are loaded to help with HTTP Protocol errors - DK Dec 2025
    // 1. Try serving from static cache
    $cleanFaction = str_replace(['..', '/', '\\'], '', $factionRequest);
    $jsonPath = __DIR__ . '/static/json/' . $cleanFaction . '.json';

    if (file_exists($jsonPath)) {
        // Serve static file directly
        header('X-Source: Static');

        // Enable Browser Caching with Validation
        $lastModifiedTime = filemtime($jsonPath);
        $etag = md5_file($jsonPath);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModifiedTime) . " GMT");
        header("Etag: $etag");

        // Caching strategy depends on whether the caller versioned the URL.
        // The lobby appends ?v=<filemtime> (see window.factionVersions / gamelobby.php),
        // which makes the URL change whenever the ship data changes. A versioned URL
        // is therefore safe to cache long-term & immutably — the browser serves it
        // instantly without a revalidation round-trip, and a patch's new ?v= forces a
        // fresh fetch. Unversioned callers keep the old must-revalidate behaviour so
        // they can never go stale.
        if (isset($_GET['v']) && $_GET['v'] !== '') {
            // Versioned URL — safe to cache forever; no revalidation needed.
            header('Cache-Control: public, max-age=31536000, immutable');
        } else {
            header('Cache-Control: no-cache, must-revalidate'); // Require validation every time

            // Check if browser has cached version (unversioned callers only)
            if (
                (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModifiedTime) ||
                (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag)
            ) {
                header("HTTP/1.1 304 Not Modified");
                exit;
            }
        }
        header('Pragma: cache');

        // We may answer this URL with either a brotli or a plain/gzip body depending on
        // what the caller advertised, so the response MUST vary on Accept-Encoding —
        // otherwise a shared cache can hand the brotli body to a gzip-only client. This
        // matters especially on the versioned path above, which is cached immutably.
        header('Vary: Accept-Encoding');

        // Serve the pre-built brotli copy when the client accepts it (written by
        // precompressBrotli() in generateStaticShipFile*.php). This skips zlib entirely:
        // no per-request compression of a multi-MB payload, and a far smaller body than
        // gzip manages. Falls through to the plain JSON below when the .br is absent
        // (dev box without the brotli extension) or the client didn't advertise br.
        $brPath = $jsonPath . '.br';
        if (is_readable($brPath) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'br') !== false) {
            ini_set('zlib.output_compression', 'Off'); // never gzip an already-brotli body
            header('Content-Encoding: br');
            header('X-LiteSpeed-No-Gzip: 1');
            header('Content-Length: ' . filesize($brPath));
            if (ob_get_length()) ob_clean();
            readfile($brPath);
            exit;
        }

        // Buffer output, gzip is handled natively by zlib.output_compression

        if(ob_get_length()) ob_clean();
        echo file_get_contents($jsonPath);
        exit;
    }

    // 2. Fallback to dynamic generation e.g. old method
    $ships = ShipLoader::getAllShips($factionRequest);
    if(ob_get_length()) ob_clean();
    echo json_encode($ships, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    $logid = Debug::error($e);
    http_response_code(500);
    if(ob_get_length()) ob_clean();
    echo json_encode([
        'error' => $e->getMessage(),
        'code'  => $e->getCode(),
        'logid' => $logid,
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

exit;