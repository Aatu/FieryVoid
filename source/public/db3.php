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



$sql = "DELETE * FROM tac_game WHERE id = 3040 AND turn = 2";
$stmt = $db->query($sql);

echo "deleted";


