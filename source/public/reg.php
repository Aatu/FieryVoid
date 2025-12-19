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
// Secret phrase requirement commented out:
if (isset($_POST["user"]) && isset($_POST["pass"]) && isset($_POST["pass2"])){
// if (isset($_POST["user"]) && isset($_POST["pass"]) && isset($_POST["pass2"]) && isset($_POST["secret"])){
		
    $user = trim($_POST["user"]);
    $pass = trim($_POST["pass"]);
    $pass2 = $_POST["pass2"];
    // $secret = $_POST["secret"];
    // global $secret_phrase;
    
    if ($pass != $pass2 || $pass == ""){
        $error = "<span style='color: red; margin-left: 3px'>Both passwords must be set and must match!</span>";
    // }else if($secret != $secret_phrase){
    //     $error = "Secret phrase is wrong";
    }else if ($user == ""){
        $error = "<span style='color: red; margin-left: 3px'>Username must be set!</span>";
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
            $error = "<span style='color: red; margin-left: 3px'>That username is already taken!</span>";
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
    <body  style="background: url('./img/maps/20.Pillars.jpg') no-repeat center center fixed; background-size: cover;">
<!--        <div class="helphide" style="float:right"> <div id="helphideimg"></div>
        </div>-->

<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">    
  <div class="top-right-row">
    <a href="index.php">Back to Login</a>
  </div>
</header>    
		<div class="reg-panel">
            <div class="resources" style="font-weight: bold;  font-size: 1.1em;">Register your account below:</div>

			<form style="margin-right: 0px;" method="post">
                <div class="error"><span><?php print($error); ?></span></div>
				<table>
				
                <!-- Secret phrase field commented out:
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
                -->

				<tr><td><label style="font-weight: normal;">Username:</label></td><td><input style="text-align: left; margin-bottom: 0px; margin-left: 60px;" type="text" name="user"></input></td></tr>
				<tr><td><label style="font-weight: normal;">Password:</label></td><td><input style="text-align: left; margin-bottom: 0px; margin-left: 60px;" type="password" name="pass"></input></td></tr>
                <tr><td><label style="font-weight: normal;">Re-type password:</label></td><td><input style="text-align: left; margin-bottom: 0px; margin-left: 60px;" type="password" name="pass2"></input></td></tr>
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
<footer class="site-disclaimer" style="margin-top: 1000px ">
  <p>
DISCLAIMER — Fiery Void is an unofficial, fan-created work based on concepts from Agents of Gaming’s Babylon 5 Wars. 
It is not affiliated with, endorsed by, or sponsored by any official rights holders. 
All trademarks and copyrights remain the property of their respective owners.
  </p>
</footer>

	</body>
</html>