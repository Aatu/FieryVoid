<?php


class Weapon extends ShipSystem{
 
	/*all (or almost all) variables will come in array form too - so they can change with mode changes*/
	/*array should be either empty (attribute does not change) or filled for all firing modes*/
    public $weapon = true;

    public $name = null;
    public $displayName ="";
    public $priority = 1;
	public $priorityArray = array();

    public $animation = "none";
	public $animationArray = array();
    public $animationImg = null;
	public $animationImgArray = array();
    public $animationImgSprite = 0;
	public $animationImgSpriteArray = array();
    public $animationColor = null;
	public $animationColorArray = array();
    public $animationColor2 = array(255, 255, 255);
	public  $animationColor2Array = array();
    public $animationWidth = 3;
	public $animationWidthArray = array();
    public $animationExplosionScale = 0.25;
	public $animationExplosionScaleArray = array();
    public $animationExplosionType = "normal";
	public $animationExplosionTypeArray = array();
    public $explosionColor = array(250, 230, 80);
	public  $explosionColorArray = array();
    public $trailLength = 40;
	public $trailLengthArray = array();
    public $trailColor = array(248, 216, 65);
	public $trailColorArray = array();
    public $projectilespeed = 17;
	public $projectilespeedArray = array();

    public $rangePenalty = 0;
	public $rangePenaltyArray = array();
    public $rangeDamagePenalty = 0;
	public $rangeDamagePenaltyArray = array();
    private $dp = 0; //damage penalty - fraction of shot that gets wasted!
	private $dpArray = array(); //array of damage penalties for all modes! - filled automatically
	private $rp = 0; //range penalty - number of crits ! effect is reflected on $range anyway, no need to hold an array
    public $range = 0;
	public $rangeArray = array();
    protected $distanceRange = 0;
    public $fireControl =  array(0, 0, 0); // fighters, <mediums, <capitals
	public $fireControlArray = array();


    public $loadingtime = 1;
	public $loadingtimeArray = array();
    public $turnsloaded;
	public $turnsloadedArray = array();

    public $overloadable = false;

    public $normalload = 0;
    public $alwaysoverloading = false;
    public $autoFireOnly = false; //ture for weapons that should never be fired manually
    public $overloadturns = 0;
    public $overloadshots = 0;
    public $extraoverloadshots = 0;
    public $extraoverloadshotsArray = array();	

    public $doNotIntercept = false; //for attacks that are not subject to interception at all - like fields and ramming
    public $uninterceptable = false;
    public $uninterceptableArray = array();
    public $canInterceptUninterceptable = false; //able to intercept shots that are normally uninterceptable, eg. Lasers
    public $noInterceptDegradation = false; //if true, this weapon will be intercepted without degradation!
    public $intercept = 0; //intercept rating
    public $freeintercept = false;  //can intercept fire directed at other unit?
    public $hidetarget = false;
    public $duoWeapon = false;
    public $dualWeapon = false;
    public $canChangeShots = false;
    public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?
	public $isRammingAttack = false; //true means hit chance calculations are completely different, relying on speed

    public $shots = 1;
	public  $shotsArray = array();
    public $defaultShots = 1;
	public  $defaultShotsArray = array();

    public $rof = 1; //??? I do not see any use of this variable, besides one point in .js checking if it's 0...

	
    public $grouping = 0;
	public $groupingArray = array();
    public $guns = 1;
	public $gunsArray = array();




    // Used to indicate a parent in case of dualWeapons
    public $parentId = -1;

    public $firingMode = 1;
    public $firingModes = array( 1 => "Standard"); //just a convenient name for firing mode
    public $damageType = ""; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	public $damageTypeArray = array();
    public $weaponClass = ""; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!
	public $weaponClassArray = array();

	//damage type-related variables
	public $isLinked = false; //for linked weapons - they will all hit the exact same system!
	public $systemKiller = false;	//for custom weapons - increased chance to hit system and not Structure
	protected $systemKillerArray = array();
	public $noOverkill = false; //this will let simplify entire Matter line enormously!
	protected $noOverkillArray = array();
	public $ballistic = false; //this is a ballictic weapon, not direct fire
	public $ballisticIntercept = false; //can intercept, but only ballistics
        public $hextarget = false; //this weapon is targeted on hex, not unit
	public $noPrimaryHits = false; //PRIMARY removed from outer charts if true
	
    public $minDamage, $maxDamage;
	public $minDamageArray = array();
	public $maxDamageArray = array();

    public $exclusive = false; //for fighter guns - exclusive weapon can't bve fired together with others

    public $useOEW = true;
    public $calledShotMod = -8;

    public $possibleCriticals = array(14=>"ReducedRange", 19=>"ReducedDamage", 25=>array("ReducedRange","ReducedDamage"));

	
	
	
	
	
	
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output = 0){
        parent::__construct($armour, $maxhealth, $powerReq, $output );

        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
	    
	    //things that are calculated and can change with mode (and are displayed in GUI) - for all modes...
	    foreach($this->firingModes as $i=>$modeName){	    
		$this->changeFiringMode($i);
		$this->setMinDamage(); $this->minDamageArray[$i] = $this->minDamage;
		$this->setMaxDamage(); $this->maxDamageArray[$i] = $this->maxDamage;
	    }
	    $this->changeFiringMode(1); //reset mode to basic
    }
	
	


    public function getRange($fireOrder)
    {
        return $this->range;
    }

    public function getWeaponForIntercept(){
        return $this;
    }

    public function getCalledShotMod(){
        return $this->calledShotMod;
    }

    protected function getWeaponHitChanceMod($turn){
        return 0;
    }

    public function getRakeSize(){
	return $this->raking;    
    }
	
	
    public function getAvgDamage(){
        $min = $this->minDamage;
        $max = $this->maxDamage;
        $avg = round(($min+$max)/2);
        return $avg;
    }


    public function effectCriticals(){
	$this->dp=0;
	$this->rp=0;
        parent::effectCriticals();
	        
        foreach ($this->criticals as $crit){
		if ($crit instanceof ReducedRange) $this->rp++;
		if ($crit instanceof ReducedDamage) $this->dp++;
	}
	    $rp = $this->rp;
	    $dp = $this->dp;
	//min/max damage arrays are created automatically, so they will always be present
	if($dp>0){
		//damage penalty: 20% of variance or straight 2, whichever is bigger; hold that as a fraction, however! - low rolls should be affected lefss than high ones, after all
		foreach($this->firingModes as $dmgMode=>$modeName){
			$mod = $dp*max(2, 0.2*($this->maxDamageArray[$dmgMode]-$this->minDamageArray[$dmgMode]) );//2 or 20% of variability, whichever is higher
			$avgDmg = ($this->maxDamageArray[$dmgMode]+$this->minDamageArray[$dmgMode])/2;
			if($avgDmg>0){
				$this->dpArray[$dmgMode] = $mod/$avgDmg;//convert to fraction -  of average result 
			}else{
				$this->dpArray[$dmgMode] = 1; //100% reduction
			}
			$this->dpArray[$dmgMode] = min(0.9,$this->dpArray[$dmgMode]); //let's not allow to reduce below something ;) - say, max damage reduction is 90%
			$this->minDamageArray[$dmgMode] = floor($this->minDamageArray[$dmgMode]*(1-$this->dpArray[$dmgMode]));
			$this->maxDamageArray[$dmgMode] = floor($this->maxDamageArray[$dmgMode]*(1-$this->dpArray[$dmgMode]));
		}
	}
		
	//range doesn't have to be an array, but may be
	while($rp>0){
		if($this->rangePenalty>=1){
			$this->rangePenalty += 1;
		}else if($this->rangePenalty>0){
			$this->rangePenalty = 1/(round(1/$this->rangePenalty)-1);
		}else{ //no range penalty - range itself will be reduced! 
			//no calculations needed
		}
		foreach($this->rangePenaltyArray As $dmgMode=>$penaltyV){
			if($this->rangePenaltyArray[$dmgMode]>=1){ //long range
				$this->rangePenaltyArray[$dmgMode] += 1;
			}else if($this->rangePenalty>0){ //short range
				$this->rangePenaltyArray[$dmgMode] = 1/(round(1/$this->rangePenaltyArray[$dmgMode])-1);
			}else{ //no range penalty - affect range itself
				if(!isset($this->rangeArray[$dmgMode]))$this->rangeArray[$dmgMode] = $this->range;
				$this->rangeArray[$dmgMode] = floor($this->rangeArray[$dmgMode] * 0.8); //loss 20% range for very crit
			}
		}
		$rp--;
	}
	    
	//make sure data from table is transferred to current variables
	$this->changeFiringMode($this->firingMode);
    } //endof function effectCriticals
	

    public function getNormalLoad(){
        if ($this->normalload == 0){
            return $this->getLoadingTime();
        }
        return $this->normalload;
    }

    public function getLoadingTime(){
        return $this->loadingtime;
    }

    public function getTurnsloaded(){
        return $this->turnsloaded;
    }

	/*if this weapon was to be used for interception of indicated shot - how high intercept mod would be?
		assume that intercept itself is legal
	*/
	public function getInterceptionMod($gamedata, $intercepted){
		$interceptMod = 0;
		$shooter = $gamedata->getShipById($intercepted->shooterid);
		$interceptedWeapon = $shooter->getSystemById($intercepted->weaponid);		
		if($interceptedWeapon->hextarget) return 0;//can't intercept uninterceptable or hextarget weapon!
		if($this->ballisticIntercept && (!($interceptedWeapon->ballistic))) return 0;//can't intercept non-ballistic if weapon can intercept only ballistics!
		
		$interceptMod = $this->getInterceptRating($gamedata->turn); 
		if(!($interceptedWeapon->ballistic || $interceptedWeapon->noInterceptDegradation)){//target is neither ballistic weapon nor has lifted degradation, so apply degradation!
			for($i = 0;$i<$intercepted->numInterceptors;$i++){
				$interceptMod -= 1; //-1 for each already intercepting weapon
			}
		}
		$interceptMod=max(0,$interceptMod) *5;//*5: d20->d100
		return $interceptMod;
	}//endof  getInterceptionMod
	
	
    public function firedOnTurn($turn){
        if ($this instanceof DualWeapon && isset($this->turnsFired[$turn])) return true;
        foreach ($this->fireOrders as $fire){
            if ($fire->type != "selfIntercept" && $fire->weaponid == $this->id && $fire->turn == $turn){
                return true;
            }
            else if($fire->type == "selfIntercept" && checkForSelfInterceptFire::checkFired($this->id, $turn)){
                return true;
            }
        } 
        return false;
    }

    private function formatFCValue($fc)
    {
        if ($fc === null)
            return "-";

        return number_format(($fc * 5), 0);
    }

    public function setSystemDataWindow($turn){

        $this->data["Resolution Priority"] = $this->priority;
        $this->data["Loading"] = $this->getTurnsloaded()."/".$this->getNormalLoad();

        $dam = $this->minDamage."-".$this->maxDamage;
        if ($this->minDamage == $this->maxDamage)
            $dam = $this->maxDamage;

        $this->data["Damage"] = $dam;

        if ($this->rangePenalty > 0){
            $this->data["Range penalty"] = number_format(($this->rangePenalty * 5), 2) . " per hex";
        }else{
            $this->data["Range"] = $this->range;
		if ($this->distanceRange > $this->range) $this->data["Range"] .= '/' . $this->distanceRange;
        }

        //public $fireControl =  array(0, 0, 0); // fighters, <mediums, <capitals

        $fcfight = $this->formatFCValue($this->fireControl[0]);
        $fcmed = $this->formatFCValue($this->fireControl[1]);
        $fccap = $this->formatFCValue($this->fireControl[2]);

        $this->data["Fire control (fighter/med/cap)"] = "$fcfight/$fcmed/$fccap";

        if ($this->guns > 1){
            $this->data["Number of guns"] = $this->guns;
        }

        if ( !($this instanceof Pulse) && $this->shots > 1){
            $this->data["Number of shots"] = $this->shots;
        }

        if ($this->intercept > 0){
            $this->data["Intercept"] = "-".$this->intercept*5;
        }

	if($this->damageType != '') $this->data["Damage type"] = $this->damageType;
	if($this->weaponClass != '') $this->data["Weapon type"] = $this->weaponClass;


        $misc = array();

        if ($this->overloadturns > 0){
            $misc[] = " OVERLOADABLE";
        }
        if ($this->uninterceptable)
            $misc[] = " UNINTERCEPTABLE";

        //if (sizeof($misc)>0)
            //$this->data["Misc"] = $misc;

        parent::setSystemDataWindow($turn);
    }

    public function onAdvancingGamedata($ship)
    {
        $data = $this->calculateLoading();
        if ($data)
            SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
    }

	
    public function setSystemData($data, $subsystem)
    {
        $array = json_decode($data, true);
        if (!is_array($array))
            return;

        foreach ($array as $i=>$entry)
        {
            if ($i == "loading"){
                if(sizeof($entry) == 4){
                    $loading = new WeaponLoading(
                        $entry[1],
                        $entry[2],
                        $entry[3],
                        $entry[4],
                        $this->loadingtime,
                        $this->firingMode
                    );
                }
                elseif(sizeof($entry) == 5){
                    $loading = new WeaponLoading(
                        $entry[1],
                        $entry[2],
                        $entry[3],
                        $entry[4],
                        $entry[5],
                        $this->firingMode
                    );
                }else{
                    $loading = new WeaponLoading(
                        $entry[1],
                        $entry[2],
                        $entry[3],
                        $entry[4],
                        $entry[5],
                        $entry[6]
                    );
                }

                $this->setLoading($loading);
            }
        }
    }

    public function setInitialSystemData($ship)
    {
        $data = $this->getStartLoading();
        if ($data)
            SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
    }

    public function getStartLoading()
    {
//        return new WeaponLoading($this->getNormalLoad(), 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
        return new WeaponLoading($this->getNormalLoad(), $this->overloadshots, 0, $this->overloadturns, $this->getLoadingTime(), $this->firingMode);
    }

    public function setLoading( $loading )
    {
        if (!$loading)
            return;

        $this->overloadturns = $loading->overloading;
        $this->overloadshots = $loading->extrashots;

        // turnsloaded is set by boostable weapons that are ready.
        // Keep your hands of in that case.
        if(!($this->boostable && ($this->loadingtime <= $this->getTurnsloaded()))){
            $this->turnsloaded = $loading->loading;
        }

        $this->loadingtime = $loading->loadingtime;
        $this->firingMode = $loading->firingmode;
    }

    protected function getAmmo($fireOrder)
    {
        return null;
    }

    protected function getLoadedAmmo()
    {
        return 0;
    }

	
    public function calculateLoading()
    {
        $normalload = $this->getNormalLoad();
        if (TacGamedata::$currentPhase == 2)
        {
            if ( $this->isOfflineOnTurn(TacGamedata::$currentTurn) )
            {
                return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            }
            else if ($this->ballistic && $this->firedOnTurn(TacGamedata::$currentTurn) )
            {
                return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            }
            else if (!$this->isOverloadingOnTurn(TacGamedata::$currentTurn))
            {
                return new WeaponLoading($this->getTurnsloaded(), 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            }
        }
        else if (TacGamedata::$currentPhase == 4)
        {
           return $this->calculatePhase4Loading();
        }
        else if (TacGamedata::$currentPhase == 1)
        {
            if ($this->overloadshots === -1)
            {
                return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            }
            else
            {
                $newloading = $this->getTurnsloaded()+1;
                if ($newloading > $normalload)
                    $newloading = $normalload;

                $newExtraShots = $this->overloadshots;
                $overloading = $this->overloadturns+1;
                if ($overloading >= $normalload && $newExtraShots == 0)
                    $newExtraShots = $this->extraoverloadshots;

                if ($overloading > $normalload)
                    $overloading = $normalload;

                return new WeaponLoading($newloading, $newExtraShots, $this->getLoadedAmmo(), $overloading, $this->getLoadingTime(), $this->firingMode);
            }

        }

        return new WeaponLoading($this->getTurnsloaded(), $this->overloadshots, $this->getLoadedAmmo(), $this->overloadturns, $this->getLoadingTime(), $this->firingMode);
    }

	
    private function calculatePhase4Loading()
    {
        if ($this->ballistic)
            return null;


        if ($this->firedOnTurn(TacGamedata::$currentTurn)){

            if ($this->overloadshots > 0)
            {
                $newExtraShots = $this->overloadshots-1;
                //if you have extra shots use them
                if ($newExtraShots === 0)
                {
                    //if extra shots are reduced to zero, go to cooldown
                    return new WeaponLoading(0, -1, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
                }
                else
                {
                    //if you didn't use the last extra shot, keep on going.
                    return new WeaponLoading($this->getTurnsloaded(), $newExtraShots, $this->getLoadedAmmo(), $this->overloadturns, $this->getLoadingTime(), $this->firingMode);
                }
            }
            else
            {
                //Situation normal, no overloading -> lose loading
                return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            }

        }else{
              //cannot save the extra shots from everload -> lose loading and cooldown
            if ($this->overloadshots > 0 && $this->overloadshots < $this->extraoverloadshots){
                if($this->isOverloadingOnTurn(TacGamedata::$currentTurn)){
                    return new WeaponLoading(1, 0, $this->getLoadedAmmo(), 1, $this->getLoadingTime(), $this->firingMode);
                }
                else{
                    return new WeaponLoading(1, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
                }
            }
        }

        return new WeaponLoading($this->getTurnsloaded(), $this->overloadshots, $this->getLoadedAmmo(), $this->overloadturns, $this->getLoadingTime(), $this->firingMode);
    }

    public function beforeTurn($ship, $turn, $phase){
        parent::beforeTurn($ship, $turn, $phase);
    }

    public function getDamage($fireOrder){
        return 0;
    }

    public function setMinDamage(){

    }

    public function setMaxDamage(){

    }

    public function calculateRangePenalty($pos, $target){
        $targetPos = $target->getCoPos();
        $dis = mathlib::getDistanceHex($pos, $targetPos);

        $rangePenalty = ($this->rangePenalty*$dis);
        $notes = "shooter: ".$pos["x"].",".$pos["y"]." target: ".$targetPos["x"].",".$targetPos["y"]." dis: $dis, rangePenalty: $rangePenalty";
        return Array("rp"=>$rangePenalty, "notes"=>$notes);
    }

	
	
    
     protected function isFtrFiringNonBallisticWeapons($shooter, $fireOrder){
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
    } //endof function isFtrFiringNonBallisticWeapons
	
	
	/*Marcin Sawicki: is there a chance that defender has choice of target section? */
	public function isTargetAmbiguous($gamedata, $fireOrder){
		if($fireOrder->calledid != -1) return false; //no choice for called shot
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);

		
		if($target == null) return true; //target is a hex rather than unit, probability of ambigousness is relatively high
		if($target instanceof FighterFlight) return false; //shot at fighter may be ambiguous, but there's no point in poostponing the decision!
		
		$pos = $shooter->getCoPos();
		$ambiguous = false;

		if($this->ballistic){
			$movement = $shooter->getLastTurnMovement($fireOrder->turn);
			$launchPos = mathlib::hexCoToPixel($movement->x, $movement->y);		
		}else{
			$launchPos = $pos;
		}
		
		if($this->ballistic){
			$ambiguous = $target->isHitSectionAmbiguousPos($launchPos, $fireOrder->turn);
		}else{
			$ambiguous = $target->isHitSectionAmbiguous($shooter, $fireOrder->turn);
		}
		return $ambiguous;
	} //endof function isTargetAmbiguous
	
	
	/*calculate base chance to hit for ramming attack*/
	public function calculateHitBaseRam($gamedata, $fireOrder){
		if ($fireOrder->calledid != -1) return 0;//can't call ramming attack!
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);
		if (($target instanceof FighterFlight) && (!($shooter instanceof FighterFlight))) return 0;//ship has no chance to ram a fighter!
		
		$hitChance = 8; //base: 40%
		
		if ($target->Enormous) $hitChance+=6;//+6 vs Enormous units
		if ($shooter->Enormous) $hitChance+=6;//+6 if ramming unit is Enormous
		if (($target->shipSizeClass >= 3) && ($shooter->shipSizeClass <3)) $hitChance += 2;//+2 if target is Capital and ramming unit is not
		if (($shooter->shipSizeClass >= 3) && ($target->shipSizeClass <3)) $hitChance -= 2;//-2 if shooter is Capital and rammed unit is not
		if (($shooter instanceof FighterFlight) && (!($target instanceof FighterFlight))) $hitChance += 4;//+4 for fighter trying to ram a ship
		$targetSpeed = abs($target->getSpeed()); //I think speed cannot be negative, but just in case ;)
		switch($targetSpeed) {
		    case 0: //+5 if the target is not moving.
			$hitChance += 5;
			break;
		    case 1://+3 if the target is moving speed 1.
			$hitChance += 3;
			break;
		    case 2://+2 if the target is moving speed 2 or 3.
		    case 3:
			$hitChance += 2;
			break;
		    case 4://+1 if the target is moving speed 4 or 5.
		    case 5:
			$hitChance += 1;
			break;
		    default: //this means >5; ‐1 for every 5 points of speed (or fraction thereof) that the target is moving faster than 5.
			$hitChance -= ceil(($targetSpeed-5)/5);
		}
		//‐1 for every level of jinking the ramming or target unit is using
		$hitChance -= Movement::getJinking($shooter, $gamedata->turn);
		$hitChance -= Movement::getJinking($target, $gamedata->turn);
		//fire control: usually 0, but units specifically designed for ramming may have some bonus!
		$hitChance += $this->fireControl[$target->getFireControlIndex()];		
		
		$hitChance = $hitChance * 5; //convert d20->d100
		
		$hitLoc = null;
		$hitLoc = $target->getHitSection($shooter, $fireOrder->turn);
	    	$target->setExpectedDamage($hitLoc, $hitChance, $this);
		
		
		$notes = $fireOrder->notes . "RAMMING, final hit chance: $hitChance";
		$fireOrder->chosenLocation = $hitLoc;
		$fireOrder->needed = $hitChance;
		$fireOrder->notes = $notes;
		$fireOrder->updated = true;
	}//endof function calculateHitBaseRam
	
	
	
    /*calculate base chance to hit (before any interception is applied) - Marcin Sawicki*/
    public function calculateHitBase($gamedata, $fireOrder){
	$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too! - certainly important for calculating hit chance...
	if ($this->isRammingAttack) return $this->calculateHitBaseRam($gamedata, $fireOrder);
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        $pos = $shooter->getCoPos();
        $jammermod = 0;
        $jinkSelf = 0;
	$jinkTarget = 0;
        $defence = 0;
        $mod = 0;
        $oew = 0;

        $hitLoc;
        $preProfileGoal;
	    
        if($this->ballistic){
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
        	$launchPos = mathlib::hexCoToPixel($movement->x, $movement->y);		
        }else{
		$launchPos = $pos;
	}
	    
	if (!$this->isInDistanceRange($shooter, $target, $fireOrder))
	{
		// Target is not in distance range. Auto-miss.
		$notes = ' Target moved out of range. ';
		$fireOrder->needed = 0; //auto-miss
		$fireOrder->notes .= $notes;
		$fireOrder->updated = true;
		return;
	}	    
	    
        $rp = $this->calculateRangePenalty($launchPos, $target);
        $rangePenalty = $rp["rp"];
        
	if($shooter instanceof FighterFlight)  $jinkSelf = Movement::getJinking($shooter, $gamedata->turn);  //count own jinking always  

        if($target instanceof FighterFlight){
            if ( (!($shooter instanceof FighterFlight)) || $this->ballistic) //non-fighters and ballistics always affected by jinking
            {
                $jinkTarget = Movement::getJinking($target, $gamedata->turn);
            }
            elseif( $jinkSelf > 0 || mathlib::getDistance($shooter->getCoPos(),  $target->getCoPos()) > 0 ){ //fighter direct fire unaffected at range 0
                $jinkTarget = Movement::getJinking($target, $gamedata->turn);
            }
        }

	$dew = $target->getDEW($gamedata->turn);
        $bdew = EW::getBlanketDEW($gamedata, $target);
        $sdew = EW::getSupportedDEW($gamedata, $target);        
        $dist = EW::getDistruptionEW($gamedata, $shooter);
        if ($this->useOEW)
        {
		$soew = EW::getSupportedOEW($gamedata, $shooter, $target);
		$oew = $shooter->getOEW($target, $gamedata->turn);
		$oew -= $dist;
		if ($oew < 0) $oew = 0;
        }else{
		$soew = 0;
		$oew = 0;
	}

        if (!($shooter instanceof FighterFlight)){
            if (Movement::isRolling($shooter, $gamedata->turn) /*&& !$this->ballistic*/){
                $mod -=3;
            }
            if (Movement::hasPivoted($shooter, $gamedata->turn) /*&& !$this->ballistic*/){
                $mod -=3;
            }
        }

	if ($fireOrder->calledid != -1){
		$mod += $this->getCalledShotMod();
		if($target->base) $mod += $this->getCalledShotMod();//called shots vs bases suffer double penalty!
        }

        if ($shooter instanceof OSAT && Movement::hasTurned($shooter, $gamedata->turn)){
            $mod -= 1;
        }

	    if($this->ballistic){ //getHitChanceMod should get explicit position only if it cannot be derived from shooter - otherwise results at rng 0 are incorrect!
		$posmod = $pos;
	    }else{
		$posmod = null;    
	    }
        $mod += $target->getHitChanceMod($shooter, $posmod, $gamedata->turn, $this);
        $mod += $this->getWeaponHitChanceMod($gamedata->turn);

        $ammo = $this->getAmmo($fireOrder);
        if ($ammo !== null){
            $mod += $ammo->getWeaponHitChanceMod($gamedata->turn);
        }
	    
        //Fighters direct fire ignore all defensive EW, be it DEW, SDEW or BDEW
	//and use OB instead of OEW
        if ($shooter instanceof FighterFlight) {
		if (Movement::getCombatPivots($shooter, $gamedata->turn)>0){
			$mod -= 1;
		}
		
		$effectiveOB = $shooter->offensivebonus;
		$firstFighter = $shooter->getSampleFighter();
		$OBcrit = $firstFighter->hasCritical("tmpsensordown");
		if ($OBcrit > 0){
	        	$effectiveOB = $shooter->offensivebonus - $OBcrit;
			$effectiveOB = max(0,$effectiveOB); //cannot bring OB below 0!
		}
		
		if (!$this->ballistic){
			$dew = 0;
			$bdew = 0;
			$sdew = 0;
			$oew = $effectiveOB;
			$soew = 0;
		}else{ //ballistics use of OB is more complicated
			$oew = 0;
			$soew = 0;
			if (!($shooter->isDestroyed() || $shooter->getFighterBySystem($fireOrder->weaponid)->isDestroyed())){
				if ($shooter->hasNavigator){// Fighter has navigator. Flight always benefits from offensive bonus.
					$oew = $effectiveOB;
				}else{ // Check if target is in current weapon arc
					$relativeBearing = $target->getBearingOnUnit($shooter);			
					if (mathlib::isInArc($relativeBearing, $this->startArc, $this->endArc)){
						// Target is in current launcher arc. Flight benefits from offensive bonus.
						// Now check if the fighter is not firing any non-ballistic weapons
						if (!$this->isFtrFiringNonBallisticWeapons($shooter, $fireOrder)) $oew = $effectiveOB;
					}
				}
        		}
		}
        }   

        if (($oew < 1) && (!($shooter instanceof FighterFlight))){
            $rangePenalty = $rangePenalty*2;
        } elseif($shooter->faction != $target->faction) {
            $jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter"=>$shooter, "target"=>$target));
	    $jammermod = $rangePenalty*$jammerValue;
        }

        if (!($shooter instanceof FighterFlight) && !($shooter instanceof OSAT)){
		$CnC = $shooter->getSystemByName("CnC");
		$mod -= ($CnC->hasCritical("PenaltyToHit", $gamedata->turn-1));
	}
        $firecontrol =  $this->fireControl[$target->getFireControlIndex()];

	$hitPenalties = $dew + $bdew + $sdew + $rangePenalty + $jinkSelf + max($jammermod, $jinkTarget);
	$hitBonuses = $oew + $soew + $firecontrol + $mod;
	$hitLoc = null;

        if($this->ballistic){
		$hitLoc = $target->getHitSectionPos($launchPos, $fireOrder->turn);
		$defence = $target->getHitSectionProfilePos($launchPos);
        }else{
		$hitLoc = $target->getHitSection($shooter, $fireOrder->turn);
		$defence = $target->getHitSectionProfile($shooter);
	}
        $goal = $defence + $hitBonuses - $hitPenalties;

        $change = round($goal*5); //d20 to d100: ($goal/20)*100 
	    $target->setExpectedDamage($hitLoc, $change, $this);

	//range penalty already logged in calculateRangePenalty... rpenalty: $rangePenalty, 
	//interception penalty not yet calculated, will be logged later
        $notes = $rp["notes"] . ", defence: $defence, DEW: $dew, BDEW: $bdew, SDEW: $sdew, Jammermod: $jammermod, , jink: $jinkSelf/$jinkTarget, OEW: $oew, SOEW: $soew, F/C: $firecontrol, mod: $mod, goal: $goal, chance: $change";
	$fireOrder->chosenLocation = $hitLoc;
        $fireOrder->needed = $change;
        $fireOrder->notes = $notes;
        $fireOrder->updated = true;
    } //endof calculateHitBase
	
	
	
	//Marcin Sawicki, October 2017: calculateHit is to be replaced by calculateHitBase! (remake of firing procedure)
	/*
    public function calculateHit($gamedata, $fireOrder){
        //debug::log("_____________");
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        //debug::log($shooter->phpclass." vs: ".$target->phpclass);
        $pos = $shooter->getCoPos();
        $jammermod = 0;
        $jinkSelf = 0;
	$jinkTarget = 0;
        $defence = 0;
        $mod = 0;
        $oew = 0;

        $hitLoc;
        $preProfileGoal;
	    
        if($this->ballistic){
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
        	$launchPos = mathlib::hexCoToPixel($movement->x, $movement->y);		
        }else{
		$launchPos = $pos;
	}
	    
            if (!$this->isInDistanceRange($shooter, $target, $fireOrder))
            {
                // Target is not in distance range. Move to next shot.
		$notes = ' Target moved out of range. ';
		$fireOrder->needed = 0; //auto-miss
		$fireOrder->notes .= $notes;
		$fireOrder->updated = true;
		    return;
            }	    
	    
        $rp = $this->calculateRangePenalty($launchPos, $target);
        $rangePenalty = $rp["rp"];

        
	if($shooter instanceof FighterFlight)  $jinkSelf = Movement::getJinking($shooter, $gamedata->turn);  //count own jinking always  


        if($target instanceof FighterFlight){
            if ( (!($shooter instanceof FighterFlight)) || $this->ballistic) //non-fighters and ballistics always affected by jinking
            {
                $jinkTarget = Movement::getJinking($target, $gamedata->turn);
            }
            elseif( $jinkSelf > 0 || mathlib::getDistance($shooter->getCoPos(),  $target->getCoPos()) > 0 ){ //fighter direct fire unaffected at range 0
                $jinkTarget = Movement::getJinking($target, $gamedata->turn);
            }
        }


	$dew = $target->getDEW($gamedata->turn);
        $bdew = EW::getBlanketDEW($gamedata, $target);
        $sdew = EW::getSupportedDEW($gamedata, $target);
        $soew = EW::getSupportedOEW($gamedata, $shooter, $target);
        $dist = EW::getDistruptionEW($gamedata, $shooter);



        if ($this->useOEW)
        {
            $oew = $shooter->getOEW($target, $gamedata->turn);
            $oew -= $dist;

            if ($oew < 0){
                $oew = 0;
            }
        }

        if (!($shooter instanceof FighterFlight)){
            if (Movement::isRolling($shooter, $gamedata->turn) && !$this->ballistic){
                $mod -=3;
            }
            if (Movement::hasPivoted($shooter, $gamedata->turn) && !$this->ballistic){
                $mod -=3;
            }
        }

	if ($fireOrder->calledid != -1){
            $mod += $this->getCalledShotMod();
		if($target->base) $mod += $this->getCalledShotMod();//called shots vs bases suffer double penalty!
        }

        if ($shooter instanceof OSAT && Movement::hasTurned($shooter, $gamedata->turn)){
            $mod -= 1;
        }

        $mod += $target->getHitChanceMod($shooter, $pos, $gamedata->turn, $this);
        $mod += $this->getWeaponHitChanceMod($gamedata->turn);

        $ammo = $this->getAmmo($fireOrder);
        if ($ammo !== null){
            $mod += $ammo->getWeaponHitChanceMod($gamedata->turn);
        }

	    

	    
        // Fighters direct fire ignore all defensive EW, be it DEW, SDEW or BDEW
	//and use OB instead of OEW
        if($shooter instanceof FighterFlight) {
		if (Movement::getCombatPivots($shooter, $gamedata->turn)>0){
			$mod -= 1;
		}
		if(!$this->ballistic){
			$dew = 0;
			$bdew = 0;
			$sdew = 0;
			$oew = $shooter->offensivebonus;
		}else{ //ballistics use of OB is more complicated
			$oew = 0;
			if(!($shooter->isDestroyed() || $shooter->getFighterBySystem($fireOrder->weaponid)->isDestroyed())){
				if($shooter->hasNavigator){// Fighter has navigator. Flight always benefits from offensive bonus.
					$oew = $shooter->offensivebonus;
				}else{ // Check if target is in current weapon arc
					$relativeBearing = $target->getBearingOnUnit($shooter);			
					if (mathlib::isInArc($relativeBearing, $this->startArc, $this->endArc)){
						// Target is in current launcher arc. Flight benefits from offensive bonus.
						// Now check if the fighter is not firing any non-ballistic weapons
						if(!$this->isFtrFiringNonBallisticWeapons($shooter, $fireOrder))$oew = $shooter->offensivebonus;
					}
				}
        		}
		}
        }   
	    
	    

        if ($oew < 1){
            $rangePenalty = $rangePenalty*2;
        } elseif($shooter->faction != $target->faction) {
            $jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter"=>$shooter, "target"=>$target));
	    $jammermod = $rangePenalty*$jammerValue;
        }
	

        if (!($shooter instanceof FighterFlight) && !($shooter instanceof OSAT)){
		$CnC = $shooter->getSystemByName("CnC");
		$mod -= ($CnC->hasCritical("PenaltyToHit", $gamedata->turn-1));
	}
        $firecontrol =  $this->fireControl[$target->getFireControlIndex()];

        $intercept = $this->getIntercept($gamedata, $fireOrder);

	    
	    
	$hitPenalties = $dew + $bdew + $sdew + $rangePenalty + $intercept + $jinkSelf + max($jammermod, $jinkTarget);
	$hitBonuses = $oew + $soew + $firecontrol + $mod;
        $preProfileGoal = $hitBonuses-$hitPenalties;

        if($this->ballistic){
		$hitLoc = $target->getHitSectionPos($launchPos, $fireOrder->turn, $preProfileGoal);
		$defence = $target->getHitSectionProfilePos($launchPos, $preProfileGoal);
        }else{
		$hitLoc = $target->getHitSection($shooter, $fireOrder->turn, $preProfileGoal);
		$defence = $target->getHitSectionProfile($shooter, $preProfileGoal);
	}
        $goal = $defence + $preProfileGoal;

        $change = round(($goal/20)*100);

	    //range penalty already logged in calculateRangePenalty... rpenalty: $rangePenalty, 
        $notes = $rp["notes"] . ", defence: $defence, DEW: $dew, BDEW: $bdew, SDEW: $sdew, Jammermod: $jammermod, , jink: $jinkSelf/$jinkTarget, intercept: $intercept, OEW: $oew, SOEW: $soew, F/C: $firecontrol, mod: $mod, goal: $goal, chance: $change";
        $fireOrder->needed = $change;
        $fireOrder->notes = $notes;
        $fireOrder->updated = true;
    } //endof calculateHit
*/
	
	
    public function getIntercept($gamedata, $fireOrder){
        $count = 0;
        $intercept = 0;
        if ($this->uninterceptable)
            return 0;

	$shooter = $gamedata->getShipById($fireOrder->shooterid);
        foreach ($gamedata->ships as $ship){
            $fireOrders = $ship->getAllFireOrders();
            foreach ($fireOrders as $fire){

                if ($fire->type == "intercept" && $fire->targetid == $fireOrder->id){

                    $deg = $count;
                    if ($this->ballistic || $this->noInterceptDegradation)
                        $deg = 0;

                    $interceptWeapon = $ship->getSystemById($fire->weaponid);
                    if (!$interceptWeapon instanceof Weapon){
                        //debug::log("DING. You cant intercept with a non-weapon....");
                        //debug::log($interceptWeapon->displayName);
                        continue;
                    }
                    $i = $interceptWeapon->getInterceptRating(TacGamedata::$currentTurn) - $deg;

                    if ($i<0
                     || $interceptWeapon->destroyed
                     || $interceptWeapon->isOfflineOnTurn(TacGamedata::$currentTurn)){
                        $i = 0;
                     }

                    if ($shooter instanceof FighterFlight) $deg--;

                    $intercept += $i;
                    $count++;
                }
            }
        }

        return $intercept;
    }

	
    public function getInterceptRating($turn){
        return $this->intercept;
    }

	
    public function getNumberOfIntercepts($gamedata, $fireOrder){
        $count = 0;
        foreach ($gamedata->ships as $ship){
            $fireOrders = $ship->getAllFireOrders();
            foreach ($fireOrders as $fire){
                if ($fire->type == "intercept" && $fire->targetid == $fireOrder->id){
                    $count++;
                }
            }
        }
        return $count;
    }

	/*some weapons have different hit chance for further shots*/
	/*default - use grouping*/
    public function getShotHitChanceMod($shotInSequence){
	    $mod = $this->grouping * $shotInSequence;
	    return $mod;
    }
	
	
	/*Marcin Sawicki - October 2017 - new version of firing procedure - assuming all data is already prepared*/
	public function fire($gamedata, $fireOrder){
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
		$target = $gamedata->getShipById($fireOrder->targetid);
		
		$fireOrder->needed -= $fireOrder->totalIntercept.
		$notes = "Interception: " . $fireOrder->totalIntercept . " sources:" . $fireOrder->numInterceptors . ", final to hit: " . $fireOrder->needed;
		$fireOrder->notes .= $notes;
		
		$pos = null; //functions will properly calculate from firing unit, which is important at range 0
		//$pos = $shooter->getCoPos();
		if ($this->ballistic){
		    $movement = $shooter->getLastTurnMovement($fireOrder->turn);
		    $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
		}
		
		$shotsFired = $fireOrder->shots; //number of actual shots fired	
		if($this->damageType == 'Pulse'){//Pulse mode always fires one shot of weapon - while 	$fireOrder->shots marks number of pulses for display purposes
			$shotsFired = 1;
		}
		for ($i=0;$i<$shotsFired;$i++){
			$needed = $fireOrder->needed;
			if($this->damageType != 'Pulse'){//non-Pulse weapons may use $grouping, too!		
				$needed = $fireOrder->needed - $this->getShotHitChanceMod($i);
			}

			//for linked shot: further shots will do the same as first!
			if($i==0){ //clear variables that may be relevant for further shots in line
				$fireOrder->linkedHit=null;
			}
			$rolled = Dice::d(100);
			if($this->isLinked && $i > 0){ //linked shot - number rolled (and effect) for furthr shots will be just the same as for first
				$rolled = $fireOrder->rolled;
			}

			//interception?
			if ($rolled > $needed && $rolled <= $needed+($fireOrder->totalIntercept*5)){ //$fireOrder->pubnotes .= "Shot intercepted. ";
			    if($this->damageType == 'Pulse'){
				$fireOrder->intercepted += $this->maxpulses;
			    }else{
				$fireOrder->intercepted += 1;
			    }
			}


			$fireOrder->notes .= " FIRING SHOT ". ($i+1) .": rolled: $rolled, needed: $needed\n";
			$fireOrder->rolled = $rolled; //might be useful for weapon itself, too - like counting damage for Anti-Matter

			//hit?
			if ($rolled <= $needed){
				$hitsRemaining=1;

				if($this->damageType == 'Pulse'){ //possibly more than 1 hit from a shot
				    $hitsRemaining = $this->rollPulses($gamedata->turn, $needed, $rolled); //this takes care of all details
				}

				while($hitsRemaining>0){
					$hitsRemaining--;
					$fireOrder->shotshit++;
					$this->beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);	
				}
			}
		}

		$fireOrder->rolled = max(1,$fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
	} //endof fire
	
	
	
	/*Marcin Sawicki - October 2017 - firing procedure is remade; old version not deleted just in case*/
    public function fireOld($gamedata, $fireOrder){
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
	
        //$this->firingMode = $fireOrder->firingMode;
	$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!

        $pos = null; //functions will properly calculate from firing unit, which is important at range 0
	//$pos = $shooter->getCoPos();
        if ($this->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
	}

        $this->calculateHit($gamedata, $fireOrder);
        $intercept = $this->getIntercept($gamedata, $fireOrder);
	$shotsFired = $fireOrder->shots; //number of actual shots fired
	
	if($this->damageType == 'Pulse'){//Pulse mode always fires one shot of weapon - while 	$fireOrder->shots marks number of pulses for display purposes
            	$shotsFired = 1;
	}
	    
        for ($i=0;$i<$shotsFired;$i++){
		$needed = $fireOrder->needed;
		if($this->damageType != 'Pulse'){//non-Pulse weapons may use $grouping, too!		
            		$needed = $fireOrder->needed - $this->getShotHitChanceMod($i);
		}
            
		//for linked shot: further shots will do the same as first!
		if($i==0){ //clear variables that may be relevant for further shots in line
			$fireOrder->linkedHit=null;
		}
		$rolled = Dice::d(100);
		if($this->isLinked && $i > 0){ //linked shot - number rolled (and effect) for furthr shots will be just the same as for first
			$rolled = $fireOrder->rolled;
			/*
			$rolled = 50; //irrelevant really, just 0<roll<100
			if($fireOrder->linkedHit==null){ //first linked shot did not hit, so neither will further ones
				$needed = 0;
				if($fireOrder->intercepted > 0) $fireOrder->intercepted++; //if first linked shot was intercepted, so will be next ones
			}else{//first linked shot did hit, and so will further ones
				$needed = 100;
			}
			*/
		}
		
		//interception?
		if ($rolled > $needed && $rolled <= $needed+($intercept*5)){ //$fireOrder->pubnotes .= "Shot intercepted. ";
		    if($this->damageType == 'Pulse'){
			$fireOrder->intercepted += $this->maxpulses;
		    }else{
			$fireOrder->intercepted += 1;
		    }
		}
		

		$fireOrder->notes .= " FIRING SHOT ". ($i+1) .": rolled: $rolled, needed: $needed\n";
		$fireOrder->rolled = $rolled; //might be useful for weapon itself, too - like counting damage for Anti-Matter
		
		//hit?
		if ($rolled <= $needed){
			$hitsRemaining=1;

			if($this->damageType == 'Pulse'){ //possibly more than 1 hit from a shot
			    $hitsRemaining = $this->rollPulses($gamedata->turn, $needed, $rolled); //this takes care of all details
			}

			while($hitsRemaining>0){
				$hitsRemaining--;
				$fireOrder->shotshit++;
				$this->beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);	
			}
		}
        }

        $fireOrder->rolled = max(1,$fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
    } //endof function fire

	
	
	
    protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
            $damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
            $this->damage($target, $shooter, $fireOrder,  $gamedata, $damage);
    }
	
	
	

	
    protected function getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location=null)  {
	    /*Location only relevant for Flash damage, which overkills to a new roll on hit table rather than to Structure*/
	    /*$damageWasDealt=true indicates this is actual overkill, instead of just passing through previously destroyed system that nevertheless was chosen as target*/
        $okSystem = null;
	$noOverkill = ( $this->noOverkill || ($this->damageType=='Piercing') );//either explicitly stated or Piercing mode shot

	if ($target instanceof FighterFlight){
            return null;
        }
	    
	if($noOverkill && $damageWasDealt){  //weapon trait: no overkill (if this is true overkill only!)
		return null;	
	}

        if ($this->damageType=='Flash'){// If overkill comes from flash damage, pick a new target in default way instead of typicaloverkill!
            $okSystem = $target->getHitSystem($shooter, $fireOrder, $this, $location); //for Flash it won't return destroyed system other than PRIMARY Structure
        }

        if ( $okSystem == null || $okSystem->isDestroyed()){ //overkill to Structure system is mounted on
            $okSystem = $target->getStructureSystem($system->location);
        }

	    
	if ($okSystem == null || $okSystem->isDestroyed())        { //overkill to PRIMARY Structure
		if($this->damageType=='Piercing'){ //Piercing does not overkill to PRIMARY
			return null;
		}else{
	    		$okSystem = $target->getStructureSystem(0);
		}
	}


        if ($okSystem == null || $okSystem->isDestroyed())        { //nowhere to overkill to
            return null;
        }

        return $okSystem;
    }//endof function getOverkillSystem
	

	
	/*collateral damage from a Flash explosion (if any), called from function damage*/
    public function doCollateralDamage($target, $shooter, $fireOrder, $gamedata, $flashDamageAmount){
	    $explosionPos = $target->getCoPos();
            $ships1 = $gamedata->getShipsInDistance($explosionPos);
            foreach($ships1 as $ship){
                if($ship === $target) continue;// make certain the target doesn't get the damage twice
		if ($ship->isDestroyed()) continue; //no point allocating
		$relativeBearing = $ship->getBearingOnUnit($target);//actual direction the damage comes from is from unit directly hit!	
                if ($ship instanceof FighterFlight){
                    foreach ($ship->systems as $fighter){
                        if ($fighter == null || $fighter->isDestroyed()){
                            continue;
			}
			//this will not be entirely correct (may allocate to inappropriate armor) - but to allocate to appropriate armor, function accepting bearing (or section hit as array) would be needed
                        $this->doDamage($ship, $shooter, $fighter, $flashDamageAmount, $fireOrder, $explosionPos, $gamedata, false);
                    }
                }else{
		    $loc = $ship->doGetHitSectionBearing($relativeBearing); //full array
		    $tmpLocation = $loc["loc"];
                    $system = $ship->getHitSystem($target, $fireOrder, $this, $tmpLocation);
                    $this->doDamage($ship, $shooter, $system, $flashDamageAmount, $fireOrder, null, $gamedata, false, $tmpLocation);
                }   
	    }
    }//endof function doCollateralDamage
	

	/*forcePrimary = true means PRIMARY indication is not rechecked*/
    public function damage($target, $shooter, $fireOrder, $gamedata, $damage, $forcePrimary = false){ 
	    /*find details of shot, proceed to doDamage*/
	    
        if($this->damageType=='Flash'){ //damage units other than base target
            $flashDamageAmount = floor($damage/4); //other units on target hex receive 25% of damage dealt to target
	    $this->doCollateralDamage($target, $shooter, $fireOrder, $gamedata, $flashDamageAmount);
        }

	    
        if ($target->isDestroyed()) return;
	    
	$tmpLocation = $fireOrder->chosenLocation;
	$launchPos = null;
        if ($this->ballistic){
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
		$launchPos = mathlib::hexCoToPixel($movement->x, $movement->y);
		if((!($tmpLocation > 0)) && (!$forcePrimary)){ //location not yet found or PRIMARY (reassignment causes no problem)
			$tmpLocation = $target->getHitSectionPos($launchPos, $fireOrder->turn);
		}
	}else{
		if((!($tmpLocation > 0)) && (!$forcePrimary)){ //location not yet found or PRIMARY (reassignment causes no problem)
 			$tmpLocation = $target->getHitSection($shooter, $fireOrder->turn);
		}
	}

	if(($target->shipSizeClass>1) && ($this->damageType=='Piercing')){ //Piercing damage will be split into 3 parts vs units larger thgan MCVs
		$facingLocation = $target->getHitSection($shooter, $fireOrder->turn, true); //do accept destroyed section as location
		//find out opposite section...
		$relativeBearing = $target->getBearingOnUnit($shooter);
		$oppositeBearing = Mathlib::addToDirection($relativeBearing, 180);
		$outLocation = $target->doGetHitSectionBearing($oppositeBearing); //technically true, even if may lead to strange effects... (in some cases, one location may be chosen twice); in this case, assume narrow point was hit
		$outLocation = $outLocation["loc"];//whole array was returned
		//find how big damage is done - split to 3 equal parts; if can't be equal, bigger portions will go to PRIMARY and facing parts
		if($outLocation == $facingLocation){ //shot enters and exits through the same section - narrow point - split into 2 parts only!
			$damageOut = 0;
			$damagePRIMARY = floor($damage/2);
			$damageEntry = ceil($damage/2);
		}else{ //standard split to 3 parts; defender allocates, so protecting Primary
			$damageEntry = ceil($damage/3);
			$damagePRIMARY = floor($damage/3);
			$damageOut = $damage - $damageEntry -$damagePRIMARY;
		}
		//first part: facing structure
		$system = $target->getHitSystem($shooter, $fireOrder, $this, $facingLocation);
        	$this->doDamage($target, $shooter, $system, $damageEntry, $fireOrder, $launchPos, $gamedata, false, $facingLocation);
		//second part: PRIMARY Structure
		$system = $target->getHitSystem($shooter, $fireOrder, $this, 0);
		$this->doDamage($target, $shooter, $system, $damagePRIMARY, $fireOrder, $launchPos, $gamedata, false, 0);
		//last part: opposite Structure
		$system = $target->getHitSystem($shooter, $fireOrder, $this,  $outLocation);
		$this->doDamage($target, $shooter, $system, $damageOut, $fireOrder, $launchPos, $gamedata, false, $outLocation);
	}elseif( ($this->damageType=='Raking') && (!($target instanceof FighterFlight)) ){ //Raking hit... but not at fighters - that's effectively Standard shot!
		//split into rakes; armor will not need to be penetrated twice!
		$fireOrder->armorIgnored = array();//reset info about pierced armor
		while($damage>0){
			$rake=min($damage, $this->getRakeSize());
			$system = $target->getHitSystem($shooter, $fireOrder, $this, $tmpLocation);
        		$this->doDamage($target, $shooter, $system, $rake, $fireOrder, $launchPos, $gamedata, false, $tmpLocation);
			$damage = $damage - $rake;
		}
	}else{ //standard mode of dealing damage
		if($fireOrder->linkedHit==null){
			$system = $target->getHitSystem($shooter, $fireOrder, $this, $tmpLocation);
		}else{
			$system=$fireOrder->linkedHit;
		}
		if($this->isLinked){ //further linked weapons will hit the exact same system!
			$fireOrder->linkedHit = $system;
		}
        	$this->doDamage($target, $shooter, $system, $damage, $fireOrder, $launchPos, $gamedata, false, $tmpLocation);
	}
    }//endof function damage

	
    public function isInLaunchRange($shooter, $target, $fireOrder)
    {
        // gameData and fireOrder is needed to check if target has jammers
        return true;
    }

	
    public function isInDistanceRange($shooter, $target, $fireOrder)
    {
        // gameData and fireOrder is needed to check if target has jammers
        return true;
    }

    protected function getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos=null){ //standard part of armor
    	$shooter = $gamedata->getShipById($fireOrder->shooterid);
        //$target = $gamedata->getShipById($fireOrder->targetid);

	$armor = 0;
	if( ($pos==null) && ($this->ballistic))  { //source of attack not explicitly defined, and weapon is ballistic
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
        }
	$armor = $system->getArmourStandard($target, $shooter, $this->weaponClass, $pos);

        $mod = $system->hasCritical("ArmorReduced", $gamedata->turn-1);
        $armor -= $mod;
	    
	$armor = max(0, $armor); //at least 0

        return $armor;
    }//endof function getSystemArmourStandard
	
	
    protected function getSystemArmourInvulnerable($target, $system, $gamedata, $fireOrder, $pos=null){ //only invulnerable portion of armor (one that can't be reduced by, say, damage type)
    	$shooter = $gamedata->getShipById($fireOrder->shooterid);
        //$target = $gamedata->getShipById($fireOrder->targetid);

	$armor = 0;
	if( ($pos==null) && ($this->ballistic))  { //source of attack not explicitly defined, and weapon is ballistic
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
        }
	$armor = $system->getArmourInvulnerable($target, $shooter, $this->weaponClass, $pos);

        $armor = max(0, $armor); //at least 0

        return $armor;
    }//endof function getSystemArmourInvulnerable
	
	

	/*returns modified damage, NOT damage modifier*/
	/*this is different function that getDamageMod of unit!*/
    protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata){
        $damage = $damage - $damage*$this->dp; //$dp is fraction of shot that gets wasted!
	    
        if ($this->rangeDamagePenalty > 0){
            $targetPos = $target->getCoPos();
	    if($pos!=null){
	    	$sourcePos = $pos;
	    }else{
	    	$sourcePos = $shooter->getCoPos();
	    }
            $dis = mathlib::getDistanceHex($sourcePos, $targetPos);
            $damage -= round($dis * $this->rangeDamagePenalty); //round to avoid damage loss at minimal ranges!
        }
	    
	//for Piercing shots at small targets (MCVs and smaller) - reduce damage by ~10% (by rules: -2 per die)
	if(($this->damageType=='Piercing')&&($target->shipSizeClass<2)) $damage = $damage*0.9;
	    
        $damage = max(0, $damage); //at least 0	    
	$damage = floor($damage);
        return $damage;
    } //endof function getDamageMod
	

    protected function getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder){
        $damage = $this->getDamage($fireOrder);
        $damage = $this->getDamageMod($damage, $shooter, $target, $pos, $gamedata);
        $damage -= $target->getDamageMod($shooter, $pos, $gamedata->turn, $this);

        return $damage;
    }
	

	
    protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null){
	    /*$pos ONLY relevant for FIGHTER armor if damage source position is different than one from weapon itself*/
	    /*otherwise best leave null BUT fill $location!*/
	    /*damageWasDealt indicates whether this hit already caused damage - important for overkill for some damage modes*/
	    
	if(!$system->isDestroyed()){ //else system was already destroyed, proceed to overkill
		$damageWasDealt = true; //actual damage was done! might be relevant for overkill allocation
		$systemHealth = $system->getRemainingHealth();
		$damage = floor($damage);//make sure damage is a whole number, without fractions!
		$armour = $this->getSystemArmourStandard($target, $system, $gamedata, $fireOrder, $pos); //standard part of armor (potentially ignored by weapon)
		$armour += $this->getSystemArmourInvulnerable($target, $system, $gamedata, $fireOrder, $pos); //this can't be ignored
		$modifiedDamage = $damage;
		$destroyed = false;
		
		//armor may be ignored for some reason...
		$armourIgnored = 0;
		if(isset($fireOrder->armorIgnored[$system->id])){
			$armourIgnored =  $fireOrder->armorIgnored[$system->id];
			$armour = $armour - $armourIgnored;
		}
		$armour = max($armour,0);
		
		if ($modifiedDamage-$armour >= $systemHealth){ //target will be destroyed
		    $destroyed = true;
		    $modifiedDamage = $systemHealth + $armour;
		}elseif($this->damageType == 'Raking' ){ //note that armour was already pierced for this shot...
			$armourIgnored = $armourIgnored+$modifiedDamage;
			$fireOrder->armorIgnored[$system->id] = $armourIgnored;
		}

		$damageEntry = new DamageEntry(-1, $target->id, -1, $fireOrder->turn, $system->id, $modifiedDamage, $armour, 0, $fireOrder->id, $destroyed, "", $this->weaponClass, $shooter->id, $this->id);
		$damageEntry->updated = true;
		$system->damage[] = $damageEntry;
		$this->onDamagedSystem($target, $system, $modifiedDamage, $armour, $gamedata, $fireOrder);

		$damage = $damage-$modifiedDamage;//reduce remaining damage by what was just dealt...
	}
	    
        if (($damage > 0) || (!$damageWasDealt)){//overkilling!
             $overkillSystem = $this->getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location);
             if ($overkillSystem != null)
                $this->doDamage($target, $shooter, $overkillSystem, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
        }

    }

    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
        return;
    }

	
	/*allow changing of basic parameters for different firing modes...
		called in method fire()
	*/
	public function changeFiringMode($newMode){ //change parameters with mode change
		//to display in GUI, shipSystem.js changeFiringMode function also needs to be redefined
		$this->firingMode = $newMode;
		$i = $newMode;
		if(isset($this->priorityArray[$i])) $this->priority = $this->priorityArray[$i];
		
		if(isset($this->animationArray[$i])) $this->animation = $this->animationArray[$i];
		if(isset($this->animationImgArray[$i])) $this->animationImg = $this->animationImgArray[$i];
		if(isset($this->animationImgSpriteArray[$i])) $this->animationImgSprite = $this->animationImgSpriteArray[$i];
		if(isset($this->animationColorArray[$i])) $this->animationColor = $this->animationColorArray[$i];
		if(isset($this->animationColor2Array[$i])) $this->animationColor2 = $this->animationColor2Array[$i];
		if(isset($this->animationWidthArray[$i])) $this->animationWidth = $this->animationWidthArray[$i];
		if(isset($this->animationExplosionScaleArray[$i])) $this->animationExplosionScale = $this->animationExplosionScaleArray[$i];
		if(isset($this->animationExplosionTypeArray[$i])) $this->animationExplosionType = $this->animationExplosionTypeArray[$i];
		if(isset($this->explosionColorArray[$i])) $this->explosionColor = $this->explosionColorArray[$i];
		if(isset($this->trailLengthArray[$i])) $this->trailLength = $this->trailLengthArray[$i];
		if(isset($this->trailColorArray[$i])) $this->trailColor = $this->trailColorArray[$i];
		if(isset($this->projectilespeedArray[$i])) $this->projectilespeed = $this->projectilespeedArray[$i];
		
		if(isset($this->rangePenaltyArray[$i])) $this->rangePenalty = $this->rangePenaltyArray[$i];
		if(isset($this->rangeDamagePenaltyArray[$i])) $this->rangeDamagePenalty = $this->rangeDamagePenaltyArray[$i];
		if(isset($this->rangeArray[$i])) $this->range = $this->rangeArray[$i];
		if(isset($this->fireControlArray[$i])) $this->fireControl = $this->fireControlArray[$i];
		if(isset($this->loadingtimeArray[$i])) $this->loadingtime = $this->loadingtimeArray[$i];
		if(isset($this->turnsloadedArray[$i])) $this->turnsloaded = $this->turnsloadedArray[$i];
		if(isset($this->extraoverloadshotsArray[$i])) $this->extraoverloadshots = $this->extraoverloadshotsArray[$i];
		if(isset($this->uninterceptableArray[$i])) $this->uninterceptable = $this->uninterceptableArray[$i];
		if(isset($this->shotsArray[$i])) $this->shots = $this->shotsArray[$i];
		if(isset($this->defaultShotsArray[$i])) $this->defaultShots = $this->defaultShotsArray[$i];
		if(isset($this->maxpulsesArray[$i])) $this->maxpulses = $this->maxpulsesArray[$i];
		if(isset($this->groupingArray[$i])) $this->grouping = $this->groupingArray[$i];
		if(isset($this->gunsArray[$i])) $this->guns = $this->gunsArray[$i];
		
		if(isset($this->damageTypeArray[$i])) $this->damageType = $this->damageTypeArray[$i];
		if(isset($this->weaponClassArray[$i])) $this->weaponClass = $this->weaponClassArray[$i];
		if(isset($this->minDamageArray[$i])) $this->minDamage = $this->minDamageArray[$i];
		if(isset($this->maxDamageArray[$i])) $this->maxDamage = $this->maxDamageArray[$i];
		if(isset($this->dpArray[$i])) $this->dp = $this->dpArray[$i];
		
		if(isset($this->systemKillerArray[$i])) $this->systemKiller = $this->systemKillerArray[$i];
		if(isset($this->noOverkillArray[$i])) $this->noOverkill = $this->noOverkillArray[$i];
		
	}//endof function changeFiringMode
	
	
	
} //end of class Weapon


class checkForSelfInterceptFire{
	private static $fired = array();
	
	public static function setFired($id, $turn){
	    if ($turn != TacGamedata::$currentTurn){
		$fired = array();
	    }
	    checkForSelfInterceptFire::$fired[] = $id;
	}

	public static function checkFired($id, $turn){  
	    if ($turn != TacGamedata::$currentTurn){
		$fired = array();
	    }
	    foreach (checkForSelfInterceptFire::$fired as $weapon){
		if ($weapon == $id){
		    return true;
		}
	    }
	    return false;
	}
} //endof class checkForSelfInterceptFire


 
?>
