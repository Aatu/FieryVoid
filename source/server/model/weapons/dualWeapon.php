<?php

class DualWeapon extends Weapon{
    
    
    public $dualWeapon = true;
    public $weapons = array();
    
    public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        
        $this->weapons = $weapons;
    }
    
    public function getWeaponForIntercept(){
        return null;
    }
    
    public function setDamage($damage){
        $this->damage[] = $damage;
        
        foreach($this->weapons as $weapon){
            $weapon->setDamage($damage);
        }
    }
    
    public function setDamages($damages){
        $this->damage = $damages;

        foreach($this->weapons as $weapon){
            $weapon->setDamages($damage);
        }
    }

    public function setCritical($critical, $turn){
        
        if (!$critical->oneturn || ($critical->oneturn && $critical->turn >= $turn-1))
            $this->criticals[] = $critical; 

        foreach($this->weapons as $weapon){
            $weapon->setCritical($critical, $turn);
        }
    }
    
    public function isOverloadingOnTurn($turn = null){
         return $this->weapons[$this->firingMode]->isOverloadingOnTurn($turn);
    }
     
    // Only for determining intercept. So not needed in new setup
    public function getFiringWeapon($fireOrder){
        $firingMode = $fireOrder->firingMode;
        return $this->weapons[$firingMode];
    }
    
    // Never called if all is well
/* should not be needed - base weapon is never actually fired for dual weapon!
    public function fire($gamedata, $fireOrder){
        $firingMode = $fireOrder->firingMode;
        $this->weapons[$firingMode]->fire($gamedata, $fireOrder);
    }
*/
    
    public function onConstructed($ship, $turn, $phase){
        parent::onConstructed($ship, $turn, $phase);
        foreach ($this->weapons as $weapon){
            $weapon->onConstructed($ship, $turn, $phase);
        }    
    }
    
    public function beforeTurn($ship, $turn, $phase){
        foreach ($this->weapons as $weapon){
            $weapon->beforeTurn($ship, $turn, $phase);
        }    
    }
    
    public function setId($id){
        $this->id = $id;
        
        foreach ($this->weapons as $i=>$weapon){
            $weapon->setId(1000 + ($id*10) + $i);
            $weapon->parentId = $id;
        } 
    }
    
    public function getFireOrders($turn = -1){
        $fires = array();
        foreach ($this->weapons as $weapon){
            $fires = array_merge($fires, $weapon->getFireOrders($turn));
        } 
        
        return $fires;
    }
    
    
    public function setFireOrders($fireOrders){
        foreach ($fireOrders as $fire)
        {
            $this->setFireOrder($fire);
        }
    }
    
    public function setFireOrder($fire)
    {
        $this->turnsFired[$fire->turn] = true;
        
        foreach($this->weapons as $weapon){
            if($weapon->id == $fire->weaponid){
                $weapon->setFireOrder($fire);
            }else{
                if($weapon->duoWeapon){
                    foreach($weapon->weapons as $subweapon){
                        if($subweapon->id == $fire->weaponid){
                            $subweapon->setFireOrder($fire);   
                        }
                    }
                }
            }
        }
    }
    
	
	
    public function onAdvancingGamedata($ship, $gamedata)
    {
        $weaponFired = false;
        $duoWeaponFired = false;
        $data = null;
        
        foreach ($this->weapons as $i=>$weapon)
        {
            if(!$weapon->duoWeapon){
                if($weapon->firedOnTurn($gamedata->turn)
                        || ($gamedata->phase == 2 && $weapon->turnsloaded == 0)){
                    $this->firingMode = $i;
                    $weaponFired = true;                    
                }                
            }else{
                $weapon->onAdvancingGamedata($ship, $gamedata);

                if($weapon->getTurnsloaded() == 0){
                    $this->firingMode = $i;
                    $duoWeaponFired = true;                    
                }
            }
        }
        
        foreach($this->weapons as $weapon){
            if($weapon->duoWeapon && $weaponFired){
                foreach($weapon->weapons as $subweapon){
                    $data = $subweapon->calculateLoading();
                    $data->loading = 0;
                    $data->overloading = 0;
                    $data->extrashots = 0;

                    SystemData::addDataForSystem($subweapon->id, 0, $ship->id, $data->toJSON());
                }
                
                continue;
            }

            $data = $weapon->calculateLoading();

            if($duoWeaponFired || ($weaponFired && $weapon->overloadshots <= 0)){
                $data->loading = 0;
                $data->overloading = 0;
                $data->extrashots = 0;
            }

            SystemData::addDataForSystem($weapon->id, 0, $ship->id, $data->toJSON());
        }
        
        $data = $this->calculateLoading();
        if($weaponFired){
            $data->loading = 0;
            $data->overloading = 0;
            $data->extrashots = 0;
        }
        
        SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
    }
    
	
    public function setInitialSystemData($ship)
    {
        foreach ($this->weapons as $i=>$weapon)
        {
            if($weapon->duoWeapon){
                $weapon->setInitialSystemData($ship);
                continue;
            }
            
            $data = $weapon->getStartLoading();
            if ($data)
                SystemData::addDataForSystem($weapon->id, 0, $ship->id, $data->toJSON());
        }

        $data = $this->getStartLoading();
        SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
    }
}


/*no longer used
class LaserPulseArray extends DualWeapon{
    
	public $firingModes = array( 
		1 => "Laser",
		2 => "Pulse"
	);
	
    public $name = "LaserPulseArray";
	public $displayName = "Laser/Pulse Array";
	
	public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
        
        $laser = new MediumLaser($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $laser->dualWeapon = true;
        $laser->parentSystem = $this;
        $laser->displayName = "Laser/Pulse array (Laser)";
        $pulse = new MediumPulse($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $pulse->dualWeapon = true;
        $pulse->parentSystem = $this;
        $pulse->displayName = "Laser/Pulse array (Pulse)";
        
		$weapons = array(
			 1 => $laser
            ,2 => $pulse
		);
		
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons);
        
    }
}
*/

