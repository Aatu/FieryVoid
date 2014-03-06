<?php

class Ammo extends Weapon
{
    public $amount = 0;
    public $cost = 0;
    public $surCharge = 0;
    public $damage = 0; // is Warhead value
    public $range = 0;
    public $ballistic = true;
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }
    
    public function getDamage($fireOrder)
    {
        return $this->damage;
    }
    
    public function setMinDamage(){     $this->minDamage = $this->damage;      }
    public function setMaxDamage(){     $this->maxDamage = $this->damage;      }    
    
    public function getHitChanceMod()
    {
        return 0;
    }
    
    public function setRangeMod($rangeMod){
        $this->range = $this->range + $rangeMod;
    }
    
    public function getRange($fireOrder)
    {
        return $this->range;
    }
    
    public function setAmount($newAmount){
        $this->amount = $newAmount;
    }
    
    public function getAmount(){
        return $this->amount;
    }
}

class BasicMissile extends Ammo
{
    public function setSystemDataWindow($turn)
    {
        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = "Basic missile";

        parent::setSystemDataWindow($turn);
    }
    
    public function getDamage($fireOrder)
    {
        return 20;
    }
    
    public function getHitChanceMod()
    {
        return 3;
    }
    
     public function getRange($fireOrder)
    {
        return 20;
    }
}

class MissileFB extends Ammo
{
    public $name = "missileFB";
    public $missileClass = "FB";
    public $displayName = "Basic Fighter Missile";
    public $cost = 8;
    public $surCharge = 0;
    public $damage = 8;
    // plopje
    public $amount = 4;
    public $range = 10;
    public $hitChanceMod = 3;
    
    function __construct($startArc, $endArc){
        parent::__construct(0, 0, 0, $startArc, $endArc);
    }
    
    public function calculateHit($gamedata, $fireOrder){
        
        debug::log("CalculateHit AMMO");
    
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        $jammermod = 0;
        
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
        $defence = $target->getDefenceValuePos($pos);
                       
        $rp = $this->calculateRangePenalty($pos, $target);
        $rangePenalty = $rp["rp"];
        
        $dew = $target->getDEW($gamedata->turn);
        $bdew = EW::getBlanketDEW($gamedata, $target);
        
        if ($target instanceof FighterFlight){
            if (!($shooter instanceof FighterFlight))
            {
                $dew = Movement::getJinking($target, $gamedata->turn);
            }
            elseif( mathlib::getDistance($shooter->getCoPos(),  $target->getCoPos()) > 0
                    ||  Movement::getJinking($shooter, $gamedata->turn) > 0){
                $dew = Movement::getJinking($target, $gamedata->turn);
            }
        }

        $mod = 0;
        $oew = 0;
        
        $sdew = EW::getSupportedDEW($gamedata, $target);
        $soew = EW::getSupportedOEW($gamedata, $shooter, $target);
        
        $mod += $this->hitChanceMod;
        
        if($shooter->hasNavigator){
            // Fighter has navigator. Flight always benefits from offensive bonus.
            $oew = $shooter->offensivebonus;
        }
        else{
            // Check if weapon is in current weapon arc
            debug::log("1");
            $shooterCompassHeading = mathlib::getCompassHeadingOfShip($shooter, $target);
            debug::log("1a. ShooterCompassHeading $shooterCompassHeading");
            $tf = $shooter->getFacingAngle();
            debug::log("2, FacingAngle $tf");
            
            if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection($this->startArc, $tf), Mathlib::addToDirection($this->endArc, $tf))){
                // Target is in current launcher arc. Flight benefits from offensive bonus.
                debug::log("3");
                $oew = $shooter->offensivebonus;
            }
        }
        
        $mod += $target->getHitChanceMod($shooter, $pos, $gamedata->turn);
        $mod += $this->getWeaponHitChanceMod($gamedata->turn);
	debug::log("4");

        
        $firecontrol =  $this->fireControl[$target->getFireControlIndex()];
        
        $intercept = $this->getIntercept($gamedata, $fireOrder);
        debug::log("5");

        // Fighters ignore all defensive EW, be it DEW, SDEW or BDEW
        $goal = ($defence - $jammermod - $rangePenalty - $intercept + $oew + $soew + $firecontrol + $mod);
        
        // plopje
        debug::log("Defense: $defence, RangePenalty: $rangePenalty, Intercept: $intercept, OEW: $oew, SOEW: $soew, CONTROL: $firecontrol, MOD: $mod");
        
        $change = round(($goal/20)*100);
        
        $notes = $rp["notes"] . ", DEW: $dew, BDEW: $bdew, SDEW: $sdew, Jammermod: $jammermod, OEW: $oew, SOEW: $soew, defence: $defence, intercept: $intercept, F/C: $firecontrol, mod: $mod, goal: $goal, chance: $change";
        
        $fireOrder->needed = $change;
        $fireOrder->notes = $notes;
        $fireOrder->updated = true;
    }

    public function getHitChanceMod()
    {
        return $this->hitChanceMod;
    }
}