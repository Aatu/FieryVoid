<?php 
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	array_walk(glob('./engine/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/weapons/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/tactical/*.php'), create_function('$v,$i', 'return require_once($v);'));
	session_start();

		
	

?>


<!DOCTYPE HTML>
<html>
<head>
	<title>B5CGM main</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<script>
	
	</script>
	
</head>


<body>
<?php

	$point = mathlib::getHexToDirection(0, 0, -1);
	
	print("<p>".$point["x"] .",". $point["y"]."</p>");
	
	$point = mathlib::getHexToDirection(60, 0, -1);
	
	print("<p>".$point["x"] .",". $point["y"]."</p>");
	
	$point = mathlib::getHexToDirection(120, 0, -1);
	
	print("<p>".$point["x"] .",". $point["y"]."</p>");
	
	$point = mathlib::getHexToDirection(180, 0, -1);
	
	print("<p>".$point["x"] .",". $point["y"]."</p>");
	
	$point = mathlib::getHexToDirection(240, 0, -1);
	
	print("<p>".$point["x"] .",". $point["y"]."</p>");
            
    $point = mathlib::getHexToDirection(300, 0, -1);
	
	print("<p>".$point["x"] .",". $point["y"]."</p>");
        
?>


</body>

</html>
