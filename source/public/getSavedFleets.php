<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'global.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$playerid = $_SESSION['user'] ?? null;
session_write_close();

try {
    $fleets = Manager::getSavedFleets($playerid);
    echo json_encode(['fleets' => $fleets, 'success' => true], JSON_NUMERIC_CHECK);
} catch (Exception $e) {
    $logid = Debug::error($e);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
        'logid' => $logid
    ]);
}
exit;
