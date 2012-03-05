<?php
class Firing{

    public static function validateFireOrders($fireOrders, $gamedata){
    
        return true;
    
    }
    
    public static function automateIntercept($gd){
        
        foreach ($gd->ships as $ship){
            foreach($ship->systems as $weapon){
                        
                if (!($weapon instanceof Weapon))
                    continue;
                       
                if ($weapon->ballistic)
                    continue;
                    
                $weapon->setLoading($ship, $gd->turn-1, 3);
                if ($weapon->turnsloaded < $weapon->loadingtime)
                    continue;
                    
                if ($weapon->intercept == 0)
                    continue;
                    
                    
            }
            
            
        }
        
    }
    
    public static function getPossibleIntercept($gd, $ship, $weapon){
        
        $intercepts = array();
        
        foreach($gd->ships as $shooter){
            if ($shooter->id == $ship->id)
                continue;
            
            if ($shooter->team == $ship->team)
                continue;
                
            foreach($shooter->fireOrders as $fire){
                if (self::isLegalIntercept($gd, $ship, $weapon, $fire)){
                    $intercepts[] = $fire;
                }
            }
        }
                
               
            
        
    }
    
    public static function isLegalIntercept($gd, $ship, $weapon, $fire){
        
        
            
        if ($weapon->intercept == 0)
            return false;
        
        $shooter = $gd->getShipById($fire->shooterid);
        $target = $gd->getShipById($fire->targetid);
        $firingweapon = $ship->getSystemById($fire->weaponid);
        
        if ($firingweapon->uninterceptable)
            return false;
                
        if ($shooter->id == $ship->id)
            return false;
            
        if ($shooter->team == $ship->team)
            return false;
            
        $pos = $shooter->getCoPos();
        if ($this->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
        }
            
        if ($target->id == $ship->id){
            $tf = $ship->getFacingAngle();
            $shooterCompassHeading = mathlib::getCompassHeadingOfPos($this, $pos);
          
            if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection($weapon->startArc,$tf), Mathlib::addToDirection($weapon->endArc,$tf) ))
                return true;
        }else{
            if (!$weapon->freeintercept)
                return false;
                
            if (mathlib::getDistanceHex($target, $ship)<=3 &&  (mathlib::getDistance($pos, $ship) < (mathlib::getDistance($target, $pos))))
                return true;
            
        }
            
            
        
    }
    
    
    public static function fireWeapons($gamedata){

        $turn = $gamedata->turn;
        $updates = array();
        $damages = array();
        
        foreach ($gamedata->ships as $ship){
        
            foreach($ship->fireOrders as $fire){

                if ($fire->turn != $gamedata->turn)
                    continue;
                    
                if ($fire->type == "intercept")
                    continue;
                
                $weapon = $ship->getSystemById($fire->weaponid);
                    
                if (!$weapon->ballistic)
                    continue;
                
                if ($fire->rolled>0)
                    continue;
                    
               
                $weapon->setLoading($ship, $gamedata->turn-1, 3);
                $weapon->fire($gamedata, $fire);
                
                
            }
        }
        
        foreach ($gamedata->ships as $ship){
        
            foreach($ship->fireOrders as $fire){

                if ($fire->turn != $gamedata->turn)
                    continue;
                    
                if ($fire->type == "intercept")
                    continue;
                
                $weapon = $ship->getSystemById($fire->weaponid);
                
                if ($weapon->ballistic)
                    continue;
                    
                if ($fire->rolled>0)
                    continue;
                    
                
                $weapon->setLoading($ship, $gamedata->turn-1, 3);
                $weapon->fire($gamedata, $fire);
                
                
            }
        }
        

    
    }
}

?>
