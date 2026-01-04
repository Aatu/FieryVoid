<?php 

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$playerid = $_SESSION['user'] ?? null;
session_write_close();

if (!$playerid) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in.']);
    exit;
}

$input = [];
// Accept JSON POST body if present
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
}

// Retrieve faction parameter from either GET or POST (POST preferred)
$factionRequest = $input['faction'] ?? $_POST['faction'] ?? $_GET['faction'] ?? null;

if (!$factionRequest) {
    http_response_code(400);
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
        
        if (!ob_start("ob_gzhandler")) ob_start(); //Try gzip, fall back to default
        
        echo file_get_contents($jsonPath);
        ob_end_flush();
        exit;
    }

    // 2. Fallback to dynamic generation e.g. old method
    $ships = ShipLoader::getAllShips($factionRequest);
    echo json_encode($ships, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    $logid = Debug::error($e);
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'code'  => $e->getCode(),
        'logid' => $logid
    ]);
}

exit;