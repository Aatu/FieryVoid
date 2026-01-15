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

// APCu Fast Poll: Check if we can exit early without touching DB
if (function_exists('apcu_fetch') && isset($_GET['gameid']) && isset($_GET['last_time'])) {
    $gameid = $_GET['gameid'];
    $serverTime = apcu_fetch("game_{$gameid}_last_update");
    if ($serverTime && $serverTime <= (float)$_GET['last_time']) {
         error_log("Gamedata: Fast Poll EXEMPT - " . $_SERVER['REMOTE_ADDR']);
         echo "{}";
         exit;
    }
}

try {
    if (isset($_POST["gameid"])) {
        if (!$playerid) {
            throw new Exception("Not logged in.");
        }

        $gameid     = $_POST["gameid"];
        $turn       = $_POST["turn"] ?? 0;
        $phase      = $_POST["phase"] ?? 0;
        $activeship = $_POST["activeship"] ?? [];
        $status     = $_POST["status"] ?? '';
        $slotid     = $_POST["slotid"] ?? 0;

        // ✅ Always pass ships as JSON string to Manager
        $ships = $_POST["ships"] ?? '[]';
        if (is_array($ships)) {
            $ships = json_encode($ships);
        }

        $ret = Manager::submitTacGamedata(
            $gameid,
            $playerid,
            $turn,
            $phase,
            $activeship,
            $ships,  // ✅ Manager expects string
            $status,
            $slotid
        );

    } elseif (isset($_GET["gameid"])) {
        $gameid     = $_GET["gameid"];
        $turn       = $_GET["turn"] ?? 0;
        $phase      = $_GET["phase"] ?? 0;
        $activeship = $_GET["activeship"] ?? [];
        $force      = isset($_GET["force"]);

        $ret = Manager::getTacGamedataJSON(
            $gameid,
            $playerid,
            $turn,
            $phase,
            $activeship,
            $force
        );

    } else {
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

/* //Old version
	ob_start("ob_gzhandler"); 
    include_once 'global.php';


	if (isset($_POST["gameid"])){
		if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		     print('{"error":"Not logged in."}');
		}

		$playerid = $_SESSION["user"];
		$gameid = $_POST["gameid"];
		$turn = $_POST["turn"];
		$phase = $_POST["phase"];
		$activeship = $_POST["activeship"];
		$ships = $_POST["ships"];
		$status = $_POST["status"];
		$slotid = $_POST["slotid"];

		//file_put_contents('/tmp/fierylog', "Gameid: $gameid gamedata.php ships:". var_export($_POST["ships"], true) ."\n\n", FILE_APPEND);

		$ret = Manager::submitTacGamedata($gameid, $playerid, $turn, $phase, $activeship, $ships, $status, $slotid);

	}else if (isset($_GET["gameid"])){
		$playerid = isset($_SESSION["user"]) ? $_SESSION["user"] : null;
		$gameid = $_GET["gameid"];
		$turn = $_GET["turn"];
		$phase = $_GET["phase"];
		$activeship = $_GET["activeship"];
        $force = isset($_GET["force"]) ? true : false;

		$ret = Manager::getTacGamedataJSON($gameid, $playerid, $turn, $phase, $activeship, $force);

	}else{
		$ret = '{"error":"Omitting required data"}';
	}

	print($ret);

*/