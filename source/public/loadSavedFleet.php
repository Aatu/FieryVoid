<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'global.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$playerid = $_SESSION['user'] ?? null;
session_write_close(); // release lock immediately

try {
    // Read input safely
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $listid = $input['listid'] ?? null;

    if ($listid === null) {
        throw new Exception("Fleet ID missing");
    }

    // Load ships from Manager
    $ships = Manager::loadSavedFleet($listid);

    // Ensure JSON-safe output
    if (!is_array($ships)) $ships = [];

    echo json_encode([
        'success' => true,
        'ships'   => $ships
    ], JSON_NUMERIC_CHECK);

} catch (Exception $e) {
    $logid = Debug::error($e);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
        'code'    => $e->getCode(),
        'logid'   => $logid
    ]);
}

exit;