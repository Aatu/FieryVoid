<?php
    include_once 'global.php';
 
	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
//      return;
	}
	
	$games = Manager::getTacGames($_SESSION["user"]);
	
	$games = json_encode($games, JSON_NUMERIC_CHECK);
	
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
			jQuery(function($){
            
				gamedata.parseServerData(<?php print($games); ?>);
                ajaxInterface.startPollingGames();

				gamedata.thisplayer = <?php print($_SESSION["user"]); ?>;                
			});

            function loadFireList(){
                ajaxInterface.getFirePhaseGames();
            }

            $(document).ready(function(){

                var header = document.getElementById("newsHeader");
                    header.innerHTML = "Latest News 15th of February 2018";
                   

                var news = document.getElementById("newsEntry");
		    /*
                    news.innerHTML += "<big><b>Merry Christmas and a Happy New Year 2018</b></big>!<br>May our little world be still here for 2019.";
                    news.innerHTML += "<br><br>";
		    */
                    news.innerHTML += "The latest update includes:";
                    news.innerHTML += "<br><br>";
                    news.innerHTML += "- new race - the <b>Abbai</b> by Wolfgang!<br>Or rather two races, as WotCR and show era Abbai fleets are separate.";
		    news.innerHTML += "<br><br>";
                    news.innerHTML += "- a few minor bugs fixed";
                    news.innerHTML += "<br><br>";
                    news.innerHTML += "Enjoy and report BUGS on FB. Also force reload (ctrl+F5) whenever something weird happens.";

                    
                    news.innerHTML += "<br><br>----------<b>LINKS</b>----------";
                    news.innerHTML +="<br><a href=\"https://www.facebook.com/groups/218482691602941/files/\" target=\"_blank\">FV FAQ</a> - differences from B5Wars and known bugs";
                    news.innerHTML +="<br><a href=\"http://b5warsvault.wikidot.com/\" target=\"_blank\">B5Wars Vault</a> - B5Wars rules and LOTS of related stuff";
                    news.innerHTML +="<br><a href=\"http://fieryvoid.net/reg.php\">Register</a> new player account";
                    news.innerHTML +="<br><a href=\"http://fieryvoid.net/chpass.php\">Change password</a> of Your account";
                     
                    	
           });
		</script>
	</head>
	<body>
	
        <img src="img/logo.png">
<!--        <div class="helphide" style="float:right" onclick="window.helper.onClickHelpHide()">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        </div>-->
        <div class="panel news">
            <div id="newsHeader">
            </div>
            <div id="newsEntry">
            </div>
        </div>
        <div class="panel large">
			<div class="logout"><a href="logout.php">LOGOUT</a></div>
			
            <table class="gametable">
                <tr><td class="centered">ACTIVE GAMES</td><td class="centered">STARTING GAMES</td><td class="centered">ACTIVE FIRE IN:</td></tr>
                <tr>
                    <td><div class="gamecontainer active subpanel">
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
                        <input type="button" id="loadFireButton" onclick="loadFireList()" value="LOAD ACTIVE FIRE">

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
