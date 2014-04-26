<?php ob_start("ob_gzhandler"); 
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
    
?>