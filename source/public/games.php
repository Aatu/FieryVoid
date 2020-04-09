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
    <!-- <div id="newsHeader">Latest News - April 2020</div> -->
    <div id="newsEntry">
        <!--
        <h3> *** Merry Christmas and a Happy New Year! *** </h3>
        <h4>   may our games be fiery and our lives peaceful</h4>
        <br>
        -->
		----------<b>Latest News - April 2020</b>----------
		
		<br><b><u>Torata</u> have arrived</b> - entire new official faction, prepared by Douglas!
        <br>
		
		<br><b>FV-related support files</b> - moved from Facebook group to here (FAQ, enhancements list...)! Also, faction list was added.
        <br><b>Stacks spilling to adjacent hex fixed</b> - if many units entered one hex, they appeared to be on nearby hexes (which might easily lead to incorrect positioning by player). This is now fixed.
        <br><b>StarWars units rebalanced</b> - generally boosted.
        <br><b>Called shots fixed</b> - now properly taking Rolled state into account.        
        <br><b>Turn into pivot fixed</b> - now fighters actually need to have necessary thrust available :) .
        <br><b>Initial Initiative changed</b> - now it assumes speed of 5 (does affect turn 1 movement order, especially for simultaneous Ini battles).
        <br><b>Accelerator interception fixed</b> - now Accelerator weapons (Molecular Pulsar, Particle Accelerator...) are properly recognized as long-recharge for purposes of interception.
        <br><b>Firing log fixed</b> - weapons that destroy systems on units other than target (Flash, Ramming...) now don't cause firing log to crash!
        <br><b>Fighter ramming is being added</b> - I'm trying to check current games to make sure where it's safe to do so, but if I do break a game... sorry!!!
        <br><b>Ipsha fix</b> - EM Hardened trait now actually doing something :)
        <br><b>Interface changes</b> - weapon order of entries changed, onmouseover help for some icons...
        <br><b>Interface more changes</b> - select all weapons now skips already declared ones, mode change no longer closes weapon menu.

        
	
        <br><br>
        Enjoy and report BUGS on FB. Also force reload (<b><u>ctrl+F5</u></b>) whenever something weird happens.

        
		
        <br><br>----------<b>LINKS</b>----------
		<br><a href="http://b5warsvault.wikidot.com/" target="_blank">B5Wars Vault</a> - B5Wars rules and LOTS of related stuff
        <br><a href="https://www.facebook.com/groups/218482691602941/" target=\"_blank\">Fiery Void Facebook group</a> - if You want to discuss, ask for help or report a bug!
		<br><a href="files/FV_FAQ.txt">Fiery Void FAQ</a> - differences from B5 Wars, known bugs... look here if something works contrary to how You expect it to!
		<br><a href="files/enhancements_list.txt">Fiery Void Enhancement list</a> - unit enhancements available, with short explanations.
		<br><a href="files/FV_factions.txt">Fiery Void Factions list</a> - short info on more exotic rules and systems of fleets available.
				
		<br><br>----------<b>PLAYER ACCOUNT</b>----------
        <br><a href="chpass.php">Change password</a> of Your account
		or <a href="reg.php">Register</a> new player account
		
		
		
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
