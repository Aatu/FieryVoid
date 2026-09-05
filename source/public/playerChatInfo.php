<?php 
ob_start();


// Errors are reported to the log but NEVER to the response body: this endpoint's
// output is parsed as JSON by the client, and a single PHP warning rendered into it
// turns a valid reply into a parse error. The ob_clean() calls below only protect the
// two paths that pass through them -- anything warning after the JSON header is sent
// would land inside the payload.
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Load required classes
require_once dirname(__DIR__) . '/server/controller/ChatManager.php';
require_once dirname(__DIR__) . '/server/controller/DBManager.php';
require_once dirname(__DIR__) . '/server/model/ChatMessage.php';
require_once dirname(__DIR__) . '/server/lib/Debug.php';

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$playerid = (int)($_SESSION["user"] ?? 0);
// Both branches below only touch the DB, never the session, so drop the per-session
// file lock now. It was previously held across both queries, serialising this
// endpoint against the chat and gamedata polls of the same tab.
session_write_close();

/*
 * 401 JSON, not a redirect.
 *
 * This used to answer an expired session with header('Location: index.php'), which
 * jQuery follows — so a dataType:'json' caller got index.php's HTML, failed to parse
 * it, and fell into chat.js's error handler, which simply retried. Every one of those
 * retries reached this file and ran two real queries: unlike chatdata.php there is no
 * APCu fast path here, so a single logged-out tab left open was an open-ended stream
 * of DB work. A 401 lets the client recognise the condition and stop.
 */
if (!$playerid) {
    http_response_code(401);
    if(ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["error" => "Not logged in."]);
    exit;
}

$ret = null;

// Handle POST request
if (isset($_POST["gameid"])) {
    $gameid = (int)$_POST["gameid"];
    $ret = ChatManager::setLastTimeChatChecked($playerid, $gameid);

// Handle GET request
} elseif (isset($_GET["gameid"])) {
    $gameid = (int)$_GET["gameid"];
    $ret = ChatManager::getLastTimeChatChecked($playerid, $gameid);

// Missing parameter
} else {
    $ret = json_encode(["error" => "Omitting required data"]);
}

// Output JSON response
if(ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');
echo $ret;

exit;
/*
ob_start("ob_gzhandler"); 
    ini_set('display_errors',1);
    error_reporting(E_ALL);
    require_once dirname(__DIR__) . '/server/controller/ChatManager.php';
    require_once dirname(__DIR__) . '/server/controller/DBManager.php';
    require_once dirname(__DIR__) . '/server/model/ChatMessage.php';
    require_once dirname(__DIR__) . '/server/lib/Debug.php';
    session_start();
        
    if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
//      print('{}');
    }else{
    
    
		if (isset($_POST["gameid"])){
			$playerid = $_SESSION["user"];
			$gameid = $_POST["gameid"];
			
            
			$ret = ChatManager::setLastTimeChatChecked($playerid, $gameid);
			
			
		}else if (isset($_GET["gameid"])){
			$playerid = $_SESSION["user"];
			$gameid = $_GET["gameid"];

			$ret = ChatManager::getLastTimeChatChecked($playerid, $gameid);
		
		}else{
                    $ret = '{"error":"Omitting required data"}';
		}
		
		print($ret);
	}
    
?>*/