<?php
/* Save Orders (SAVE_ORDERS_PLAN.md §2, Stage 1) - park the orders a player has given so far in
   the current phase without committing them, or throw the parked copy away.

     POST application/json {"action":"save", "gameid":N, "turn":N, "phase":N, "orders":"<draft JSON>"}
                           -> {"savedAt": <unix time>}
     POST application/json {"action":"clear", "gameid":N}
                           -> {"cleared": true}
     any failure           -> {"error": "...", "code": ..., "logid": ...}

   The player comes from the session ONLY, never from the request.

   `orders` travels as a JSON STRING inside the body, the same way gamedata.php's `ships` does.
   The body itself is decoded as an associative array, which would turn every empty {} in the
   draft into [] - so the draft has to reach Manager::saveOrders as the text the client wrote.

   Deliberately NOT in server_load_guard.php's known-poll list: like saveFleet.php, this is a
   user action and is limited like one. */
require_once 'global.php';

header('Content-Type: application/json; charset=utf-8');

$playerid = $_SESSION['user'] ?? null;
session_write_close(); // release the session lock - nothing below writes to the session

try {
    $input = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
    }

    if (!is_array($input) || !isset($input['action']) || !isset($input['gameid'])) {
        $ret = '{"error":"Omitting required data"}';
    } else if (!$playerid) {
        throw new Exception("Not logged in.");
    } else if ($input['action'] === 'save') {
        $ret = Manager::saveOrders(
            $input['gameid'],
            $playerid,
            $input['turn'] ?? null,
            $input['phase'] ?? null,
            $input['orders'] ?? null
        );
    } else if ($input['action'] === 'clear') {
        $ret = Manager::clearSavedOrders($input['gameid'], $playerid);
    } else {
        $ret = '{"error":"Unknown action"}';
    }

} catch (Exception $e) {
    $logid = Debug::error($e);
    $ret = json_encode([
        "error" => $e->getMessage(),
        "code"  => $e->getCode(),
        "logid" => $logid
    ]);
}

if (ob_get_length()) ob_clean();
echo $ret;

exit;
