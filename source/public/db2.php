<?php
$database = 'B5CGM';
$user='aatu';
$pw='Kiiski';
$server = 'localhost';





$link = mysql_connect($server, $user, $pw);
mysql_select_db($database, $link);
mysql_set_charset('UTF-8', $link);



$db = new PDO("mysql:host=localhost;dbname=B5CGM;chartset=utf8", $user, $pw,  array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);



$sql = "SELECT id, username FROM player";
$stmt = $db->query($sql);
$resultsA = $stmt->fetchAll(PDO::FETCH_ASSOC);


$sql = "SELECT creator, COUNT(*) count FROM tac_game GROUP BY creator";
$stmt = $db->query($sql);
$resultsB = $stmt->fetchAll(PDO::FETCH_ASSOC);


$sql = "SELECT playerid, COUNT(*) count FROM tac_playeringame GROUP BY playerid";
$stmt = $db->query($sql);
$resultsC = $stmt->fetchAll(PDO::FETCH_ASSOC);



for ($i = 0; $i < sizeof($resultsA); $i++){
	$resultsA[$i]["times"] = 0;
	for ($j = 0; $j < sizeof($resultsB); $j++){
		if ($resultsA[$i]["id"] == $resultsB[$j]["creator"]){
			$resultsA[$i]["times"] += $resultsB[$j]["count"];
		}
	}
	for ($j = 0; $j < sizeof($resultsC); $j++){
		if ($resultsA[$i]["id"] == $resultsC[$j]["playerid"]){
			$resultsA[$i]["times"] += $resultsC[$j]["count"];
		}
	}
}


usort($resultsA, function($a, $b){
	if ($a["times"] > $b["times"]){
		return -1;
	}
	else {
		return 1;
	}
});


foreach ($resultsA as $result){
	if ($result["times"] > 0){
		echo "Player: ".$result["username"]." with ID: ".$result["id"]." was seen: ".$result["times"]."<br>";
	}
}


