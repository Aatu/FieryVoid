<?php
class Mathlib{
	
	public static $hexWidth = 86.60254;
	
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
		//print(self::$hexWidth);
		//var_dump($start);
		//var_dump($end);
        $dis = sqrt(($end["x"]-$start["x"])*($end["x"]-$start["x"]) + ($end["y"]-$start["y"])*($end["y"]-$start["y"]));
        $disInHex = $dis / self::$hexWidth;
		return $disInHex;
	}
	
	
	public static function getCompassHeadingOfPos($observer, $pos){
		
		$oPos = $observer->getCoPos();
		$tPos = $pos;
		
		if ($oPos["x"] == $tPos["x"] && $oPos["y"] == $tPos["y"]){
			$oPos =  $observer->getPreviousCoPos();
			
		}
		
		//print($observer->name . " ".$oPos["x"] .", ". $oPos["x"]. " and ". $tPos["y"] .", ". $tPos["y"]);
		return self::getCompassHeadingOfPoint($oPos, $tPos);
		
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
	
	public static function getHexToDirection($d, $x, $y){
		
		if ($y%2==0){
			return self::getHexToDirectionEven($d, $x, $y);
		}else{
			return self::getHexToDirectionUneven($d, $x, $y);
		}
		
	}
	
	public static function getHexToDirectionEven($d, $x, $y){
		if ($d == 0){
			return array("x"=>$x+1, "y"=>$y, "xO"=>0, "yO"=>0);
		}
		if ($d == 1){
			return array("x"=>$x, "y"=>$y+1, "xO"=>0, "yO"=>0);
		}
		if ($d == 2){
			return array("x"=>$x-1, "y"=>$y+1, "xO"=>0, "yO"=>0);
		}
		if ($d == 3){
			return array("x"=>$x-1, "y"=>$y, "xO"=>0, "yO"=>0);
		}
		if ($d == 4){
			return array("x"=>$x-1, "y"=>$y-1, "xO"=>0, "yO"=>0);
		}
		if ($d == 5){
			return array("x"=>$x, "y"=>$y-1, "xO"=>0, "yO"=>0);
		}
		
		return array("x"=>$x, "y"=>$y, "xO"=>0, "yO"=>0);
	
	}

	public static function getHexToDirectionUneven($d, $x, $y){
		if ($d == 0){
			return array("x"=>$x+1, "y"=>$y, "xO"=>0, "yO"=>0);
		}
		if ($d == 1){
			return array("x"=>$x+1, "y"=>$y+1, "xO"=>0, "yO"=>0);
		}
		if ($d == 2){
			return array("x"=>$x, "y"=>$y+1, "xO"=>0, "yO"=>0);
		}
		if ($d == 3){
			return array("x"=>$x-1, "y"=>$y, "xO"=>0, "yO"=>0);
		}
		if ($d == 4){
			return array("x"=>$x, "y"=>$y-1, "xO"=>0, "yO"=>0);
		}
		if ($d == 5){
			return array("x"=>$x+1, "y"=>$y-1, "xO"=>0, "yO"=>0);
		}
		
		return array("x"=>$x, "y"=>$y, "xO"=>0, "yO"=>0);
	
	}
	
	public static function hexCoToPixel($h, $v){
		$hl = 50;
        $a = $hl*0.5;
        $b = $hl*0.8660254; //0.86602540378443864676372317075294
                
        $x  = 0;
        $y = 0;
      
        if ($v%2 == 0){
            $x = $h*$b*2;
        }else{
            $x = $h*$b*2+$b;
        }
        
        $y = $v*$hl*2-($a*$v);
        
        $x -= $b*2;
        $y -= $hl*1.5;
                
        $x += $b;
        $y += $hl;
        
        return array("x"=>$x, "y"=>$y);
	}
	
	
}

?>
