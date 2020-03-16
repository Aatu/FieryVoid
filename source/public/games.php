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
    <div id="newsHeader">Latest News - March 2020</div>
    <div id="newsEntry">
        <!--
        <h3> *** Merry Christmas and a Happy New Year! *** </h3>
        <h4>   may our games be fiery and our lives peaceful</h4>
        <br>
        -->
        
        <br><b>Firing log modified</b> - now number of firing orders displayed as well as number of shots, like 1(5)/2(12) shots hit for Pulse weapon.
        <br><br><b>Fleet checker modified</b> - corrections were made to the way Restricted/Limited units are handled.
        <br><br><b>New command ships</b> - Abbai Nakarsa and Narn G'Tal now give fleet command bonus.
        <br><br><b>New graphics</b> - a number of minor factions got their own ship icons, courtesy of Douglas!
        <br><br><b>Adaptive Armor is back</b> - that was in major demand, yes? :)
        <br><br><b>New firing routines</b> - damage dealing routines were modified (...do not ask). So if shot's effect looks doubtful to You, please report - <b><u>bugs in damage dealing area are more probable than usually</u></b>. 

        
<br>
<!--        
        The latest update includes:
        <br><br>
            <b>Fleet check update</b> - Rare and Uncommon fleetwide restrictions now checked separately.
        <br>
            <b>Fleet check update part 2</b> - units that require special attention (custom, special, unique...) are more exposed.
        <br>
            <b>Flight sizes</b> - Flight size of 1 or 2 fighters are now allowed (useful for superheavies or for filling out remaining points); now Minbari standard tournament fleet (utilizing 5 Tishats) is possible!
        <br>
            <b>WotCR Centauri overhaul</b> - for those who want to try slightly less advanced factions. Deficiences fixed where found, hit charts added, old variants filled - as well as some new hulls.
        <br>
            <b>Sustained fire fixed</b> - now only default mode shots will be sustained! (and weapon will be shut down at the end of sustain cycle, too)
        <br>
            <b>Fleet check update part 3</b> - ship hangar requirements are checked! (LCVs typically do require appropriate berthing space).
        <br>
            <b>Drifting ship minor fix</b> - now a shutdown ship cannot maneuver any more.
        <br>
            <b>Game description</b> - now You can put Your wishes and scenario condition right in the game!
-->
        
        
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
