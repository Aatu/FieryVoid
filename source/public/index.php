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
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<!--		<script src="client/helper.js"></script>-->
	</head>
	<body>
        <img src="img/logo.png">
<!--        <div class="helphide" style="float:right"> <div id="helphideimg"></div>
        </div>-->
		<div class="panel" style="width:400px;margin:auto;">
			<a href="./reg.php">Register new player account</a><br>
			or <b>Log in:</b>
			<form method="post">
				<table>
				<tr><td><label>Username:</label></td><td><input type="text" name="user"></input></td></tr>
				<tr><td><label>Password:</label></td><td><input type="password" name="pass"></input></td></tr>
				</table>
				<div><input type="submit" value="login"></submit></div>
			</form>
		</div>

<!--        <div id="globalhelp" class="helppanel">
        <?php
//        	$messagelocation='index.php';
//        	$ingame=false;
//        	include("helper.php")
        ?>
        </div>-->

	</body>
</html>
