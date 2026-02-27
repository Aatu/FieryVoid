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
    $pass2 = trim($_POST["pass2"]);
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
        <link href="styles/reg.css" rel="stylesheet" type="text/css">
		<script src="https://code.jquery.com/jquery-4.0.0.min.js"></script>
<!--		<script src="client/helper.js"></script>-->
	</head>
    <body class="reg-background">
<!--        <div class="helphide" style="float:right"> <div id="helphideimg"></div>
        </div>-->

<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">    
  <div class="top-right-row">
    <a href="index.php">Back to Login</a>
  </div>
</header>    
		<div class="reg-panel">
            <div class="resources reg-resources">Register your account below:</div>

			<form class="reg-form" method="post">
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

				<tr class="reg-input-row">
                    <td><label for="user">Username:</label></td>
                    <td><input class="reg-input-field" type="text" name="user" id="user"></input></td>
                </tr>
				<tr class="reg-input-row">
                    <td><label for="pass">Password:</label></td>
                    <td>
                        <div class="password-wrapper">
                            <input class="password-input-field" type="password" name="pass" id="pass"></input>
                            <span class="password-toggle-icon" onclick="togglePassword('pass', this)">
                                <!-- Eye Icon (SVG) -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr class="reg-input-row">
                    <td><label for="pass2">Re-type password:</label></td>
                    <td>
                        <div class="password-wrapper">
                            <input class="password-input-field" type="password" name="pass2" id="pass2"></input>
                            <span class="password-toggle-icon" onclick="togglePassword('pass2', this)">
                                <!-- Eye Icon (SVG) -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </span>
                        </div>
                    </td>
                </tr>
				</table>
                <div class="submit-container">
                    <input type="submit" class="btn btn-primary reg-submit-btn" value="Register">
                </div>                		
			</form>

            <script>
            function togglePassword(inputId, icon) {
                var input = document.getElementById(inputId);
                var path = icon.querySelector('svg path'); // Naive selection for icon swap if we wanted it
                
                if (input.type === "password") {
                    input.type = "text";
                    icon.style.color = "#8bcaf2"; // Highlight when visible
                } else {
                    input.type = "password";
                    icon.style.color = "#888"; // Dim when hidden
                }
            }
            </script>
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