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
    <!-- <div id="newsHeader">Latest News - September 2020</div> -->
    <div id="newsEntry">

	    
       
<!--		
        <h3> *** Merry Christmas and a Happy New Year! *** </h3>
        <h4><i>may our games be fiery and our lives peaceful</i></h4>
        <br>
-->	    
		<big><b>Welcome to Fiery Void, an adaptation of the 'Babylon 5 Wars' tabletop game, by Agents of Gaming!</b></big>

		<br><br>---------- <b>LATEST NEWS - April 2025</b> ----------

	    <br><br><b>Features and Enhancements</b>
	    <br>Combat Logs for previous turns can now be reviewed without watching Replay.
	    <br>A new Deployment Zone Preview image has been added to Create Game screen, to show how Deployment Zones settings will look like on game map.
	    <br>ISD, Tier and Custom filters added to Fleet Selections screen.
	    <br>Split targeting firing modes rolled out to several new weapons, to allow targeting of different ships on the same turn.
	    <br>Info Tab will now automatically update Fleet Lists and Fleet Values without needing to refresh the web page.
	    <br>Nexus and Escalation Wars units receiving ability to board enemy ships.



	    <br><br><b>Bug Fixes</b>
	    <br>Fixed Battle Laser system notes / M-Missile public notes.
	    <br>Fixed Octurion Battleship Twin Array Forward Arcs.
	    <br>Asteroids should now not be placed within Moon terrain.
	    <br>Opacity now properly works for Ballistic Line sprites.
	    <br>Added ISD dates for all official faction ships that were missing these.



	    
       <br><br>
<!--	        
		 <br><br>---------- <b>update 20th of January</b> ---------- 
	    <br><b><u><big>Thirdspace</big></u> arrived</b> - custom faction by Douglas
	    <br><b>Ammo Magazine for fighters</b> - Dilgar, Hurr
	    <br><b>Vree and Pak'ma'ra updated</b> - courtesy of Douglas
       <br><br>
-->	
	    

        <br><br>---------- <b>HOW TO GET STARTED</b> ----------
		<br><a href="files/Fiery_Void_-_How_to_Play.docx">How to Play Fiery Void</a> - Text tutorial (friendlier than full blown game manual - to help you get started quickly!)
		<br><a href="https://www.youtube.com/watch?v=pOLp4RF9cjY&list=PLTGKagm5KkMxB8oKBiIUeoBQTRYz2z0-3" target=\"_blank\">Video tutorials</a> - Video tutorials are also available on YouTube!
	    <br><a href="https://discord.gg/kjZAjr3" target=\"_blank\">Discord</a> - Join our community to help find games and discuss all things B5 / Fiery Void!
        <br><a href="https://www.facebook.com/groups/218482691602941/" target=\"_blank\">Facebook group</a> - We're also on Facebook!
        	
        <br><br>---------- <b>FURTHER INFO</b> ----------        			
		<br><a href="files/FV_factions.txt">Fiery Void Factions & Tier List</a> - Overview of rules and systems of the fleets available in Fiery Void
	    <br><a href="files/FV_FAQ.txt">Fiery Void FAQ</a> - Differences from B5 Wars, known bugs... look here if something works contrary to how you expect it to!
		<br><a href="files/enhancements_list.txt">Common Systems & Enhancement List</a> - Details of enhancements and other common systems e.g. Boarding / Missiles	    
		<br><a href="http://b5warsvault.wikidot.com/" target="_blank">B5Wars Vault</a> - Full B5Wars <a href="http://www.tesarta.com/b5wars/aogwarskitchensink.pdf" target="_blank">rules</a> and LOTS of other related B5W stuff
        
        <br><br>Enjoy and report BUGS on Discord or Facebook. Also force reload <big><font color="red">(<b><u>ctrl+F5</u></b>)</big> <b>whenever something weird happens</b></font>.		
				
		<br><br>---------- <b>ACCOUNT MANAGEMENT</b> ----------
        <br><a href="chpass.php">Change password</a> of your account
		or <a href="reg.php">Register</a> new player account
		
		        
<!--		
        <br><br>---------- <b>LINKS</b> ----------
		<br><a href="http://b5warsvault.wikidot.com/" target="_blank">B5Wars Vault</a> - B5Wars <a href="http://www.tesarta.com/b5wars/aogwarskitchensink.pdf" target="_blank">rules</a> (under the name of AoG Wars) and LOTS of related stuff
	    	<br><a href="https://discord.gg/kjZAjr3" target=\"_blank\">Discord channel</a> - talk about FV/B5 with likeminded people!
        	<br><a href="https://www.facebook.com/groups/218482691602941/" target=\"_blank\">Facebook group</a> - if You want to discuss, ask for help or report a bug! <i>(nowadays mostly superseded by discord)</i>
		<br><a href="files/Fiery_Void_-_How_to_Play.docx">How to play FV</a> - text tutorial (way friendlier than full blown game manual - to start up quickly!).
		<br><a href="https://www.youtube.com/channel/UCpzERJTeVoFVon_QqWQxesw/featured" target=\"_blank\">Video tutorials</a> - Fiery Void video tutorials are available on YouTube!
	    	<br><a href="files/FV_FAQ.txt">Fiery Void FAQ</a> - differences from B5 Wars, known bugs... look here if something works contrary to how You expect it to!
		<br><a href="files/enhancements_list.txt">Fiery Void Enhancement list</a> - unit enhancements available, with short explanations.
		<br><a href="files/FV_factions.txt">Fiery Void Factions list</a> - short info on more exotic rules and systems of fleets available.
		<br><a href="files/FV_tiers.txt">Fiery Void Tiers list</a> - UNOFFICIAL and SUBJECTIVE factions grouping.
		<br><a href="randomTest.php">RNG test</a> - what is Lady's mood today?
				
		<br><br>---------- <b>PLAYER ACCOUNT</b> ----------
        <br><a href="chpass.php">Change password</a> of Your account
		or <a href="reg.php">Register</a> new player account
-->		
		
		
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
