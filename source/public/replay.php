<?php ob_start("ob_gzhandler");
include_once 'global.php';

$playerid = isset($_SESSION["user"]) ? $_SESSION["user"] : null;
$gameid = $_GET["gameid"];
$turn = $_GET["turn"];

$ret = Manager::getReplayGameData($playerid, $gameid, $turn);

print($ret);


