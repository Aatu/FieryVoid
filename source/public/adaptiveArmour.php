<?php ob_start("ob_gzhandler"); 
    include_once 'global.php';


	$shipId = $_GET["id"];
	debug::log($shipId);


	DBManager::setValidAdaptiveSettings($gamedata, $shipId);

	$ret = ["shipId" => $shipId];
	$retJSON = json_encode($ret, JSON_NUMERIC_CHECK);

	
	print($retJSON);
    
?>