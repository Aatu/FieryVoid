<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$playerid = $_SESSION['user'] ?? null;
session_write_close(); // ✅ release lock

// Handle JSON POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_SERVER["CONTENT_TYPE"]) &&
    strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {

    $input = json_decode(file_get_contents('php://input'), true);
    if (is_array($input)) {
        $_POST = $input;
    }
}

try {
    if (isset($_POST["name"])) {
        if (!$playerid) {
            throw new Exception("Not logged in.");
        }

        $name   = $_POST["name"] ?? null;
        $userid = $playerid; // always trust session, not client input
        $points = isset($_POST["points"]) ? (int)$_POST["points"] : null;      

        // ✅ Always pass ships as JSON string to Manager
        $ships = $_POST["ships"] ?? '[]';
        if (is_array($ships)) {
            $ships = json_encode($ships);
        }

        $ret = Manager::submitSavedFleet(
            $name,
            $userid,
            $points,
            $ships,
        );
    }else {
        $ret = '{"error":"Omitting required data"}';
    }

} catch (Exception $e) {
    $logid = Debug::error($e);
    $ret = json_encode([
        "error" => $e->getMessage(),
        "code"  => $e->getCode(),
        "logid" => $logid
    ]);
}

echo $ret;

exit;