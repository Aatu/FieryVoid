<?php 


// Debugging (remove or adjust for production)
ini_set('display_errors', 1);
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

// Redirect if not logged in
if (empty($_SESSION["user"])) {
    header('Location: index.php');
    exit;
}

$playerid = (int)$_SESSION["user"];
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