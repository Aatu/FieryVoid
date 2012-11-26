<?php
    include_once 'global.php';
    
    if ($_SESSION["access"] != 5)
    {
        echo 'access: '. $_SESSION["access"];
        //header('Location: index.php');
        return;
    }
    
    if (isset($_POST["changeToUser"]))
    {
        $_SESSION['user'] = $_POST["changeToUser"];
       
    }

?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Fiery Void - Login</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	</head>
	<body>
        <img src="img/logo.png">
		<div class="panel" style="width:400px;margin:auto;">
			<form method="post">
				<table>
				<tr><td><label>Username:</label></td><td><input type="text" name="changeToUser"></input></td></tr>
				</table>
                <?php
                    if (isset($_POST["changeToUser"]))
                    {
                        print("Your userid is now: " . $_SESSION['user']);

                    }
                ?>
				<div><input type="submit" value="Change userid"></submit></div>
				
			</form>
		</div>

	</body>
</html>
