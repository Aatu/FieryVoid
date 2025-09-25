<?php
	include_once 'global.php';
/*	if (isset($_SESSION["user"]) && $_SESSION["user"] != false){
		header('Location: games.php');
	}
*/	
	
    $error = "";
	if (isset($_POST["user"]) && isset($_POST["passold"]) && isset($_POST["passnew"]) && isset($_POST["passnew2"])){
		
        $user = trim($_POST["user"]);
        $passold = trim($_POST["passold"]);
        $passnew = trim($_POST["passnew"]);
        $passnew2 = trim($_POST["passnew2"]);

        if ($passnew != $passnew2 || $passnew == ""){
            $error = "Both passwords must be set and must match!";
        }else if($passold == ""){
            $error = "Old password must be entered";
        }else if ($user == ""){
            $error = "Username must be entered!";
        }else{
            
            $result = Manager::changePassword($user, $passold, $passnew);
            if ($result === true){
                $userid = Manager::authenticatePlayer($user, $passnew);
		
                if ($userid != false){
 			$error = "Password change successful!";
 			$user = '';
 			$passold = '';
 			$_POST["user"] = '';
                }
            }else if ($result === null){
                $error = "An internal server error occurred!";
            }else{
                $error = "Password change not succesful.";
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
        <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">   		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	</head>
    <body  style="background: url('./img/maps/14.PlanetsNear.jpg') no-repeat center center fixed; background-size: cover;">
        <img src="img/logo.png">		

		<div class="reg-panel" style="margin-top: 40px; padding: 15px 15px 10px 15px">
				
			<form method="post">
							
				<div class="error"><span><?php print($error); ?></span></div>
				<table style="font-size: 14px; margin-left: 12px;">
					<tr><td><label>Username:</label></td><td ><input type="text" name="user"></td></tr>
					<tr><td><label>Password (old):</label></td><td><input type="password" name="passold"></td></tr>
					<tr><td><label>Password (new):</label></td><td><input type="password" name="passnew"></td></tr>
					<tr><td><label>Retype new password:</label></td><td><input type="password" name="passnew2"></td></tr>
				</table>
				<div style="margin-right: 10px; margin-top: 10px; margin-bottom: 5px; text-align: right; font-size: 12px;">
					<span ><a href="games.php" style="color: #8bcaf2">Go back to Game Lobby</a></span>
					<span style="margin-left: 3px;">or</span> 										
					<input class="btn btn-primary" type="submit" value="Change Password"style="margin-left: 4px; font-size: 12px;"></input>

				</div>	

			</form>
		</div>


	</body>
</html>
