<?php
include_once 'global.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"] == false) {
    header('Location: index.php');
//      return;
}

$games = "[]";
if (isset($_SESSION["user"])) {
    $games = Manager::getTacGames($_SESSION["user"]);

    $games = json_encode($games, JSON_NUMERIC_CHECK);
} 

?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Fiery Void - Games</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="styles/base.css" rel="stylesheet" type="text/css">
    <link href="styles/lobby.css" rel="stylesheet" type="text/css">
    <link href="styles/games.css" rel="stylesheet" type="text/css">
    <link href="styles/confirm.css" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <!--		<script src="client/helper.js"></script>-->
    <script src="client/games.js"></script>
    <script src="client/ajaxInterface.js"></script>
    <script src="client/player.js"></script>
    <script src="client/mathlib.js"></script>
    <script src="client/UI/confirm.js"></script>
    <script>
        jQuery(function ($) {

            gamedata.parseServerData(<?php print($games); ?>);
            ajaxInterface.startPollingGames();

            gamedata.thisplayer = <?php print($_SESSION["user"]); ?>;
        });

        function loadFireList() {
            ajaxInterface.getFirePhaseGames();
        }

    </script>
</head>
<body>

<img src="img/logo.png">
<!--        <div class="helphide" style="float:right" onclick="window.helper.onClickHelpHide()">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        </div>-->
<div class="panel news">
    <div id="newsHeader">Latest News - September 2019</div>
    <div id="newsEntry">
        <!--
        <h3> *** Merry Christmas and a Happy New Year! *** </h3>
        <h4>   may our games be fiery and our lives peaceful</h4>
        <br>
        -->
        The latest update includes:
        <br><br>
            <b>Minor fixes</b> - layout fixes mostly
        <br>
            <b>New missile ships</b> - primarily Govall with full special missile load :)
        <br>
            <b>Hit charts</b> - hit charts returned! mouseover ship image :) . You'll see ship notes there as well.
        <br>
            <b>Special comments</b> - are properly displayed as multiline again
        <br>
            <b>CCEW modified</b> - now CCEW reaches out only 10 hexes, as it should. If You want to reach further, You need proper OEW (now usable on fighters).
        <br>
            <b>Drazi Serpent guns</b> - fixed
        <br>
            <b>Damage allocation improved</b> - better splitting fire incoming at section split. Base owners should be particularly happy :) .
        <br>
            <b>Range 0 speed 0 firing arcs fixed</b> - as long as only one of units involved is speed 0!
        <br>
            <b>Particle Repeater now reacts properly to boosting</b> - Particle Gun too!
        <br>
            <b>Defensive fire side effects handled properly</b> - which means Grav Pulsars and Particle Repeaters now shutdown if boosted and fired defensively!
        <br>
            <b>Boosted systems</b> now are highlighted so they can be identified on a glance
        <br>
            <b>Missile hit chance fixed</b> - onscreen, as actual firing was already being resolved correctly
        
        <!--
        <br><br>
        - last but not least - brand new collection of BUGS!;
        -->
        <br><br>
        Enjoy and report BUGS on FB. Also force reload (ctrl+F5) whenever something weird happens.

        <br><br>----------<b>LINKS</b>----------
        <br><a href="https://www.facebook.com/groups/218482691602941/files/" target=\"_blank\">FV FAQ</a> - differences from B5Wars and known bugs
        <br><a href="http://b5warsvault.wikidot.com/" target="_blank">B5Wars Vault</a> - B5Wars rules and LOTS of related stuff
        <br><a href="reg.php">Register</a> new player account
        <br><a href="chpass.php">Change password</a> of Your account
    </div>
</div>
<div class="panel large">
    <div class="logout"><a href="logout.php">LOGOUT</a></div>

    <table class="gametable">
        <tr>
            <td class="centered">ACTIVE GAMES</td>
            <td class="centered">STARTING GAMES</td>
            <td class="centered">RECENT ACTIVITY IN:</td>
        </tr>
        <tr>
            <td>
                <div class="gamecontainer active subpanel">
                    <div class="notfound">No active games</div>
                </div>
            </td>
            <td>
                <div class="gamecontainer lobby subpanel">
                    <div class="notfound">No starting games</div>
                </div>

            </td>
            <td>
                <div id="fireList" class="gamecontainer fire subpanel">
                </div>
            </td>
            <td>
                <a class="link" href="creategame.php">CREATE GAME</a>
                <input type="button" id="loadFireButton" onclick="loadFireList()" value="LOAD RECENTLY ACTIVE">

            </td>
        </tr>
    </table>


</div>
<div id="globalchat" class="panel large" style="height:150px;">
    <?php
    $chatgameid = 0;
    $chatelement = "#globalchat";
    include("chat.php")
    ?>
</div>

<!--        <div id="globalhelp" class="helppanel">
        <?php
//        	$messagelocation='games.php';
//        	$ingame=false;
//        	include("helper.php")
?>
        </div>-->

</body>
</html>
