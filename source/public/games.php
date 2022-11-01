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
        <h4>may our games be fiery and our lives peaceful</h4>
        <br>
-->
       
		

		---------- <b>LATEST NEWS - October 2022</b> ----------
	    <br><b>text tutorial for new players</b> - courtesy of Douglas
	    <br><b>Variable missile loads</b> - new factions are in (Llort)
	    <br><b>Trek (Federation TOS) rebalanced</b> - still in playtest phase
	    
	    	<br><br>---------- <b>update 24th of October</b> ---------- 
	    <br><b>Variable missile loads</b> - Hurr
	    <br><b>Particle Impeder made kosher</b> - now it's boosted with EW rather than Power (and boost stops all incoming fire rather than just fighters)
	    <br><b>Abbai made kosher</b> - with proper Impeder available, Abbai regained their rightful EW, while losing matching Power surplus. Now they can be even better protected... but not all around :)
	    <br><b>Fixed map as default</b> - when creating new game, default choice is now fixed map of standard size
	    <br><b>Map size switches</b> - as knife fights seem popular lately, I have added buttons to set proper map dimensions and deployment zones for such a fight (and back to standard size, too) with a single click

	    
	    	<br><br>---------- <b>update 31st of October</b> ----------
	    <br><b>Nexus universe polising</b> - courtesy of Geoffrey
	    <br><b>Particle Impeder display</b> - now shows boost level (=shield rating) rather than weapon default arming status (irrelevant with 1/turn weapon). 
	    <br><b>Ballistics calculations corrected</b> - hit chance calculations for ballistics were corrected (somehow they took current rather than launch position to determine angle of impact...)
 	    <br><b>Vorlon Discharge Cannon corrected</b> - now it should correctly draw power
 	    <br><b>Jump Engine hit table corrected</b> - I've found some entries calling for Jump Drive or JumpEngine hits - those were corrected to Jump Engine that's actually the correct system name in FV
	    
<!--	    
		 <br><br>---------- <b>update 26th of September</b> ---------- 
	    <br><b>Variable missile loads</b> - new factions are in (Dilgar, Drazi)
	    <br><b>Suicidal Dilgar</b> - Delegor added (scenario unit)
-->
	    
		
	    
        <br><br><br>
        Enjoy and report BUGS on FB. Also force reload <big><font color="red">(<b><u>ctrl+F5</u></b>) whenever something weird happens</font></big>.

        
		
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
