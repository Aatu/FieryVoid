<?php
/* An Expert Graviton Controller's rotation of the gravitic drive (OFFICERS_PLAN.md Stage 31) - the player clicked the Rotate icon
   of a unit moving now, and the SERVER rolls the d6 (Manager::rotateGraviticDrive -> Officers::rotateGravitic), locking the unit's
   moves up to the rotation (MoveLock).

     POST application/json {"gameid":N, "turn":N, "shipid":N, "moves":"<this turn's rows, JSON>"}
                           -> {"rows": [this turn's rows up to and including the rotation], "die": n}
     any refusal           -> {"error": "...", plus "stale" when the client should stop asking}

   The player comes from the session ONLY, never from the request. `moves` travels as a JSON STRING inside the body, as mineSweep.php's
   does: the body itself is decoded as an array, which would turn every empty {} in the rows into [] - and the rows are stored as sent.
   Not in server_load_guard.php's known-poll list: a user action, limited like one. */
require_once 'global.php';

header('Content-Type: application/json; charset=utf-8');

$playerid = $_SESSION['user'] ?? null;
session_write_close(); // release the session lock - nothing below writes to the session

try {
    $input = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
    }

    if (!is_array($input) || !isset($input['gameid']) || !isset($input['shipid'])) {
        $ret = '{"error":"Omitting required data"}';
    } else if (!$playerid) {
        throw new Exception("Not logged in.");
    } else {
        $ret = Manager::rotateGraviticDrive(
            $input['gameid'],
            $playerid,
            $input['turn'] ?? null,
            $input['shipid'],
            $input['moves'] ?? null
        );
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
