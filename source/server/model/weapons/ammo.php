<?php

class Ammo extends Weapon
{
    public $ballistic = true;
    public $amount = 0;
    public $cost = 0;
    //public $surCharge = 0;
    public $damage = 0; // is Warhead value
    public $range = 0;
    public $distanceRange = 0;
    public $hitChanceMod = 0;
    public $priority = 4;
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $fireControl = null){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        
        if($fireControl != null){
            $this->fireControl = $fireControl;
        }else{
            $this->fireControl = array(0, 0, 0);
        }
    }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();

        $strippedSystem->amount = $this->amount;
       
        return $strippedSystem;
    }
    
    public function getDamage($fireOrder)
    {
        return $this->damage;
    }
    
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }    
    
    public function setRangeMod($rangeMod){
        $this->range = $this->range + $rangeMod;
    }
    
    /*no longer needed?
    public function getRange($fireOrder)
    {
        return $this->range;
    }
    */
        
    
    public function setAmount($newAmount){
        $this->amount = $newAmount;
    }
    
    public function getAmount(){
        return $this->amount;
    }
}

class MissileB extends Ammo
{
    public $name = "missileB";
    public $missileClass = "B";
    public $displayName = "Basic Missile";
    public $cost = 0;
    //public $surCharge = 0;
    public $damage = 20;
    public $amount = 0;
    public $range = 20;
    public $distanceRange = 60;
    public $hitChanceMod = 3;
    //public $ballistic = true;
    public $priority = 6;

    function __construct($startArc, $endArc, $fireControl = null){
        parent::__construct(0, 0, 0, $startArc, $endArc, $fireControl);
    }

    public function setSystemDataWindow($turn)
    {
        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = "Basic missile";

        parent::setSystemDataWindow($turn);
    }
    
    /* no longer needed?
    public function getWeaponHitChanceMod($turn)
    {
        return $this->hitChanceMod;
    }
    
     public function getRange($fireOrder)
    {
        return $this->range;
    }
    */

    public function getDamage($fireOrder)
    {
        return $this->damage;
    }
    
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }    
}



class MissileFB extends Ammo
{
    public $name = "missileFB";
    public $missileClass = "FB";
    public $displayName = "Basic Fighter Missile";
    public $cost = 8;
    //public $surCharge = 0;
    public $damage = 10;
    //public $amount = 0;
    public $range = 10;
    public $distanceRange = 30;
    public $hitChanceMod = 3;
    //public $ballistic = true;
    public $priority = 4;
    
    function __construct($startArc, $endArc, $fireControl = null){
        parent::__construct(0, 0, 0, $startArc, $endArc, $fireControl);
    }

    
    
    public function getDamage($fireOrder){        return 10;   }
    public function setMinDamage(){     $this->minDamage = 10;      }
    public function setMaxDamage(){     $this->maxDamage = 10;      }        
}

class MissileFY extends MissileFB
{
    public $name = "missileFY";
    public $missileClass = "FY";
    public $displayName = "Dogfight Missile";
    public $cost = 2;
    public $damage = 6;
    public $range = 8;
    public $distanceRange = 16;
    public $priority = 3;
    
    function __construct($startArc, $endArc, $fireControl = null){
        parent::__construct($startArc, $endArc, $fireControl);
    }

    public function getDamage($fireOrder){  return 8;   }
    public function setMinDamage(){     $this->minDamage = 8;      }
    public function setMaxDamage(){     $this->maxDamage = 8;      }        
}



class LightBallisticTorpedo extends MissileFB
{
    public $name = "lightBallisticTorpedo";
    public $missileClass = "LBT";
    public $displayName = "Light Ballistic Torpedo";
    public $cost = 8;
    //public $surCharge = 0;
    public $damage = 10;
    public $amount = 0;
    public $range = 25;
    public $distanceRange = 25;
    public $hitChanceMod = 0;
    public $priority = 3;
    
    function __construct($startArc, $endArc, $fireControl){
        parent::__construct($startArc, $endArc, $fireControl);
    }

    public function getDamage($fireOrder){        return Dice::d(6,2);   }
    public function setMinDamage(){     $this->minDamage = 2;      }
    public function setMaxDamage(){     $this->maxDamage = 12;      }        
}


class LightIonTorpedo extends MissileFB
{
    public $name = "lightIonTorpedo";
    public $missileClass = "LIT";
    public $displayName = "Light Ion Torpedo";
    public $cost = 8;
    //public $surCharge = 0;
    public $damage = 10;
    public $amount = 0;
    public $range = 20;
    public $distanceRange = 20;
    public $hitChanceMod = 0;
    public $priority = 4;
    
    function __construct($startArc, $endArc, $fireControl = null){
        parent::__construct($startArc, $endArc, $fireControl);
    }

    public function getDamage($fireOrder){        return 10;   }
    public function setMinDamage(){     $this->minDamage = 10;      }
    public function setMaxDamage(){     $this->maxDamage = 10;      }        
}


class MissileD extends Ammo
{
    public $name = "missileD";
    public $missileClass = "D";
    public $displayName = "Light Missile";
    public $cost = 0;
    //public $surCharge = 0;
    public $damage = 12;
    public $amount = 0;
    public $range = 15;
    public $distanceRange = 45;
    public $hitChanceMod = 3;
    //public $ballistic = true;
    public $priority = 6;

    function __construct($startArc, $endArc, $fireControl = null){
        parent::__construct(0, 0, 0, $startArc, $endArc, $fireControl);
    }

    public function setSystemDataWindow($turn)
    {
        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = "Light missile";

        parent::setSystemDataWindow($turn);
    }
    
    /* no longer needed?
    public function getWeaponHitChanceMod($turn)
    {
        return $this->hitChanceMod;
    }
    
     public function getRange($fireOrder)
    {
        return $this->range;
    }
    */

    public function getDamage($fireOrder)
    {
        return $this->damage;
    }
    
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }    
}
