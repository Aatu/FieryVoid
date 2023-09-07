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
       
		

		---------- <b>LATEST NEWS - August 2023</b> ----------
	    <br><b>Shock Cannon fixed</b> - now causes correct power shortages on hitting Structure
	    <br><b>Minbari ships have shuttle hangars tracked</b> - so Combat Flyers could be properly based
	    <br><b>A LOT of minor updates by Geoffrey</b> - layot fixed here, ship added there :)
	    <br><b>A LOT of Nexus updates, including Velrax rebalance</b> - courtesy of Geoffrey
	    <br><b>A LOT of graphics updates</b> - courtesy of Douglas
	    <br><b>Thirdspace updates</b> - by Douglas
	    <br><b>Current combat value shown (on Info pane)</b> - for now NOT taking enhancements into account 
	    <br><b>Total value of enhancements picked shown in Fleet checker</b> - only actual enhancements are counted, not options

		 <br><br>---------- <b>update 5th of August</b> ---------- 
	    <br><b>Combat value updated</b> - now counts enhancements as well (new games only!)
	    <br><b>Combat value updated</b> - seriously damaged fighters have slightly less value
	    <br><b>Combat value updated</b> - bases and OSATs aren't penalized for lacking systems they don't need :)
	    
		 <br><br>---------- <b>update 6th of August</b> ---------- 
	    <br><b>Combat value updated</b> - some more detail added - system destruction is counted, rather than estimated from structural damage
	    
		 <br><br>---------- <b>update 7th of August</b> ---------- 
	    <br><b>Combat value updated</b> - because I wouldn't have been myself if I didn't tinker some more, apparently
	    <br><b>Thirdspace updates by Douglas</b> - ones that <i>should</i> have been added by the first update... well, they're here now - sorry all!!!
	    <br><b>Missiles ignoring jinking at range 0</b> - bug fixed

		 <br><br>---------- <b>update 28th of August</b> ---------- 
	    <br><b>Combat value updated</b> - Weapons and ElInt Sensors are considered more valuable
	    <br><b>Thirdspace balancing</b> - by Douglas
	    <br><b>Nexus pre-tournament balancing</b> - by Geoffrey
	    
		 <br><br>---------- <b>update 30th of August</b> ---------- 
	    <br><b>pre-tournament fix</b> - Velrax Hasert fighters are available again
	    <br><b>Tiers visible in fleet selection</b> - onmouseover You can see power rating comment
	    
<!--	        
		 <br><br>---------- <b>update 20th of January</b> ---------- 
	    <br><b><u><big>Thirdspace</big></u> arrived</b> - custom faction by Douglas
	    <br><b>Ammo Magazine for fighters</b> - Dilgar, Hurr
	    <br><b>Vree and Pak'ma'ra updated</b> - courtesy of Douglas
-->	    
		
	    
        <br><br><br>
        Enjoy and report BUGS on FB. Also force reload <big><font color="red">(<b><u>ctrl+F5</u></b>)</big> <b>whenever something weird happens</b></font>.

        
		
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
