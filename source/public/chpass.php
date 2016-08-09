<?php
	include_once 'global.php';
	if (isset($_SESSION["user"]) && $_SESSION["user"] != false){
		header('Location: games.php');
	}
	
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
                  	$_SESSION["user"] = $userid['id'];
                    $_SESSION["access"] = $userid['access'];
                    header('Location: games.php');
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
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	</head>
	<body>
		<div class="panel" style="width:400px;margin:auto;">
			<form method="post">
                <div class="error"><span><?php print($error); ?></span></div>
			<table>
				<tr><td><label>Username:</label></td><td><input type="text" name="user"></input></td></tr>
				<tr><td><label>Password (old):</label></td><td><input type="password" name="passold"></input></td></tr>
				<tr><td><label>Password (new):</label></td><td><input type="password" name="passnew"></input></td></tr>
                		<tr><td><label>Retype new password:</label></td><td><input type="password" name="passnew2"></input></td></tr>
			</table>
				<div><input type="submit" value="Change"></submit></div>
				
			</form>
		</div>


	</body>
</html>
