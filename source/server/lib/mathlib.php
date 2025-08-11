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
            //target speed 0 is always considered to lose Ini for relative position purposes			
			//if Observer has speed 0 consider Target to have better Ini!
            if ( (BaseShip::hasBetterIniative($observer, $target) && ($observer->getSpeed()!=0)) || ($target->getSpeed()==0) ){
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

    
    //New, working in testing at least, function for Pixel To Hex coordinate conversion.
    public static function pixelCoToHex($px, $py) {
        $r = (2/3) * $py;
        $q = ($px / sqrt(3)) + (0.5 * ($r & 1));
    
        return self::hexRound($q, $r);
    }

    
    private static function hexRound($q, $r) {
        $x = $q;
        $z = $r;
        $y = -$x - $z;
    
        $rx = round($x);
        $ry = round($y);
        $rz = round($z);
    
        $x_diff = abs($rx - $x);
        $y_diff = abs($ry - $y);
        $z_diff = abs($rz - $z);
    
        if ($x_diff > $y_diff && $x_diff > $z_diff) {
            $rx = -$ry - $rz;
        } elseif ($y_diff > $z_diff) {
            $ry = -$rx - $rz;
        } else {
            $rz = -$rx - $ry;
        }
    
        return array("x" => $rx, "y" => $rz);
    }
    
    //Just a function for testing is pixelCoToHex is working correctly, or could be adjusted for vice versa - DK 03.25
    public static  function testHexConversion($q, $r){         
        $pixel = self::hexCoToPixel(new OffsetCoordinate($q, $r));       
        $hex = self::pixelCoToHex($pixel["x"], $pixel["y"]);        
            
        if ($hex["x"] == $q && $hex["y"] == $r) { 
            echo "PASS: ($q, $r) -> ({$pixel["x"]}, {$pixel["y"]}) -> ({$hex["x"]}, {$hex["y"]})\n"; 
        }else { 
            echo "FAIL: ($q, $r) -> ({$pixel["x"]}, {$pixel["y"]}) -> ({$hex["x"]}, {$hex["y"]})\n"; 
        } 
    } 
 

    /*
    private static function doLinesIntersect($p1, $p2, $p3, $p4) {
        $d1 = ['x' => $p2['x'] - $p1['x'], 'y' => $p2['y'] - $p1['y']];
        $d2 = ['x' => $p4['x'] - $p3['x'], 'y' => $p4['y'] - $p3['y']];
        
        $denom = self::crossProduct($d1, $d2);
        if ($denom == 0) return false; // Parallel lines
    
        $t = self::crossProduct(['x' => $p3['x'] - $p1['x'], 'y' => $p3['y'] - $p1['y']], $d2) / $denom;
        $u = self::crossProduct(['x' => $p3['x'] - $p1['x'], 'y' => $p3['y'] - $p1['y']], $d1) / $denom;
        
        return $t >= 0 && $t <= 1 && $u >= 0 && $u <= 1;
    }
    */


    private static function crossProduct($a, $b) {
        return $a['x'] * $b['y'] - $a['y'] * $b['x'];
    }

    private static function subtract($a, $b) {
        return ['x' => $a['x'] - $b['x'], 'y' => $a['y'] - $b['y']];
    }

    private static function isBetween($a, $b, $c, $EPSILON) {
        return (
            min($a['x'], $c['x']) - $EPSILON <= $b['x'] && $b['x'] <= max($a['x'], $c['x']) + $EPSILON &&
            min($a['y'], $c['y']) - $EPSILON <= $b['y'] && $b['y'] <= max($a['y'], $c['y']) + $EPSILON
        );
    }   


    private static function doLinesIntersect($p1, $p2, $p3, $p4) {
        $EPSILON = 1e-10;
        /*
        function crossProduct($a, $b) {
            return $a['x'] * $b['y'] - $a['y'] * $b['x'];
        }
        */

        $d1 = self::subtract($p2, $p1);
        $d2 = self::subtract($p4, $p3);
        $denom = self::crossProduct($d1, $d2);
        $diff = self::subtract($p3, $p1);

        if (abs($denom) < $EPSILON) {
            if (abs(self::crossProduct($diff, $d1)) > $EPSILON) return false;
            // Colinear — check for overlap
            return (
                self::isBetween($p1, $p3, $p2, $EPSILON) ||
                self::isBetween($p1, $p4, $p2, $EPSILON) ||
                self::isBetween($p3, $p1, $p4, $EPSILON) ||
                self::isBetween($p3, $p2, $p4, $EPSILON)
            );
        }

        $t = self::crossProduct($diff, $d2) / $denom;
        $u = self::crossProduct($diff, $d1) / $denom;

        return $t >= -$EPSILON && $t <= 1 + $EPSILON && $u >= -$EPSILON && $u <= 1 + $EPSILON;
    }


    private static function getHexCorners($hex, $hexSize) {
        $shrinkFactor = 1; // Was previsouly used to shrink hex a little below.
        $hexCo = self::hexCoToPixelLoS($hex);
       
        $cx = $hexCo['x'];
        $cy = $hexCo['y'];
    
        $angles = [30, 90, 150, 210, 270, 330];
        $corners = [];
        
        foreach ($angles as $angle) {
            $radians = deg2rad($angle);
            $corners[] = [
                'x' => $cx + $shrinkFactor * $hexSize * cos($radians),
                'y' => $cy + $shrinkFactor * $hexSize * sin($radians)
            ];
        }
        return $corners;
    }
    
    public static function checkLineOfSight($start, $end, $blockedHexes) {
        $startPixel = self::hexCoToPixelLoS($start);
        $endPixel = self::hexCoToPixelLoS($end);
        $hexSize = 50;
        $filterRadius = 5;                

        //Get variables for bounding box below
        $lineMinQ = min($start->q, $end->q);
        $lineMaxQ = max($start->q, $end->q);
        $lineMinR = min($start->r, $end->r);
        $lineMaxR = max($start->r, $end->r);

        //Filter out the start and end hexes, as these should not block shots.
        $filteredBlockedHexes = array_filter($blockedHexes, function ($hex) use ($start, $end) {
            return !($hex->q == $start->q && $hex->r == $start->r) &&
                   !($hex->q == $end->q && $hex->r == $end->r);
        });
        
        foreach ($filteredBlockedHexes as $hex) { 
            //Bounding box to reduce the number of blocked hexes we have to consider.           
            if ($hex->q < $lineMinQ - $filterRadius || $hex->q > $lineMaxQ + $filterRadius ||
                $hex->r < $lineMinR - $filterRadius || $hex->r > $lineMaxR + $filterRadius) {
                continue;
            }
            //Get the boundaries of the blocking hex.
            $corners = self::getHexCorners($hex, $hexSize);
            $cornerCount = count($corners);

            for ($i = 0; $i < $cornerCount; $i++) {
                $p1 = $corners[$i];
                $p2 = $corners[($i + 1) % $cornerCount];
                //Check if shot intersects with those boundaries.
                if (self::doLinesIntersect($startPixel, $endPixel, $p1, $p2)) {
                    return true; // Line crosses a hex edge
                }
            }
        }
        return false; // LoS is NOT blocked
    }

    //Separate function for hex to pixel conversions in LoS calculations.  Basially multiplies hexCoToPixel by 50, but this keeps values same as Front End which is helpful for debugging etc.
    public static function hexCoToPixelLoS(OffsetCoordinate $position, $hexSize = 50) {
        $x = sqrt(3) * ($position->q - 0.5 * ($position->r & 1)) * $hexSize;
        $y = (3/2 * $position->r) * $hexSize;
    
        return array("x" => $x, "y" => $y);
    }
        
    public static function getNeighbouringHexes($position, $radius = 1) {
        if ($radius <= 0) return [];

        // Helper: get neighbours in odd-row offset coordinates
        $getOffsetNeighbors = function($pos) {
            $isOddRow = ($pos['r'] % 2) !== 0;
            if ($isOddRow) {
                return [
                    ['q' => $pos['q'] + 1, 'r' => $pos['r']],      // Right
                    ['q' => $pos['q'] - 1, 'r' => $pos['r']],      // Left
                    ['q' => $pos['q'] - 1, 'r' => $pos['r'] + 1],  // Upper left
                    ['q' => $pos['q'] - 1, 'r' => $pos['r'] - 1],  // Lower left
                    ['q' => $pos['q'],     'r' => $pos['r'] + 1],  // Upper right (shifted)
                    ['q' => $pos['q'],     'r' => $pos['r'] - 1]   // Lower right (shifted)
                ];
            } else {
                return [
                    ['q' => $pos['q'] + 1, 'r' => $pos['r']],      // Right
                    ['q' => $pos['q'] - 1, 'r' => $pos['r']],      // Left
                    ['q' => $pos['q'] + 1, 'r' => $pos['r'] + 1],  // Upper right
                    ['q' => $pos['q'] + 1, 'r' => $pos['r'] - 1],  // Lower right
                    ['q' => $pos['q'],     'r' => $pos['r'] + 1],  // Upper left (shifted)
                    ['q' => $pos['q'],     'r' => $pos['r'] - 1]   // Lower left (shifted)
                ];
            }
        };

        // Use associative array as visited set
        $seen = [];
        $key = function($p) { return $p['q'] . ',' . $p['r']; };

        // Mark center visited
        $center = ['q' => $position->q, 'r' => $position->r];
        $seen[$key($center)] = true;

        $frontier = [$center];
        $results = [];

        // Expand outward by rings
        for ($d = 1; $d <= $radius; $d++) {
            $next = [];
            foreach ($frontier as $node) {
                foreach ($getOffsetNeighbors($node) as $n) {
                    $k = $key($n);
                    if (!isset($seen[$k])) {
                        $seen[$k] = true;
                        $next[] = $n;
                        $results[] = $n; // store all within <= radius
                    }
                }
            }
            $frontier = $next;
        }

        return $results;
    }


    /*
    public static function getNeighbouringHexes($position, $radius = 1) {
        if($radius == 1){    
            $isOddRow = $position->r % 2 !== 0;    
            $neighborOffsets = $isOddRow 
                ? [
                    [+1,  0], // Right
                    [-1,  0], // Left
                    [-1, +1], // Upper left
                    [-1, -1], // Lower left
                    [0, +1],  // Upper right (shifted)
                    [0, -1]   // Lower right (shifted)
                ]
                : [
                    [+1,  0], // Right
                    [-1,  0], // Left
                    [+1, +1], // Upper right
                    [+1, -1], // Lower right
                    [0, +1],  // Upper left (shifted)
                    [0, -1]   // Lower left (shifted)
                ];
        
            // Generate neighboring hexes
            $neighbors = array_map(function($offset) use ($position) {
                return [
                    'q' => $position->q + $offset[0],
                    'r' => $position->r + $offset[1]
                ];
            }, $neighborOffsets);

            return $neighbors;
        }else if($radius == 2){
            //Radius 2.
            $isOddRow = $position->r % 2 !== 0;    
            $neighborOffsets = $isOddRow 
                ? [[+1, 0], [-1, 0], [-1, +1], [-1, -1], [0, +1], [0, -1],
                [+2, 0], [+1, -1], [+1, -2], [0, -2], [-1, -2], [-2, -1], 
                [-2, 0], [-2, +1], [-1, +2], [0, +2], [+1, +2], [+1, +1]]

                : [[+1, 0], [-1, 0], [+1, +1], [+1, -1], [0, +1], [0, -1], 
                [+2, 0], [+2, -1], [+1, -2], [0, -2], [-1, -2], [-1, -1], 
                [-2, 0], [-1, +1], [-1, +2], [0, +2], [+1, +2], [+2, +1]];

            // Generate neighboring hexes
            $neighbors = array_map(function($offset) use ($position) {
                return [
                    'q' => $position->q + $offset[0],
                    'r' => $position->r + $offset[1]
                ];
            }, $neighborOffsets);

            return $neighbors;
        }    
    }    
    */


    
/* //OLD METHOD OF pixelCoToHex() WHICH DIDN@T SEEM TO WORK CORRECTLY ANYWAY - DK 02.25
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
        
        
        //if (gamedata.scroll.y % 2 == 0){
         //   h = (x/(b*2))+0.5;
            
        //}else{
            
            
        //}
        
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
    */        


}


?>
