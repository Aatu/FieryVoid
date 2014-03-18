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

class MissileB extends Ammo
{
    public $name = "missileB";
    public $missileClass = "B";
    public $displayName = "Basic Missile";
    public $cost = 0;
    public $surCharge = 0;
    public $damage = 20;
    public $amount = 0;
    public $range = 20;
    public $hitChanceMod = 3;
    public $ballistic = true;

    function __construct($startArc, $endArc){
        parent::__construct(0, 0, 0, $startArc, $endArc);
    }

    public function setSystemDataWindow($turn)
    {
        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = "Basic missile";

        parent::setSystemDataWindow($turn);
    }
    
    public function getHitChanceMod()
    {
        return $this->hitChanceMod;
    }
    
     public function getRange($fireOrder)
    {
        return $this->range;
    }

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
    public $surCharge = 0;
    public $damage = 10;
    public $amount = 0;
    public $range = 10;
    public $hitChanceMod = 3;
    public $ballistic = true;
    
    function __construct($startArc, $endArc){
        parent::__construct(0, 0, 0, $startArc, $endArc);
    }
    
    protected function isFiringNonBallisticWeapons($shooter, $fireOrder){
        // first get the fighter that is armed with this weapon
        // We have to go looking for it because the shooter is a flight,
        // not an individual fighter.
        $fighterSys = $shooter->getFighterBySystem($fireOrder->weaponid);

        // now recheck all the fighter's weapons
        foreach($fighterSys->systems as $weapon){
            if(!$weapon->ballistic && $weapon->firedOnTurn(TacGamedata::$currentTurn)){
                return true;
            }
        }

        return false;
    }
    
    public function calculateHit($gamedata, $fireOrder){
        
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
        $mod -= Movement::getJinking($shooter, $gamedata->turn);
        
        // First check if the fighter/squad that fired this shot is still alive.
        if(!($shooter->isDestroyed() || $shooter->getFighterBySystem($fireOrder->weaponid)->isDestroyed())){
            if($shooter->hasNavigator){
                // Fighter has navigator. Flight always benefits from offensive bonus.
                $oew = $shooter->offensivebonus;
            }
            else{
                // Check if weapon is in current weapon arc
                $shooterCompassHeading = mathlib::getCompassHeadingOfShip($shooter, $target);
                $tf = $shooter->getFacingAngle();

                if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection($this->startArc, $tf), Mathlib::addToDirection($this->endArc, $tf))){
                    // Target is in current launcher arc. Flight benefits from offensive bonus.
                    // Now check if the fighter is not firing any non-ballistic weapons
                    if(!$this->isFiringNonBallisticWeapons($shooter, $fireOrder)){
                        $oew = $shooter->offensivebonus;
                    }
                }
            }
        }
        
        $mod += $target->getHitChanceMod($shooter, $pos, $gamedata->turn);
        $mod += $this->getWeaponHitChanceMod($gamedata->turn);
        
        $firecontrol =  $this->fireControl[$target->getFireControlIndex()];
        
        $intercept = $this->getIntercept($gamedata, $fireOrder);

        // Fighters only ignore all defensive EW, be it DEW, SDEW or BDEW for non-ballistic weapons
        // This is a ballistic weapon, so all defensive EW is taken into account.
        $goal = ($defence - $dew - $bdew - $sdew - $jammermod - $rangePenalty - $intercept + $oew + $soew + $firecontrol + $mod);
        
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
    
    function __construct($startArc, $endArc){
        parent::__construct($startArc, $endArc);
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
    public $surCharge = 0;
    public $damage = 10;
    public $amount = 0;
    public $range = 25;
    public $hitChanceMod = 0;
    
    function __construct($startArc, $endArc){
        parent::__construct($startArc, $endArc);
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
    public $surCharge = 0;
    public $damage = 10;
    public $amount = 0;
    public $range = 20;
    public $hitChanceMod = 0;
    
    function __construct($startArc, $endArc){
        parent::__construct($startArc, $endArc);
    }

    public function getDamage($fireOrder){        return 10;   }
    public function setMinDamage(){     $this->minDamage = 10;      }
    public function setMaxDamage(){     $this->maxDamage = 10;      }        
}