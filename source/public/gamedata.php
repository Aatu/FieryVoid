<?php
// global.php handles output buffering and compression via fv_compress_output()
require_once 'global.php';

header('Content-Type: application/json; charset=utf-8');


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

// APCu Fast Poll: Check if we can exit early without touching DB.
// Must use the SAME prefix Manager writes last_update under (Manager::getCachePrefix(),
// which includes the deploy-version suffix) — otherwise after a deploy this reads a
// stale/absent key and the optimization silently stops working.
if (function_exists('apcu_fetch') && isset($_GET['gameid']) && isset($_GET['last_time'])) {
    $prefix = Manager::getCachePrefix();
    $gameid = $_GET['gameid'];
    $serverTime = apcu_fetch("{$prefix}game_{$gameid}_last_update");
    // Treat "no newer data" with the same 1ms tolerance Manager uses when it
    // compares timestamps (Manager::getTacGamedataJSON: abs(ts - timestamp) < 0.001).
    // A bare <= here while Manager uses a tolerance window means a change landing
    // within the same millisecond could be classified differently by the two paths.
    if ($serverTime && $serverTime <= (float)$_GET['last_time'] + 0.001) {
         Manager::apcuLog("FAST-POLL EXEMPT game=$gameid serverTime=$serverTime last_time={$_GET['last_time']}");
         echo "{}";
         exit;
    }
    Manager::apcuLog("FAST-POLL MISS game=$gameid serverTime=" . var_export($serverTime, true) . " last_time={$_GET['last_time']} → full request");
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
        "logid" => $logid,
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
}

// Clean any stray text/whitespace that was output by included files
// Note: global.php's universal handler will capture $ret for compression.
if(ob_get_length()) ob_clean();

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