<?php

class DualWeapon extends Weapon{
    
    
    public $dualWeapon = true;
    public $weapons = array();
    //private $turnsFired = array();
    
    public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        
        $this->weapons = $weapons;
    }
    
    public function getWeaponForIntercept(){
        return null;
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
    public function fire($gamedata, $fireOrder){
        $firingMode = $fireOrder->firingMode;
        $this->weapons[$firingMode]->fire($gamedata, $fireOrder);
    }
    
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
    
    public function getFireOrders(){
        $fires = array();
        foreach ($this->weapons as $weapon){
            $fires = array_merge($fires, $weapon->getFireOrders());
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
    
    public function onAdvancingGamedata($ship)
    {
        $weaponDataArray = array();
        $weaponFired = false;
        
        foreach ($this->weapons as $i=>$weapon)
        {
            if(!$weapon->duoWeapon){
                $data = $weapon->calculateLoading();
                debug::log("calculatingLoading of $weapon->id");
                debug::log("calc results: ".$data->toJSON());
                $weaponDataArray[$i] = $data;

                if($data->loading == 0){
                    debug::log("Dual onAdvance: weapon id $weapon->id has 0 turns loaded");
                    $this->firingMode = $i;
                    $weaponFired = true;                    
                }                
            }else{
                $weapon->onAdvancingGamedata($ship);

                if($weapon->getTurnsloaded() == 0){
                    debug::log("Dual onAdvance: weapon id $weapon->id has 0 turns loaded");
                    $this->firingMode = $i;
                    $weaponFired = true;                    
                }
            }
        }
        
        foreach($weaponDataArray as $i=>$data){
            if($weaponFired){
                $data->loading = 0;
                $data->overloading = 0;
            }
            
            SystemData::addDataForSystem($this->weapons[$i]->id, 0, $ship->id, $data->toJSON());
        }
        
        $data = $this->calculateLoading();
        if($weaponFired){
            $data->loading = 0;
            $data->overloading = 0;
        }
        
        SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
    }
    
    public function setInitialSystemData($ship)
    {
        foreach ($this->weapons as $i=>$weapon)
        {
            if($weapon->duoWeapon){
                debug::log("setting initial data duo weapon $weapon->id");
                $weapon->setInitialSystemData($ship);
                continue;
            }
            
            $data = $weapon->getStartLoading();
            debug::log("");
            if ($data)
                SystemData::addDataForSystem($weapon->id, 0, $ship->id, $data->toJSON());
        }

        $data = $this->getStartLoading();
        SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
    }
}

class LaserPulseArray extends DualWeapon{
    
	public $firingModes = array( 
		1 => "Laser",
		2 => "Pulse"
	);
	
    public $name = "LaserPulseArray";
	public $displayName = "Laser/Pulse array";
	
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


