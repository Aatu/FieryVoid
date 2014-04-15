<?php ob_start("ob_gzhandler"); 
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

