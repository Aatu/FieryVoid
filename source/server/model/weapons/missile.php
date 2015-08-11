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
    public $priority = 8;
    public $hits = array();

    protected $distanceRangeMod = 0;
    
    public $firingModes = array(
        1 => "B"
    );
    
    public $missileArray = array();
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $MissileB = new MissileB($startArc, $endArc, $this->fireControl);
        $this->missileArray = array(
            1 => $MissileB
        );
    }

    public function setSystemDataWindow($turn){
        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = "Basic missile";

        parent::setSystemDataWindow($turn);
    }
    
    public function isInDistanceRange($shooter, $target, $fireOrder){
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
    
        if(mathlib::getDistanceHex($pos,  $target->getCoPos()) > $this->distanceRange)
        {
            $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
            return false;
        }
        return true;
    }
    
    public function setAmmo($firingMode, $amount){
        if(count($this->missileArray) > 0){
            $this->missileArray[$firingMode]->amount = $amount;
        }
    }
    
    protected function getAmmo($fireOrder){
        return $this->missileArray[$fireOrder->firingMode];
    }

    public function testAmmoExplosion($ship, $gamedata){
        $toDO;

        $roll = Dice::d(20);
        if ($roll >= 19){
            if ($this instanceof BombRack){
                $toDO = 35;
            } else if ($this instanceof ReloadRack){
                $toDO = 120;
            }
            else $toDO = 70;

            debug::log("ammo exp for: ".$toDO." on".$this->displayName." id: ".$this->id);

            $this->ammoExplosion($ship, $gamedata, $toDO);
            $crit = $this->addCritical($ship->id, "AmmoExplosion", $gamedata);
        }
    }


    public function ammoExplosion($ship, $gamedata, $toDO){
        $rake = 10;
        $left = $toDO;

        while ($left > 0){
            if ($ship->isDestroyed()){
                break;
            }

            $system = $this;

            if ($this->isDestroyed()){
                $system = $this->getHitSystem($ship);
            }

            if ($left < $rake){
                $rake = $left;
            }

            $this->ammoExplosionDamage($ship, $system, $rake, $gamedata);
            $left -= $rake;
        }
    }


    public function getHitSystem($ship){

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
        
        $damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $system->id, $modifiedDamage, $armour, 0, -1, $destroyed, "");
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

    
    public function addCritical($shipid, $phpclass, $gamedata){

        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
        return $crit;
    }
}       



class SMissileRack extends MissileLauncher{
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
    
    public function getDamage($fireOrder)
    {
//        $ammo = new $this->firingModes[$fireOrder->firingMode];
//        return $ammo->getDamage();
        return 20;
    }
    public function setMinDamage(){     $this->minDamage = 20 - $this->dp;}
    public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;}     
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

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }
    
    public function getDamage($fireOrder)
    {
//        $ammo = new $this->firingModes[$fireOrder->firingMode];
//        return $ammo->getDamage();
        return 20;
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
    public $rangeMod = 10;
    protected $distanceRangeMod = 10;

    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

    }
    
    public function getDamage($fireOrder)
    {
//        $ammo = new $this->firingModes[$fireOrder->firingMode];
//        return $ammo->getDamage();
        return 20;
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
    public $rangeMod = 10;
    protected $distanceRangeMod = 10;
    
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
    public function setMinDamage(){     $this->minDamage = 20 - $this->dp;}
    public function setMaxDamage(){     $this->maxDamage = 20 - $this->dp;} 
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
    protected $distanceRangeMod = 0;

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

        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = $this->missileArray[$this->firingMode]->displayName;
        $this->data["Damage"] = $this->missileArray[$this->firingMode]->damage;
        $this->data["Range"] = $this->missileArray[$this->firingMode]->range;
    }

    public function calculateHit($gamedata, $fireOrder){
        $ammo = $this->missileArray[$fireOrder->firingMode];
        $ammo->calculateHit($gamedata, $fireOrder);
    }
    
    public function setId($id){
        
        parent::setId($id);
        
        $counter = 0;
        
        foreach ($this->missileArray as $missile){
            $missile->setId(1000 + ($id*10) + $counter);
            $counter++;
        } 
    }

    
    public function setFireControl($fighterOffensiveBonus){
        $this->fireControl[0] = $fighterOffensiveBonus;
        $this->fireControl[1] = $fighterOffensiveBonus;
        $this->fireControl[2] = $fighterOffensiveBonus;
    }
    
    public function getLaunchRange(){
        return $this->missileArray[$this->firingMode]->range;
    }
    
    public function getDistanceRange(){
        return $this->missileArray[$this->firingMode]->range * 3 + $this->distanceRangeMod;
    }
    
    public function isInDistanceRange($shooter, $target, $fireOrder)
    {
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);
        $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
    
        if(mathlib::getDistanceHex($pos,  $target->getCoPos()) > $this->getDistanceRange())
        {
            $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
            return false;
        }
        
        return true;
    }

//    protected function getAmmo($fireOrder)
//    {
//        return new $this->missileArray[$fireOrder->firingMode];
//    }
    
    public function addAmmo($missileClass, $amount){
        foreach($this->missileArray as $missile){
            if(strcmp($missile->missileClass, $missileClass) == 0){
                $missile->setAmount($amount);
                break;
            }
        }
    }
    
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
    
    public function getDamage($fireOrder)
    {
        $ammo = $this->missileArray[$fireOrder->firingMode];
        return $ammo->getDamage();
    }
    
    public function setMinDamage(){ 0;}
    public function setMaxDamage(){ 0;}     
}

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
    
    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);

        $this->data["Weapon type"] = "Torpedo";
        $this->data["Damage type"] = "Standard";
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

class BombRack extends MissileLauncher{

    public $name = "BombRack";
    public $displayName = "Bomb Rack";
    public $loadingtime = 2;
    public $iconPath = "bombRack.png";
    public $firingMode = 1;
    public $maxAmount = 8;
    public $ballistic = true;
    public $animationExplosionScale = 0.25;
    public $projectilespeed = 8;
    public $animationWidth = 4;
    public $trailLength = 100;
    protected $distanceRangeMod = 0;

    public $firingModes = array(
        1 => "B"
    );
    
    //public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 
    // For FV, the only option for a bomb rack is to load it with missiles.
    // In that case, it behaves exactly like a SMissile-rack, including
    // the FC.
    public $fireControl = array(1, 2, 3); // fighters, <mediums, <capitals 
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

        $MissileB = new MissileB($startArc, $endArc, $this->fireControl);
        
        $this->missileArray = array(
            1 => $MissileB
        );
    }
    
    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);

        $this->data["Weapon type"] = "Missile";
        $this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = $this->missileArray[$this->firingMode]->displayName;
        $this->data["Damage"] = $this->missileArray[$this->firingMode]->damage;
        $this->data["Range"] = $this->missileArray[$this->firingMode]->range;
    }

    public function calculateHit($gamedata, $fireOrder){
        $ammo = $this->missileArray[$fireOrder->firingMode];
        $ammo->calculateHit($gamedata, $fireOrder);
    }
    
//    protected function getAmmo($fireOrder)
//    {
//        return new $this->missileArray[$fireOrder->firingMode];
//    }

    public function addAmmo($missileClass, $amount){
        foreach($this->missileArray as $missile){
            if(strcmp($missile->missileClass, $missileClass) == 0){
                $missile->setAmount($amount);
                break;
            }
        }
    }

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

    public function getLaunchRange(){
        return $this->missileArray[$this->firingMode]->range;
    }
    
    public function getDistanceRange(){
        return $this->missileArray[$this->firingMode]->range * 3 + $this->distanceRangeMod;
    }
}