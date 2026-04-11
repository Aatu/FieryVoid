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
        header('Cache-Control: no-cache, must-revalidate'); // Require validation every time

        // Check if browser has cached version
        if (
            (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModifiedTime) ||
            (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag)
        ) {
            header("HTTP/1.1 304 Not Modified");
            exit;
        }
        header('Pragma: cache');
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