<?php


$db = 'B5CGM';
$user='aatu';
$pw='Kiiski';

//$user='root';
//$pw='';


mysql_connect($database_host ?? "localhost", $user, $pw) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());


mysql_query("CREATE TABLE tac_adaptivearmour (
    id INT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    gameid INT,    
    shipid INT,
    particlepoints INT,
    particlealloc INT,
    laserpoints INT,
    laseralloc INT,
    molecularpoints INT,
    molecularalloc INT,
    matterpoints INT,
    matteralloc INT,
    plasmapoints INT,
    plasmaalloc INT,
    electromagneticpoints INT,
    electromagneticalloc INT,
    antimatterpoints INT,
    antimatteralloc INT,
    ionpoints INT,
    ionalloc INT,
    graviticpoints INT,
    graviticalloc INT,
    ballisticpoints INT,
    ballisticalloc INT
 )")
or die(mysql_error());





 $table  = 'tac_fireorder';
 $column = 'damageclass';
 $add = mysql_query("ALTER TABLE $table ADD $column text( 25 )");





 $table  = 'tac_damage';
 $column = 'damageclass';
 $add = mysql_query("ALTER TABLE $table ADD $column text( 25 )");


 /*

 $key = "matter";

 $gameid = 10;

 $damage = 500;;


            $sql = "UPDATE `B5CGM`.`tac_adaptivearmour` SET `".$key."points` = '".$key."points' + 3 
                    WHERE"." gameid = $gameid AND shipid = $damage";

*/



echo "ding";



?>