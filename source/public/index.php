<?php
    include_once 'global.php';
//	if (!isset($_SESSION["hidehelper"])) {
//		$_SESSION['hidehelper'] = false;
//	}

	if (isset($_SESSION["user"]) && $_SESSION["user"] != false){
		header('Location: games.php');
	}
	
	if (isset($_POST["user"]) && isset($_POST["pass"])){
		
		$user = Manager::authenticatePlayer($_POST["user"], $_POST["pass"]);
		
		if ($user != false){
			$_SESSION["user"] = $user['id'];
            $_SESSION["access"] = $user['access'];
			header('Location: games.php');
		}
	}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Fiery Void - Login</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
  		<link href="styles/gamesNew.css" rel="stylesheet" type="text/css">		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<!--		<script src="client/helper.js"></script>-->
	</head>
	<body  style="background: url('./img/maps/14.PlanetsNear.jpg') no-repeat center center fixed; background-size: cover;">
        <img src="img/logo.png">
<!--        <div class="helphide" style="float:right"> <div id="helphideimg"></div>
        </div>-->
		<div class="reg-panel">
			<a style="color: #8bcaf2; font-size: 1.1em;" href="./reg.php"><b><u>Register new account</u></b></a>
			<span style="font-weight: bold; font-size: 1.1em;"> or log in below:</span>
			<form method="post">
				<table style="text-align: center; margin-left: 30px; margin-top: 5px; ">
				<tr><td><label style="margin-left: 30px; margin-right: 20px; font-weight: bold;">Username:</label></td><td><input style="text-align: left;" type="text" name="user"></input></td></tr>
				<tr><td><label style="margin-left: 30px; margin-right: 20px; font-weight: bold;">Password:</label></td><td><input style="text-align: left;" type="password" name="pass"></input></td></tr>
				</table>
                <div style="text-align: right;">
                    <input type="submit" class="btn btn-primary" style="margin-top: 10px; margin-bottom: 0px; margin-right: 25px;" value="Login">
                </div>	
			</form>
		
		</div>

<!--        <div id="globalhelp" class="helppanel">
        <?php
//        	$messagelocation='index.php';
//        	$ingame=false;
//        	include("helper.php")
        ?>
        </div>-->
<footer class="site-disclaimer">
  <p>
DISCLAIMER — Fiery Void is an unofficial, fan-created work based on concepts from Agents of Gaming’s Babylon 5 Wars. 
It is not affiliated with, endorsed by, or sponsored by Warner Bros., its subsidiaries, or any official rights holders. 
All trademarks and copyrights remain the property of their respective owners.
  </p>
</footer>

	</body>
</html>
