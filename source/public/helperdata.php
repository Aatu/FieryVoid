<?php
    include_once 'global.php';
	if (isset($_POST["hidehelper"])) {
		$_SESSION['hidehelper'] = $_POST["hidehelper"];
		//print($_SESSION['hidehelper']);
	} else if (isset($_GET["hidehelper"])){
		print($_SESSION['hidehelper']);
	} else if (isset($_GET["message"]) && isset($_GET["helpimg"]) && isset($_GET["nextpageid"])){
	//werkt nu altijd hetzelfde; zou onderscheid moeten krijgen tussen gamemode normal n tutorial
		$messagelocation='gamephase'.$_GET["message"];
		Debug::Log($messagelocation);
	    $ret=json_encode(HelpManager::getHelpMessage($messagelocation),JSON_NUMERIC_CHECK);
		print($ret);
		Debug::Log($ret);
	} 

?>
