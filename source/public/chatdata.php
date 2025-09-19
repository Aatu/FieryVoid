<?php 

/* //SAFER VERSION DEPENDING ON APACHE SETTINGS
declare(strict_types=1);

// --- Output buffering with gzip if available ---
if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}
header('Content-Type: application/json; charset=utf-8');
*/

$__fv_buffering = false;
if (!headers_sent() && !ini_get('zlib.output_compression')) {
    ob_start();
    $__fv_buffering = true;
}

header('Content-Type: application/json; charset=utf-8');
// --- Required classes ---
require_once dirname(__DIR__) . '/server/controller/ChatManager.php';
require_once dirname(__DIR__) . '/server/controller/DBManager.php';
require_once dirname(__DIR__) . '/server/model/ChatMessage.php';
require_once dirname(__DIR__) . '/server/lib/Debug.php';

// --- Session handling ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$playerid = $_SESSION['user'] ?? null;
session_write_close(); // allow concurrent AJAX

if (!$playerid) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in.'], JSON_UNESCAPED_UNICODE);
    if ($__fv_buffering) { ob_end_flush(); }
    exit;
}

$ret = ['error' => 'Omitting required data'];

try {
    // --- POST: Submit new message ---
    if (isset($_POST['gameid'], $_POST['message'])) {
        $gameid = (int) $_POST['gameid'];
        $message = trim((string)$_POST['message']);

        // Keep old behavior: allow gameid=0 for global
        if ($message === '') {
            throw new InvalidArgumentException('Message cannot be empty.');
        }

        $ret = ChatManager::submitChatMessage($playerid, $message, $gameid);

    // --- GET: Poll messages ---
    } elseif (isset($_GET['gameid'], $_GET['lastid'])) {
        $gameid = (int) $_GET['gameid'];
        $lastid = (int) $_GET['lastid'];

        // Allow 0 for global chat and first poll
        if ($gameid < 0 || $lastid < 0) {
            throw new InvalidArgumentException('Invalid game ID or last message ID.');
        }

        $ret = ChatManager::getChatMessages($playerid, $lastid, $gameid);
    }

    // --- Output JSON ---
    if (is_string($ret)) {
        // ChatManager already returned JSON
        echo $ret;
    } else {
        echo json_encode($ret, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

} catch (Throwable $e) {
    $logid = Debug::error($e);
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'code'  => $e->getCode(),
        'logid' => $logid
    ]);
}

if ($__fv_buffering) { ob_end_flush(); }
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
    
    
		if (isset($_POST["gameid"]) && isset($_POST["message"])){
			$playerid = $_SESSION["user"];
			$gameid = $_POST["gameid"];
			$message = $_POST["message"];
			
            
			$ret = ChatManager::submitChatMessage($playerid, $message, $gameid);
			
			
		}else if (isset($_GET["gameid"]) && isset($_GET["lastid"])){
			$playerid = $_SESSION["user"];
			$gameid = $_GET["gameid"];
			$lastid = $_GET["lastid"];

			$ret = ChatManager::getChatMessages($playerid, $lastid, $gameid);
		
		}else{
            $ret = '{"error":"Omitting required data"}';
		}
		
		print($ret);
	}
    
?>
*/