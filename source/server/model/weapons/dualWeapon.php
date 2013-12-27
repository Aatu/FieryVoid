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
        parent::setId($id);
        
        $counter = 0;
        
        foreach ($this->weapons as $weapon){
            $weapon->setId(1000 + ($id*10) + $counter);
            $weapon->parentId = $id;
            $counter++;
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
        $this->weapons[$fire->firingMode]->setFireOrder($fire);
    }
    
    public function onAdvancingGamedata($ship)
    {
        $weaponDataArray = array();
        $weaponFired = false;
        
        foreach ($this->weapons as $i=>$weapon)
        {
            $data = $weapon->calculateLoading();
            $weaponDataArray[$i] = $data;
            
            if($data->loading == 0){
                $this->firingMode = $i;
                $weaponFired = true;
            }
        }
        
        foreach($weaponDataArray as $i=>$data){
            if($weaponFired){
                $data->loading = 0;
            }
            
            SystemData::addDataForSystem($this->weapons[$i]->id, 0, $ship->id, $data->toJSON());
        }
        
        $data = $this->calculateLoading();
        if($weaponFired){
            $data->loading = 0;
        }
        
        SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
    }
    
    public function setInitialSystemData($ship)
    {
        $data = $this->getStartLoading();
        SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
                
        foreach ($this->weapons as $i=>$weapon)
        {
            $data = $weapon->getStartLoading();
            if ($data)
                SystemData::addDataForSystem($weapon->id, 0, $ship->id, $data->toJSON());
        }
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


