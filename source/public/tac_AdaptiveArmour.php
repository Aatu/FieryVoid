<?php

$db = 'B5CGM';
$user='aatu';
$pw='Kiiski';

$db_connect = mysql_connect("localhost", $user, $pw)
or die (mysql_error());

mysql_select_db($db, $db_connect);

$sql = "CREATE TABLE tac_adaptivearmour
(

    entry int NOT NULL AUTO_INCREMENT,
    gameid int,
    shipid int,
    particle int,
    laser int,
    plasma int,
    molecular int,
    electromagnetic int,
    matter int,
    gravitic int,
   	antimatter int,
    ionic int,
    ballistic int,
    rparticle int,
    rlaser int,
    rplasma int,
    rmolecular int,
    relectromagnetic int,
    rmatter int,
    rgravitic int,
   	rantimatter int,
    rionic int,
    rballistic int,
		
    PRIMARY KEY (entry),
 )";

mysql_query($sql, $db_connect);

echo "done";

?>