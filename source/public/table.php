<?php



$db = 'B5CGM';
$user='aatu';
$pw='Kiiski';

//$user='root';
//$pw='';

$db_connect = mysql_connect("localhost", $user, $pw)
or die (mysql_error());

mysql_select_db($db, $db_connect);




$sql = "CREATE TABLE tac_flightsize
(

    entry int NOT NULL AUTO_INCREMENT,
    gameid int,    
    shipid int,
    flightsize int,
        
    PRIMARY KEY (entry),
 )";



mysql_query($sql, $db_connect);

echo "done";


?>