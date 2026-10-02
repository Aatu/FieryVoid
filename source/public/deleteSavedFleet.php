<?php
ob_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'global.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$playerid = $_SESSION['user'] ?? null;
session_write_close();

try {
    // The player comes from the session only: Manager::deleteSavedFleet deletes a fleet only
    // if it belongs to them.
    if (!$playerid) {
        throw new Exception("Not logged in.");
    }

    // ✅ read JSON payload correctly
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id === null) {
        throw new Exception("Fleet ID missing");
    }

    $ret = Manager::deleteSavedFleet($id, $playerid);

    if(ob_get_length()) ob_clean();
    echo $ret;
} catch (Exception $e) {
    $logid = Debug::error($e);
    if(ob_get_length()) ob_clean();
    echo json_encode([
        "error" => $e->getMessage(),
        "code"  => $e->getCode(),
        "logid" => $logid
    ]);
}
exit;
