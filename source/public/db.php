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

$sql = "SELECT phpclass, COUNT(*) count FROM tac_ship GROUP BY phpclass";

$stmt = $db->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$list = array();


usort($results, function($a, $b){
	if ($a["count"] > $b["count"]){
		return -1;
	}
	else return 1;	
});


for ($i = 0; $i < 40; $i++){
	$list[] = $results[$i];
}


foreach ($list as $ship){
	echo $ship["phpclass"].", times seen in combat: ".$ship["count"]."<br>";
}