<?php

/**
 * getFactions.php – Returns all available factions as JSON
 * Modernized for PHP 8 + Apache
 */

if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';

/* //SAFER VERSION DEPENDING ON APACHE SETTINGS
declare(strict_types=1);

// ✅ Enable safe output buffering with gzip if possible
if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';
*/
// ✅ Start session safely and release early to avoid blocking concurrent AJAX
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$playerid = $_SESSION['user'] ?? null;
session_write_close(); // ✅ Release session lock immediately

// ✅ Authorization check
if (!$playerid) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

try {
    $factions = Manager::getAllFactions();

    // ✅ Encode safely with numeric check & partial output to prevent fatal on bad UTF‑8
    echo json_encode(
        $factions,
        JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );

} catch (Throwable $e) {
    $logid = Debug::error($e);
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'code'  => $e->getCode(),
        'logid' => $logid
    ], JSON_UNESCAPED_UNICODE);
}

ob_end_flush();
exit;
/* //OLD version
include_once 'global.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"] == false) {
    echo json_encode(["error" => "unauthorized"]);
    exit;
}

$factions = Manager::getAllFactions();

echo json_encode($factions, JSON_NUMERIC_CHECK);
*/