<?php 

if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';

/* //SAFER VERSION DEPENDING ON APACHE SETTINGS
declare(strict_types=1);

// ✅ Output compression (safe)
if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$playerid = $_SESSION['user'] ?? null;
session_write_close();

if (!$playerid) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in.']);
    ob_end_flush();
    exit;
}

// Accept JSON POST body if present
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $_POST = $input;
}

// Retrieve faction parameter from either GET or POST (POST preferred)
$factionRequest = $_POST['faction'] ?? $_GET['faction'] ?? null;

if (!$factionRequest) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing faction parameter']);
    ob_end_flush();
    exit;
}

try {
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

ob_end_flush();
exit;