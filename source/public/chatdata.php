<?php ob_start("ob_gzhandler"); 
    ini_set('display_errors',1);
    error_reporting(E_ALL);
    require_once dirname(__DIR__) . '/server/controller/ChatManager.php';
    require_once dirname(__DIR__) . '/server/controller/DBManager.php';
    require_once dirname(__DIR__) . '/server/model/ChatMessage.php';
    require_once dirname(__DIR__) . '/server/lib/Debug.php';
    session_start();
        
    if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
        print('{}');
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