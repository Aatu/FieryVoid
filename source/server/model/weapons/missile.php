<?php

class MissileLauncher extends Weapon{
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
    public $firingMode = 1;
    public $rangeMod = 0;
    public $priority = 6;
    public $hits = array();

    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ballistic"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!    
    
    protected $distanceRangeMod = 0;
    
    private $rackExplosionDamage = 70; //how much damage will this weapon do in case of catastrophic explosion
    private $rackExplosionThreshold = 19; //how high roll is needed for rack explosion
    
    public $firingModes = array(
        1 => "B"
    );
    
    public $missileArray = array();
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $base=false){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

        if ($base){ //mounted on base - double the launch range
            $this->rangeMod = $this->rangeMod + $this->range; 
            $this->range = $this->range *2;            
        }
        
        $MissileB = new MissileB($startArc, $endArc, $this->fireControl);
        $this->missileArray = array(
            1 => $MissileB
        );
    }
    
    public function setSystemDataWindow($turn){
        //$this->data["Weapon type"] = "Ballistic";
        //$this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = "Basic missile";
        $this->data["Missile Hit Mod"] = $this->missileArray[1]->hitChanceMod*5;

        parent::setSystemDataWindow($turn);
    }
    
    public function isInDistanceRange($shooter, $target, $fireOrder){
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $pos = mathlib::hexCoToPixel($movement->position);
    
        if(mathlib::getDistanceHex($pos,  $target->getCoPos()) > $this->distanceRange)
        {
            $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
            return false;
        }
        return true;
    }
    
    
    
    public function getWeaponHitChanceMod($turn)
    {
        $ammo = $this->missileArray[$this->firingMode];
        return $ammo->hitChanceMod;
    }
    
    
    public function setAmmo($firingMode, $amount){
        if(count($this->missileArray) > 0){
            $this->missileArray[$firingMode]->amount = $amount;
        }
    }
    
    protected function getAmmo($fireOrder){
        return $this->missileArray[$fireOrder->firingMode];
    }

    
    public function testCritical($ship, $gamedata, $crits, $add = 0){ //add testing for ammo explosion!
        $explodes = false;

        $roll = Dice::d(20);
        if ($roll >= $this->rackExplosionThreshold) $explodes = true;
        
        if($explodes){
            $this->ammoExplosion($ship, $gamedata, $this->rackExplosionDamage);            
            $this->addMissileCritOnSelf($ship->id, "AmmoExplosion", $gamedata);
        }else{
            $crits = parent::testCritical($ship, $gamedata, $crits, $add);
        }
        
        return $crits;
    } //endof function testCritical


    public function ammoExplosion($ship, $gamedata, $damage){
        //first, destroy self if not yet done...
        if (!$this->isDestroyed()){
            $this->noOverkill = true;
            $fireOrder =  new FireOrder(-1, "ammoExplosion", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 'standard', 100, 1, 1, 1, 0, null, null, 'ballistic');
            /*
            $dmgToSelf = $this->getRemainingHealth();
            $dmgToSelf += $this->getSystemArmourStandard($system, $gamedata, $fireOrder, null); 
            $dmgToSelf += $this->getSystemArmourInvulnerable($system, $gamedata, $fireOrder, null); 
            */
            $dmgToSelf = 1000; //rely on $noOverkill instead of counting exact amount left - 1000 should be more than enough...
            $this->doDamage($ship, $ship, $this, $dmgToSelf, $fireOrder, $pos, $gamedata, true, $this->location);
        }
        
        //then apply damage potential as a hit...
        if($damage>0){
            $this->noOverkill = false;
            $this->damageType = 'Flash'; //should be Raking by the rules, but Flash is much easier to do - and very fitting for explosion!
            $fireOrder =  new FireOrder(-1, "ammoExplosion", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 'flash', 100, 1, 1, 1, 0, null, null, 'ballistic');
            $this->doDamage($ship, $ship, $this, $damage, $fireOrder, null, $gamedata, false, $this->location); //show $this as target system - this will ensure its destruction, and Flash mode will take care of the rest
        }
    }

/*no longer needed
    public function getHitSystemShip($ship){

        $systems = array();
        $total = 0;
        $current = 0;
        foreach ($ship->systems as $system){
            if ($system->location == $this->location){
                $systems[] = $system;

                $multi = 1;

                if ($system instanceof Structure){
                    $multi = 0.5;
                }
                $total += $system->maxhealth * $multi;
            }
        }

        foreach ($systems as $system){
            $multi = 1;
            if ($system instanceof Structure){
                $multi = 0.5;
            }

            $current += $system->maxhealth * $multi;
            $roll = Dice::d($total);

            if ($roll <= $current){
                if ($system->isDestroyed()){
                    foreach ($ship->systems as $sys){
                        if ( $sys->location == $system->location && !$sys->isDestroyed() && get_class($sys) == get_class($system) ){
               //     debug::log($system->displayName." destroyed, taking new one");
                            $system = $sys;
                        }
                    }
                }
                if ($system->isDestroyed()){
                    $system = $ship->getStructureSystem($this->location);
                }
                return $system;
            }
        }
    }


    public function ammoExplosionDamage($ship, $system, $damage, $gamedata){
    //    debug::log("Damage Loop");

        $armour = $system->armour;
        foreach ($this->hits as $previous){
            if ($previous->systemid == $system->id)
                $armour -= $previous->damage;
        }
        
        $systemHealth = $system->getRemainingHealth();
        $modifiedDamage = $damage;
        
        if ($armour < 0){
            $armour = 0;
        }        

        $destroyed = false;

        if ($damage-$armour >= $systemHealth){
            $destroyed = true;
            $modifiedDamage = $systemHealth + $armour;
        }
        
        $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $system->id, $modifiedDamage, $armour, 0, -1, $destroyed, "particle");
        $damageEntry->updated = true;
        $system->damage[] = $damageEntry;
        $this->hits[] = $damageEntry;
     //   debug::log("REGULAR vs:".$system->displayName."__".$system->location." id: ".$system->id." left: ".$systemHealth." for: ".$modifiedDamage."--armour: ".$armour." destroyed: ".$destroyed);
     //   debug::log("remaining: ".($system->getRemainingHealth()));

        if ($damage-$armour > $systemHealth){
            $damage = $damage-$modifiedDamage;             
            $okSystem = $ship->getStructureSystem($this->location);
    //        debug::log("destroyed, overkilling remaining ".$damage." versus ok system: ".$okSystem->displayName." ".$okSystem->location);

            if ($okSystem->isDestroyed()){
                $okSystem = $ship->getStructureSystem(0);
    //           debug::log("ok system killed, now: ".$okSystem->displayName." ".$okSystem->location);
            }

            $armour = $okSystem->armour;

            foreach ($this->hits as $previous){
                if ($previous->systemid == $okSystem->id)
                    $armour -= $previous->damage;
            }

            if ($armour < 0){
                $armour = 0;
            }

            $destroyed = false;

            $okSystemHealth = $okSystem->getRemainingHealth();


            if ($damage-$armour >= $okSystemHealth){
                $destroyed = true;
            }

            $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $okSystem->id, $damage, $armour, 0, -1, $destroyed, "");
            $damageEntry->updated = true;
            $okSystem->damage[] = $damageEntry;
            $this->hits[] = $damageEntry;
     //       debug::log("OK vs:".$okSystem->displayName."__".$okSystem->location." id: ".$okSystem->id." left: ".$okSystemHealth."for: ".$damage."--armour: ".$armour." destroyed: ".$destroyed);
     //       debug::log("remaining: ".($okSystem->getRemainingHealth()));
        }
    }
*/
    
    public function addMissileCritOnSelf($shipid, $phpclass, $gamedata){
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
    }
    
} //endof class MissileLauncher      



class SMissileRack extends MissileLauncher{
    public $name = "sMissileRack";
    public $displayName = "Class-S Missile Rack";
    public $range = 20;
    public $distanceRange = 60;
    public $loadingtime = 2;
    public $iconPath = "missile1.png";

    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 
    
    public function getDamage($fireOrder)
    {
//        $ammo = new $this->firingModes[$fireOrder->firingMode];
//        return $ammo->getDamage();
        return 20;
    }
    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;}     
}


class RMissileRack extends MissileLauncher{
    public $name = "rMissileRack";
    public $displayName = "Class-R Missile Rack";
    public $range = 20;
    public $distanceRange = 60;
    public $loadingtime = 1;
    public $iconPath = "missile2.png";

    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 
    
    public function getDamage($fireOrder)
    {
//        $ammo = new $this->firingModes[$fireOrder->firingMode];
//        return $ammo->getDamage();
        return 20;
    }
    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;}     
}



class SoMissileRack extends MissileLauncher
{
    public $name = "soMissileRack";
    public $displayName = "Class-SO Missile Rack";
    public $range = 20;
    public $distanceRange = 60;
    public $loadingtime = 2;
    public $iconPath = "missile1.png";

    public $fireControl = array(2, 2, 2); // fighters, <mediums, <capitals 
    
    public function getDamage($fireOrder)
    {
//        $ammo = new $this->firingModes[$fireOrder->firingMode];
//        return $ammo->getDamage();
        return 20;
    }
    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;}     
}

class LMissileRack extends MissileLauncher
{
    public $name = "lMissileRack";
    public $displayName = "Class-L Missile Rack";
    public $range = 30;
    public $distanceRange = 70;
    public $loadingtime = 2;
    public $iconPath = "missile1.png";
    public $rangeMod = 10;
    protected $distanceRangeMod = 10;

    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 

    /*
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }*/
    
    public function getDamage($fireOrder)
    {
//        $ammo = new $this->firingModes[$fireOrder->firingMode];
//        return $ammo->getDamage();
        return 20;
    }
    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;}     
}


class LHMissileRack extends MissileLauncher
{
    public $name = "lHMissileRack";
    public $displayName = "Class-LH Missile Rack";
    public $range = 30;
    public $distanceRange = 70;
    public $loadingtime = 1;
    public $iconPath = "missile2.png";
    public $rangeMod = 10;
    protected $distanceRangeMod = 10;
    private $rackExplosionDamage = 0; //this rack directs explosion damage outwards - is itself destroyed, but does not damage ship beyond that
    
    public $fireControl = array(4, 4, 4); // fighters, <mediums, <capitals 
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }
    
    public function getDamage($fireOrder)
    {
//        $ammo = new $this->firingModes[$fireOrder->firingMode];
//        return $ammo->getDamage();
        return 20;
    }
    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;} 
}


class BMissileRack extends MissileLauncher {

    public $name = "bMissileRack";
    public $displayName = "Class-B Missile Rack";
    public $range = 30;
    public $distanceRange = 60;
    public $loadingtime = 1;
    public $iconPath = "missile3.png";
    public $rangeMod = 10;
    
    private $rackExplosionDamage = 0; //this rack directs explosion damage outwards - is itself destroyed, but does not damage ship beyond that
    
    
    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $base = false){

        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

        if ($base){
            $this->range = 60;
            $this->rangeMod = 30;
        }

    }

    /*
    public function testAmmoExplosion($ship, $gamedata){
        return false;
    }
    */


    public function getDamage($fireOrder){
        return 20;
    }

    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;} 
}


class BombRack extends MissileLauncher
{
    public $name = "BombRack";
    public $displayName = "Bomb Rack";
    public $range = 20;
    public $distanceRange = 60;
    public $loadingtime = 2;
    public $iconPath = "bombRack.png";
    
    private $rackExplosionDamage = 30; //Bomb Rack carries fewer missiles than standard missile launcher...

    public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 
    
    public function getDamage($fireOrder)
    {
        return 20;
    }
    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;}     
}



class FighterMissileRack extends MissileLauncher
{
    public $name = "FighterMissileRack";
    public $displayName = "Fighter Missile Rack";
    public $loadingtime = 1;
    public $iconPath = "fighterMissile.png";
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    public $ballistic = true;
    public $animationExplosionScale = 0.15;
    public $projectilespeed = 10;
    public $animationWidth = 2;
    public $trailLength = 60;
    //protected $distanceRangeMod = 0;
    public $priority = 4;

    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
    
    public $firingModes = array(
        1 => "FB"
    );
    
    function __construct($maxAmount, $startArc, $endArc){
        parent::__construct(0, 0, 0, $startArc, $endArc);
        $MissileFB = new MissileFB($startArc, $endArc, $this->fireControl);
        $this->missileArray = array(
            1 => $MissileFB
        );
        $this->maxAmount = $maxAmount;
    }
    
    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);

        //$this->data["Weapon type"] = "Ballistic";
        //$this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = $this->missileArray[$this->firingMode]->displayName;
        //$this->data["Damage"] = $this->missileArray[$this->firingMode]->damage;
        
        if($this->missileArray[$this->firingMode]->minDamage != $this->missileArray[$this->firingMode]->maxDamage){
            $this->data["Damage"] = "".$this->missileArray[$this->firingMode]->minDamage."-".$this->missileArray[$this->firingMode]->maxDamage;
        }else{
            $this->data["Damage"] = "".$this->missileArray[$this->firingMode]->minDamage;
        }
        
        $distRange = max($this->missileArray[$this->firingMode]->range, $this->missileArray[$this->firingMode]->distanceRange);
        $this->data["Range"] = $this->missileArray[$this->firingMode]->range;
        if( $distRange > $this->missileArray[$this->firingMode]->range) $this->data["Range"] .= '/' . $this->missileArray[$this->firingMode]->distanceRange;
    }

    /* no longer needed
    public function calculateHit($gamedata, $fireOrder){
        $ammo = $this->missileArray[$fireOrder->firingMode];
        $ammo->calculateHit($gamedata, $fireOrder); //essentially ammo is the weapon, not launcher! OB is calculated separately, so launcher FC is irrelevant
    }*/
    
    public function setId($id){
        parent::setId($id);
        $counter = 0;
        foreach ($this->missileArray as $missile){
            $missile->setId(1000 + ($id*10) + $counter);
            $counter++;
        } 
    }


    /* not needed?
    public function setFireControl($fighterOffensiveBonus){
        $this->fireControl[0] = $fighterOffensiveBonus;
        $this->fireControl[1] = $fighterOffensiveBonus;
        $this->fireControl[2] = $fighterOffensiveBonus;
    }
    */
    
    /*MissileLauncher covers this - isInDistanceRange()
    public function getLaunchRange(){
        return $this->missileArray[$this->firingMode]->range;
    }
    
    public function getDistanceRange(){
        //return $this->missileArray[$this->firingMode]->range * 3 + $this->distanceRangeMod;
        return $this->missileArray[$this->firingMode]->distanceRange;
    }
    */
    
    /* no longer needed
    public function isInDistanceRange($shooter, $target, $fireOrder) //will this be used anywhere?... Ammo will be checked for distance range, not this
    {
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
    
        if(mathlib::getDistanceHex($pos,  $target->getCoPos()) > $this->getDistanceRange())
        {
            $fireOrder->pubnotes .= " FIRING SHOT (LAUNCHER): Target moved out of distance range.";
            return false;
        }
        
        return true;
    }
    */
    
    public function addAmmo($missileClass, $amount){
        foreach($this->missileArray as $missile){
            if(strcmp($missile->missileClass, $missileClass) == 0){
                $missile->setAmount($amount);
                break;
            }
        }
    }
    
/*no longer needed, kept just in case
    public function fire($gamedata, $fireOrder){
        $ammo = $this->missileArray[$fireOrder->firingMode];
        
        if($ammo->amount > 0){
            $ammo->amount--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, TacGamedata::$currentGameID, $this->firingMode, $ammo->amount);
        }
        else{
            
            $fireOrder->notes = "No ammo available of the selected type.";
            $fireOrder->updated = true;
            return;
        }
        
        $ammo->fire($gamedata, $fireOrder);
    }
*/    
    
    public function fire($gamedata, $fireOrder){ //just decrease ammo and move to standard
        $ammo = $this->missileArray[$fireOrder->firingMode];
        if($ammo->amount > 0){
            $ammo->amount--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $ammo->amount, $gamedata->turn);
        }
        else{
            $fireOrder->notes = "No ammo available of the selected type.";
            $fireOrder->updated = true;
            return;
        }
        parent::fire($gamedata, $fireOrder);
    }
    
    public function getDamage($fireOrder){
        $ammo = $this->missileArray[$fireOrder->firingMode];
        return $ammo->getDamage($fireOrder);
    }
    
    public function setMinDamage(){
        if(isset($this->missileArray[$this->firingMode])){  //it might not be set in the beginning!
            $ammo = $this->missileArray[$this->firingMode];
            $ammo->setMinDamage();
            $this->minDamage =  $ammo->minDamage;
        }
    }
    public function setMaxDamage(){
        if(isset($this->missileArray[$this->firingMode])){  //it might not be set in the beginning!
            $ammo = $this->missileArray[$this->firingMode];
            $ammo->setMaxDamage();
            $this->maxDamage =  $ammo->maxDamage;
        }
    }
    
    /* MissileLauncher covers this
    public function getWeaponHitChanceMod($turn)
    {
        $ammo = $this->missileArray[$fireOrder->firingMode];
        return $ammo->hitChanceMod;
    }
    */
    
    
    /*here: copy missile data to launcher itself!*/
    public function changeFiringMode($newMode){ //change parameters with mode change
        parent::changeFiringMode($newMode);
        if(isset($this->missileArray[$newMode])){  //it might not be set in the beginning!
            $ammo = $this->missileArray[$newMode];
            $this->setMinDamage();
            $this->setMaxDamage();
            $this->range = $ammo->range;
            $this->distanceRange = $ammo->distanceRange;
            $this->priority = $ammo->priority;
            //$this->fireControl = $ammo->fireControl; //FC should be left that of launcher, after all
            $this->displayName = $ammo->displayName; //so missile name goes into log instead of launcher name
        }
    }
    
} //endof FighterMissileRack



class FighterTorpedoLauncher extends FighterMissileRack
{
    public $name = "FighterTorpedoLauncher";
    public $displayName = "Fighter Torpedo Launcher";
    public $loadingtime = 1;
    public $iconPath = "fighterTorpedo.png";
    public $rangeMod = 0;
    public $firingMode = 1;
    public $maxAmount = 0;
    protected $distanceRangeMod = 0;
    public $priority = 4;

    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
    
    public $firingModes = array(
        1 => "LBT"
    );
    
    function __construct($maxAmount, $startArc, $endArc){
        parent::__construct($maxAmount, $startArc, $endArc);
        
        $LBTorp = new LightBallisticTorpedo($startArc, $endArc, $this->fireControl);
        
        $this->missileArray = array(
            1 => $LBTorp
        );
        
        $this->maxAmount = $maxAmount;
    }
    
    
/*not needed, basic weapon provides
    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);

        //$this->data["Weapon type"] = "Ballistic";
        //$this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = $this->missileArray[$this->firingMode]->displayName;
        if($this->missileArray[$this->firingMode]->minDamage != $this->missileArray[$this->firingMode]->maxDamage){
            $this->data["Damage"] = "".$this->missileArray[$this->firingMode]->minDamage."-".$this->missileArray[$this->firingMode]->maxDamage;
        }else{
            $this->data["Damage"] = "".$this->missileArray[$this->firingMode]->minDamage;
        }
        $this->data["Range"] = $this->missileArray[$this->firingMode]->range;
    }
    
    public function getDistanceRange(){
        return $this->missileArray[$this->firingMode]->range;
    }
    */
}


/*implements weapon because it can do damage like one - on rack explosion*/
class ReloadRack extends MissileLauncher//ShipSystem
{
    public $name = "ReloadRack";
    public $displayName = "Reload Rack";
    public $iconPath = "missileReload.png";
    
   
    //it can explode, too...
    public $weapon = false; //well, it can't be actually fired
    public $damageType = 'Flash'; //needed to simulate that it's a weapon
    public $weaponClass = 'Ballistic';
    private $rackExplosionDamage = 150; //how much damage will this weapon do in case of catastrophic explosion
    private $rackExplosionThreshold = 19; //how high roll is needed for rack explosion
    
    function __construct($armour, $maxhealth){
        //parent::__construct($armour, $maxhealth, 0, 0); //that's for extending ShipSystem
        parent::__construct($armour, $maxhealth, 0, 0, 0);//that's for extending MissileLauncher
    }
    
/*not needed if implementing MissileLauncher takes care of it!    
    public function testCritical($ship, $gamedata, $crits, $add = 0){ //add testing for ammo explosion!
        $explodes = false;

        $roll = Dice::d(20);
        if ($roll >= $this->rackExplosionThreshold) $explodes = true;
        
        if($explodes){
            $this->ammoExplosion($ship, $gamedata, $this->rackExplosionDamage);            
            $this->addMissileCritOnSelf($ship->id, "AmmoExplosion", $gamedata);
        }else{
            parent::testCritical($ship, $gamedata, $crits, $add);
        }
    } //endof function testCritical


    public function ammoExplosion($ship, $gamedata, $damage){
        //first, destroy self if not yet done...
        if (!$this->isDestroyed()){
            $this->noOverkill = true;
            $fireOrder =  new FireOrder(-1, "ammoExplosion", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 'standard', 100, 1, 1, 1, 0, null, null, 'ballistic');
            $dmgToSelf = 1000; //rely on $noOverkill instead of counting exact amount left - 1000 should be more than enough...
            $this->doDamage($ship, $ship, $this, $dmgToSelf, $fireOrder, $pos, $gamedata, true, $this->getLocation());
        }
        
        //then apply damage potential as a hit...
        if($damage>0){
            $this->noOverkill = false;
            $this->damageType = 'Flash'; //should be Raking by the rules, but Flash is much easier to do - and very fitting for explosion!
            $fireOrder =  new FireOrder(-1, "ammoExplosion", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 'flash', 100, 1, 1, 1, 0, null, null, 'ballistic');
            $this->doDamage($ship, $ship, $this, $damage, $fireOrder, null, $gamedata, false, $this->getLocation()); //show $this as target system - this will ensure its destruction, and Flash mode will take care of the rest
        }
    }
    
    public function addMissileCritOnSelf($shipid, $phpclass, $gamedata){
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
    }
*/    
} //endof class ReloadRack






?>
