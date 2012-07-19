<?php
    include_once 'global.php';

	if (isset($_SESSION["user"]) && $_SESSION["user"] != false){
		header('Location: games.php');
	}
	
	if (isset($_POST["user"]) && isset($_POST["pass"])){
		
		$userid = Manager::authenticatePlayer($_POST["user"], $_POST["pass"]);
		
		if ($userid != false){
			$_SESSION["user"] = $userid;
			header('Location: games.php');
		
		}
		
	}
	
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>FieryVoid</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	</head>
	<body>
		<div class="panel" style="width:400px;margin:auto;">
			<form method="post">
				<table>
				<tr><td><label>Username:</label></td><td><input type="text" name="user"></input></td></tr>
				<tr><td><label>Password:</label></td><td><input type="password" name="pass"></input></td></tr>
				</table>
				<div><input type="submit" value="login"></submit></div>
				
			</form>
		</div>

	</body>
</html>