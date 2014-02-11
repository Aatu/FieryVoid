    <?php

class MissileLauncher extends Weapon
{
    public $useOEW = false;
    public $ballistic = true;
    public $trailColor = array(141, 240, 255);
    public $animation = "trail";
    public $animationColor = array(50, 50, 50);
    public $animationExplosionScale = 0.25;
    public $projectilespeed = 8;
    public $animationWidth = 4;
    public $trailLength = 100;
    public $distanceRange = 0;
    
    public $firingModes = array(
        1 => "BasicMissile"
    );
    
    public $missileCount = array(
        1 => 20
    );
        

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function setSystemDataWindow($turn)
    {
        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = "Basic missile";

        parent::setSystemDataWindow($turn);
    }
    
    public function isInDistanceRange($shooter, $target, $fireOrder)
    {
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
    
        if(mathlib::getDistanceHex($pos,  $target->getCoPos()) > $this->distanceRange)
        {
            $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
            return false;
        }
        
        return true;
    }
}

class SMissileRack extends MissileLauncher
{
    public $name = "sMissileRack";
    public $displayName = "Class-S Missile Rack";
    public $range = 20;
    public $distanceRange = 60;
    public $loadingtime = 2;
    public $iconPath = "missile1.png";

    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }
    protected function getAmmo($fireOrder)
    {
        return new $this->firingModes[$fireOrder->firingMode];
    }
    
    public function getDamage($fireOrder)
    {
        $ammo = new $this->firingModes[$fireOrder->firingMode];
        return $ammo->getDamage();
    }
    public function setMinDamage(){     $this->minDamage = 20 - $this->dp;}
    public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;}     
}


class LMissileRack extends MissileLauncher
{
    public $name = "lMissileRack";
    public $displayName = "Class-L Missile Rack";
    public $range = 30;
    public $distanceRange = 70;
    public $loadingtime = 2;
    public $iconPath = "missile1.png";

    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }
    protected function getAmmo($fireOrder)
    {
        return new $this->firingModes[$fireOrder->firingMode];
    }
    
    public function getDamage($fireOrder)
    {
        $ammo = new $this->firingModes[$fireOrder->firingMode];
        return $ammo->getDamage();
    }
    public function setMinDamage(){     $this->minDamage = 20 - $this->dp;}
    public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;}     
}

class LHMissileRack extends MissileLauncher
{
    public $name = "lHMissileRack";
    public $displayName = "Class-LH Missile Rack";
    public $range = 30;
    public $distanceRange = 70;
    public $loadingtime = 1;
    public $iconPath = "missile2.png";
    
    public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals 
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }
    protected function getAmmo($fireOrder)
    {
        return new $this->firingModes[$fireOrder->firingMode];
    }
    
    public function getDamage($fireOrder)
    {
        $ammo = new $this->firingModes[$fireOrder->firingMode];
        return $ammo->getDamage();
    }
    public function setMinDamage(){     $this->minDamage = 20 - $this->dp;}
    public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;} 
}

class ReloadRack extends ShipSystem
{
    // This needs to be implemented
    public $name = "ReloadRack";
    public $displayName = "Reload Rack (tbd)";
    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);

    }
}

class BombRack extends Weapon{
    
    // This needs to be implemented
    public $name = "BombRack";
    public $displayName = "Bomb Rack";
    public $ballistic = true;
    public $targetImmobile = true;

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }
}