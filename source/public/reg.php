<?php
include_once 'global.php';

//	if (!isset($_SESSION["hidehelper"])) {
//		$_SESSION['hidehelper'] = false;
//	}

//moving immediately to games.php may not be always good
/*
	if (isset($_SESSION["user"]) && $_SESSION["user"] != false){
		header('Location: games.php');
	}
*/	
	
    $error = "";
	if (isset($_POST["user"]) && isset($_POST["pass"]) && isset($_POST["pass2"]) && isset($_POST["secret"])){
		
        $user = trim($_POST["user"]);
        $pass = trim($_POST["pass"]);
        $pass2 = $_POST["pass2"];
        $secret = $_POST["secret"];
        global $secret_phrase;
        
        if ($pass != $pass2 || $pass == ""){
            $error = "Both passwords must be set and must match!";
        }else if($secret != $secret_phrase){
            $error = "Secret phrase is wrong";
        }else if ($user == ""){
            $error = "Username must be set!";
        }else{
            
            $result = Manager::registerPlayer($_POST["user"], $_POST["pass"]);

            if ($result === true){
                $userid = Manager::authenticatePlayer($_POST["user"], $_POST["pass"]);
		
                if ($userid != false){
                    $_SESSION["user"] = $userid['id'];
                    $_SESSION["access"] = $userid['access'];
                    header('Location: games.php');

                }
            }else if ($result === null){
                $error = "An internal server error occurred!";
            }else{
                $error = "Username is already taken.";
            }
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
<!--		<script src="client/helper.js"></script>-->
	</head>
	<body>
<!--        <div class="helphide" style="float:right"> <div id="helphideimg"></div>
        </div>-->
		<div class="panel" style="width:400px;margin:auto;">
			<form method="post">
                <div class="error"><span><?php print($error); ?></span></div>
				<table>
                <tr><td><label>Secret phrase <i>(ask on FieryVoid Facebook group!)</i>:</label></td><td><input type="text" name="secret"></input></td></tr>
				<tr><td><label>Username:</label></td><td><input type="text" name="user"></input></td></tr>
				<tr><td><label>Password:</label></td><td><input type="password" name="pass"></input></td></tr>
                <tr><td><label>Retype password:</label></td><td><input type="password" name="pass2"></input></td></tr>
				</table>
				<div><input type="submit" value="Register"></submit></div>
				
			</form>
		</div>

<!--        <div id="globalhelp" class="helppanel">
        <?php
//        	$messagelocation='reg.php';
//        	$ingame=false;
//        	include("helper.php")
        ?>
        </div>-->

	</body>
</html>
