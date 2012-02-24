<?php
class Mathlib{
	
	public static function addToHexFacing($facing, $add){
	
		if (($facing + $add) > 5){
			return self::addToHexFacing(0, ($facing + $add - 6));
		}
		
		if (($facing + $add) < 0){
			return self::addToHexFacing(6, $facing + $add);
		}
		
		return $facing + $add;
	
	}
	
	public static function getDistance($start, $end){
		return sqrt(($end["x"]-$start["x"])*($end["x"]-$start["x"]) + ($end["y"]-$start["y"])*($end["y"]-$start["y"]));
	}
	
	public static function getDistanceHex($start, $end){
		$hexWidth = 50*0.8660254*2;
        $dis = sqrt(($end["x"]-$start["x"])*($end["x"]-$start["x"]) + ($end["y"]-$start["y"])*($end["y"]-$start["y"]));
        $disInHex = $dis / $hexWidth;
		return $disInHex;
	}
	
	
	
	
	public static function getCompassHeadingOfShip($observer, $target){
		
		$oPos = $observer->getCoPos();
		$tPos = $target->getCoPos();
		
		if ($oPos["x"] == $tPos["x"] && $oPos["y"] == $tPos["y"]){
			if (BaseShip::hasBetterIniative( $observer, $target)){
				$oPos =  $observer->getPreviousCoPos();
			}else{
				$tPos =  $target->getPreviousCoPos();
			}
		
		}
		return self::getCompassHeadingOfPoint($oPos, $tPos);
		
	}
		
	public static function getCompassHeadingOfPoint($observer, $target){
		$dX = $target["x"] - $observer["x"];
		$dY = $target["y"] - $observer["y"];
		$heading = 0.0;
					
		if ($dX == 0){
			if ($dY>0){
				$heading = 180.0;
			}else{
				$heading = 0.0;
			}
			
		}else if ($dY == 0){
			if ($dX>0){
				$heading = 90.0;
			}else{
				$heading = 270.0;

			}
		}else if ($dX>0 && $dY<0 ){
			$heading = Mathlib::radianToDegree(atan($dX/abs($dY)));
		}else if ($dX>0 && $dY>0 ){
			$heading = Mathlib::radianToDegree(atan($dY/$dX)) + 90;
		}else if ($dX<0 && $dY>0){
			$heading = Mathlib::radianToDegree(atan(abs($dX)/$dY)) + 180;
		}else if ($dX<0 && $dY<0){
			$heading = Mathlib::radianToDegree(atan($dY/$dX)) + 270;
		}
		
		
		return Mathlib::addToDirection(round($heading), -90);
	}
	
	public static function radianToDegree($angle){
			return $angle * (180.0 / pi());
	}
	
	public static function addToDirection($current, $add){
		$ret = 0;
		if ($current + $add > 360){
			$ret =  0+($add-(360-$current));
				
		}else if ($current + $add < 0){
			$ret = 360 + ($current + $add);
		}else{	
			$ret = $current + $add;
		}

		return $ret;
	}
	
	public static function isInArc($direction, $start, $end){
	
		if ($start == $end)
			return true;
			
		if (($direction == 0 && $start == 360) || ($direction == 0 && $end == 360))
			return true;
	
		if ($start > $end){
			return ($direction >= $start || $direction <= $end);
			
		}else if ($direction >= $start && $direction <= $end){
			return true;
		}
	
		return false;
	
		
	}


}

?>