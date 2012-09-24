<?php

    if ($_SESSION["access"] != 5)
        return;
    
    if (isset($_POST["changeToUser"]))
    {
        $_SESSION['user'] = $_POST["changeToUser"];
    }

?>
