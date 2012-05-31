<?php ob_start("ob_gzhandler"); 
    ini_set('display_errors',1);
    error_reporting(E_ALL);
    array_walk(glob('./engine/*.php'), create_function('$v,$i', 'return require_once($v);'));
    array_walk(glob('./engine/ships/*.php'), create_function('$v,$i', 'return require_once($v);'));
    array_walk(glob('./engine/ships/*/*.php'), create_function('$v,$i', 'return require_once($v);'));
    array_walk(glob('./engine/weapons/*.php'), create_function('$v,$i', 'return require_once($v);'));
    array_walk(glob('./engine/tactical/*.php'), create_function('$v,$i', 'return require_once($v);'));
    session_start();
    
    

        
    if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
        print("{error:'Not logged in.'}");
    }else{
    
    
		if (isset($_POST["gameid"])){
			$playerid = $_SESSION["user"];
			$gameid = $_POST["gameid"];
			$turn = $_POST["turn"];
			$phase = $_POST["phase"];
			$activeship = $_POST["activeship"];
			$ships = $_POST["ships"];
			
            //file_put_contents('/tmp/fierylog', "Gameid: $gameid gamedata.php ships:". var_export($_POST["ships"], true) ."\n\n", FILE_APPEND);
            
			$ret = Manager::submitTacGamedata($gameid, $playerid, $turn, $phase, $activeship, $ships);
			
			
		}else if (isset($_GET["gameid"])){
			$playerid = $_SESSION["user"];
			$gameid = $_GET["gameid"];
			$turn = $_GET["turn"];
			$phase = $_GET["phase"];
			$activeship = $_GET["activeship"];

			$ret = Manager::getTacGamedataJSON($gameid, $playerid, $turn, $phase, $activeship);
		
		}else{
			$ret["error"] = true;
			$ret["msg"] = "Omitting required data"; 
			json_encode($ret);
		}
		
		
		print($ret);
	}
    
?>
