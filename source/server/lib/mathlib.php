<?php
class Mathlib{
    
    public static $hexWidth = 86.60254;
    public static $hexSideLenght = 50;
    public static $hexHeight = 75;
    
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

        if ($start instanceof BaseShip) {
            $start = $start->getHexPos();
        }

        if ($end instanceof BaseShip) {
            $end = $end->getHexPos();
        }

        if (!($start instanceof OffsetCoordinate) || !($end instanceof OffsetCoordinate)) {
            throw new Exception("You need to give 'getDistanceHex' OffsetCoordinates or BaseShips");
        }

        return $start->distanceTo($end);
    }
    
    public static function getDistanceOfShipInHex(BaseShip $ship1, BaseShip $ship2){
        $start = $ship1->getCoPos();
        $end = $ship2->getCoPos();
        
        $dis = sqrt(($end["x"]-$start["x"])*($end["x"]-$start["x"]) + ($end["y"]-$start["y"])*($end["y"]-$start["y"]));
        $disInHex = $dis / self::$hexWidth;
        return $disInHex;
        
    }
    
    
    public static function getCompassHeadingOfPos($observer, $pos){
        
        $oPos = $observer->getCoPos();
        $tPos = ($pos instanceof OffsetCoordinate) ? self::hexCoToPixel($pos) : $pos;

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

        $heading = self::radianToDegree(atan2($target["y"] - $observer["y"], $target["x"] - $observer["x"]));

        if ($heading > 0) {
            $heading = 360 - $heading;
        } else {
            $heading = abs($heading);
        }

        return $heading;

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
    
    public static function getPointInDirection( $r, $a, $cx, $cy){
            
        $x = $cx + $r * cos($a* pi() / 180);
        $y = $cy + $r * sin($a* pi() / 180);
        
        return array("x"=>$x, "y"=>$y);
    }
    
    public static function getHexToDirection($d, $x, $y){

        throw new Exception("Update to offset cordinates");
        $pos = self::hexCoToPixel($x, $y);
        $pos2 = self::getPointInDirection(self::$hexHeight, $d, $pos["x"], $pos["y"]);
        return self::pixelCoToHex($pos2["x"], $pos2["y"]);
        
        
    }
    
    
    public static function hexCoToPixel(OffsetCoordinate $position){

        $x = sqrt(3) * ($position->q - 0.5 * ($position->r & 1));
        $y = 3/2 * $position->r;

        return array("x"=>$x, "y"=>$y);
    }
    
    //ATTENTION, this is bloody magic! (I don't really know how it works)
    public static function pixelCoToHex($px, $py){
                
        $hl = self::$hexSideLenght;
        $a = $hl*0.5;
        $b = $hl*0.8660254; //0.86602540378443864676372317075294
        
        
    
        $x = $px-$b;
        $y = $py-$hl;
        
        $x += $b*2;
        $y += $hl*1.5;
     
        $h = ($x/($b*2))+1; 
        
        /*
        if (gamedata.scroll.y % 2 == 0){
            h = (x/(b*2))+0.5;
            
        }else{
            
            
        }*/
        $hx = $h;       
        $xmod = $hx - floor($hx);
        if ($xmod > 0.5){
            $xmod -= 0.5;
        }else{
            $xmod = 0.5 -$xmod;
        }
        $xmod *= 2;
        $ymod = $a*$xmod;
        
        $start = $a-$ymod;
        $i = 0;
        if ($py<=$start){
            while (true){
                
                $hexl = 0;
                if ($i%2==0){
                    $hexl = $hl +(($a-$ymod)*2);
                }else{
                    $hexl = $hl +(($ymod)*2);
                }
                
                $start -= $hexl;
                if ($py>=$start){
                    $hy = $i;
                    break;
                }
                $i--;
            }
        }else{
            
            while (true){
                
                $hexl = 0;
                if ($i%2==0){
                    $hexl = $hl +(($a-$ymod)*2);
                }else{
                    $hexl = $hl +(($ymod)*2);
                }
                
                $start += $hexl;
                if ($py<=$start){
                    $hy = $i;
                    break;
                }
                $i++;
            }
        }
    
        if ($hy  % 2==0){
            $hx = round($hx-1);
        }else{
            $hx = floor($hx);
        }
        
        if ($hx == -0)
            $hx = 0;
    
        if ($hy == -0)
            $hy = 0;
            
        return array("x"=>$hx, "y"=>$hy);
    }
    
    
}

?>
