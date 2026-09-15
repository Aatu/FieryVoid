<?php

class MissileLauncher extends Weapon{
    public $useOEW = false;
    public $ballistic = true;
	
    public $animation = "trail";
    public $animationColor = array(50, 50, 50);
	
    public $distanceRange = 0;
    public $firingMode = 1;
    public $rangeMod = 0;
    public $priority = 6;
    public $hits = array();

    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ballistic"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!    
    
    protected $distanceRangeMod = 0;
    
    private $rackExplosionDamage = 70; //how much damage will this weapon do in case of catastrophic explosion; officially "every missile remaining", here in FV it's severely reduced - but still a major threat
    private $rackExplosionThreshold = 19; //how high roll is needed for rack explosion
    
    public $firingModes = array(
        1 => "B"
    );
    
    public $missileArray = array();
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $base=false){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

		//Stabilized missiles should have triple the range, not double - Geoffrey (06 September 2021)
		//modified by Marcin on 20th of December
		
        if ($base){ //mounted on base - triple the launch range
		
            $this->rangeMod = $this->rangeMod + ($this->range * 2); 
			$this->range = $this->range * 3;
//GTS            $this->rangeMod = $this->rangeMod + $this->range; 
//GTS            $this->range = $this->range *2;      

			//$this->range = $this->distanceRange; //much simplified! ...but not working due to weapon/ammo interaction :(
		}

        $MissileB = new MissileB($startArc, $endArc, $this->fireControl);
        $this->missileArray = array(
            1 => $MissileB
        );
    }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();

        // Ammo objects in missileArray are data containers without a ship unit reference,
        // so they can't use the full stripForJson() chain. Strip public properties directly,
        // skipping empty arrays to reduce JSON payload.
        $strippedSystem->missileArray = array_map(
            function($missile) {
                $stripped = new stdClass();
                $reflect = new ReflectionObject($missile);
                foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
                    $key = $prop->getName();
                    $value = $missile->$key;
                    if (is_array($value) && empty($value)) continue;
                    if ($value === '') continue;
                    $stripped->$key = $value;
                }
                return $stripped;
            },
            $this->missileArray
        );
        return $strippedSystem;
    }
    
    public function setSystemDataWindow($turn){
        //$this->data["Weapon type"] = "Ballistic";
        //$this->data["Damage type"] = "Standard";
        $this->data["Ammo"] = "Basic missile";
        $this->data["Missile Hit Mod"] = $this->missileArray[1]->hitChanceMod*5;

        parent::setSystemDataWindow($turn);
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
		$fireOrder = new FireOrder(
			-1, "normal", $ship->id, $ship->id,
			$this->id, -1, $gamedata->turn, 1, 
			100, 100, 1, 1, 0,
			0,0,'Ballistic',10000
		);					
		$fireOrder->pubnotes = "Magazine explosion";
		$fireOrder->addToDB = true;
		$this->fireOrders[] = $fireOrder;
					
        //first, destroy self if not yet done...
        if (!$this->isDestroyed()){
			$remaining =  $this->getRemainingHealth();
			$damageEntry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $this->id, $remaining, 0, 0, -1, true, false, "", "Ballistic");
			$damageEntry->updated = true;
			$damageEntry->shooterid = $ship->id; //additional field
			$damageEntry->weaponid = $this->id; //additional field
			$this->damage[] = $damageEntry;
        }
        
        //then apply damage potential as a hit...
        if($damage>0){
            $this->noOverkill = false;
            $this->damageType = 'Flash'; //should be Raking by the rules, but Flash is much easier to do - and very fitting for explosion!
            $this->doDamage($ship, $ship, $this, $damage, $fireOrder, null, $gamedata, false, $this->location); //show $this as target system - this will ensure its destruction, and Flash mode will take care of the rest
        }
    }
    
    public function addMissileCritOnSelf($shipid, $phpclass, $gamedata){
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
    }
} //endof MissileLauncher 


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
    
    public function getDamage($fireOrder)
    {
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
    
    
    public function getDamage($fireOrder)
    {
        return 20;
    }
    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;} 
}


class BMissileRack extends MissileLauncher {

    public $name = "bMissileRack";
    public $displayName = "Class-B Missile Rack";
    public $range = 20;
    public $distanceRange = 60;
    public $loadingtime = 1;
    public $iconPath = "missile3.png";
    public $rangeMod = 10;
    
    private $rackExplosionDamage = 0; //this rack directs explosion damage outwards - is itself destroyed, but does not damage ship beyond that
    
    
    public $fireControl = array(3, 3, 3); // fighters, <mediums, <capitals 
    

    public function getDamage($fireOrder){
        return 20;
    }

    public function setMinDamage(){     $this->minDamage = 20 ;}
    public function setMaxDamage(){     $this->maxDamage = 20 ;} 
}


class AMissileRack extends MissileLauncher{
    public $name = "aMissileRack";
    public $displayName = "Class-A Missile Rack";
    public $range = 15;
	public $rangeMod = -5;
    public $distanceRange = 45;
    public $loadingtime = 1;
    public $iconPath = "missile1.png";
	
    private $rackExplosionDamage = 45; //using much less potent missiles than standard class-S launcher

    public $fireControl = array(10, 3, 3); // fighters, <mediums, <capitals 

    
    public function getDamage($fireOrder)
    {
        return 15;
    }
    public function setMinDamage(){     $this->minDamage = 15 ;}
    public function setMaxDamage(){     $this->maxDamage = 15 ;}     
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
	/*
    public $animationExplosionScale = 0.15;
    public $projectilespeed = 10;
    public $animationWidth = 2;
    public $trailLength = 60;
	*/
    //protected $distanceRangeMod = 0;
    public $priority = 5; //large fighter weapon

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
    
    public function setId($id){
        parent::setId($id);
        $counter = 0;
        foreach ($this->missileArray as $missile){
            $missile->setId(1000 + ($id*10) + $counter);
            $counter++;
        } 
    }

    
    public function addAmmo($missileClass, $amount){
        foreach($this->missileArray as $missile){
            if(strcmp($missile->missileClass, $missileClass) == 0){
                $missile->setAmount($amount);
                break;
            }
        }
    }
    
    
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

    /* Hangar Ops Stage 17: legacy ballistic missile reload while docked.
     * Driven by HangarOps::serviceDockedFlights (same per-turn tick that drives
     * Weapon::whileDocked for matter weapons and AmmoMagazine::whileDocked for
     * modern fighter missiles). Each restocked round draws its $ammo->cost from
     * the carrier's HANG_ORD pool via HangarOps::drawReload.
     *
     * Rate: 1 missile per FIGHTER per turn, even when the fighter mounts
     * multiple FighterMissileRack launchers. Matches the AmmoMagazine cadence
     * (Stage 15) rather than the per-launcher cadence of Stage 14 matter
     * weapons. The first FighterMissileRack subsystem encountered on the
     * fighter does the per-turn work for the whole fighter: walks every
     * FighterMissileRack sibling, picks the priciest missing round across all
     * of them (tiebreak most-missing), restocks one, then stamps a transient
     * flag on the parent Fighter so the remaining siblings' whileDocked calls
     * early-return.
     *
     * Persistence: tac_ammo per-turn row via Manager::updateAmmoInfo (same
     * path fire() uses). The pool-spend side rides on Stage 15's hangarOrdReserve
     * note via drawReload's mutation on the primary hangar's $reloadPoolSpent.
     */
    public function whileDocked($flight, $carrier, $hangar, $gamedata){
        if (!($flight instanceof FighterFlight)) return;
        if ($this->isDestroyed($gamedata->turn)) return;
        //$this->getUnit() returns the FighterFlight, not the parent Fighter
        //(FighterFlight::addSystem wires setUnit($this) for both the Fighter
        //and every sub-system — see FighterFlight.php:297-304). Resolve the
        //parent Fighter via the flight's existing system-id lookup so the
        //sibling-walk and the per-fighter gate target the right scope.
        $fighter = $flight->getFighterBySystem($this->id);
        if (!$fighter) return;

        //Per-fighter rate gate. $missileRackReloadedTurn is transient — lives
        //only for the current criticalPhaseEffects pass; next turn-load builds
        //fresh Fighter objects with the property unset, so the gate resets.
        if (!empty($fighter->missileRackReloadedTurn)
            && (int)$fighter->missileRackReloadedTurn >= (int)$gamedata->turn) return;

        //Build candidate list from EVERY FighterMissileRack-class sibling on
        //this fighter, not just $this — ensures we pick the best across the
        //fighter's whole loadout, not just the first launcher iterated.
        $candidates = array();
        foreach ($fighter->systems as $sib) {
            if (!($sib instanceof FighterMissileRack)) continue;
            if ($sib->isDestroyed($gamedata->turn)) continue;
            $cap = (int)$sib->maxAmount;
            if ($cap <= 0) continue;
            if (!is_array($sib->missileArray)) continue;
            foreach ($sib->missileArray as $mode => $ammo) {
                if (!$ammo) continue;
                if ((int)$ammo->amount >= $cap) continue;
                $candidates[] = array(
                    'launcher' => $sib,
                    'mode'     => $mode,
                    'ammo'     => $ammo,
                    'price'    => (int)$ammo->cost,
                    'cap'      => $cap,
                );
            }
        }
        if (empty($candidates)) return;

        //Priciest first; tiebreak by most-missing (cap - amount).
        usort($candidates, function($a, $b){
            if ($a['price'] !== $b['price']) return $b['price'] - $a['price'];
            $aMissing = $a['cap'] - (int)$a['ammo']->amount;
            $bMissing = $b['cap'] - (int)$b['ammo']->amount;
            return $bMissing - $aMissing;
        });

        foreach ($candidates as $cand) {
            if (!HangarOps::drawReload($carrier, $cand['price'])) continue;
            $cand['ammo']->amount = min($cand['cap'], (int)$cand['ammo']->amount + 1);
            //fire() uses $fireOrder->shooterid (the flight id) as the ship id
            //and $this->firingMode as the mode — match that exactly so loading
            //(setAmmo($mode, $amount)) targets the right launcher subsystem.
            Manager::updateAmmoInfo(
                $flight->id,
                $cand['launcher']->id,
                $gamedata->id,
                $cand['launcher']->firingMode,
                $cand['ammo']->amount,
                $gamedata->turn
            );
            $fighter->missileRackReloadedTurn = (int)$gamedata->turn;
            return;   //1 missile per fighter per turn
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
    public $priority = 4; //priority: typical fighter weapon (correct for Light Ballistic Torpedo's 2d6)


    public $animation = "torpedo";

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
    
}


/*implements weapon because it can do damage like one - on rack explosion*/
class ReloadRack extends Weapon //ShipSystem
{
    public $name = "ReloadRack";
    public $displayName = "Reload Rack";
    public $iconPath = "missileReload.png";
	
    public $fireControl = array(null, null, null); // this is a weapon in name only, it cannot actually fire!
    
    //public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
    //public $isTargetable = false; //false means it cannot be targeted at all by called shots! - good for technical systems :)
   
    //it can explode, too...
    //public $weapon = false; //well, it can't be actually fired BUT if marked, explosions will not be shown in log!
    public $damageType = 'Flash'; //needed to simulate that it's a weapon
    public $weaponClass = 'Ballistic';
    private $rackExplosionDamage = 300; //how much damage will this weapon do in case of catastrophic explosion (80 missiles... that's devastating)
    private $rackExplosionThreshold = 20; //how high roll is needed for rack explosion    
	
		protected $possibleCriticals = array(); //Reload Rack does not suffer any criticals (barring catastrophic explosion)
		
	
    function __construct($armour, $maxhealth){
        //parent::__construct($armour, $maxhealth, 0, 0); //that's for extending ShipSystem
        parent::__construct($armour, $maxhealth, 0, 0, 0);//that's for extending Weapon
		
		$this->data["Special"] = 'Additional missile magazine for actual combat launchers. No actual effect in FV.';
		
		if ($this->rackExplosionThreshold < 21) { //can explode - inform player!
			$chance = (21 - $this->rackExplosionThreshold) * 5; //percentage chance of explosion
			$this->data["Special"] .= '<br>Can explode if damaged or destroyed, dealing ' . $this->rackExplosionDamage . ' damage in Flash mode (' . $chance . '% chance).';
		}
    }
	
	public function getDamage($fireOrder){ return 0; }
	public function setMinDamage(){ 
		$this->minDamage = 0; 
	}
	public function setMaxDamage(){
		$this->maxDamage = 0; 
	}	
	
	
	public function criticalPhaseEffects($ship, $gamedata){ //add testing for ammo explosion!
	
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.
	
		if(!$this->isDamagedOnTurn($gamedata->turn)) return; //if there is no damage this turn, then no testing for explosion
        $explodes = false;
        $roll = Dice::d(20);
        if ($roll >= $this->rackExplosionThreshold) $explodes = true;
        		
        if($explodes){
            $this->ammoExplosion($ship, $gamedata, $this->rackExplosionDamage, $roll);  
        }
    } //endof function testCritical
    public function ammoExplosion($ship, $gamedata, $damage, $roll){
        //first, destroy self if not yet done...
        if (!$this->isDestroyed()){
            $this->noOverkill = true;
            $fireOrder =  new FireOrder(-1, "normal", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 1, 100, 1, 1, 1, 0, null, null, 'ballistic');//needed, rolled, shots, shotshit, intercepted
			$fireOrder->addToDB = true;
			$fireOrder->pubnotes = "Missile magazine explosion (roll $roll)!";
			$this->fireOrders[] = $fireOrder;							
            $dmgToSelf = 1000; //rely on $noOverkill instead of counting exact amount left - 1000 should be more than enough...
            $this->doDamage($ship, $ship, $this, $dmgToSelf, $fireOrder, null, $gamedata, true, $this->location);
        }        
        //then apply damage potential as a hit... should be Raking by the rules, let's do it as Flash instead (not quite the same, but easier to do)
        if($damage>0){
            $this->noOverkill = false;
            $this->damageType = 'Flash'; //should be Raking by the rules, but Flash is much easier to do - and very fitting for explosion!
            $fireOrder =  new FireOrder(-1, "normal", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 1, 100, 1, 1, 1, 0, null, null, 'ballistic');
			$fireOrder->addToDB = true;
			$fireOrder->pubnotes = "Missile magazine explosion (roll $roll)!";
			$this->fireOrders[] = $fireOrder;					
            $this->doDamage($ship, $ship, $this, $damage, $fireOrder, null, $gamedata, false, $this->location); //show $this as target system - this will ensure its destruction, and Flash mode will take care of the rest
        }
    }
} //endof class ReloadRack



/*weapon with unlimited ammo, able to use multiple munitions (modes of fire), simulating many missile launchers in game on demand (at constructor)
pricing proposal: standard launchers + 25/launcher, improved range additional +10, improved fire rate: double these numbers (so class-S +25, class-L +35, class-R +50, class-LH +70)
skip -L but double that when mounted on a base, dues to Stable rule... so class-S/L on base would be alike at 50, class R/LH/B also alike at 100 
*/
class MultiMissileLauncher extends Weapon{
	public $name = "multiMissileLauncher";
        public $displayName = "ToBeSetInConstructor";
    public $useOEW = false;
    public $ballistic = true;
    public $animation = "trail";
    public $animationColor = array(50, 50, 50);
	/*
    public $trailColor = array(141, 240, 255);
    public $animationExplosionScale = 0.6;
    public $projectilespeed = 8;
    public $animationWidth = 4;
    public $trailLength = 100;
	*/
    public $range = 20;
    public $distanceRange = 60;
    public $firingMode = 1;
    public $rangeMod = 0;
    public $priority = 6;
    public $hits = array();
    public $loadingtime = 2;
    public $iconPath = "missile1.png";    

    private $rackExplosionDamage = 70; //how much damage will this weapon do in case of catastrophic explosion
    private $rackExplosionThreshold = 20; //how high roll is needed for rack explosion    
    
    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ballistic"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	
	public $firingModes = array(1=>'Basic', 2=>'LongRange', 3=>'Heavy', 4=>'Flash', 5=>'Piercing', 6=>'AntiFighter'); //equals to available missiles; data is basic - if launcher is special, constructor will modify it
	public $damageTypeArray = array(1=>'Standard', 2=>'Standard', 3=>'Standard', 4=>'Flash', 5=>'Piercing', 1=>'Standard'); //indicates that this weapon does damage in Pulse mode
    public $fireControlArray = array( 1=>array(6, 6, 6), 2=>array(6,6,6), 3=>array(3,6,6), 4=>array(6,6,6), 5=>array(3,6,6), 6=>array(9,6,6) ); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler
    //typical (class-S) launcher is FC 3/3/3 and warhead +3 - which would mean 6/6/6!
    public $rangeArray = array(1=>20, 2=>30, 3=>10, 4=>20, 5=>20, 6=>15); 
	public $distanceRangeArray = array(1=>60, 2=>90, 3=>30, 4=>60, 5=>60, 6=>45); 
    
    /*ATYPICAL constructor: doesn't take health and power usage, but takes desired launcher type - and does appropriate modifications*/
        function __construct($armour, $launcherType, $startArc, $endArc, $base=false)
        {
		switch($launcherType){ //modifications dependent on launcher type...
			case 'SO': //standard old: lesser FC, holds less missiles (= lesser crit potential!)
				$this->displayName = "Class-SO Missile Rack";
				$maxhealth = 6;
				foreach ($this->fireControlArray as $key=>$FCarray){
					$this->fireControlArray[$key][0] -= 1; //fighter
					$this->fireControlArray[$key][1] -= 1; //medium
					$this->fireControlArray[$key][2] -= 1; //Cap
				}
				$this->iconPath = "missile1.png";  
				$this->rackExplosionDamage = 40;
				break;
			case 'L': //Long range: +10 launch range
				$this->displayName = "Class-L Missile Rack";
				$maxhealth = 6;
				$this->iconPath = "missile1.png";
				foreach ($this->rangeArray as $key=>$rng) {
					$this->rangeArray[$key] += 10; 
					$this->distanceRangeArray[$key] += 10; 
				}     
				break;				
			case 'LH': //Long range, Hardened: +10 launch range, RoF 1/turn, does not explode, better FC
				$this->displayName = "Class-LH Missile Rack";
				$maxhealth = 8;
				$this->iconPath = "missile2.png";
				foreach ($this->rangeArray as $key=>$rng) {
					$this->rangeArray[$key] += 10; 
					$this->distanceRangeArray[$key] += 10; 
				}     
				foreach ($this->fireControlArray as $key=>$FCarray){
					$this->fireControlArray[$key][0] += 1; //fighter
					$this->fireControlArray[$key][1] += 1; //medium
					$this->fireControlArray[$key][2] += 1; //Cap
				}
				$this->loadingtime = 1; //fires every turn
				$this->rackExplosionDamage = 0; //how much damage will this weapon do in case of catastrophic explosion
				$this->rackExplosionThreshold = 21; //how high roll is needed for rack explosion   
				break;	
			case 'B': //just like LH, really, just a bit tougher and with more ammo (irrelevant here) - though without FC bonus; stable but constructor takes care of that
				$this->displayName = "Class-B Missile Rack";
				$maxhealth = 9;
				$this->iconPath = "missile3.png";
				foreach ($this->rangeArray as $key=>$rng) {
					$this->rangeArray[$key] += 10; 
					$this->distanceRangeArray[$key] += 10; 
				}     
				$this->loadingtime = 1; //fires every turn
				$this->rackExplosionDamage = 0; //how much damage will this weapon do in case of catastrophic explosion
				$this->rackExplosionThreshold = 21; //how high roll is needed for rack explosion   
				break;			
			case 'R': //Rapid fire: RoF 1/turn, increased explosion chance
				$this->displayName = "Class-R Missile Rack";
				$maxhealth = 6;
				$this->iconPath = "missile2.png";
				$this->loadingtime = 1; //fires every turn
				$this->rackExplosionThreshold = 19; //how high roll is needed for rack explosion 
				break;	
				
			default: //this includes class-S, which is pretty default :)
				$this->displayName = "Class-S Missile Rack";
				$maxhealth = 6;
				$this->iconPath = "missile1.png";  
				//range, rangeArray, distanceRange, loadingtime, fireControlArray, rackExplosionDamage, rackExplosionThreshold - standard
				break;
		}
		
		if ($base){ //mounted on base: launch range = distance range
			foreach ($this->rangeArray as $key=>$rng) {
		    		$this->rangeArray[$key] = $this->distanceRangeArray[$key]; 
			}         
		}
		
		$this->range = $this->rangeArray[1]; //base range = first range
		$this->distanceRange = $this->distanceRangeArray[1]; //base range = first range
		
		parent::__construct($armour, $maxhealth, 0, $startArc, $endArc);
        }
	
	
	public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 2: //Long-Range
				return 15; 
				break;
			case 3: //Heavy
				return 30; 
				break;
			case 5: //Piercing
				return 30; 
				break;
			case 6: //Anti-Fighter
				return 15; 
				break;
			default: //most missiles do the same damage
				return 20; 
				break;	
		}
	}
	public function setMinDamage(){ 
		switch($this->firingMode){
			case 2: //Long-Range
				$this->minDamage = 15; 
				break;
			case 3: //Heavy
				$this->minDamage = 30; 
				break;
			case 5: //Piercing
				$this->minDamage = 30; 
				break;
			case 6: //Anti-Fighter
				$this->minDamage = 15; 
				break;
			default: //most missiles do the same damage
				$this->minDamage = 20; 
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
	public function setMaxDamage(){
		switch($this->firingMode){
			case 2: //Long-Range
				$this->maxDamage = 15; 
				break;
			case 3: //Heavy
				$this->maxDamage = 30; 
				break;
			case 5: //Piercing
				$this->maxDamage = 30; 
				break;
			case 6: //Anti-Fighter
				$this->maxDamage = 15; 
				break;
			default: //most missiles do the same damage
				$this->maxDamage = 20; 
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
    
	public function setSystemDataWindow($turn){
		$this->data["Range"] = $this->range . '/' . $this->distanceRange;
		$this->data["Special"] = 'Multiple munitions available! No ammo limits. This weapon may explode when damaged.';
		$this->data["Special"] .= '<br>Available munitions (besides Basic):';
		$this->data["Special"] .= '<br> - Long Range: +10 range, dmg 15';
		$this->data["Special"] .= '<br> - Heavy: -10 range, -3 FC vs fighters, dmg 30';
		$this->data["Special"] .= '<br> - Flash: does damage in Flash mode';
		$this->data["Special"] .= '<br> - Piercing: Piercing mode, -3 FC vs fighters, dmg 30';
		$this->data["Special"] .= '<br> - Anti-Fighter: rng 15, +3 FC vs fighters, dmg 15';
		$this->data["Special"] .= '<br>Note that in a regular game You do not have unlimited access to all these munitions. Use them at Your (and Your opponents) discretion';
		$this->data["Special"] .= ' - although I tried to make the ship pricy enough that special missile spam is justified ;)';
		parent::setSystemDataWindow($turn);
        }
    
	/*moving to Weapon class - this is generally useful!
    public function isInDistanceRange($shooter, $target, $fireOrder){
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);    
        if(mathlib::getDistanceHex($movement->position,  $target) > $this->distanceRange)
        {
            $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
            return false;
        }
        return true;
    }
	*/
    
    
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
            $dmgToSelf = 1000; //rely on $noOverkill instead of counting exact amount left - 1000 should be more than enough...
            $this->doDamage($ship, $ship, $this, $dmgToSelf, $fireOrder, null, $gamedata, true, $this->location);
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
    
    public function addMissileCritOnSelf($shipid, $phpclass, $gamedata){
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
    }        
 
} //endof class MultiMissileLauncher  


/*weapon with unlimited ammo, able to use multiple munitions (modes of fire), simulating many missile launchers in game on demand (at constructor)
pricing proposal: standard launchers + 25/launcher, long range +10, rapid fire double that, class-B (unlimited range) 100
*/
class MultiBombRack extends Weapon{
	public $name = "multiBombRack";
        public $displayName = "Bomb Rack";
    public $useOEW = false;
    public $ballistic = true;
    public $animation = "trail";
    public $animationColor = array(50, 50, 50);
	/*
    public $trailColor = array(141, 240, 255);
    public $animationExplosionScale = 0.4;
    public $projectilespeed = 8;
    public $animationWidth = 4;
    public $trailLength = 100;
	*/
    public $range = 20;
    public $distanceRange = 60;
    public $firingMode = 1;
    public $rangeMod = 0;
    public $priority = 6;
    public $hits = array();
    public $loadingtime = 2;
    public $iconPath = "bombRack.png";    
    private $rackExplosionDamage = 30; //how much damage will this weapon do in case of catastrophic explosion
    private $rackExplosionThreshold = 20; //how high roll is needed for rack explosion    
    
    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ballistic"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	
	public $firingModes = array(1=>'Basic', 2=>'Flash'); //equals to available missiles; data is basic - if launcher is special, constructor will modify it
	public $damageTypeArray = array(1=>'Standard', 2=>'Flash'); //indicates that this weapon does damage in Pulse mode
    public $fireControlArray = array( 1=>array(4, 5, 6), 2=>array(4, 5, 6) ); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler
    public $rangeArray = array(1=>20, 2=>20); //distanceRange remains fixed, as it's improbable that anyone gets out of missile range and this would need more coding
    //typical (class-S) launcher is FC 3/3/3 and warhead +3 - which would mean 6/6/6!
    
    /*ATYPICAL constructor: doesn't take health and power usage, but takes desired launcher type - and does appropriate modifications*/
        function __construct($armour, $launcherType, $startArc, $endArc, $base=false)
        {
		switch($launcherType){ //modifications dependent on launcher type... actually a placeholder here, really
				/*
			case 'SO': //a reminder how to do it ;)
				$this->displayName = "Class-SO Missile Rack";
				$maxhealth = 6;
				foreach ($this->fireControlArray as $key=>$FCarray){
					$this->fireControlArray[$key][0] -= 1; //fighter
					$this->fireControlArray[$key][1] -= 1; //medium
					$this->fireControlArray[$key][2] -= 1; //Cap
				}
				$this->iconPath = "missile1.png";  
				$this->rackExplosionDamage = 40;
				break;
				*/
				
			default: //this includes class-S, which is pretty default :)
				$maxhealth = 6;
				break;
		}
		
		if ($base){ //mounted on base - +40 launch range
			foreach ($this->rangeArray as $key=>$rng) {
		    		$this->rangeArray[$key] += 40; 
			}         
		}
		
		$this->range = $this->rangeArray[1]; //base range = first range
		
		parent::__construct($armour, $maxhealth, 0, $startArc, $endArc);
        }
	
	
        public function getDamage($fireOrder){ 
		switch($this->firingMode){
			case 1: //Basic missile
				return 20; 
				break;
			case 2: //Flash missile
				return 20; 
				break;
			default: //most missiles do the same damage
				return 20; 
				break;	
		}
	}
        public function setMinDamage(){ 
		switch($this->firingMode){
			case 1: //Basic
				$this->minDamage = 20; 
				break;
			case 2: //Flash
				$this->minDamage = 20; 
				break;
			default: //most missiles do the same damage
				$this->minDamage = 20; 
				break;	
		}
		$this->minDamageArray[$this->firingMode] = $this->minDamage;
	}
        public function setMaxDamage(){
		switch($this->firingMode){
			case 1: //Basic
				$this->maxDamage = 20; 
				break;
			case 2: //Flash
				$this->maxDamage = 20; 
				break;
			default: //most missiles do the same damage
				$this->maxDamage = 20; 
				break;	
		}
		$this->maxDamageArray[$this->firingMode] = $this->maxDamage;
	}
    
	public function setSystemDataWindow($turn){
		$this->data["Range"] = $this->range . '/' . $this->distanceRange;
		$this->data["Special"] = 'Multiple munitions available! No ammo limits. This weapon may explode when damaged.';
		$this->data["Special"] .= '<br>Available munitions (besides Basic):';
		$this->data["Special"] .= '<br> - Flash: does damage in Flash mode';
		$this->data["Special"] .= '<br>Note that in a regular game You do not have unlimited access to all these munitions. Use them at Your (and Your opponents) discretion';
		$this->data["Special"] .= ' - although I tried to make the ship pricy enough that special missile spam is justified ;)';
		parent::setSystemDataWindow($turn);
        }
    
    public function isInDistanceRange($shooter, $target, $fireOrder){
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);    
        if(mathlib::getDistanceHex($movement->position,  $target) > $this->distanceRange)
        {
            $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
            return false;
        }
        return true;
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
            $dmgToSelf = 1000; //rely on $noOverkill instead of counting exact amount left - 1000 should be more than enough...
            $this->doDamage($ship, $ship, $this, $dmgToSelf, $fireOrder, null, $gamedata, true, $this->location);
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
    
    public function addMissileCritOnSelf($shipid, $phpclass, $gamedata){
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
    }   
 
} //endof class MultiBombRack




/*Class-S Missile Rack - weapon that looks at central magazine to determine available firing modes (and number of actual rounds available)
	holds 20 missiles
*/
class AmmoMissileRackS extends Weapon{
	public $name = "ammoMissileRackS";
        public $displayName = "Class-S Missile Rack";
    public $iconPath = "missile1.png";    
		
	public $checkAmmoMagazine = true;
	
    public $useOEW = false; //missiles are NOT using OEW in any form; they do have built-in seeking head instead (in FieryVoid merged into Fire Control for simplicity)
    public $ballistic = true;
    public $animation = "trail";
    public $animationColor = array(50, 50, 50);
    public $range = 20;
    public $distanceRange = 60;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 2;
	public $normaload = 2;    
	private $dpArray = array(); //array of damage penalties for all modes! - filled automatically

	protected $availableAmmoAlreadySet = false; //set to true if calling constructor from derived weapon that sets different ammo options

    protected $rackExplosionDamage = 75; //how much damage will this weapon do in case of catastrophic explosion
	//officially it's a quarter of total power of warheads in magazine; but in FV this number is fixed (as missiles in magazine are not tracked)
	// estimated warheads in magazine: 400 (20 missiles, dmg 20 each - assuming Basic missiles)
	// a quarter of the above: 100
	// reduce somewhat by expected expendientures (say, *0.75): 75
	// also, official rules expect Raking mode, not Flash - but it's somewhat of a washout (Flash offers higher chance of destroying section, but lower of damage penetrating deeper)
    protected $rackExplosionThreshold = 20; //how high roll is needed for rack explosion (d20)
    
    public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $weaponClass = "Ballistic"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! 
	
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(3,3,3);
	protected $basicRange=20;
	protected $basicDistanceRange = 60;
	
	public $firingModes = array(); //equals to available missiles
	public $damageTypeArray = array(); //indicates that this weapon does damage in Pulse mode
    public $fireControlArray = array(); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler
    //typical (class-S) launcher is FC 3/3/3 and warhead +3 - which would mean 6/6/6!
    public $rangeArray = array(); 
	public $distanceRangeArray = array(); 

	protected $ammoClassesArray = array();//FILLED IN CONSTRUCTOR! classes representing POTENTIALLY available ammo - so firing modes are always shown in the same order
	
	private $ammoMagazine; //reference to ammo magazine
	private $ammoClassesUsed = array();
	
	private $base = false; //Fixing stabilized missile launch ranges
	
//For Stealth missile
	public $hidetarget = false;
	public $hidetargetArray = array();
//Adding Pulse variables for Starburst missiles	
	public $maxpulses = 0;    
	public $rof = 0;
	public $useDie = 0; //die used for base number of hits
	public $fixedBonusPulses = 0;//for weapons doing dX+Y pulse	
	public $maxpulsesArray = array();
	public $rofArray = array();
	public $useDieArray = array();
	public $fixedBonusPulsesArray = array();	
//Extra variable Multiwarhead	
    public $calledShotMod = -8; //Normal called shot modifier is -8
	public $calledShotModArray = array();    		  
//Extra variables for KK Missile
	public $specialRangeCalculation = false; //To allow front-end to work for KK missiles.
	public $specialRangeCalculationArray = array(); 	
	public $rangePenalty = 0;	
	public $rangePenaltyArray = array(); 
	public $noLockPenalty = false;
	public $noLockPenaltyArray = array();		
//Extra variables for HARM Missile	
	public $specialHitChanceCalculation = false;
	public $specialHitChanceCalculationArray = array();			
//Extra for Interceptor missiles
	public $intercept = 0;	//Adding Intercept variables for Interceptor missiles	
	public $interceptArray = array(); 
	public $ballisticIntercept = false;	//Can only intercept other missiles.	    	
	public $ballisticInterceptArray = array();	//Might not actually be needed currently.
    public $canModesIntercept = true;	//Some missile launchers can have Interceptor missiles.
//Extra variables for Jammer missile	
    public $hextarget = false;
   	public $hextargetArray = array();
    public $animationArray = array();
    public $animationExplosionScale = 0; //0 means it will be set automatically by standard constructor, based on average damage yield
    public $animationExplosionScaleArray = array();
	public $uninterceptable = false;
	public $uninterceptableArray = array();	 
	public $doNotIntercept = false;
	public $doNotInterceptArray = array();	             	
	//Variables for Ballistic Mine Launcher	
	public $mineRange = 0;
	public $mineRangeArray = array();	
		
    /*ATYPICAL constructor: takes ammo magazine class and (optionally) information about being fitted to stable platform*/
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		//VERY IMPORTANT: fill $ammoClassesArray (cannot be done as constants!
		//classes representing POTENTIALLY available ammo - so firing modes are always shown in the same order
		//remember that appropriate enhancements need to be enabled on ship itself, too!
		
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray[] =  new AmmoMissileB();
			$this->ammoClassesArray[] =  new AmmoMissileL();
			$this->ammoClassesArray[] =  new AmmoMissileH();
			$this->ammoClassesArray[] =  new AmmoMissileF();
			$this->ammoClassesArray[] =  new AmmoMissileA();
			$this->ammoClassesArray[] =  new AmmoMissileP();
			$this->ammoClassesArray[] =  new AmmoMissileD(); //...though only Alacans, Narn and Sorithians use those, as simple Basic missiles are far superior
			$this->ammoClassesArray[] =  new AmmoMissileC();				
			$this->ammoClassesArray[] =  new AmmoMissileS();
			$this->ammoClassesArray[] =  new AmmoMissileHM(); //Homing Missile - only a POTENTIAL mode; becomes real only when the magazine actually holds Homing rounds (AMMO_HM enhancement)
			$this->ammoClassesArray[] =  new AmmoMissileJ();
			$this->ammoClassesArray[] =  new AmmoMissileK();
			$this->ammoClassesArray[] =  new AmmoMissileM();
			$this->ammoClassesArray[] =  new AmmoMissileKK();
			$this->ammoClassesArray[] =  new AmmoMissileX();
			$this->ammoClassesArray[] =  new AmmoMissileI();
			$this->ammoClassesArray[] =  new AmmoMissileZ();											
			$this->availableAmmoAlreadySet = true;
		}
	
		$this->ammoMagazine = $magazine;
//		$this->recompileFiringModes();
		if($base){
			$this->basicRange = $this->basicDistanceRange;
			$this->base = $base;
		}
		if ( $maxhealth == 0 ) $maxhealth = 6;
            	if ( $powerReq == 0 ) $powerReq = 0;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc); //class-S launcher: structure 6, power usage 0
		$magazine->subscribe($this); //subscribe to any further changes in ammo availability
		$this->recompileFiringModes();
	}
    
	
	// GTS
    protected function getAmmoMagazine(){
        return $this->ammoMagazine;
    }	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		
		$this->data["Special"] = 'Available firing modes depend on ammo bought as unit enhancements. Ammunition available is tracked by central Ammunition Magazine system.';
		if ($this->rackExplosionThreshold < 21) { //can explode - inform player!
			$chance = (21 - $this->rackExplosionThreshold) * 5; //percentage chance of explosion
			$this->data["Special"] .= '<br>Can explode if damaged or destroyed, dealing ' . $this->rackExplosionDamage . ' damage in Flash mode (' . $chance . '% chance).';
		}	
	}
	
	/*prepare firing modes - in order as indicated by $ammoCLassesArray (so every time the order is the same and classes aren't mixed), but use only classes actually held by magazine (no matter the count - 0 rounds is fine)
		if magazine holds no ammo - still list first entry on the list (weapon has to have SOME data!)
	*/
 	public function recompileFiringModes(){
		//clear existing arrays
		$this->firingModes = array(); //equals to available missiles
		$this->damageTypeArray = array(); //indicates that this weapon does damage in Pulse mode
		$this->weaponClassArray = array();
    		$this->fireControlArray = array(); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler
    		$this->rangeArray = array(); 
		$this->distanceRangeArray = array();
		$this->priorityArray = array();
		$this->priorityAFArray = array();
		$this->dpArray = array();
		$this->damageTypeArray = array();
		$this->weaponClassArray = array();
		$this->noOverkillArray = array();
		$this->minDamageArray = array();
		$this->maxDamageArray = array();
		$this->ammoClassesUsed = array();
		$this->hidetargetArray = array();
		$this->maxpulsesArray = array();//Adding Pulse functions for Starburst missiles	
		$this->rofArray = array();
		$this->useDieArray = array();
		$this->fixedBonusPulsesArray = array();
		$this->calledShotModArray = array();	//Adding calledShotMod variable for Multiwarhead Missile.
		$this->specialRangeCalculationArray = array(); //Adding variables for KK Missile
		$this->rangePenaltyArray = array();
		$this->noLockPenaltyArray = array();		 
		$this->specialHitChanceCalculationArray = array();						
		$this->interceptArray = array();//Adding Intercept variables for Interceptor missiles	
		$this->ballisticInterceptArray = array();	    		
		$this->hextargetArray = array();//For Jammer missile
		$this->animationArray = array();											
		$this->animationExplosionScaleArray = array();
		$this->uninterceptableArray	= array();
		$this->doNotInterceptArray	= array();
		$this->mineRangeArray = array();								
		//add data for all modes to arrays
		$currMode = 0;
		foreach ($this->ammoClassesArray as $currAmmo){
			$isPresent = $this->ammoMagazine->getAmmoPresence($currAmmo->modeName);//does such ammo exist in magazine?
			if($isPresent){
				$currMode++;
				//fill all arrays for indicated mode
				$this->ammoClassesUsed[$currMode] = $currAmmo;
				$this->firingModes[$currMode] = $currAmmo->modeName;
				$this->damageTypeArray[$currMode] = $currAmmo->damageType; 
				$this->weaponClassArray[$currMode] = $currAmmo->weaponClass; 	
				
				$fc0 = 0;
				if(($this->basicFC[0] === null) || ($currAmmo->fireControlMod[0]===null)) {
					$fc0 = null;
				}else{
					$fc0 = $this->basicFC[0] + $currAmmo->fireControlMod[0];
				}
				$fc1 = $this->basicFC[1];
				if(($this->basicFC[1] === null) || ($currAmmo->fireControlMod[1]===null)) {
					$fc1 = null;
				}else{
					$fc1 = $this->basicFC[1] + $currAmmo->fireControlMod[1];
				}
				$fc2 = $this->basicFC[2];
				if(($this->basicFC[2] === null) || ($currAmmo->fireControlMod[2]===null)) {
					$fc2 = null;
				}else{
					$fc2 = $this->basicFC[2] + $currAmmo->fireControlMod[2];
				}
				$this->fireControlArray[$currMode] = array($fc0, $fc1, $fc2); // fighters, <mediums, <capitals ; INCLUDES MISSILE WARHEAD (and FC if present)! as effectively it is the same and simpler
				
				if ($this->base){
//					$this->rangeArray[$currMode] = $this->basicRange + $currAmmo->rangeMod; 
					$this->rangeArray[$currMode] = $this->basicDistanceRange + $currAmmo->distanceRangeMod; 
					$this->distanceRangeArray[$currMode] = $this->basicDistanceRange + $currAmmo->distanceRangeMod; 
				}else{
					$this->rangeArray[$currMode] = $this->basicRange + $currAmmo->rangeMod; 
					$this->distanceRangeArray[$currMode] = $this->basicDistanceRange + $currAmmo->distanceRangeMod; 
				}
				$this->priorityArray[$currMode] = $currAmmo->priority;
				$this->priorityAFArray[$currMode] = $currAmmo->priorityAF;
				$this->noOverkillArray[$currMode] = $currAmmo->noOverkill;
				$this->minDamageArray[$currMode] = $currAmmo->minDamage;
				$this->maxDamageArray[$currMode] = $currAmmo->maxDamage;
				$this->hidetargetArray[$currMode] = $currAmmo->hidetarget;//For Stealth missiles							
				$this->maxpulsesArray[$currMode] = $currAmmo->maxpulses;//Adding Pulse functions for Starburst missiles	
				$this->rofArray[$currMode] = $currAmmo->rof;
				$this->useDieArray[$currMode] = $currAmmo->useDie;
				$this->fixedBonusPulsesArray[$currMode] = $currAmmo->fixedBonusPulses;
			    $this->calledShotModArray[$currMode] = $currAmmo->calledShotMod;	//Adding calledShotMod variable for Multiwarhead Missile.
				$this->specialRangeCalculationArray[$currMode] = $currAmmo->specialRangeCalculation; //Adding variables for KK Missile
				$this->rangePenaltyArray[$currMode] = $currAmmo->rangePenalty;
				$this->noLockPenaltyArray[$currMode] = $currAmmo->noLockPenalty;				
				$this->specialHitChanceCalculationArray[$currMode] = $currAmmo->specialHitChanceCalculation;							    
				$this->interceptArray[$currMode] = $currAmmo->intercept;//Adding Intercept variables for Interceptor missiles	
				$this->ballisticInterceptArray[$currMode] = $currAmmo->ballisticIntercept;
				$this->hextargetArray[$currMode] = $currAmmo->hextarget;//For Jammer missile
				$this->animationArray[$currMode] = $currAmmo->animation;
				$this->animationExplosionScaleArray[$currMode] = $currAmmo->animationExplosionScale;				
				$this->uninterceptableArray[$currMode] = $currAmmo->uninterceptable;				
				$this->doNotInterceptArray[$currMode] = $currAmmo->doNotIntercept;
				$this->mineRangeArray[$currMode] = $currAmmo->mineRange;														
			}
		}
			
		//if there is no ammo available - add entry for first ammo on the list... or don't, just fill firingModes (this one is necessary) - assume basic weapons data resemble something like basic desired mode
		if ($currMode < 1){
			$this->firingModes[1] = 'NoAmmunitionAvailable';
		}
			
		//change mode to 1, to call all appropriate routines connected with mode change
		$this->changeFiringMode(1);		
		//remember about effecting criticals, too!
	//	$this->effectCriticals(); //This was applying criticals twice! DK			
	}//endof function recompileFiringModes
	
	
	
	
 	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->firingModes = $this->firingModes; 
		if (!empty($this->damageTypeArray)) $strippedSystem->damageTypeArray = $this->damageTypeArray; 
		if (!empty($this->weaponClassArray)) $strippedSystem->weaponClassArray = $this->weaponClassArray;
		$strippedSystem->basicFC = $this->basicFC; 		 
		if (!empty($this->fireControlArray)) $strippedSystem->fireControlArray = $this->fireControlArray; 
		if (!empty($this->rangeArray)) $strippedSystem->rangeArray = $this->rangeArray; 
		if (!empty($this->distanceRangeArray)) $strippedSystem->distanceRangeArray = $this->distanceRangeArray; 
		if (!empty($this->priorityArray)) $strippedSystem->priorityArray = $this->priorityArray; 
		if (!empty($this->priorityAFArray)) $strippedSystem->priorityAFArray = $this->priorityAFArray; 
		if (!empty($this->dpArray)) $strippedSystem->dpArray = $this->dpArray; 
		if (!empty($this->noOverkillArray))$strippedSystem->noOverkillArray = $this->noOverkillArray; 
		if (!empty($this->minDamageArray))$strippedSystem->minDamageArray = $this->minDamageArray; 
		if (!empty($this->maxDamageArray))$strippedSystem->maxDamageArray = $this->maxDamageArray; 
		if (!empty($this->hidetargetArray))$strippedSystem->hidetargetArray = $this->hidetargetArray;	//For Stealth Missiles			
		if (!empty($this->maxpulsesArray))$strippedSystem->maxpulsesArray = $this->maxpulsesArray;//Adding Pulse functions for Starburst missiles	
		if (!empty($this->rofArray))$strippedSystem->rofArray = $this->rofArray;
		if (!empty($this->useDieArray))$strippedSystem->useDieArray = $this->useDieArray;
		if (!empty($this->fixedBonusPulsesArray))$strippedSystem->fixedBonusPulsesArray = $this->fixedBonusPulsesArray;	
		if (!empty($this->calledShotModArray))$strippedSystem->calledShotModArray = $this->calledShotModArray;	//Adding calledShotMod variable for Multiwarhead Missile.
		if (!empty($this->specialRangeCalculationArray))$strippedSystem->specialRangeCalculationArray = $this->specialRangeCalculationArray; //Adding for KK Missile
		if (!empty($this->rangePenaltyArray))$strippedSystem->rangePenaltyArray = $this->rangePenaltyArray; //Adding Range and No Lock variables for KK Missiles
		if (!empty($this->noLockPenaltyArray))$strippedSystem->noLockPenaltyArray = $this->noLockPenaltyArray;		
		if (!empty($this->specialHitChanceCalculationArray))$strippedSystem->specialHitChanceCalculationArray = $this->specialHitChanceCalculationArray;//Adding marker for HARM missiles special hitchance calculation		
		if (!empty($this->interceptArray))$strippedSystem->interceptArray = $this->interceptArray;//Adding Intercept nad ballisticIntercept variables for Interceptor missiles	
		if (!empty($this->ballisticInterceptArray))$strippedSystem->ballisticInterceptArray = $this->ballisticInterceptArray;
		if (!empty($this->hextargetArray))$strippedSystem->hextargetArray = $this->hextargetArray;//For Jammer missile
		if (!empty($this->animationArray))$strippedSystem->animationArray = $this->animationArray;
		if (!empty($this->animationExplosionScaleArray))$strippedSystem->animationExplosionScaleArray = $this->animationExplosionScaleArray;
		if (!empty($this->uninterceptableArray))$strippedSystem->uninterceptableArray = $this->uninterceptableArray;		
		if (!empty($this->doNotInterceptArray))$strippedSystem->doNotInterceptArray = $this->doNotInterceptArray;
		if (!empty($this->mineRangeArray))$strippedSystem->mineRangeArray = $this->mineRangeArray;				
		return $strippedSystem;
	} 

	//actually use getDamage() method of ammo!
    public function getDamage($fireOrder)
    {
		$currAmmo = null;
        //find appropriate ammo
		if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
			$currAmmo = $this->ammoClassesUsed[$this->firingMode];
		}
	    
		//execute getDamage()
		if($currAmmo){
			return $currAmmo->getDamage($fireOrder);
		}else{
			return 0;	
		}
    }

    public function setMinDamage(){
        if(isset($this->ammoClassesUsed[$this->firingMode])){
             $this->minDamage = $this->ammoClassesUsed[$this->firingMode]->minDamage;
        }
    }
    public function setMaxDamage(){
         if(isset($this->ammoClassesUsed[$this->firingMode])){
             $this->maxDamage = $this->ammoClassesUsed[$this->firingMode]->maxDamage;
        }       
    }
	

	/*some missiles have special effects on impact*/
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder)
    {
		$currAmmo = null;
        //find appropriate ammo
		if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
			$currAmmo = $this->ammoClassesUsed[$this->firingMode];
		}
		if ($currAmmo) $currAmmo->onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
		
        parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);
    }//endof function onDamagedSystem

	
	public function criticalPhaseEffects($ship, $gamedata){ //add testing for ammo explosion!
	
		parent::criticalPhaseEffects($ship, $gamedata);//Call parent to apply effects like Limpet Bore.		
	
		if(!$this->isDamagedOnTurn($gamedata->turn)) return; //if there is no damage this turn, then no testing for explosion
        $explodes = false;
        $roll = Dice::d(20);
        if ($roll >= $this->rackExplosionThreshold) $explodes = true;
        		
        if($explodes){
            $this->ammoExplosion($ship, $gamedata, $this->rackExplosionDamage, $roll);  
        }
    } //endof function testCritical
    public function ammoExplosion($ship, $gamedata, $damage, $roll){
        //first, destroy self if not yet done...
        if (!$this->isDestroyed()){
            $this->noOverkill = true;
            $fireOrder =  new FireOrder(-1, "normal", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 1, 100, 1, 1, 1, 0, null, null, 'MagazineExplosion');//needed, rolled, shots, shotshit, intercepted
			$fireOrder->addToDB = true;
//			$fireOrder->pubnotes = "Missile magazine explosion (roll $roll)!";
			$this->fireOrders[] = $fireOrder;							
            $dmgToSelf = 1000; //rely on $noOverkill instead of counting exact amount left - 1000 should be more than enough...
            $this->doDamage($ship, $ship, $this, $dmgToSelf, $fireOrder, null, $gamedata, true, $this->location);
        }        
        //then apply damage potential as a hit... should be Raking by the rules, let's do it as Flash instead (not quite the same, but easier to do)
        if($damage>0){
            $this->noOverkill = false;
            $this->damageType = 'Flash'; //should be Raking by the rules, but Flash is much easier to do - and very fitting for explosion!
            $fireOrder =  new FireOrder(-1, "normal", $ship->id,  $ship->id, $this->id, -1, 
                    $gamedata->turn, 1, 100, 1, 1, 1, 0, null, null, 'MagazineExplosion');
			$fireOrder->addToDB = true;
			$fireOrder->pubnotes = "<br>A missile magazine explodes! (Rolled $roll)";
			$this->fireOrders[] = $fireOrder;					
            $this->doDamage($ship, $ship, $this, $damage, $fireOrder, null, $gamedata, false, $this->location); //show $this as target system - this will ensure its destruction, and Flash mode will take care of the rest
        }
    }
    //Adding Pulse functions for Starburst missiles
    protected function getPulses($turn)
        {
           $currAmmo = null;
        //find appropriate ammo
		if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
			$currAmmo = $this->ammoClassesUsed[$this->firingMode];
		} 
		//execute getPulses()
		if($currAmmo){
			return $currAmmo->getPulses($turn);
		}else{
			return 0;	
			}
    	 }
    	 
    protected function getExtraPulses($needed, $rolled)
   		 {
		$currAmmo = null;
        //find appropriate ammo
		if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
			$currAmmo = $this->ammoClassesUsed[$this->firingMode];
		}
		//execute getExtraPulses()
		if($currAmmo){
			return $currAmmo->getExtraPulses($needed, $rolled);
		}else{
			return 0;	
			}
	    }	    
	public function rollPulses($turn, $needed, $rolled)
	    {
			$currAmmo = null;
	        //find appropriate ammo
			if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
				$currAmmo = $this->ammoClassesUsed[$this->firingMode];
			}
			//execute rollPulses()
			if($currAmmo){
				return $currAmmo->rollPulses($turn, $needed, $rolled);
			}else{
				return 0;	
			}
	    }
	    
    public function beforeFiringOrderResolution($gamedata) //For Multiwarhead missile
    {
	  /*HOMING MISSILE: persist this turn's re-attack orders BEFORE anything resolves, and before
	    the early return further down. See persistHomingOrders for why it has to be here.*/
	  $this->persistHomingOrders($gamedata);

      $firingOrders = $this->getFireOrders($gamedata->turn);
    	
      $originalFireOrder = null;
              foreach ($firingOrders as $fireOrder) { 
              	   if ($fireOrder->type == 'ballistic') { 
                    $originalFireOrder = $fireOrder;
                    break; //no need to search further
                    }
				}    			
				
        if($originalFireOrder==null) return; //no appropriate fire order, end of work	
    	
    	
		$currAmmo = null;
        //find appropriate ammo
		if (array_key_exists($originalFireOrder->firingMode,$this->ammoClassesUsed)){
			$currAmmo = $this->ammoClassesUsed[$originalFireOrder->firingMode];
		}
		if ($currAmmo) $currAmmo->beforeFiringOrderResolution($gamedata, $this, $originalFireOrder);

        parent::beforeFiringOrderResolution($gamedata);
    
	}	//endof function beforeFiringOrderResolution	

	// Add function to check firing modes for an intercept rating, in case Interceptor Missiles are equipped.
    public function switchModeForIntercept()
    {
		    $maxIntercept = 0; // Initialize as null.
		    $bestFiringMode = 1; // Initialize with default mode

		    foreach ($this->interceptArray as $firingMode => $interceptValue) { //Search through Firing Modes to see if any have Intercept Ratings.
		        if ($interceptValue > $maxIntercept) { //If so, find highest intercept (to future proof) and pass Intercept Rating and Firing Mode it's associate with.	        
		            $maxIntercept = $interceptValue;
		            $bestFiringMode = $firingMode;
		        }    
		    $this->changeFiringMode($bestFiringMode); // Switch to the appropriate Firing Mode for best Intercept.
		    $this->intercept = $maxIntercept;
  		  }
	}

	/* ===== AUTO-INTERCEPTOR-MISSILES (3 of 3) - THE AMMO CHECK ================================
	   Yes: ammo IS verified before an intercept is allowed, per shot, in sequence.

	   Firing::isLegalIntercept calls this for every candidate intercept (firing.php, the
	   canInterceptAtAll line), and it answers from AmmoMagazine::canDrawInterceptor, which tests
	   remainingAmmo AND (ammoCountArray['Interceptor'] - interceptorUsed) >= 1. interceptorUsed is
	   bumped by doDrawAmmo from fireDefensively just below, so the Nth intercept this turn sees the
	   N-1 rounds already spent and the magazine cannot be overdrawn.

	   What this does NOT do is ask whether the player WANTED to spend the round - that decision is
	   made upstream, at site 1 of 3 in Firing::getUnassignedInterceptors. Ammo availability is the
	   only thing standing between a loaded launcher and an automatic intercept.
	   ====================================================================================== */
	//can intercept only if Magazine holds enough ammo of correct type...
	public function canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)
	{
		$ammoIsAvailable = false;
    	$modeName = $this->firingModes[$this->firingMode];	
		
		$magazine = $this->getAmmoMagazine();
		if($magazine){ //else something is wrong - weapon is put on a ship without Ammo Magazine!
			if($magazine->canDrawInterceptor($modeName)) $ammoIsAvailable = true;
		}
		return $ammoIsAvailable;
	}

	public function fireDefensively($gamedata, $interceptedWeapon)//Note that a missile has been used when launcher is fired defensively.
	{
		$magazine =  $this->getAmmoMagazine();
    	$modeName = $this->firingModes[$this->firingMode];
    			
		if($magazine){ //else something is wrong - weapon is put on a ship without Ammo Magazine!
			$magazine->doDrawAmmo($gamedata,$modeName);
		}
		parent::fireDefensively($gamedata, $interceptedWeapon);
	}	

    public function getCalledShotMod()  	//For Multiwarhead missiles
        {
            $currAmmo = null;
            //find appropriate ammo
            if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
                $currAmmo = $this->ammoClassesUsed[$this->firingMode];
            }
            //execute getCalledShotMod()
            if($currAmmo){
                return $currAmmo->getCalledShotMod();
            }else{
                return $this->calledShotMod;
            }
        }//endof function getCalledShotMod


     public function fire($gamedata, $fireOrder)	//For Multiwarhead & Jammer missiles
    {
		$currAmmo = null;
        //find appropriate ammo
		if (array_key_exists($this->firingMode,$this->ammoClassesUsed)){
			$currAmmo = $this->ammoClassesUsed[$this->firingMode];
		}
		if ($currAmmo) $currAmmo->fire($gamedata, $fireOrder);

	    // Check if hextarget as these modes cannot use normal fire() function.
        if ($fireOrder->targetid !== -1) {
            // Call the parent method for weapon hit calculation if not hex targeted.		
        	parent::fire($gamedata, $fireOrder);
		}
		
	}//endof function fire	


	public function calculateRangePenalty($distance)
	{
	    $currAmmo = null;
	    
	    // find appropriate ammo
	    if (array_key_exists($this->firingMode, $this->ammoClassesUsed)) {
	        $currAmmo = $this->ammoClassesUsed[$this->firingMode];
	    }
	    
	    // Check if $currAmmo is not null before calling the method
	    if($currAmmo){
	        return $currAmmo->calculateRangePenalty($distance);
	    }else{
	        return 0;
	    }
	    //parent::calculateRangePenalty($distance);        
	}//endof function calculateRangePenalty


    public function calculateHitBase($gamedata, $fireOrder)
    {
         // Check if hextarget as these modes cannot use normal calculateHitBase() function.
        if ($fireOrder->targetid !== -1) {
            // Call the parent method for weapon hit calculation if not hex targeted.
            parent::calculateHitBase($gamedata, $fireOrder);
        }  
           	
        $currAmmo = null;
        
        // Find appropriate ammo
        if (array_key_exists($this->firingMode, $this->ammoClassesUsed)){
            $currAmmo = $this->ammoClassesUsed[$this->firingMode];
        }
        
        if ($currAmmo) $currAmmo->calculateHitBase($gamedata, $fireOrder);

	}//endof function calculateHitBase


	/* ==========================================================================================
	   HOMING MISSILE (Class HM) - HOMING_MISSILE_PLAN.md

	   Everything below is delegation. The behaviour itself lives on AmmoMissileHM (baseSystems.php);
	   these are the six places the engine has to be told about it, and between them they keep
	   Weapon::fire, Firing:: and every phase class completely unaware that homing exists.

	   THE LIFECYCLE, in one place:
	     turn N   phase 4  generateIndividualNotes() reads the RESOLVED order and, on a clean miss
	                       with fuel left, INSERTS next turn's re-attack order (persistNextPassOrder)
	                       and writes a 'HomingMissile' note carrying the missile's identity, its
	                       cumulative travel, the hex the target was standing on and that row's id.
	     turn N+1 any load onIndividualNotesLoaded() finds that row already loaded and ADOPTS it by
	                       id (or, for a note that predates the row, rebuilds it in memory).
	     turn N+1 phase 4  beforeFiringOrderResolution() is now only the fallback insert, then the
	                       order resolves like any other missile and the cycle repeats.
	   ========================================================================================== */

	/*The Homing ammo class this rack could fire, or null. Read off $ammoClassesArray (what the rack
	  COULD hold) and NOT $ammoClassesUsed (what the magazine actually holds), because
	  onIndividualNotesLoaded runs BEFORE BaseShip::onConstructed and therefore before
	  Enhancements::setEnhancements has put a single Homing round in the magazine - at that moment
	  $ammoClassesUsed and $firingModes know nothing about Homing at all.
	  Racks that reset $ammoClassesArray to their own short list (A, D) simply answer null here.*/
	protected function getHomingAmmo()
	{
		foreach ($this->ammoClassesArray as $currAmmo){
			if (!empty($currAmmo->isHoming)) return $currAmmo;
		}
		return null;
	}

	/* ⚠️⚠️ THE RUNTIME CARRIER FOR A MISSILE'S IDENTITY AND FUEL, and it has to be this rather
	   than anything on the fire order.

	   Weapon::calculateHitBase ASSIGNS $fireOrder->notes (weapon.php ~1927), it does not append, so
	   a tag written there is destroyed the instant the shot's hit chance is computed. The first cut
	   of this feature did exactly that: every pass came back as a brand-new pass-1 missile with 0
	   hexes flown, so it could never run out of fuel and would have flown for ever (game 4328).

	   Scope is ONE REQUEST, which is all that is needed - onIndividualNotesLoaded, calculateHitBase
	   and generateIndividualNotes all run inside the same FireGamePhase::advance, on the same
	   objects. Between turns the IndividualNote is the carrier.

	   The order object is held alongside its state deliberately: it pins the object for the life of
	   the request, so spl_object_id cannot be recycled underneath the key, and lets a lookup prove
	   identity rather than trust the id. */
	protected $homingStates = array();

	protected function setHomingState($fireOrder, $state)
	{
		$this->homingStates[spl_object_id($fireOrder)] = array('order' => $fireOrder, 'state' => $state);
	}

	protected function getHomingState($fireOrder)
	{
		if ($fireOrder === null) return null;
		$key = spl_object_id($fireOrder);
		if (!isset($this->homingStates[$key])) return null;
		if ($this->homingStates[$key]['order'] !== $fireOrder) return null; //recycled id - not ours
		return $this->homingStates[$key]['state'];
	}

	/*Does this persisted order and this note describe the same missile? Everything the note holds
	  that ALSO survives on the order: the target it is chasing, the hex it is coming from, and the
	  mode it flies in. Used only to stop a note rebuilding an order that already exists.
	  ⚠️ Every field off a DB-loaded order is a STRING - cast both sides.*/
	protected function homingOrderMatchesState($fireOrder, $state)
	{
		return ((int)$fireOrder->targetid   === (int)$state['targetid']
			&&  (int)$fireOrder->x          === (int)$state['q']
			&&  (int)$fireOrder->y          === (int)$state['r']
			&&  (int)$fireOrder->firingMode === (int)$state['mode']);
	}

	/*The per-ORDER launch hex of a homing re-attack: where its target was standing when the
	  previous pass missed ("treating its target's previous location as its new launch hex").
	  null for every other order, which is what makes each override below fall through to parent.

	  ⚠️ Takes no $gamedata deliberately - isInDistanceRange is not given any.*/
	protected function getHomingLaunchHex($fireOrder)
	{
		if ($fireOrder === null) return null;
		if ($fireOrder->damageclass !== AmmoMissileHM::REATTACK_CLASS) return null;
		if (!is_numeric($fireOrder->x) || !is_numeric($fireOrder->y)) return null;
		return new OffsetCoordinate((int)$fireOrder->x, (int)$fireOrder->y);
	}

	/*1 of 6. The launch hex. calculateHitBase, Weapon::fire, getIncomingBearing and getIncomingPos
	  ALL route through getFiringHex, so bearing, hit section and defence profile come out right
	  for a re-attack with no further change anywhere.

	  ⚠️ NOT done by setting $hasSpecialLaunchHexCalculation = true. That is a CLASS-WIDE flag and
	  it also switches off the launcher's line-of-sight fire-control penalty in calculateHitBase -
	  for ordinary launches from this same rack as well. LoS always matters (user ruling).*/
	public function getFiringHex($gamedata, $fireOrder)
	{
		$homingHex = $this->getHomingLaunchHex($fireOrder);
		if ($homingHex !== null) return $homingHex;
		return parent::getFiringHex($gamedata, $fireOrder);
	}

	/*2 of 6. Fuel. The stock test asks "did the target move further than distanceRange from the
	  launch hex"; a homing missile has to add everything it has already flown to that.*/
	public function isInDistanceRange($shooter, $target, $fireOrder)
	{
		$launchPos = $this->getHomingLaunchHex($fireOrder);
		if ($launchPos === null) return parent::isInDistanceRange($shooter, $target, $fireOrder);

		$homing = $this->getHomingAmmo();
		if ($homing === null) return parent::isInDistanceRange($shooter, $target, $fireOrder);

		/*⭐ THE BUDGET IS THE ONE CAPTURED AT LAUNCH, not the launcher's current figure. A Class-F
		  rack rewrites its own range arrays as its loading state changes (recalculateFireControl),
		  and a missile already in the air must not have its fuel tank resized because the rack
		  that fired it has since been reloaded differently (user report, game 4328).
		  Falls back to the live figure only for a missile that predates the captured field.*/
		$state = $this->getHomingState($fireOrder);
		$budget = ($state !== null && (int)$state['budget'] > 0)
			? (int)$state['budget']
			: $homing->getFuelBudget($this, (int)$fireOrder->firingMode);
		if ($budget <= 0) return true; //0 means unlimited range

		$travelled = ($state !== null) ? (int)$state['travelled'] : 0;

		if (($travelled + mathlib::getDistanceHex($launchPos, $target)) > $budget){
			$fireOrder->pubnotes .= " FIRING SHOT: Homing Missile ran out of fuel.";
			return false;
		}
		return true;
	}

	/*3 of 6. ⚠️ NOT OPTIONAL, AND SILENT IF OMITTED.
	  Weapon::calculateLoading treats ANY fire order on a ballistic weapon this turn as "it fired"
	  and zeroes the rack's loading. A homing missile in flight is not this launcher firing - it
	  left the rails on an earlier turn - so without this the rack would never reload and could
	  never launch again for as long as the missile stayed up.

	  Written as a filter-and-delegate rather than a copy of the parent's body so it cannot drift
	  from it, and short-circuits on the count comparison for every launcher with nothing in flight
	  (i.e. all of them, in almost every game).*/
	public function firedOnTurn($turn)
	{
		$allOrders = $this->fireOrders;
		$realOrders = array();
		foreach ($allOrders as $fire){
			if ($fire->damageclass === AmmoMissileHM::REATTACK_CLASS) continue;
			$realOrders[] = $fire;
		}
		if (count($realOrders) === count($allOrders)) return parent::firedOnTurn($turn);

		$this->fireOrders = $realOrders;
		$firedOnTurn = parent::firedOnTurn($turn);
		$this->fireOrders = $allOrders;
		return $firedOnTurn;
	}

	/*⭐⭐ THE NEXT PASS'S ROW, WRITTEN AT THE END OF THE PASS THAT CREATED IT (2026-09-03).

	  A missile that survives its pass is public knowledge from that moment - it announced itself by
	  not exploding - so its next attack run must be a real, named, addressable shot from the very
	  first load of the next turn, not a nameless in-memory ghost that only becomes real when the
	  Firing phase resolves. Three separate bugs all came from it having id -1 until then
	  (game 4328):

	    - the ballistic icon container keys its duplicate test on the fire order id, so a SECOND
	      missile with id -1 was folded into the first and drew no launch hex at all;
	    - a manual intercept order names the order it intercepts in its own targetid, so declaring
	      interception on either of two id -1 missiles credited BOTH on the defender's screen;
	    - and the server then threw those orders away, because Firing::automateIntercept resolves
	      that targetid against this turn's orders by id and -1 names nothing.

	  Inserting it HERE is what makes the id stable: this runs once, server-side, inside
	  FireGamePhase::advance, and the row carries turn+1, so it is invisible for the remainder of
	  this turn (DBManager::getFireOrdersForShips only ever fetches the current turn) and is loaded
	  and adopted by onIndividualNotesLoaded on every load of the next one.

	  ⚠️ NOT attached to $this->fireOrders. It does not belong to the turn being resolved, and every
	  sweep that follows in FireGamePhase::advance walks this turn's orders.

	  Returns the new row id, or 0 if the insert failed - in which case the note records 0 and the
	  missile falls back to the in-memory rebuild plus persistHomingOrders, exactly as before.*/
	protected function persistNextPassOrder($gamedata, $homing, $state)
	{
		$nextTurn = (int)$gamedata->turn + 1;
		$nextPass = $homing->buildReattackOrder($gamedata, $this, $state, $nextTurn);
		$newId = (int)Manager::insertSingleFiringOrder($gamedata, $nextPass);
		return ($newId > 0) ? $newId : 0;
	}

	/*4 of 6. THE FALLBACK id. Since persistNextPassOrder above, a re-attack normally arrives with a
	  real id already on it and this finds nothing to do; what is left for it is a missile whose note
	  predates that change, or whose insert failed. Kept because a synthetic order MUST hold a real
	  id before it deals damage - see below - and this is the last point at which that can be fixed.

	  ⚠️ WHY HERE AND NOT AT REBUILD TIME. onIndividualNotesLoaded runs on EVERY gamedata build for
	  EVERY viewer, so inserting there would write a row per poll. Firing::prepareFiring calls
	  beforeFiringOrderResolution on every system of every ship exactly ONCE per resolution, before
	  any hit chance is calculated - the one hook that is both single-shot and server-authoritative.

	  ⚠️ AND IT HAS TO HAPPEN BEFORE THE SHOT RESOLVES. DamageEntry copies $fireOrder->id at the
	  moment damage is dealt; with the usual id = -1 DBManager::submitDamages back-fills it by
	  querying "same gameid+turn+shooterid+weaponid, shotshit > 0" and taking $result[0] with NO
	  ORDER BY - so a rack that also fired normally this turn would silently log the homing hit
	  against the wrong order. See howto_create_fire_orders.

	  Orders that some other path already persisted (the movement-phase jump-out sweep, or
	  PreFiringGamePhase, both of which submit getNewFireOrders()) come back from the DB with a real
	  id and are skipped here; onIndividualNotesLoaded's guard is what stops them being rebuilt.*/
	protected function persistHomingOrders($gamedata)
	{
		foreach ($this->getFireOrders($gamedata->turn) as $fire){
			if ($fire->damageclass !== AmmoMissileHM::REATTACK_CLASS) continue;
			if ((int)$fire->id > 0) continue; //already in the database

			$fire->id      = (int)Manager::insertSingleFiringOrder($gamedata, $fire);
			$fire->addToDB = false; //inserted - do not let submitFireorders insert it a second time
			$fire->updated = true;  //...but DO let updateFireOrders write the resolution back
		}
	}

	/*Take a persisted re-attack order back off the launcher - used when its note says the missile
	  should not be flying this turn after all. Detaches only; the row stays in the database, where
	  nothing will read it again (it is stamped with this turn and this turn is already loaded).*/
	protected function withdrawHomingOrder($state, &$persisted, &$byOrderId)
	{
		$orderId = (int)$state['orderid'];
		if ($orderId <= 0 || !isset($byOrderId[$orderId])) return;

		$index = $byOrderId[$orderId];
		if (!isset($persisted[$index])) return;

		$doomed = $persisted[$index];
		unset($persisted[$index], $byOrderId[$orderId]);

		foreach ($this->fireOrders as $key => $fire){
			if ($fire !== $doomed) continue;
			unset($this->fireOrders[$key]); //gaps are fine - hideSystemFireOrders leaves them too
			break;
		}
	}

	/*5 of 6. Rebuild every missile still in flight into a fire order for THIS turn.

	  Runs on every gamedata build, which is what makes the missile visible to both players and lets
	  the defender assign manual interception against it in the Fire Declaration phase.

	  ⭐ INCLUDING IN INITIAL ORDERS (2026-09-03, user ruling). deleteHiddenData strips every OTHER
	  current-turn ballistic order from every phase-1 payload, because a launch declared this turn
	  is a secret until both sides commit. A missile that is already in the air is the opposite of a
	  secret - it revealed itself by surviving last turn's pass - and both players need it on the
	  board while they are deciding this turn's orders, not only after they have committed them.
	  TacGamedata::hideSystemFireOrders carries the exemption, and Firing::validateFireOrders is
	  what stops the client posting the now-visible order back and duplicating the row.

	  ⚠️ THE GUARD IS LOAD-ORDER DEPENDENT AND CORRECT: DBManager::getTacShips calls
	  getFireOrdersForShips BEFORE getSystemDataForShips, so anything already persisted for this
	  turn is in $this->fireOrders by the time this runs and cannot be duplicated.*/
	public function onIndividualNotesLoaded($gamedata)
	{
		$homing = $this->getHomingAmmo();
		$lastTurn = (int)$gamedata->turn - 1;

		if ($homing !== null && $lastTurn >= 1){
			/*Re-attack orders for THIS turn that are already in hand. Since 2026-09-03 that is the
			  NORMAL case, not the exception: the row was written at the end of last turn's Firing
			  phase (persistNextPassOrder) and has just been read back. One is claimed per note.*/
			$persisted = array();
			foreach ($this->getFireOrders($gamedata->turn) as $fire){
				if ($fire->damageclass !== AmmoMissileHM::REATTACK_CLASS) continue;
				$persisted[] = $fire;
			}

			/*⭐ CLAIM BY ROW ID FIRST. Since 2026-09-03 the note carries the id of the row that was
			  inserted for it (see AmmoMissileHM::unpackState field 9), so two missiles that are
			  otherwise INDISTINGUISHABLE - same target, same launch hex, same mode, which is the
			  normal case for two missiles chasing one ship, and exactly what game 4328 turn 3 had -
			  stay told apart for ever. The field match below is the legacy path only.*/
			$byOrderId = array();
			foreach ($persisted as $i => $fire){
				$byOrderId[(int)$fire->id] = $i;
			}

			foreach ($this->individualNotes as $currNote){
				if ($currNote->notekey !== 'HomingMissile') continue;
				if ((int)$currNote->turn !== $lastTurn) continue; //only LAST turn's survivors

				$state = $homing->unpackState($currNote->notevalue);
				if ($state === null) continue;

				/*⚠️ Only "does the target still exist" is asked here. isDestroyed() is NOT reliable
				  at this point - a system latches $destroyed in onConstructed, which has not run
				  yet - so that question is left to resolveHomingOutcome at phase 4.

				  The row written for this pass a turn ago has to be taken back off the board with
				  it. Before the row was written up front, "no target" simply meant nothing was
				  rebuilt; now the order exists whatever happened to its target, and one left
				  attached would reach prepareFiring, which hands a null target to calculateHitBase.
				  The DB row is left where it is - it is for THIS turn only and is never read
				  again.*/
				if ($gamedata->getShipById($state['targetid']) === null){
					$this->withdrawHomingOrder($state, $persisted, $byOrderId);
					continue;
				}

				//Already on the board? Adopt it and move on - do NOT build a second one.
				$claimed = null;
				$orderId = (int)$state['orderid'];

				if ($orderId > 0 && isset($byOrderId[$orderId])){
					$i = $byOrderId[$orderId];
					if (isset($persisted[$i])){
						$claimed = $persisted[$i];
						unset($persisted[$i], $byOrderId[$orderId]);
					}
				}

				/*Legacy notes ONLY (orderid 0): a missile that was already in flight when the row
				  started being written up front. Matched on the order's own persisted fields, which
				  cannot tell two identical missiles apart - which is why it counts rather than keys,
				  and why it is no longer the primary path.*/
				if ($claimed === null && $orderId <= 0){
					foreach ($persisted as $i => $fire){
						if (!$this->homingOrderMatchesState($fire, $state)) continue;
						$claimed = $fire;
						unset($persisted[$i], $byOrderId[(int)$fire->id]);
						break;
					}
				}

				if ($claimed === null){
					$claimed = $homing->buildReattackOrder($gamedata, $this, $state);
					$this->fireOrders[] = $claimed;
				}

				$this->setHomingState($claimed, $state);
			}
		}

		parent::onIndividualNotesLoaded($gamedata); //clears the note list, as the base class does
	}

	/*6 of 6. Record which homing missiles survived their pass.

	  ⚠️ PHASE 4 ONLY, and that is what keeps this off POST-side ships. process() also calls
	  generateIndividualNotes, on ships rebuilt from the player's submission that have neither
	  enhancements applied nor notes loaded (arch_post_side_ship_reconstruction) - but those calls
	  arrive in phases 1 and 3. Phase 4 exists only inside FireGamePhase::advance, which sets it
	  before loading server-authoritative gamedata and then runs prepareFiring -> fireWeapons ->
	  THIS, on the very objects that just resolved.*/
	public function generateIndividualNotes($gamedata, $dbManager)
	{
		parent::generateIndividualNotes($gamedata, $dbManager);

		if ((int)$gamedata->phase !== 4) return;
		$homing = $this->getHomingAmmo();
		if ($homing === null) return;
		$ship = $this->getUnit();
		if ($ship === null) return;

		foreach ($this->getFireOrders($gamedata->turn) as $fire){
			$isReattack = ($fire->damageclass === AmmoMissileHM::REATTACK_CLASS);
			$modeName = isset($this->firingModes[$fire->firingMode]) ? $this->firingModes[$fire->firingMode] : null;
			if (!$isReattack && $modeName !== $homing->modeName) continue;

			//The state this pass FLEW under (null for a fresh launch), off the map rather than the
			//order - $notes cannot carry it, see the ⚠️⚠️ on $homingStates.
			$state = $homing->resolveHomingOutcome($gamedata, $this, $fire, $this->getHomingState($fire));
			if ($state === null) continue; //hit, shot down, or out of fuel - the missile is gone

			//⭐ THE NEXT PASS GETS ITS DATABASE ROW HERE, before its note is written.
			$state['orderid'] = $this->persistNextPassOrder($gamedata, $homing, $state);

			//notekey 13 chars, notekey_human 30 - both columns are varchar(40) and OVERFLOW IS A
			//FATAL that aborts the whole submission, not a truncation.
			$this->individualNotes[] = new IndividualNote(
				-1, TacGamedata::$currentGameID, $gamedata->turn, $gamedata->phase,
				$ship->id, $this->id,
				'HomingMissile', 'Homing missile still in flight',
				$homing->packState($state)
			);
		}
	}

} //endof class AmmoMissileRackS


/*Class-L Missile Rack - weapon that looks at central magazine to determine available firing modes (and number of actual rounds available)
	all functionality prepared in standard class-S rack
	holds 20 missiles
*/
class AmmoMissileRackL extends AmmoMissileRackS{
	public $name = "ammoMissileRackL";
        public $displayName = "Class-L Missile Rack";
    public $iconPath = "missile1.png";    
	
    public $range = 30;
    public $distanceRange = 70;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 2; 
     
    
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(3,3,3);
	protected $basicRange=30;
	protected $basicDistanceRange = 70;

    protected $rackExplosionDamage = 75; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 20; //how high roll is needed for rack explosion   
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
            	if ( $powerReq == 0 ) $powerReq = 0;					
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
} //endof class AmmoMissileRackL


/*Class-LH Missile Rack - weapon that looks at central magazine to determine available firing modes (and number of actual rounds available)
	all functionality prepared in standard class-S rack
	holds 20 missiles
*/
class AmmoMissileRackLH extends AmmoMissileRackS{
	public $name = "ammoMissileRackLH";
        public $displayName = "Class-LH Missile Rack";
    public $iconPath = "missile2.png";    
	
    public $range = 30;
    public $distanceRange = 70;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 1;
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(4,4,4);
	protected $basicRange=30;
	protected $basicDistanceRange = 70;

    protected $rackExplosionDamage = 0; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 21; //how high roll is needed for rack explosion        
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 8;
            	if ( $powerReq == 0 ) $powerReq = 0;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
} //endof class AmmoMissileRackLH



/*Class-B Missile Rack - weapon that looks at central magazine to determine available firing modes (and number of actual rounds available)
	all functionality prepared in standard class-S rack
	holds 60 missiles
*/
class AmmoMissileRackB extends AmmoMissileRackS{
	public $name = "ammoMissileRackB";
        public $displayName = "Class-B Missile Rack";
    public $iconPath = "missile3.png";    
	
    public $range = 30;
    public $distanceRange = 70;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 1;
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(3,3,3);
	protected $basicRange=30;
	protected $basicDistanceRange = 70;

    protected $rackExplosionDamage = 0; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 21; //how high roll is needed for rack explosion         
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 9;
            	if ( $powerReq == 0 ) $powerReq = 0;
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
} //endof class AmmoMissileRackB



/*Class-R Missile Rack - weapon that looks at central magazine to determine available firing modes (and number of actual rounds available)
	all functionality prepared in standard class-S rack
	holds 20 missiles
*/
class AmmoMissileRackR extends AmmoMissileRackS{
	public $name = "ammoMissileRackR";
        public $displayName = "Class-R Missile Rack";
    public $iconPath = "missile2.png";    
	
    public $range = 20;
    public $distanceRange = 60;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 1;
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(3,3,3);
	protected $basicRange=20;
	protected $basicDistanceRange = 60;

    protected $rackExplosionDamage = 75; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 19; //how high roll is needed for rack explosion          
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
            	if ( $powerReq == 0 ) $powerReq = 0;					
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	
	}
} //endof class AmmoMissileRackR



/*Class-SO Missile Rack - weapon that looks at central magazine to determine available firing modes (and number of actual rounds available)
	all functionality prepared in standard class-S rack
	holds 12 missiles (as it's often fitted to old ships - check munitions reasonably available!)
*/
class AmmoMissileRackSO extends AmmoMissileRackS{
	public $name = "ammoMissileRackSO";
        public $displayName = "Class-SO Missile Rack";
    public $iconPath = "missile1.png";    
	
    public $range = 20;
    public $distanceRange = 60;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 2;
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(2,2,2);
	protected $basicRange=20;
	protected $basicDistanceRange = 60;

    protected $rackExplosionDamage = 45; //how much damage will this weapon do in case of catastrophic explosion (Class-SO launcher has smaller magazine than Class-S)
    protected $rackExplosionThreshold = 20; //how high roll is needed for rack explosion  

	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
            	if ( $powerReq == 0 ) $powerReq = 0;			
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
	
} //endof class AmmoMissileRackSO



/*Class-O Missile Rack - GEOFFREY, please fill as appropriate - ATM it's just a copy of class-SO ammo missile rack!
*/
class AmmoMissileRackO extends AmmoMissileRackS{
	public $name = "ammoMissileRackO";
        public $displayName = "Class-O Missile Rack";
    public $iconPath = "missile1.png";    
	
    public $range = 20;
    public $distanceRange = 60;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 3;
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(2,2,2);
	protected $basicRange=20;
	protected $basicDistanceRange = 60;

    protected $rackExplosionDamage = 30; //how much damage will this weapon do in case of catastrophic explosion (Class-O launcher has smaller magazine than Class-S)
    protected $rackExplosionThreshold = 20; //how high roll is needed for rack explosion          
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
            	if ( $powerReq == 0 ) $powerReq = 0;					
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
	
} //endof class AmmoMissileRackO



/*Class-A Missile Rack - weapon that looks at central magazine to determine available firing modes (and number of actual rounds available)
	all functionality prepared in standard class-S rack
	holds 20 missiles (Antifighter Missiles ONLY, at no additional price)
*/
class AmmoMissileRackA extends AmmoMissileRackS{
	public $name = "ammoMissileRackA";
        public $displayName = "Class-A Missile Rack";
    public $iconPath = "missile2.png";    
	
    public $range = 20; //antifighter missile itself will reduce it - this way the same missile fits all racks
    public $distanceRange = 60;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 1;
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(4,0,0);
	protected $basicRange=20;
	protected $basicDistanceRange = 60;

    protected $rackExplosionDamage = 56; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 19; //how high roll is needed for rack explosion   
    
	public $canModesIntercept = false;	//A-Racks can't have Interceptor Missiles          
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
		if ( $powerReq == 0 ) $powerReq = 0;

		//reset missile availability! (Parent sets way too much)
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray = array();
			$this->ammoClassesArray[] =  new AmmoMissileA();
			$this->availableAmmoAlreadySet = true;
		}						
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
} //endof class AmmoMissileRackA


//Class-D Missile Rack - holds 20 missiles (only carries Interceptor (I - default), chaff (C), and anti-fighter (A) missiles)
class AmmoMissileRackD extends AmmoMissileRackS{
	public $name = "AmmoMissileRackD";
    public $displayName = "Class-D Missile Rack";
    public $iconPath = "ClassDMissileRack.png";    

    public $range = 20; //antifighter missile itself will reduce it - this way the same missile fits all racks
    public $distanceRange = 60;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 1;
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(3, 3, 3);
	protected $basicRange=20;
	protected $basicDistanceRange = 60;
	
	public $intercept = 0;
	public $ballisticIntercept = true;	

    protected $rackExplosionDamage = 15; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 20; //how high roll is needed for rack explosion  
                    
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
		if ( $powerReq == 0 ) $powerReq = 0;

		//reset missile availability! (Parent sets way too much)
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray = array();
			$this->ammoClassesArray[] =  new AmmoMissileI(); 			
			$this->ammoClassesArray[] =  new AmmoMissileA();
			$this->ammoClassesArray[] =  new AmmoMissileC();
			$this->ammoClassesArray[] =  new AmmoMissileZ();							

			$this->availableAmmoAlreadySet = true;
		}						
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		
		$this->data["Special"] = 'Available firing modes depend on ammo bought as unit enhancements. Ammunition available is tracked by central Ammunition Magazine system.';
		if ($this->rackExplosionThreshold < 21) { //can explode - inform player!
			$chance = (21 - $this->rackExplosionThreshold) * 5; //percentage chance of explosion
			$this->data["Special"] .= '<br>Pre-loaded with 20 Interceptor Missiles, which can intercept other ballistics at -30% each.';
			$this->data["Special"] .= '<br>Can explode if damaged or destroyed, dealing ' . $this->rackExplosionDamage . ' damage in Flash mode (' . $chance . '% chance).';
		}	
	}	
} //endof class AmmoMissileRackD





/*Class-G Missile Rack - custom weapon that looks at central magazine to determine available firing modes (and number of actual rounds available)
	all functionality prepared in standard class-S rack
	holds 20 missiles
*/
class AmmoMissileRackG extends AmmoMissileRackS{
	public $name = "ammoMissileRackG";
    public $displayName = "Guided Missile Rack";
    public $iconPath = "missile1.png";    

    public $range = 25;
    public $distanceRange = 65;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 2; 

    public $useOEW = true;
    
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(3,3,3);
	protected $basicRange=25;
	protected $basicDistanceRange = 65;

    protected $rackExplosionDamage = 75; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 20; //how high roll is needed for rack explosion   
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
            	if ( $powerReq == 0 ) $powerReq = 0;					
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
} //endof class AmmoMissileRackG


// GTS_Triad

class AmmoMissileRackTriad extends AmmoMissileRackS {
    public $name = "AmmoMissileRackTriad";
    public $displayName = "Triad Missile Rack";
    public $iconPath = "TriadMissileRack.png";

	public $factionAge = 3;//Ancient weapon, which sometimes has consequences!

    public $priority = 6;
    public $loadingtime = 1;
    public $powerReq = 0; // power is handled via boost mechanism (6 power per boost-mode shot)

    protected $basicFC = array(3, 5, 5);
    protected $rackExplosionDamage = 0;
    protected $rackExplosionThreshold = 30;

    public $boostable = false;
    public $boostEfficiency = 6;
    public $maxBoostLevel = 1;
    public $powerReqArray = array();
    public $boostModeIndex = null;

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base = false) {
        if ($maxhealth == 0) $maxhealth = 6;
        parent::__construct($armour, $maxhealth, 0, $startArc, $endArc, $magazine, $base);
    }

    public function recompileFiringModes() {
        parent::recompileFiringModes();

        // Double the range of all normal missile modes
        foreach ($this->rangeArray as $mode => $range) {
            $this->rangeArray[$mode] = $range * 2;
            $this->distanceRangeArray[$mode] = $range * 2 * 3;
            $this->powerReqArray[$mode] = 0;
        }

        // Add boost mode
        $nextMode = count($this->firingModes) + 1;
        $this->boostModeIndex = $nextMode;

        $this->firingModes[$nextMode]                     = "Extra Power (Basic Missile)";
        $this->damageTypeArray[$nextMode]                 = "Standard";
        $this->weaponClassArray[$nextMode]                = "Ballistic";
        $this->fireControlArray[$nextMode]                = array(6, 8, 8);
        $this->rangeArray[$nextMode]                      = 40;
        $this->distanceRangeArray[$nextMode]              = 120;
        $this->priorityArray[$nextMode]                   = 6;
        $this->priorityAFArray[$nextMode]                 = 6;
        $this->noOverkillArray[$nextMode]                 = false;
        $this->minDamageArray[$nextMode]                  = 20;
        $this->maxDamageArray[$nextMode]                  = 20;
        $this->hidetargetArray[$nextMode]                 = false;
        $this->maxpulsesArray[$nextMode]                  = 0;
        $this->rofArray[$nextMode]                        = 0;
        $this->useDieArray[$nextMode]                     = 0;
        $this->fixedBonusPulsesArray[$nextMode]           = 0;
        $this->calledShotModArray[$nextMode]              = -8;
        $this->specialRangeCalculationArray[$nextMode]    = false;
        $this->rangePenaltyArray[$nextMode]               = 0;
        $this->noLockPenaltyArray[$nextMode]              = false;
        $this->specialHitChanceCalculationArray[$nextMode] = false;
        $this->interceptArray[$nextMode]                  = 0;
        $this->ballisticInterceptArray[$nextMode]         = false;
        $this->hextargetArray[$nextMode]                  = false;
        $this->animationArray[$nextMode]                  = "trail";
        $this->animationExplosionScaleArray[$nextMode]    = 0;
        $this->uninterceptableArray[$nextMode]            = false;
        $this->doNotInterceptArray[$nextMode]             = false;
        $this->mineRangeArray[$nextMode]                  = 0;
        $this->powerReqArray[$nextMode]                   = 6; // for display purposes only
    }

    public function changeFiringMode($newMode) {
        parent::changeFiringMode($newMode);
        if ($this->boostModeIndex !== null && $newMode == $this->boostModeIndex) {
            $this->powerReq = 6;
            $this->boostable = true;
        } else {
            $this->powerReq = 0;
            $this->boostable = false;
        }
    }

    public function getDamage($fireOrder) {
        if ($this->boostModeIndex !== null && $fireOrder->firingMode == $this->boostModeIndex) {
            return 20;
        }
        return parent::getDamage($fireOrder);
    }

    public function setSystemDataWindow($turn) {
        parent::setSystemDataWindow($turn);
        $this->data["Special"] .= "<br>Extra Power mode: spend 6 power to generate and fire a basic missile without consuming rack ammunition.";
        $this->data["Special"] .= "<br>All other modes fire from the magazine at no power cost. Launch range for all modes is doubled.";
    }

    public function calculateHitBase($gamedata, $fireOrder) {
        if ($this->boostModeIndex !== null && $fireOrder->firingMode == $this->boostModeIndex) {
            // Bypass AmmoMissileRackS (which draws ammo) — use base Weapon hit calc
            Weapon::calculateHitBase($gamedata, $fireOrder);
            return;
        }
        parent::calculateHitBase($gamedata, $fireOrder);
    }

    public function fire($gamedata, $fireOrder) {
        if ($this->boostModeIndex !== null && $fireOrder->firingMode == $this->boostModeIndex) {
            // Bypass AmmoMissileRackS (which draws ammo) — use base Weapon fire
            Weapon::fire($gamedata, $fireOrder);
            return;
        }
        parent::fire($gamedata, $fireOrder);
    }

    public function stripForJson() {
        $stripped = parent::stripForJson();
        if (!empty($this->powerReqArray)) {
            $stripped->powerReqArray = $this->powerReqArray;
        }
        $stripped->boostModeIndex  = $this->boostModeIndex;
        $stripped->boostable       = $this->boostable;
        $stripped->boostEfficiency = $this->boostEfficiency;
        $stripped->maxBoostLevel   = $this->maxBoostLevel;
        return $stripped;
    }
}










/*Bomb Rack - weapon that looks at central magazine to determine available firing modes (and number of actual rounds available)
	all functionality prepared in standard class-S rack
	holds 8 missiles (Basic Missiles by default at no price (unless filled with actual bombs), the only other option is Flash missiles)
*/
class AmmoBombRack extends AmmoMissileRackS{
	public $name = "ammoBombRack";
        public $displayName = "Bomb Rack";
    public $iconPath = "bombRack.png";    
	
    public $range = 20;
    public $distanceRange = 60;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 2;
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(1,2,3);
	protected $basicRange=20;
	protected $basicDistanceRange = 60;

    protected $rackExplosionDamage = 30; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 20; //how high roll is needed for rack explosion
    
 	public $canModesIntercept = false;	//Bomb Racks should never be able to intercept.           
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
		if ( $powerReq == 0 ) $powerReq = 0;
		
		//reset missile availability! (Parent sets way too much)
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray = array();
			$this->ammoClassesArray[] =  new AmmoMissileB();
			$this->ammoClassesArray[] =  new AmmoMissileF();
			$this->ammoClassesArray[] =  new AmmoMissileD(); //For Narn Dag'Kur			
			$this->availableAmmoAlreadySet = true;
		}		
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
} //endof class AmmoBombRack



/*fighter missile launcher using AmmoMagazine
*/
class AmmoFighterRack extends AmmoMissileRackS{
	public $name = "ammoFighterRack";
        public $displayName = "Missile";
    public $iconPath = "fighterMissile.png";
	
    public $range = 10;
    public $distanceRange = 30;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 1;
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(0,0,0);
	protected $basicRange=10;
	protected $basicDistanceRange = 30;

    protected $rackExplosionDamage = 0; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 22; //how high roll is needed for rack explosion 
    
	public $canModesIntercept = false;	//Fighter Racks can't have Interceptor Missiles             
	
	function __construct($startArc, $endArc, $magazine, $base=false) //fighter-sized OSATs might benefit from being stable!
	{		
		//reset missile availability! (Parent is a shipborne launcher with different set of available missiles)
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray = array();
			$this->ammoClassesArray[] =  new AmmoMissileFB();
			$this->ammoClassesArray[] =  new AmmoMissileFL();
			$this->ammoClassesArray[] =  new AmmoMissileFH();
			$this->ammoClassesArray[] =  new AmmoMissileFY();
			$this->ammoClassesArray[] =  new AmmoMissileFD();
			$this->ammoClassesArray[] =  new AmmoMissileFDum();			
			$this->availableAmmoAlreadySet = true;
		}		
		parent::__construct(0, 1, 0, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}
} //endof class AmmoFighterRack



class AmmoMissileRackF extends AmmoMissileRackS {
		public $name = "AmmoMissileRackF";
        public $displayName = "Class-F Missile Rack";
        public $iconPath = "ClassFMissileRack.png";

	    public $range = 35;
	    public $distanceRange = 75;
	    public $firingMode = 1;

		public $priority = 6; 		
		public $loadingtime = 1;
		public $normalload = 2;
		
		public $basicFC=array(3,3,3);
		public $basicRange = 35;
		public $basicDistanceRange = 75;				

		protected $rackExplosionDamage = 38; //how much damage will this weapon do in case of catastrophic explosion
		protected $rackExplosionThreshold = 20; //how high roll is needed for rack explosion (d20)

		private $firedInRapidMode = false; //was this weapon fired in rapid mode (this turn)?
		private $firedInLongRangeMode = false;//was this weapon fired in Long Range mode this turn?
		private $noHexTargeting = false;		

		function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
		{
		if ( $maxhealth == 0 ) $maxhealth = 6;
            	if ( $powerReq == 0 ) $powerReq = 0;					
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
		}

        public function setSystemDataWindow($turn){	
		$this->recalculateFireControl(); //necessary for correct  data outside of firing.	
		parent::setSystemDataWindow($turn);	
			$this->data["Special"] .= '<br>When fully loaded, can fire Normal mode (with 20 hex range before modifiers), or Long Range (with +15 hexes to normal range, but reduced Fire Control).';
			$this->data["Special"] .= '<br>NOTE - Weapon will select mode automatically based on the range of your selected target.';			
			$this->data["Special"] .= '<br>After one turn loading, can fire in Rapid mode (with reduced range and Fire Control) - but NOT after using Long Range mode in previous turn (icon will show as greyed out).';
		}

		
	private function nullFireControl() {//Extra function needed to null Fire Control values across ALL ammo types in recalculateFireControl.
			$this->fireControl = array(null,null,null);//I need this method if launched has NO ammo modes.
			$this->fireControlArray = array();
			
			$this->noHexTargeting = true;				
	
			$nullFC = array(null, null, null); //I need this method if there's ammo equipped.
			$this->basicFC = $nullFC; 
		}		

	private function modifyFireControl(&$subArray) {//Extra function needed to modify Fire Control values across ALL ammo types in recalculateFireControl.
  		 	 foreach ($subArray as $key => &$value) {
    		    if (is_numeric($value)) {
            $subArray[$key] = $value - 2;
      				  }
    			}
		}

	private function modifyRange(&$subArray) {//Extra function needed to modify Range values across ALL ammo types in recalculateFireControl.
  		 	foreach ($subArray as $key => &$value) {
					if (is_numeric($value)) {
						$subArray[$key] = $value - 20;
      				}
    		}
		}		

	private function modifyDistanceRange(&$subArray) {//Extra function needed to modify Distance Range values across ALL ammo types in recalculateFireControl.
  		 	 foreach ($subArray as $key => &$value) {
    		    if (is_numeric($value)) {
            $subArray[$key] = $value - 30;
      				  }
    			}
		}	

    public function beforeFiringOrderResolution($gamedata) //Necessary for recalculateFireControl to apply to actual firing results.
	    {		
	        parent::beforeFiringOrderResolution($gamedata);
			$this->recalculateFireControl();        
		}	//endof function beforeFiringOrderResolution	
	
	
	//recalculates fire control as appropriate for current loading time!
	private function recalculateFireControl(){

	 	if (($this->turnsloaded < 2) && (($this->firedInRapidMode) ||  (TacGamedata::$currentPhase == 1))) {

	        foreach ($this->fireControlArray as &$subArray) {
	            $this->modifyFireControl($subArray);
	        }

			$this->modifyRange($this->rangeArray);
			
	 		$this->modifyDistanceRange($this->distanceRangeArray);     
	    }


		if ($this->firedInLongRangeMode) {   	
	    	foreach ($this->fireControlArray as &$subArray) {
	            $this->modifyFireControl($subArray);
	        }
		}    

	    $this->changeFiringMode(1);

} // end of recalculateFireControl

	
	// This method generates additional non-standard information in the form of individual system notes
	 public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
			/*⚠️ The parent writes the HOMING MISSILE survival note, and it does it at phase 4 - which
			  this method's own switch does not handle at all, so the two cannot collide. Without
			  this call a Homing missile fired from a Class-F rack would simply never come back, on
			  six of the eleven Kor-Lyan hulls that can buy them. See HOMING_MISSILE_PLAN.md.*/
			parent::generateIndividualNotes($gameData, $dbManager);

			$ship = $this->getUnit();
			switch($gameData->phase){
				case 1: //Initial phase 
					//if weapon is marked as firing in Rapid mode, make a note of it!
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						if($this->firedInRapidMode){
							$notekey = 'RapidFire';
							$noteHuman = 'fired in Rapid mode';
							$noteValue = 'R';
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}
						
						if($this->firedInLongRangeMode){
							$notekey = 'LongRanged';
							$noteHuman = 'fired in Long Range mode';
							$noteValue = 'L';
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}						
					}
                    break;
				
				case 3: //Firing phase 

					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
					//if weapon is marked as firing in Rapid mode, make a note of it!
							if($this->firedInRapidMode){
							$notekey = 'RapidFire';
							$noteHuman = 'fired in Rapid mode';
							$noteValue = 'R';
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}
					//Or, if weapon is marked as firing in Rapid mode, make a note of it!						
						if($this->firedInLongRangeMode){
							$notekey = 'LongRanged';
							$noteHuman = 'fired in Long Range mode';
							$noteValue = 'L';
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}						
					}					
				break;
				
		}
	} //endof function generateIndividualNotes
	
	//act on notes just loaded - to be redefined by systems as necessary
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote) 
			if($currNote->turn == $gamedata->turn) if ($currNote->notevalue == 'R'){ //only current round matters!
			$this->firedInRapidMode = true;			
		}
		
		foreach ($this->individualNotes as $currNote) 
			if($currNote->turn == $gamedata->turn) if ($currNote->notevalue == 'L'){ //only current round matters!
			$this->firedInLongRangeMode = true;			
		}
		
		foreach ($this->individualNotes as $currNote) //To null firecontrol if fired long range PREVIOUS turn, Rapid not available.
			if($currNote->turn == $gamedata->turn-1) if ($currNote->notevalue == 'L'){ //only current round matters!
				
			$this->nullFireControl();//Null fire control for weapon, to prevent firing after Long Range shot.
        	$this->iconPath = "ClassFMissileRackTechnical.png";
		}		
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
//		$this->recalculateFireControl(); //necessary for the variable to affect actual firing
		/*⚠️ LAST, and in place of the bare "$this->individualNotes = array()" this used to end on.
		  The parent reads the HOMING MISSILE notes here and rebuilds any missile still in flight -
		  so it has to run BEFORE the list is emptied, and it does its own clearing on the way out
		  (ShipSystem::onIndividualNotesLoaded). Without this a Homing missile fired from a Class-F
		  rack would silently never return. See HOMING_MISSILE_PLAN.md.*/
		parent::onIndividualNotesLoaded($gamedata);
	} //endof function onIndividualNotesLoaded
	
	
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for variable firedInRapidMode
		if(is_array($this->individualNotesTransfer)) foreach($this->individualNotesTransfer as $entry) {
			if ($entry == 'R') $this->firedInRapidMode = true;
		}
			
		if(is_array($this->individualNotesTransfer)) foreach($this->individualNotesTransfer as $entry) {
			if ($entry == 'L') $this->firedInLongRangeMode = true;
		}			
		$this->individualNotesTransfer = array(); //empty, just in case
	}		
			
	public function stripForJson(){
			$strippedSystem = parent::stripForJson();
			$strippedSystem->data = $this->data;
			$strippedSystem->minDamage = $this->minDamage;
			$strippedSystem->minDamageArray = $this->minDamageArray;
			$strippedSystem->maxDamage = $this->maxDamage;
			$strippedSystem->maxDamageArray = $this->maxDamageArray;		
			$strippedSystem->fireControl = $this->fireControl;
			$strippedSystem->fireControlArray = $this->fireControlArray;
			$strippedSystem->range = $this->range;
			$strippedSystem->rangeArray = $this->rangeArray;
			$strippedSystem->firedInRapidMode = $this->firedInRapidMode;			
			$strippedSystem->firedInLongRangeMode = $this->firedInLongRangeMode;
			$strippedSystem->noHexTargeting = $this->noHexTargeting;													
			$strippedSystem->iconPath = $this->iconPath;			
			return $strippedSystem;
		}

}//end of class AmmoMissileRackF


class BallisticMineLauncher extends AmmoMissileRackS{
	public $name = "BallisticMineLauncher";
    public $displayName = "Ballistic Mine Launcher";
    public $iconPath = "BallisticMineLauncher.png";

    // Ship classes that this weapon can dynamically spawn mid-game.
    // game.php reads this to preload blueprints into window.staticShips.
    public $spawnableClasses = [
        'spawnCaptorKLB',
        'spawnCaptorKLH',
        'spawnCaptorKLW',
    ];
	
    public $range = 30;
    public $distanceRange = 60;
    public $firingMode = 1;
    public $priority = 6;
    public $loadingtime = 2;
	public $hextarget = true;
	public $hidetarget = true;	     
	private $specialPosNoLauncher = true; //Allows mine explosion to animate AND for Mine to originated from there.	     
    
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(0,0,0);
	protected $basicRange = 30;
	protected $basicDistanceRange = 60; //Just so scattering past 30 hexes doesn't cause an issue.
	
	public $animation = "bolt";
	public $animationColor = array(245, 90, 90);
    public $animationExplosionScale = 0.25; //0 means it will be set automatically by standard constructor, based on average damage yield
    public $animationExplosionScaleArray = array();
	public $animationExplosionType = "AoE";
	protected $alwaysHideFireOrders = true;

    protected $rackExplosionDamage = 0; //how much damage will this weapon do in case of catastrophic explosion
    protected $rackExplosionThreshold = 21; //Not sure these can explode in same way as Missile Racks.  Set above threshold for now.  
	


	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 7;
            	if ( $powerReq == 0 ) $powerReq = 4;

			//Set mine availability! (Cannot fire missiles like S-Rack)
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray = array();
			$this->ammoClassesArray[] =  new AmmoBLMineB();
			$this->ammoClassesArray[] =  new AmmoBLMineH();				
			$this->ammoClassesArray[] =  new AmmoBLMineW();			
			$this->availableAmmoAlreadySet = true;
		}	            		
            						
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}   


    public function beforeFiringOrderResolution($gamedata)
    {
      $firingOrders = $this->getFireOrders($gamedata->turn);
    	
      $originalFireOrder = null;

		foreach($firingOrders as $fireOrder){
				if (($fireOrder->type == 'ballistic')) { //original order exists!
					//if it's targeted on unit - retarget on hex
					if ($fireOrder->targetid != -1) {
						$targetship = $gamedata->getShipById($fireOrder->targetid);
						//insert correct target coordinates: last turns' target position
						$targetPos = $targetship->getHexPos();
						$fireOrder->x = $targetPos->q;
						$fireOrder->y = $targetPos->r;
						$fireOrder->targetid = -1; //correct the error
						$fireOrder->calledid = -1; //just in case
					}
						
                    $originalFireOrder = $fireOrder;
                    break; //no need to search further
                }
		}          				
        if($originalFireOrder==null) return; //no appropriate fire order, end of work
    
		$updatedFireOrder = $originalFireOrder; // Start as original fire order.
			
        $shooter = $gamedata->getShipById($originalFireOrder->shooterid);
        $movement = $shooter->getLastTurnMovement($originalFireOrder->turn);
        $posLaunch = $movement->position;//at moment of launch!!!
        $hexTarget = new OffsetCoordinate($originalFireOrder->x, $originalFireOrder->y);

        $rolled = Dice::d(100);
        $fireOrder->rolled = $rolled; //...and hit, regardless of value rolled


        if ($rolled > 75) { //deviation!
            $maxdis = $posLaunch->distanceTo($hexTarget);
            $dis = Dice::d(6); //deviation distance            
            $dis = min($dis, floor($maxdis));
            $direction = Dice::d(6)-1; //deviation direction

            $hexTarget = $hexTarget->moveToDirection($direction, $dis);
                
            $originalFireOrder->pubnotes .= " deviation from " . $originalFireOrder->x . ' ' . $originalFireOrder->y;
            $originalFireOrder->x = $hexTarget->q; //Update fireOrder
            $originalFireOrder->y = $hexTarget->r;
            $originalFireOrder->pubnotes .= " to " . $originalFireOrder->x . ' ' . $originalFireOrder->y;
            $originalFireOrder->pubnotes .= ". Shot deviates $dis hexes. ";
                              
            $updatedFireOrder = $originalFireOrder; //Update fire order with new landing coordinates for next part.
        }				       
			    
		//Now used updated x and y coordinates after scatter to find ships in range.		    
	    $finalHexTarget = new OffsetCoordinate($updatedFireOrder->x, $updatedFireOrder->y);
        $mineRange = $this->mineRangeArray[$originalFireOrder->firingMode];
        $IFFSystem = $shooter->getIFFSystem();
		
		if ($IFFSystem){ ////Returns true if ship has the Identify Friend or Foe Enhancment.
		    $mineTarget = $this->getClosestEnemyShip($gamedata, $shooter, $finalHexTarget, $mineRange); //Find the closest viable enemy ship only, then attack it.
		}else{	
		    $mineTarget = $this->getClosestShip($gamedata, $shooter, $finalHexTarget, $mineRange); //Just find the closest viable ship, then attack it.			
		}        
    
		if ($mineTarget instanceof BaseShip || $mineTarget instanceof FighterFlight) { // Check if $mineTarget is a valid ship/fighter flight
			$newFireOrder = new FireOrder(
				-1, "ballistic", $shooter->id, $mineTarget->id,
				$this->id, -1, $gamedata->turn, $originalFireOrder->firingMode, 
				0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
				0,0,'SecondAttack',-1 //X, Y, damageclass, resolutionorder
			);		
			$newFireOrder->addToDB = true;
			$this->fireOrders[] = $newFireOrder;
		    $originalFireOrder->pubnotes .= "<br>Mine launched. ";								
		}else{ //No valid targets.
		    $originalFireOrder->pubnotes .= "<br>Mine launched, but no valid target to attack this turn.";
			$this->createLoiteringMine($gamedata, $originalFireOrder, $shooter, $mineRange, $IFFSystem);
		}

	} //endof beforeFiringOrderResolution


    public function createLoiteringMine($gamedata, $fireOrder, $shooter, $mineRange, $IFFSystem){
		$mine = null;
		switch($mineRange){ //We can discern mine type here by it's range.
			case 2:
				$mine = new spawnCaptorKLH($gamedata->id, $shooter->userid, "Kovost-H Captor Mine", $shooter->slot);					
				break;
			case 3:
				$mine = new spawnCaptorKLB($gamedata->id, $shooter->userid, "Kovost Captor Mine", $shooter->slot);
				break;
			case 5:
				$mine = new spawnCaptorKLW($gamedata->id, $shooter->userid, "Kovost-W Captor Mine", $shooter->slot);			
				break;
			default:
				$mine = new spawnCaptorKLB($gamedata->id, $shooter->userid, "Kovost Captor Mine", $shooter->slot);
				break;			
		}	
		if($mine !== null) {
			$shipid = Manager::insertSingleShip($gamedata, $mine, $shooter->userid);
			$mine->spawned = $gamedata->turn;

			if($IFFSystem){ //Pass IFF enhancement on to newly created mine!
				Manager::insertSingleEnhancement($gamedata, $shipid, 'IFF_SYS', 1, 'Identify Friend or Foe (IFF) System');
			}

			//Create new movement orders to $targetPos.
			$deployMine = new MovementOrder(null, "deploy", new OffsetCoordinate($fireOrder->x, $fireOrder->y), 0, 0, 0, 0, 0, false, $gamedata->turn, 0, 0);
			//Add movement order to database
			Manager::insertSingleMovement($gamedata->id, $shipid, $deployMine);	

			// Initialize weapon loading so the mine doesn't spawn uncharged
			$mine->id = $shipid;
			SystemData::initSystemData($gamedata->turn, $gamedata->id);
			foreach ($mine->systems as $system) {
				$system->setInitialSystemData($mine);
				if ($system instanceof Weapon) {
					$load = $system->getStartLoading();
					if ($load) {
						$load->loading = $system->loadingtime; // Set to fully loaded
						SystemData::addDataForSystem($system->id, 0, $shipid, $load->toJSON());
					}
				}
			}
			Manager::insertSystemData(SystemData::getAndPurgeAllSystemData());

			//Now create a note so we remember what turn mine was created.
			$note = new IndividualNote(
						-1,
						$gamedata->id,
						1, // Set to 1 so the note is loaded in replay for all turns
						1, // Set to 1
						$shooter->id,
						$this->id,
						$shipid,
						"New Mine spawned",
						$gamedata->turn
			);

			Manager::insertIndividualNote($note);

		}
	}


	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote){ //Search all notes, they should be process in order so the latest event applies.
        	$ship = $gamedata->getShipById($currNote->notekey);		
			$ship->spawned = $currNote->notevalue; //Update spawned value to turn that mine was created.						
		}

		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
		$this->individualNotes = array();		
	} //endof function onIndividualNotesLoaded


	public function getClosestShip($gamedata, $mine, $pos, $maxRange = 0){

	    if ($pos instanceof BaseShip) {
	        $pos = $pos->getHexPos();
	    }

	    if (!($pos instanceof OffsetCoordinate)) {
	        throw new Exception("only OffsetCoordinate supported");
	    }

	    $closestShips = array(); // Array to store equally closest ships
	    $closestDistance = 100; // Initialize with a large value

	    foreach ($gamedata->ships as $ship){
	        if ($ship->unavailable) continue;
	        if ($ship->isTerrain()) continue; 
	        if ($ship->mine) continue;           
	        if ($ship->isDestroyed()) continue;                         

			$jammerValue = $ship->getSpecialAbilityValue("Jammer", array("shooter" => $mine, "target" => $ship));
			$effectiveMaxRange = ($jammerValue > 0) ? floor($maxRange / 2) : $maxRange;

	        $distance = Mathlib::getDistanceHex($ship->getHexPos(), $pos);

	        if ($distance <= $effectiveMaxRange && $distance < $closestDistance){
	            // New closest distance found, clear the array and add this ship
	            $closestShips = array($ship);
	            $closestDistance = $distance;
	        } elseif ($distance == $closestDistance) {
	            // Add ship to equally close ships
	            $closestShips[] = $ship;
	        }
	    }

	    // Randomly select among equally close ships
	    if (!empty($closestShips)) {
	        $randomIndex = array_rand($closestShips);
	        return $closestShips[$randomIndex];
	    } else {
	        return null; // No ships found within range
	    }
	}

	
	public function getClosestEnemyShip($gamedata, $mine, $pos, $maxRange = 0){

	    if ($pos instanceof BaseShip) {
	        $pos = $pos->getHexPos();
	    }

	    if (!($pos instanceof OffsetCoordinate)) {
	        throw new Exception("only OffsetCoordinate supported");
	    }

	    $closestShips = array(); // Array to store equally closest ships
	    $closestDistance = 100; // Initialize with a large value

	    foreach ($gamedata->ships as $ship){
	        if ($ship->unavailable) continue;
	        if ($ship->isTerrain()) continue;  
	        if ($ship->mine) continue;     
			if ($ship->team == $mine->team)	        
				continue;
	        if ($ship->isDestroyed()) continue;              
		
			$jammerValue = $ship->getSpecialAbilityValue("Jammer", array("shooter" => $mine, "target" => $ship));
			$effectiveMaxRange = ($jammerValue > 0) ? floor($maxRange / 2) : $maxRange;		

	        $distance = Mathlib::getDistanceHex($ship->getHexPos(), $pos);

	        if ($distance <= $effectiveMaxRange && $distance < $closestDistance){
	            // New closest distance found, clear the array and add this ship
	            $closestShips = array($ship);
	            $closestDistance = $distance;
	        } elseif ($distance == $closestDistance) {
	            // Add ship to equally close ships
	            $closestShips[] = $ship;
	        }
	    }

	    // Randomly select among equally close ships
	    if (!empty($closestShips)) {
	        $randomIndex = array_rand($closestShips);
	        return $closestShips[$randomIndex];
	    } else {
	        return null; // No ships found within range
	    }
	}

	    
    public function calculateHitBase($gamedata, $fireOrder)
    {
		if ($fireOrder->targetid == -1) {				
			$fireOrder->needed = 100;				
			$fireOrder->updated = true;
		} else{
			//Mine direct shot - default routine will do!
			$this->hextarget = false;
			weapon::calculateHitBase($gamedata, $fireOrder);
		}
	}	


	public function fire($gamedata, $fireOrder){
		if($fireOrder->targetid == -1) { //initial "tareting location".
			$fireOrder->shotshit++;	//This however does NOT cause the hex targeted shot to explode on hex, or cause hit on ship in hex :|
	       	$fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!		     		
		}else{ //Normal fire routine for direct shots.			
			$this->hextarget = false;
			weapon::fire($gamedata, $fireOrder);		
		}
	}//endof fire


	public function getFiringHex($gamedata, $fireOrder) {

		$launchPos = null;
		$allFireOrders = $this->getFireOrders($gamedata->turn);
		$newLaunchPos = null;			
		    
        foreach($allFireOrders as $launched){	       	

			if ($launched->targetid == -1) { //Find hex targeted fire order
	            $hexTarget = new OffsetCoordinate($launched->x, $launched->y);
	            $newLaunchPos = $hexTarget; //Update variable with launch pos for direct fire.	            
			    break;				       
			    }			 
		} 
			         			        
		if ($fireOrder->targetid != -1) { //Fireorder is for mine attack, not launcher.
            $launchPos = $newLaunchPos; 	            			       
	    }		             			        
				
		if($launchPos == null) $launchPos = parent::getFiringHex($gamedata, $fireOrder); //Go back to normal function for instances when getFiringHex is being called for hex targeted shot. 
			return $launchPos;

	} //endof getFiringHex    


	public function notActuallyHexTargeted($fireOrder)
	{
		if ($fireOrder->targetid != -1)	{
			$this->hextarget = false;
		}else{ 
		return;
		}	
	}	
	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Range"] = $this->range; //Don't need to display distanceRange like Missile Racks do :)
		$this->data["Special"] = 'Available firing modes depend on ammo, tracked in Ammo Magazine system.';
		$this->data["Special"] .= '<br>Hex-targeted weapon with a 25% chance to scatter.';
		$this->data["Special"] .= '<br>Will try to attack the closest ship from the hex where it lands, up to its maximum radius (halved against units with Jammer) and can be intercepted.';	
		$this->data["Special"] .= '<br>Damage, Fire Control and Range from target hex depends on ammo type:';	
		$this->data["Special"] .= '<br>  - Basic: 1d10 + 16 damage, +40 to hit and 3 hex radius.';	
		$this->data["Special"] .= '<br>  - Wide-Range: 1d10 + 12 damage, +30 to hit and 5 hex radius.';	
		$this->data["Special"] .= '<br>  - Heavy: 1d10 + 24 damage, +25 to hit and 2 hex radius.';	
		$this->data["Special"] .= '<br>If no targets are available the mine will remain in place until destroyed or finds a target. See FV FAQ for details about Mines.';																				
	}	

        public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->specialPosNoLauncher = $this->specialPosNoLauncher; 
            $strippedSystem->alwaysHideFireOrders = $this->alwaysHideFireOrders;			                             
            return $strippedSystem;
        }
	
} //endof class BallisticMineLauncher


class AbbaiMineLauncher extends BallisticMineLauncher{
	public $name = "AbbaiMineLauncher";
    public $displayName = "Mine Launcher";
    public $iconPath = "AbbaiMineLauncher.png";

    // Overrides parent — Abbai launcher spawns its own mine types.
    public $spawnableClasses = [
        'spawnCaptorWotcrAbbaiA',
        'spawnCaptorWotcrAbbaiB',
    ];
	
    public $range = 20;
    public $distanceRange = 40;   
    
	//basic launcher data, before being modified by actual missiles
	protected $basicFC=array(0,0,0);
	protected $basicRange = 20;
	protected $basicDistanceRange = 40; //Just so scattering past 30 hexes doesn't cause an issue.
	
	function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base=false)
	{
		if ( $maxhealth == 0 ) $maxhealth = 6;
            	if ( $powerReq == 0 ) $powerReq = 3;

			//Set mine availability! (Cannot fire missiles like S-Rack)
		if(!$this->availableAmmoAlreadySet){
			$this->ammoClassesArray = array();
			$this->ammoClassesArray[] =  new AmmoBistifA();			
			$this->ammoClassesArray[] =  new AmmoBistifB();					
			$this->availableAmmoAlreadySet = true;
		}	            		
            						
		parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $magazine, $base); //Parent routines take care of the rest
	}   

	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		$this->data["Range"] = $this->range; //Don't need to display distanceRange like Missile Racks do :)
		$this->data["Special"] = 'Available firing modes depend on ammo, tracked in Ammo Magazine system.';
		$this->data["Special"] = 'Hex-targeted weapon with a 25% chance to scatter.';
		$this->data["Special"] .= '<br>Will try to attack the closest ship from the hex where it detonates, up to its maximum radius and can be intercepted.';
		$this->data["Special"] .= '<br>If several ships are of equal distance to the mines, it will choose a target randomly.';		
		$this->data["Special"] .= '<br>Damage, Fire Control and Range from target hex depends on ammo type:';	
		$this->data["Special"] .= '<br>  - Basic: 12 damage, +10 to hit and 4 hex radius.';	
		$this->data["Special"] .= '<br>  - Wide-Range: 12 damage, +10 to hit and 7 hex radius.';	
		$this->data["Special"] .= '<br>If no targets are available the mine will remain in place until destroyed or it finds a target.';															
		$this->data["Special"] .= '<br>See Fiery Void FAQ for more details about Mines.';		
	}	
	
    public function createLoiteringMine($gamedata, $fireOrder, $shooter, $mineRange, $IFFSystem){
		$mine = null;
		switch($mineRange){ //We can discern mine type here by it's range.
			case 4:
				$mine = new spawnCaptorWotcrAbbaiA($gamedata->id, $shooter->userid, "Bistif-A Captor Mine", $shooter->slot);					
				break;
			case 7:
				$mine = new spawnCaptorWotcrAbbaiB($gamedata->id, $shooter->userid, "Bistif-B Captor Mine", $shooter->slot);
				break;
			default:
				$mine = new spawnCaptorWotcrAbbaiA($gamedata->id, $shooter->userid, "Bistif-A Captor Mine", $shooter->slot);
				break;			
		}	
		if($mine !== null) {
			$shipid = Manager::insertSingleShip($gamedata, $mine, $shooter->userid);
			$mine->spawned = $gamedata->turn;

			if($IFFSystem){ //Pass IFF enhancement on to newly created mine!
				Manager::insertSingleEnhancement($gamedata, $shipid, 'IFF_SYS', 1, 'Identify Friend or Foe (IFF) System');
			}

		//Create new movement orders to $targetPos.
        $deployMine = new MovementOrder(null, "deploy", new OffsetCoordinate($fireOrder->x, $fireOrder->y), 0, 0, 0, 0, 0, false, $gamedata->turn, 0, 0);
		//Add movement order to database
		Manager::insertSingleMovement($gamedata->id, $shipid, $deployMine);	

        // Initialize weapon loading so the mine doesn't spawn uncharged
        $mine->id = $shipid;
        SystemData::initSystemData($gamedata->turn, $gamedata->id);
        foreach ($mine->systems as $system) {
            $system->setInitialSystemData($mine);
            if ($system instanceof Weapon) {
                $load = $system->getStartLoading();
                if ($load) {
                    $load->loading = $system->loadingtime; // Set to fully loaded
                    SystemData::addDataForSystem($system->id, 0, $shipid, $load->toJSON());
                }
            }
        }
        Manager::insertSystemData(SystemData::getAndPurgeAllSystemData());

			//Now create a note so we remember what turn mine was created.
			$note = new IndividualNote(
						-1,
						$gamedata->id,
						1, // Set to 1 so the note is loaded in replay for all turns
						1, 
						$shooter->id,
						$this->id,
						$shipid,
						"New Mine spawned",
						$gamedata->turn
			);

			Manager::insertIndividualNote($note);

		}
	}


} //endof class AbbaiMineLauncher

?>

