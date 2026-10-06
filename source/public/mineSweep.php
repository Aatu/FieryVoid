<?php
/* Mine detection: live sweeping (MINE_DETECTION_PLAN.md §1.4.3, Stage 2) - a unit with a Detect
   Mines rating has entered one or more hexes, and its player's client asks what it found there.

     POST application/json {"gameid":N, "turn":N, "shipid":N, "known":N, "moves":"<this turn's rows, JSON>"}
                           -> {"final": n, "found": [{"id", "q", "r", "f"}]}
                           -> {"final": n, "noRating": true}
     any refusal           -> {"error": "...", plus "stale" | "busy" | "reload" when the client should act on it}

   `known` is how many of the unit's hexes the client already knows to be final. The player comes
   from the session ONLY, never from the request.

   `moves` travels as a JSON STRING inside the body (trap T12), the way gamedata.php's `ships` and
   saveOrders.php's `orders` do: the body itself is decoded as an associative array, which would
   turn every empty {} in the rows into [] - and the rows are stored for a reload as they were sent.

   Deliberately NOT in server_load_guard.php's known-poll list: like saveOrders.php, this is a user
   action and is limited like one. The client sends one request at a time. */
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
        $ret = Manager::sweepMines(
            $input['gameid'],
            $playerid,
            $input['turn'] ?? null,
            $input['shipid'],
            $input['known'] ?? null,
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
