<?php ob_start("ob_gzhandler"); 
    include_once 'global.php';

        
    if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
//      print('{"error":"Not logged in."}');
    }else{
    
    
		if (isset($_POST["gameid"])){
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
			$playerid = $_SESSION["user"];
			$gameid = $_GET["gameid"];
			$turn = $_GET["turn"];
			$phase = $_GET["phase"];
			$activeship = $_GET["activeship"];

			$ret = Manager::getTacGamedataJSON($gameid, $playerid, $turn, $phase, $activeship);
		
		}else{
            $ret = '{"error":"Omitting required data"}';
		}
		
		print($ret);
	}
    
?>
