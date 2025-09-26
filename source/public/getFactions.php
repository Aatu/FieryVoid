<?php

/**
 * getFactions.php – Returns all available factions as JSON
 * Modernized for PHP 8 + Apache
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';

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