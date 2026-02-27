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
        <link href="styles/reg.css" rel="stylesheet" type="text/css">
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!--		<script src="client/helper.js"></script>-->
	</head>
	<body class="login-background">
        <img src="img/logo.png">
<!--        <div class="helphide" style="float:right"> <div id="helphideimg"></div>
        </div>-->
		<div class="reg-panel">
			<a class="login-link" href="./reg.php"><b><u>Register new account</u></b></a>
			<span class="login-or-text"> or log in below:</span>
			<form method="post">
				<table class="login-table">
				<tr><td><label for="user" class="login-label">Username:</label></td><td><input class="login-input" type="text" name="user" id="user"></input></td></tr>
				<tr><td><label for="pass" class="login-label">Password:</label></td><td>
                    <div class="login-password-wrapper">
                        <input class="login-input" type="password" name="pass" id="pass"></input>
                        <span class="password-toggle-icon" onclick="togglePassword('pass', this)">
                            <!-- Eye Icon (SVG) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                              <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                        </span>
                    </div>
                </td></tr>
				</table>
                <div class="login-submit-container">
                    <input type="submit" class="btn btn-primary login-submit-btn" value="Login">
                </div>	
			</form>
            <script>
            function togglePassword(inputId, icon) {
                var input = document.getElementById(inputId);
                
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
//        	$messagelocation='index.php';
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
