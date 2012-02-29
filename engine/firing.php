<?php
class Firing{

    public static function validateFireOrders($fireOrders, $gamedata){
    
        return true;
    
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
