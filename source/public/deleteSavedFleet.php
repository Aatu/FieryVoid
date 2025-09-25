<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'global.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$playerid = $_SESSION['user'] ?? null;
session_write_close();

try {
    // ✅ read JSON payload correctly
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id === null) {
        throw new Exception("Fleet ID missing");
    }

    $ret = Manager::deleteSavedFleet($id);

    echo json_encode(['listId' => $id, 'success' => true], JSON_NUMERIC_CHECK);
} catch (Exception $e) {
    $logid = Debug::error($e);
    echo json_encode([
        "error" => $e->getMessage(),
        "code"  => $e->getCode(),
        "logid" => $logid
    ]);
}
exit;
