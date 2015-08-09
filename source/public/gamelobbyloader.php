<?php ob_start("ob_gzhandler"); 
    include_once 'global.php';

        
    if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
//      print('{"error":"Not logged in."}');
    }else{
    
    
		if (isset($_GET["faction"])){
			$factionRequest = $_GET["faction"]; 
			$ret = ShipLoader::getAllShips($factionRequest);
		}else{
            $ret = '{"error":"Omitting required data"}';
		}
		
		print($ret);
	}
    
?>