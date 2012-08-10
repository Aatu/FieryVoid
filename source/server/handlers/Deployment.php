<?php

class Deployment 
{
    private static function getValidDeploymentArea($ship)
    {
        if ($ship->team == 1){
            return array("x" => -30, "y" => 0, "w" => 16, "h" => 50);
        }else{
            return array("x" => 30, "y" => 0, "w" => 16, "h" => 50);
        }
    }
    
    private static function validateDeploymentArea($gamedata, $ship, $move){
        
        $slot = $gamedata->slots[$ship->slot];
        $hexpos = Mathlib::hexCoToPixel($move->x, $move->y);
        
        $deppos = Mathlib::hexCoToPixel($slot->depx, $slot->depy);
        
        if ($slot->deptype == "box"){
            $depw = $slot->depwidth*Mathlib::$hexWidth;
            $deph = $slot->depheight*Mathlib::$hexHeight;
            if ($hexpos["x"] <= ($deppos["x"]+($depw/2)) && $hexpos["x"] > ($deppos["x"]-($depw/2))){
                if ($hexpos["y"] <= ($deppos["y"]+($deph/2)) && $hexpos["y"] >= ($deppos["y"]-($deph/2))){
                    return true;
                }
            }
        }else if ($slot->deptype=="distance"){
            if (Mathlib::getDistance($deppos, $hexpos) <= $slot->depheight*Mathlib::$hexWidth){
                if (Mathlib::getDistance($deppos, $hexpos) > $slot->depwidth*Mathlib::$hexWidth){
                    return true;
                }
            }
        }else{
            if (Mathlib::getDistance($deppos, $hexpos) <= $slot->depwidth*Mathlib::$hexWidth){
                return true;
            }
        }
         
         
        return false;
        
    }
    
    public static function validateDeployment($gamedata, $ships)
    {
        $shipIdMoves = array();
        foreach ($ships as $ship)
        {
            if ($ship->userid !== $gamedata->forPlayer)
                continue;
                
            $moves = array();
            $found = false;
            foreach ($ship->movement as $move)
            {
                if ($found)
                    throw new Exception("Deployment validation failed: Found more than one deployment entry for ship $ship->name.");
                
                if ($move->type == "deploy")
                {
                    $found = true;
                    $servership = $gamedata->getShipById($ship->id);
                    if (self::validateDeploymentArea($gamedata, $servership, $move))
                    {
                        $moves[] = $move;
                        $servership->movement[] = $move;
                    }else{
                        throw new Exception("Deployment validation failed: Illegal placement. Ship: " . $ship->name . "(".$move->x .",".$move->y.")");
                    }
                }
            }
            
            if (!$found)
                throw new Exception("Deployment validation failed: Entry not found for ship $ship->name.");
            
            $shipIdMoves[$ship->id] = $moves;
        }
        
        return $shipIdMoves;
    }
    
}