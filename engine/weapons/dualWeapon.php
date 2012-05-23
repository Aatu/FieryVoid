<?php

class dualWeapon extends Weapon{
    
    public $weapons = array();
    
    public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons) {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        
        $this->weapons = $weapons;
    }
    
    public function fire($gamedata, $fireOrder){

        $firingMode = $fireOrder->firingMode;
        $this->weapons[$firingMode]->fire($gamedata, $fireOrder);
    }
    
    public function onConstructed($ship, $turn, $phase){
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
}

