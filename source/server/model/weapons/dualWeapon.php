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
        $this->weapons[$fire->firingMode]->setFireOrder($fire);
    }
    
    
    public function getStartLoading($gameid, $ship)
    {
        $loadings = array();
        foreach ($this->weapons as $subId => $weapon){
            $loading = $weapon->getStartLoading($gameid, $ship);
            $loading->subsystem = $subId;
            $loadings[] = $loading;
        }
        return $loadings;
    }
    
    public function setLoading( $loading )
    {
        if (!$loading)
            return;
        
        $this->weapons[$loading->subsystem]->setLoading($loading);
        
    }
    
    public function calculateLoading( $gameid, $phase, $ship, $turn )
    {
        $loadings = array();
        foreach ($this->weapons as $subId => $weapon){
            $loading = $weapon->calculateLoading( $gameid, $phase, $ship, $turn );
            
            if (!$loading)
                continue;
            
            $loading->subsystem = $subId;
            $loadings[] = $loading;
        }
        
        if (count($loadings)==0)
            return null;
        
        return $loadings;
    }
}

class LaserPulseArray extends DualWeapon{
    
	public $firingModes = array( 
		1 => "Laser",
		2 => "Pulse"
	);
	
	public $displayName = "Laser/Pulse array";
	
	public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
		$weapons = array(
			 1 => new MediumLaser($armour, $maxhealth, $powerReq, $startArc, $endArc)
            ,2 => new MediumPulse($armour, $maxhealth, $powerReq, $startArc, $endArc)
		);
		
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons);
        
    }
}

