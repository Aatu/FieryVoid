<?php
/**
 * slot.php – Handles slot actions (take/leave) and returns updated game data.
 * Modernized for PHP 8 + Apache
 */
/* //SAFER VERSION DEPENDING ON APACHE SETTINGS
declare(strict_types=1);

// ✅ Output compression (safe)
if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';
*/
if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start();  // buffer output, no gzip
}

header('Content-Type: application/json; charset=utf-8');

require_once 'global.php';

// ✅ Start session safely and release early to avoid blocking concurrent AJAX
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$playerid = $_SESSION['user'] ?? null;
session_write_close(); // ✅ allows concurrent AJAX requests

// ✅ Defensive default response
$ret = [
    'error' => 'AJAX request is omitting required data'
];

// ✅ Validate login
if (!$playerid) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in.'], JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

// ✅ Collect and sanitize POST input
$action = $_POST['action'] ?? '';
$gameid = isset($_POST['gameid']) ? (int)$_POST['gameid'] : 0;
$slotid = isset($_POST['slotid']) ? (int)$_POST['slotid'] : 0;

if (!$action || !$gameid) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters.'], JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

try {
    switch ($action) {
        case 'takeslot':
            if ($slotid <= 0) {
                throw new Exception("Invalid slot ID.");
            }
            Manager::takeSlot($playerid, $gameid, $slotid);

            // ✅ Use numeric check & partial output to avoid fatal on bad UTF8
            $gd = Manager::getTacGamedataJSON($gameid, $playerid, -1, 0, -1);
            $ret = $gd ?: '{}';
            break;

        case 'leaveslot':
            if ($slotid <= 0) {
                throw new Exception("Invalid slot ID.");
            }
            Manager::leaveLobbySlot($playerid, $gameid, $slotid);

            $gd = Manager::getTacGamedataJSON($gameid, $playerid, -1, 0, -1);
            $ret = $gd ?: '{}';
            break;

        default:
            http_response_code(400);
            $ret = ['error' => 'Unknown action'];
            break;
    }
} catch (Throwable $e) {
    // ✅ Log error and return JSON safely
    $logid = Debug::error($e);
    http_response_code(500);
    $ret = [
        'error' => $e->getMessage(),
        'code'  => $e->getCode(),
        'logid' => $logid
    ];
}

// ✅ Output JSON
if (!is_string($ret)) {
    $ret = json_encode($ret, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
}

echo $ret;
ob_end_flush();
exit;




/*
ob_start("ob_gzhandler"); 
    include_once 'global.php';

    $ret = '{"error":"AJAX request is omitting required data"}';    
    if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: games.php');
//      print('{"error":"Not logged in."}');
    }else if (isset($_POST["action"])){
    
        $action = $_POST["action"];
        
        if ( $action == 'takeslot' && isset($_POST["gameid"]) && isset($_POST["slotid"])){
            Manager::takeSlot($_SESSION["user"], $_POST["gameid"], $_POST["slotid"]);
            $ret = Manager::getTacGamedataJSON($_POST["gameid"], $_SESSION["user"], -1, 0, -1);
        }
		
		if ($action == 'leaveslot' && isset($_POST["slotid"]) && isset($_POST["gameid"])){
            Manager::leaveLobbySlot($_SESSION["user"], $_POST["gameid"], $_POST["slotid"]);
            $ret = Manager::getTacGamedataJSON($_POST["gameid"], $_SESSION["user"], -1, 0, -1);
        }
		
		
	}
    print($ret);
?>
*/
