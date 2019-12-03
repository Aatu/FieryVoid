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
    <div id="newsHeader">Latest News - November 2019</div>
    <div id="newsEntry">
        <!--
        <h3> *** Merry Christmas and a Happy New Year! *** </h3>
        <h4>   may our games be fiery and our lives peaceful</h4>
        <br>
        -->
        <u>The <b>Cascor</b> have arrived - entire new official faction, courtesy of <b>Thomas Solway</b>!</u>

<b>StarWars overhauled</b> - for those who want to use alternate universe ships. After playtesting the large Star Wars units slight changes were made to make them more interesting in engagements. Also, new units were added (mostly not so iconic).
<br><br>
        <b><u>Rule changes</u></b>
        <br><b>Fighter damage allocation</b> - now attempting to actively minimize damage to flight. Not as good as tabletop player would do (in fact, doesn't even have access to informatio player would have), but should be far better than random allocation. Expect heavy fighter flights to (re-)gain a lot of their toughness.
        <br><b>Antifighter firing order optimized</b> - as fighters allocate damage to minimize it, weapons firing at them get arranged for maximum effect.

<br>
<br><b><u>Gameplay streamlining</b></u>
 <br>       <b>Boost streamlined</b> - You can boost even if You don't have power for it (although You'll have to find power for it before commit, of course ;) )
 <br>       <b>EW boost streamlined</b> - You can change Sensor output even if You already started to assign EW. It'll be made legal at commit.
<br><b><i>Remove All EW</i> function added</b> - now You can reset Your EW without finding every target You allocated it to!
<br><b>Trait information exposed</b> - various unit traits are now shown as notes. You don't have to remember whether a given ship is supposed to be Gravitic or what kind of hangar small craft requires!
<br>
<br><b><u>General reminder</b></u>
<br>You can look inside the game, see details, and THEN decide whether to pick it up. You're not forced to take or leave the game based on name alone.
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
