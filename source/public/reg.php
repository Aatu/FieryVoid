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
        <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">        
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<!--		<script src="client/helper.js"></script>-->
	</head>
    <body  style="background: url('./img/webBackgrounds/lp5.jpg') no-repeat center center fixed; background-size: cover;">
<!--        <div class="helphide" style="float:right"> <div id="helphideimg"></div>
        </div>-->
        <img src="img/logo.png">
        
		<div class="news-panel" style="width:500px; margin:auto; margin-top: 50px; padding: 15px 15px 15px 15px;">
			<form style="margin-right: 0px;" method="post">
                <div class="error"><span><?php print($error); ?></span></div>
				<table>
				<tr><td><label style="font-weight: bold;">
                        Secret phrase:
                        <br><span style="font-weight: normal;">
                            (If not sure, ask in our 
                            <b><a style="color: #8bcaf2" href="https://discord.gg/Pmmdfz4NbC"><u>Discord</u></a></b> or 
                            <b><a style="color: #8bcaf2" href="https://www.facebook.com/groups/fieryvoid"><u>Facebook</u></a></b> groups)
                        </span>
                        </label>
                    </td><td><input style="text-align: right; margin-bottom: 15px; margin-left: 30px;" type="text" name="secret"></td>
                </tr>
				<tr><td><label style="font-weight: bold;">Username:</label></td><td><input style="text-align: right; margin-bottom: 0px; margin-left: 30px;" type="text" name="user"></input></td></tr>
				<tr><td><label style="font-weight: bold;">Password:</label></td><td><input style="text-align: right; margin-bottom: 0px; margin-left: 30px;" type="password" name="pass"></input></td></tr>
                <tr><td><label style="font-weight: bold;">Re-type password:</label></td><td><input style="text-align: right; margin-bottom: 0px; margin-left: 30px;" type="password" name="pass2"></input></td></tr>
				</table>
                <div style="text-align: right;">
                    <input type="submit" class="btn btn-primary" style="margin-top: 10px; margin-bottom: 0px; margin-right: 5px;" value="Register">
                </div>                		
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
