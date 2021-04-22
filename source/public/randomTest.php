<?php
include_once 'global.php';

$numberOfRolls = 10000; //how many times dice are rolled
$dieSize = 100;//let's roll d100, that's primary FV die roll
$noOfBlocks = 5;//into how many blocks the pool is split for clumping assessment
$blockSize = $dieSize/$noOfBlocks;

$resultList = array(); //results rolled
$resultByRoll = array(); //how many times each result happened
$longestClump = 0;
$numberOfClumps = 0;
$averageClumpLength = 0;
$averageRoll = 0;

$rollTotal = 0;
$currentResult = 0;
$currentClumpLength = 0;
$currentClumpValue = 0;
$previousClumpValue = 0;

?>


<!DOCTYPE HTML>
<html>
<head>
    <title>Fiery Void - RNG Test</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>


<?php
/* HEADER */
$description = '<big><b>Fiery Void RNG Test</b></big><br>'; 
echo $description;
$description = "Test proceeds as follows: <b>$dieSize</b>-sided die is rolled <b>$numberOfRolls</b> times. Average is counted, as well as number of times each result happened.<br>"; 
echo $description;
$description = "In addition to the above, pool of possible results is split to <b>$noOfBlocks</b> ranges, and average and maximum length of results (clump) within same range is counted.<br>"; 
echo $description;
echo '<br><br>';
?>


<?php
/* actual testing */
for($i=1;$i<=$numberOfRolls;$i++){
	$currentResult = Dice::d($dieSize, 1);
	
	$resultList[] = $currentResult;
	
	$rollTotal += $currentResult;
	
	if(array_key_exists($currentResult,$resultByRoll)){		
		$resultByRoll[$currentResult] += 1;
	}else{
		$resultByRoll[$currentResult] = 1;
	}
		
	$currentClumpValue = ceil($currentResult/$blockSize);
	if($currentClumpValue==$previousClumpValue){//clump continues!
		$currentClumpLength++;
		if($currentClumpLength>$longestClump) $longestClump=$currentClumpLength;
	}else{//new clump!
		$numberOfClumps++;
		$currentClumpLength = 1;
		$previousClumpValue = $currentClumpValue;
	}
	
}

//everything is rolled and running sums are ready, now final calculations...
$averageRoll = $rollTotal/$numberOfRolls;
$averageClumpLength = $numberOfRolls/max(1,$numberOfClumps);
?>



<?php
/* results */
echo '<br><big>RESULTS:</big><br>';
echo "Average roll: <b>$averageRoll</b><br>";
echo "Longest clump length: <b>$longestClump</b>, average clump length: <b>$averageClumpLength</b><br>";
?>


<br><br><table>
<tr><td colspan = "2"><big>Rolls by value</big></td></tr>
<tr><td><b>Value</b></td><td><b>No of rolls</b></td></tr>
<?php
for($i=1;$i<=$dieSize;$i++){
	$value = $i;
	$count = 0;
	if(array_key_exists($i,$resultByRoll)){		
		$count = $resultByRoll[$i];
	}
	echo "<tr><td>$value</td><td>$count</td></tr>";
}
?>
</table>



<br><br><table>
<tr><td><big>Actual rolls</big></td></tr>
<?php
foreach($resultList as $roll){
	echo "<tr><td>$roll</td></tr>";
}
?>
</table>


</body>
</html>