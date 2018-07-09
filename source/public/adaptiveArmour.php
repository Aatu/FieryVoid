<?php ob_start("ob_gzhandler"); 
    include_once 'global.php';


	$gameid = $_GET["gameid"];
	$shipid = $_GET["shipid"];
	$turn = $_GET["turn"];

	try {
		$link = new PDO("mysql:host=" . ($database_host ?? 'localhost') . ";dbname=B5CGM","root","",
						array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}
	catch(PDOException $ex){
	    die(json_encode(array('outcome' => false, 'message' => 'Unable to connect')));
	}

	if ($link){
		debug::log("alive connection");
		$stmt = $link->prepare(
				            "SELECT 
				                *
				            FROM 
				                tac_adaptivearmour
				            WHERE 
				                gameid = :gameid
				            AND
				                shipid = :shipid"
			            );

		$stmt->execute(array(
			":gameid" =>$gameid,
			":shipid" => $shipid
			));


		$row = $stmt->fetch();

		if ($row){
			foreach ($row as $result){
				debug::log($result);
			}
		}
		else {				
			debug::log("ERROR");
		}
	}
	else 
		debug::log("no connection");


/*

        while($stmt->fetch()){
            $value["particle"] = array($particlepoints, $particlealloc);
            $value["laser"] = array($laserpoints, $laseralloc);
            $value["molecular"] = array($molecularpoints, $molecularalloc);
            $value["matter"] = array($matterpoints, $matteralloc);
            $value["plasma"] = array($plasmapoints, $plasmaalloc);
            $value["electromagnetic"] = array($electromagneticpoints, $electromagneticalloc);
            $value["antimatter"] = array($antimatterpoints, $antimatteralloc);
            $value["ion"] = array($ionpoints, $ionalloc);
            $value["gravitic"] = array($graviticpoints, $graviticalloc);
            $value["ballistic"] = array($ballisticpoints, $ballisticalloc);
        }

*/

	
//	print(json_encode($ret));
    
?>