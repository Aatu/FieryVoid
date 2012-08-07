<?php

class DualWeapon extends Weapon{
    
    
    public $dualWeapon = true;
    public $weapons = array();
    private $turnsFired = array();
    
    public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        
        $this->weapons = $weapons;
    }
    
    public function getWeaponForIntercept(){
        return null;
    }
    
    public function getFiringWeapon($fireOrder){
        $firingMode = $fireOrder->firingMode;
        return $this->weapons[$firingMode];
    }
    
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
        foreach ($this->weapons as $weapon){
            $weapon->setId($id);
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
        foreach ($this->weapons as $i=>$weapon)
        {
            $data = $weapon->calculateLoading();
            if ($data)
                SystemData::addDataForSystem($this->id, $i, $ship->id, $data->toJSON());
        }
    }
    
    public function setSystemData($data, $subsystem)
    {
        $this->weapons[$subsystem]->setSystemData($data, $subsystem);
        
    }
    
    public function setInitialSystemData($ship)
    {
        foreach ($this->weapons as $i=>$weapon)
        {
            $data = $weapon->getStartLoading();
            if ($data)
                SystemData::addDataForSystem($this->id, $i, $ship->id, $data->toJSON());
        }
    }
    
    public function setLoading( $loading )
    {
        if (!$loading)
            return;
        
        $this->weapons[$loading->subsystem]->setLoading($loading);
        
    }
    
}

class LaserPulseArray extends DualWeapon{
    
	public $firingModes = array( 
		1 => "Laser",
		2 => "Pulse"
	);
	
	public $displayName = "Laser/Pulse array";
	
	public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
        
        $laser = new MediumLaser($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $laser->parentSystem = $this;
        $pulse = new MediumPulse($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $pulse->parentSystem = $this;
        
		$weapons = array(
			 1 => $laser
            ,2 => $pulse
		);
		
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons);
        
    }
}

