<?php

class Deployment 
{
    private static function getValidDeploymentArea($ship)
    {
        if ($ship->team == 1){
            return array("x" => -40, "y" => 0, "w" => 16, "h" => 50);
        }else{
            return array("x" => 40, "y" => 0, "w" => 16, "h" => 50);
        }
    }
    
    private static function validateDeploymentArea($gamedata, $ship, $move){
        
        $dep = self::getValidDeploymentArea($ship);
        
        if ($move->x <= ($dep["x"]+($dep["w"]/2)) && $move->x > ($dep["x"]-($dep["w"]/2)))
        {
            if ($move->y <= ($dep["y"]+($dep["h"]/2)) && $move->y >= ($dep["y"]-($dep["h"]/2)))
            {
                if (count($gamedata->getShipsInDistance(array("x"=>$move->x, "y"=>$move->y))) === 0)
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
                    throw new Exception("Deployment validation failed: Found more than one deployment entry.");
                
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
                throw new Exception("Deployment validation failed: Entry not found.");
            
            $shipIdMoves[$ship->id] = $moves;
        }
        
        return $shipIdMoves;
    }
    
}