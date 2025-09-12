<?php

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

// --- Start session and immediately release lock for concurrent requests ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$playerid = $_SESSION['user'] ?? null;
session_write_close();

$gameid = isset($_GET['gameid']) ? (int)$_GET['gameid'] : 0;
$turn   = isset($_GET['turn'])   ? (int)$_GET['turn']   : 0;

if ($gameid <= 0 || $turn < 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid gameid/turn.'], JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

try {
    $ret = Manager::getReplayGameData($playerid, $gameid, $turn);

    // getReplayGameData often returns a JSON string already
    if (!is_string($ret)) {
        $ret = json_encode($ret, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    echo $ret;

} catch (Throwable $e) {
    $logid = Debug::error($e);
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'logid' => $logid
    ]);
}

exit;

/*
ob_start("ob_gzhandler");
include_once 'global.php';

$playerid = isset($_SESSION["user"]) ? $_SESSION["user"] : null;
$gameid = $_GET["gameid"];
$turn = $_GET["turn"];

$ret = Manager::getReplayGameData($playerid, $gameid, $turn);

print($ret);
*/

