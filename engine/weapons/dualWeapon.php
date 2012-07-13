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
    
    public function setCriticals($criticals, $turn){
        foreach ($this->weapons as $weapon){
            $weapon->setCriticals($criticals, $turn);
        }  
    }
    
    public function setDamage($damages){
        parent::setDamage($damages);
        foreach ($this->weapons as $weapon){
            $weapon->setDamage($damages);
        } 
    }
    
    public function setPower($power){
        parent::setPower($power);
        foreach ($this->weapons as $weapon){
            $weapon->setPower($power);
        } 
    }
    
    public function setId($id){
        parent::setId($id);
        foreach ($this->weapons as $weapon){
            $weapon->setId($id);
        } 
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

