<?php


class Weapon extends ShipSystem
{
    /*all (or almost all) variables will come in array form too - so they can change with mode changes*/
    /*array should be either empty (attribute does not change) or filled for all firing modes*/
    public $weapon = true;

    public $name = null;
    public $displayName = "";
    public $priority = 1; //array may be skipped, in which case this variable value will be used for all modes
    public $priorityArray = array();
    public $priorityAF = 0; //array must be set explicitly - otherwise it will be generated, ignoring this variable! 
    public $priorityAFArray = array();
	
	public $checkAmmoMagazine = false; //does this weapon require actual ammunition in AmmoMagazine to fire?

	/*not used any more
    public $animationImg = null;
    public $animationImgArray = array();
    public $animationImgSprite = 0;
    public $animationImgSpriteArray = array();
    public $animationColor2 = array(255, 255, 255);
    public $animationColor2Array = array();
    public $animationWidth = 3;
    public $animationWidthArray = array();
    public $animationExplosionType = "normal";
    public $animationExplosionTypeArray = array();
    public $explosionColor = array(250, 230, 80);
    public $explosionColorArray = array();
    public $trailLength = 40;
    public $trailLengthArray = array();
    public $trailColor = array(248, 216, 65);
    public $trailColorArray = array();
    public $projectilespeed = 17;
    public $projectilespeedArray = array();
	*/

    public $animation = "none"; //options: "laser" (continuous beam), "torpedo" (a glowing oscillating ball), "bolt" (a discrete bolt/projectile of elongated shape), "ball" (area of effect - simply a sphere (well, circle), with radius equal to number of hexes to be encompassed)
	 //any other value equals "bolt"
	 //unless weapon is hextargeted, in which case any entry equals "ball"...
    public $animationArray = array();
    public $animationColor = array(0, 0, 0); //if not redefined - make it completely black, just to suggest something is wrong ;)
    public $animationColorArray = array();
    public $animationExplosionScale = 0; //0 means it will be set automatically by standard constructor, based on average damage yield
    public $animationExplosionScaleArray = array();
	public $noProjectile = false; //Marker for front end to make projectile invisible for weapons that shouldn't have one.   

	public $doubleRangeIfNoLock = false; //in case of no lock-on default procedure is to double range penalty; some weapons (notably most Antimatter ones) double range itself instead
    public $rangePenalty = 0;
    public $rangePenaltyArray = array();
    public $specialRangeCalculation = false; //set to true if weapon should use its own range calculation IN FRONT END (server side range calculation is in weapon class anyway)
    public $specialRangeCalculationArray = array(); //set to true if weapon should use its own range calculation IN FRONT END (server side range calculation is in weapon class anyway)    
    public $specialHitChanceCalculation = false; //set to true for HARM missiles to allow front-end to show correct hitchance.
  	public $specialHitChanceCalculationArray = array();
    public $rangeDamagePenalty = 0;
    public $rangeDamagePenaltyArray = array();
    private $dp = 0; //damage penalty - fraction of shot that gets wasted!
    private $dpArray = array(); //array of damage penalties for all modes! - filled automatically
    private $rp = 0; //range penalty - number of crits ! effect is reflected on $range anyway, no need to hold an array
    public $range = 0;
    public $rangeArray = array();
    public $distanceRange = 0;
    public $distanceRangeArray = array();
    public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
    public $fireControlArray = array();


    public $loadingtime = 1;
    public $loadingtimeArray = array();
    public $turnsloaded;
    public $turnsloadedArray = array();
	public $maxVariableShots = 0; //For front end to know how many shots weapon CAN fire for variable-shot weapons. 	  
	public $canSplitShots = false; //For Front end to allow weapons to target different enemies in same firing round. 
	public $canSplitShotsArray = array(); 
    protected $multiModeSplit = false; //If you want the Front End to see this, pass it in strpForJson() in weapon :)
    protected $splitArcs = false; //Used to tell Front End that weapon has 2 or more separate arcs, passed manually via stripForJson()
    public $overloadable = false;

    public $normalload = 0;
    public $alwaysoverloading = false;
    public $autoFireOnly = false; //ture for weapons that should never be fired manually
    public $overloadturns = 0;
    public $overloadshots = 0;
    public $extraoverloadshots = 0;
    public $extraoverloadshotsArray = array();

    public $doNotIntercept = false; //for attacks that are not subject to interception at all - like fields and ramming
    public $doNotInterceptArray = array(); //for attacks that are not subject to interception at all - like fields and ramming
    public $uninterceptable = false;
    public $uninterceptableArray = array();
    public $canInterceptUninterceptable = false; //able to intercept shots that are normally uninterceptable, eg. Lasers
    public $noInterceptDegradation = false; //if true, this weapon will be intercepted without degradation!
	public $doInterceptDegradation = false; //if true, this weapon will be intercepted with normal degradation, even if a ballistic
    public $intercept = 0; //intercept rating
    public $freeintercept = false;  //can intercept fire directed at other unit?
    public $freeinterceptspecial = false;  //has its own routine for handling decision whether it's capable of interception - for freeintercept only?
    public $hidetarget = false;
	public $hidetargetArray = array();  //for weapons that do not show their target
    //public $duoWeapon = false; 
    //public $dualWeapon = false;
    public $canChangeShots = false;
    public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?
	public $isRammingAttack = false; //true means hit chance calculations are completely different, relying on speed
	public $raking = 10;//size of rake (for Raking weapons only)
	public $rakingArray = array();//size of rake (for multi-mode weapons with variable rake size)
	public $noLockPenalty = true;
	public $noLockPenaltyArray = array();	

//	protected $overrideCallingRestrictions = false; //when set to true overrides default Called Shot setting (e.g., make a ballistic do a called shot)
//	protected $canOnlyCalledShot = false;	
	protected $hasSpecialLaunchHexCalculation = false; //Weapons like Proximty Laser use a separate launcher system to determine point of shot.
    public $ignoresLoS = false;
    public $canModesIntercept = false;	//Some missile launchers can have Interceptor missiles. variable for Front End so it knows to use weapon-specific function to search for intercept rating across firing modes e.g. Interceptor Missile on missile launcher.
		
    public $shots = 1;
    public $shotsArray = array();
    public $defaultShots = 1;
    public $defaultShotsArray = array();

    public $rof = 1; 
	

    public $grouping = 0;
    public $groupingArray = array();
    public $guns = 1;
    public $gunsArray = array();

    // Used to indicate a parent in case of dualWeapons
    public $parentId = -1;
	public $preFires = false; //Denotes whether weapon fires in pre-firing phase on normal firing phase
    public $firingMode = 1;
    public $firingModes = array(1 => "Standard"); //just a convenient name for firing mode
    public $modeLetters = 1;//Default to show only first letter of alt firing modes.  Up to 3 letter looks ok.
    public $damageType = ""; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
    public $damageTypeArray = array();
    public $weaponClass = "Particle"; //MANDATORY (first letter upcase) weapon class - overrides $this->data["Weapon type"] if set! Make Particle default to prevent AA bugs when "" passed.
    public $weaponClassArray = array();

    //damage type-related variables
    public $isLinked = false; //for linked weapons - they will all hit the exact same system!
    public $noOverkill = false; //this will let simplify entire Matter line enormously!
    protected $noOverkillArray = array();
    public $doOverkill = false; //opposite of $noOverkill - allows Piercing shots to overkill (eg. Shadow Heavy Molecular Slicer Beam has such ability)
    public $ballistic = false; //this is a ballistic weapon, not direct fire
    public $ballisticIntercept = false; //can intercept, but only ballistics
    public $hextarget = false; //this weapon is targeted on hex, not unit
   	public $hextargetArray = array(); //For AntimatterShredder
   	protected $noTargetHexIcon = false;		
		
    public $noPrimaryHits = false; //PRIMARY removed from outer charts if true
	
	public $ignoreAllEW = false;//weapon ignores EW and lock-ons completely (at the moment Antimatter Shredder does so)
	public $ignoreAllEWArray = array();
	public $ignoreJinking = false;//weapon ignores jinking completely (at the moment Antimatter Shredder does so)
	public $ignoreJinkingArray = array();

    public $minDamage, $maxDamage;
    public $minDamageArray = array();
    public $maxDamageArray = array();

	//some weapons might use variable firing arc...	
    public $startArcArray = array(); 
	public $endArcArray = array();
	
    public $exclusive = false; //for fighter guns - exclusive weapon can't bve fired together with others

    public $useOEW = true;
    public $calledShotMod = -8;
	public $calledShotModArray = array();     
	public $factionAge = 1; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

    protected $possibleCriticals = array(14 => "ReducedRange", 19 => "ReducedDamage", 25 => array("ReducedRange", "ReducedDamage"));

    protected $firedDefensivelyAlready = 0; //marker used for weapons capable of firing multiple defensive shots, but suffering backlash once
	protected $autoHit = false;//To show 100% hit chance in front end, Need to pass in strpForJson			        
    protected $autoHitArray = array(); //Need to pass in strpForJson    
	protected $shootsStraight = true; //Denotes for Front End to use Line Arcs, not circles. Need to pass in strpForJson
	protected $specialArcs = true;	//Denotes for Front End to redirect to weapon specific function to get arcs. Need to pass in strpForJson
	protected $canTargetAllies = false; //To allow front end to target allies.
	protected $canTargetAlliesArray = array(); //To allow front end to target allies.
    protected $canTargetAll = false; //Allows weapon to target allies AND enemies, pass to Front End in strpForJson()

	//Weapons are repaired before "average system", but after really important things! 
	public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired
    

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output = 0)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $output);

        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
	    
        //things that are calculated and can change with mode (and are displayed in GUI) - for all modes...
        foreach ($this->firingModes as $i => $modeName) {
            $this->changeFiringMode($i);
            $this->setMinDamage();
            $this->minDamageArray[$i] = $this->minDamage;
            $this->setMaxDamage();
            $this->maxDamageArray[$i] = $this->maxDamage;

			//set AF priority, too!
			$this->setPriorityAF(); 
			$this->priorityAFArray[$i] = $this->priorityAF;
		
			//...and scale!
			if (!isset($this->animationExplosionScaleArray[$i]) || ($this->animationExplosionScaleArray[$i]<=0)){
				if($this->animationExplosionScale>0){ //default exists - use it!
					$this->animationExplosionScaleArray[$i] = $this->animationExplosionScale;
				}else{ //no default - calculate from scratch
					$this->animationExplosionScaleArray[$i] = $this->dynamicScale(0);
				}
			}
        }
        $this->changeFiringMode(1); //reset mode to basic
    }

	/*dynamic assignment of animation scale - based on damage dealt
	  $avgDmg - average damage of weapon 
	   - if provided - function will just use it directly (not modifying it in any way)
	   - if provided as 0 - function will look for it on its own, taking weapon properties into account
	  $multiplier - additional modifier for damage calculation (will be used only if $avgDmg is 0
	
	*/
	public function dynamicScale($avgDmg, $multiplier = 1){
		$toReturn = 0.15;		
		if($avgDmg<=0){ //no damage passed - calculate average!
			$avgDmg = $this->getAvgDamage() * $multiplier;
		
			//modify by mode!
			if( ($this->damageType == 'Raking') || ($this->damageType == 'Piercing') ){ //Raking weapons usually have higher yield than comparable Standard weapons, tone this down
				$avgDmg = $avgDmg*0.75; 
			}
			if($this->damageType == 'Flash'){ //make Flash bigger!
				$avgDmg = $avgDmg*1.25; 
			}
			//low Raking mode indicates weaker weapon/thinner beam than damage would suggest, while high Raking mode larger one!
			if($this->raking <10){
				$avgDmg = $avgDmg*0.9; 
			}elseif($this->raking >10){
				$avgDmg = $avgDmg*1.1; 
			}
			//Matter weapons score relatively low damage, but ignore armor - make them more notable ;)
			if($this->weaponClass == 'Matter') {
				$avgDmg = min($avgDmg+6, $avgDmg*1.5); //multiply by 1.5, but no more than +6 
			}
		}
		
		//assign correct size
		/*values changed after discussion with Douglas
		if($avgDmg<4.5){ //REALLY light - even less than d6+1!
			$toReturn = 0.1; 
		}elseif($avgDmg<7.5){ //very light - less than d6+4
			$toReturn = 0.15; 
		}elseif($avgDmg<10){ //light
			$toReturn = 0.2;
		}elseif($avgDmg<12){ //light/medium
			$toReturn = 0.25;
		}elseif($avgDmg<14){ //medium
			$toReturn = 0.3;
		}elseif($avgDmg<17){ //medium/heavy
			$toReturn = 0.35;
		}else{ //heavy and very heavy
			$toReturn = 0.3+(0.1*floor($avgDmg/10)); //0.3 +1 per every full 10 points of average damage
		}
		*/		
		if($avgDmg<4.5){ //REALLY light - even less than d6+1!
			$toReturn = 0.08; 
		}elseif($avgDmg<7.5){ //very light - less than d6+4
			$toReturn = 0.12; 
		}elseif($avgDmg<10){ //light
			$toReturn = 0.15;
		}elseif($avgDmg<12){ //light/medium
			$toReturn = 0.2;
		}elseif($avgDmg<14){ //medium
			$toReturn = 0.25;
		}elseif($avgDmg<17){ //medium/heavy
			$toReturn = 0.3;
		}else{ //heavy and very heavy
			$toReturn = 0.25+(0.1*floor($avgDmg/10)); //0.3 +1 per every full 10 points of average damage
		}
		
		return $toReturn;
	}//endof function dynamicScale()
    
    public function stripForJson() {
        $strippedSystem = parent::stripForJson();

		if ($this instanceof Weapon) {
			$strippedSystem->turnsloaded = $this->turnsloaded;
			$strippedSystem->turnsloadedArray = $this->turnsloadedArray;
			$strippedSystem->overloadturns = $this->overloadturns;
			$strippedSystem->overloadshots = $this->overloadshots;
			$strippedSystem->extraoverloadshots = $this->extraoverloadshots;
			$strippedSystem->extraoverloadshotsArray = $this->extraoverloadshotsArray;
			$strippedSystem->fireOrders = $this->fireOrders;
			
			if(isset($this->ammunition)){
				$strippedSystem->ammunition = $this->ammunition;
				$strippedSystem->data = $this->data;
			}
			/*this needs to be sent only if weapon suffered crits!*/
			if (count($this->criticals)>0) { //if there was a critical, send all potentially changed data; otherwise, they should be standard and don't need to be sent extra!				
				$strippedSystem->range = $this->range;
				$strippedSystem->rangeArray = $this->rangeArray;	
				$strippedSystem->rangePenalty = $this->rangePenalty;
   				$strippedSystem->rangePenaltyArray = $this->rangePenaltyArray;    
				$strippedSystem->minDamage = $this->minDamage;
				$strippedSystem->maxDamage = $this->maxDamage;
				$strippedSystem->minDamageArray = $this->minDamageArray;
				$strippedSystem->maxDamageArray = $this->maxDamageArray;
				$strippedSystem->data = $this->data;    
			}
		//Hyach Specialists sometimes require additional info to be sent to front end.
		$ship = $this->unit;
		if ($ship->getSystemByName("HyachSpecialists")){ //Does ship have Specialists system?
			$specialists = $ship->HyachSpecialists;
			$specAllocatedArray = $specialists->specAllocatedCount;
			foreach ($specAllocatedArray as $specsUsed=>$specValue){
				if ($specsUsed == 'Defence'){ //Defence modifies intercept rating, show in system window.
					$strippedSystem->data = $this->data; 				
				}
				if ($specsUsed == 'Weapon'){ //Weapon modifies damage, show in system window.
					if ($this->maxDamage > 0){
												
						$newMinDamage = $this->minDamage+3;
						$newMaxDamage = $this->maxDamage+3;	
       				if ($this->minDamage != $this->maxDamage){									
						$this->data["Damage"] = $newMinDamage . "-" . $newMaxDamage;
					}else{
						$this->data["Damage"] = $newMaxDamage;						
					}					
						$strippedSystem->data = $this->data; 
						$strippedSystem->data["Damage"] = $this->data["Damage"]; 				
					}
				}		
			}
		}				
			
		}
        return $strippedSystem;
    }

	//when intercepting shot directed at another ship - can the weapon do it? (called if $freeintercept == true && $freeinterceptspecial == true)
	public function canFreeInterceptShot($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)
	{
		return false;
	}
	
    //can intercept at all? true means standard rules apply, false blocks interception
	//added for Vorlon weapons that need to check whether they have power available to continue firing, but interface is large to enable possible further needs
	public function canInterceptAtAll($gd, $fire, $shooter, $target, $interceptingShip, $firingweapon)
	{
		return true;
	}

    public function getRange($fireOrder)
    {
        return $this->range;
    }

    public function getWeaponForIntercept()
    {
        return $this;
    }

    public function getCalledShotMod()
    {
        return $this->calledShotMod;
    }

    protected function getWeaponHitChanceMod($turn)
    {
        return 0;
    }

    public function getRakeSize()
    {
        return $this->raking;
    }

    public function getAvgDamage()
    {
        $min = $this->minDamage;
        $max = $this->maxDamage;
        $avg = round(($min + $max) / 2);
        return $avg;
    }
	
	//reimplemented by weapons that have special interaction with shields - eg. SHadow Phasing Pulse Cannon line
    public function shieldInteractionDefense($target, $shooter, $pos, $turn, $shield, $mod) {
		return $mod; //default - just return shield value		
	}
	
	//reimplemented by weapons that have special interaction with shields - eg. SHadow Phasing Pulse Cannon line
    public function shieldInteractionDamage($target, $shooter, $pos, $turn, $shield, $mod) {
		return $mod; //default - just return shield value		
	}
	
    protected function setPriorityAF(){
	//sets AF priority if not set explicitly by weapon creator
	//attempt to put high-damage weapons first, to attempt to kill fighters outright or cause heavy damage (so light guns can concentrate on smaller number of craft or finish off damaged one)
	//priorities:
	// 1,2,10 - no change, those are kind of special
	//then Raking, they tend to have high damage output and against fighters they become effectively Standard
	//then heavy Standard weapons
	//then everything else in reverse order (note there's no need to put Matter at the end!)
	if ($this->priorityAF > 0) return;
	switch($this->priority){
		case 7: //heavy Raking
			$this->priorityAF = 3;
			break;
			
		case 8: //light Raking
			$this->priorityAF = 4;
			break;
			
		case 6: //heavy Standard 
			$this->priorityAF = 5;
			break;
			
		case 9: //Matter and such
			$this->priorityAF = 6;
			break;			
			
		case 5: 
			$this->priorityAF = 7;
			break;
			
		case 4: 
			$this->priorityAF = 8;
			break;
			
		case 3: 
			$this->priorityAF = 9;
			break;
			
		default: //no change needed, go for $priority (1,2,10)
			$this->priorityAF = $this->priority;
	}	    
    }


    public function effectCriticals()
    {
        $this->dp = 0;
        $this->rp = 0;
        parent::effectCriticals();

        foreach ($this->criticals as $crit) {
            if ($crit instanceof ReducedRange) $this->rp++;
            if ($crit instanceof ReducedDamage) $this->dp++;
        }
        $rp = $this->rp;
        $dp = $this->dp;
        //min/max damage arrays are created automatically, so they will always be present
        if ($dp > 0) {
            $this->effectCriticalDamgeReductions($dp);
            /* //Moved to be it's owned function for it can be called separately from setSystemDataWindow() as well as onConstructed() - DK Apr 2025         
            foreach ($this->firingModes as $dmgMode => $modeName) {
                $mod = $dp * max(2, 0.2 * ($this->maxDamageArray[$dmgMode] - $this->minDamageArray[$dmgMode]));//2 or 20% of variability, whichever is higher
                $avgDmg = ($this->maxDamageArray[$dmgMode] + $this->minDamageArray[$dmgMode]) / 2;
                if ($avgDmg > 0) {
                    $this->dpArray[$dmgMode] = $mod / $avgDmg;//convert to fraction -  of average result
                } else {
                    $this->dpArray[$dmgMode] = 1; //100% reduction
                }
                $this->dpArray[$dmgMode] = min(0.9, $this->dpArray[$dmgMode]); //let's not allow to reduce below something ;) - say, max damage reduction is 90%
                $this->minDamageArray[$dmgMode] = floor($this->minDamageArray[$dmgMode] * (1 - $this->dpArray[$dmgMode]));
                $this->maxDamageArray[$dmgMode] = floor($this->maxDamageArray[$dmgMode] * (1 - $this->dpArray[$dmgMode]));
            } */
        }

        //range doesn't have to be an array, but may be
        while ($rp > 0) {
            if ($this->rangePenalty >= 1) {
                $this->rangePenalty += 1;
            } else if ($this->rangePenalty > 0) {
                $this->rangePenalty = 1 / (round(1 / $this->rangePenalty) - 1);
            } else { //no range penalty - range itself will be reduced!
                //no calculations needed
            }
            foreach ($this->rangePenaltyArray As $dmgMode => $penaltyV) {
                if ($this->rangePenaltyArray[$dmgMode] >= 1) { //long range
                    $this->rangePenaltyArray[$dmgMode] += 1;
                } else if ($this->rangePenalty > 0) { //short range
                    $this->rangePenaltyArray[$dmgMode] = 1 / (round(1 / $this->rangePenaltyArray[$dmgMode]) - 1);
                } else { //no range penalty - affect range itself
                    if (!isset($this->rangeArray[$dmgMode])) $this->rangeArray[$dmgMode] = $this->range;
                    $this->rangeArray[$dmgMode] = floor($this->rangeArray[$dmgMode] * 0.8); //loss 20% range for very crit
                }
            }
            $rp--;
        }

        //make sure data from table is transferred to current variables
        $this->changeFiringMode($this->firingMode);
    } //endof function effectCriticals

    public function effectCriticalDamgeReductions($dp, $repeat = false){
        //damage penalty: 20% of variance or straight 2, whichever is bigger; hold that as a fraction, however! - low rolls should be affected lefss than high ones, after all        
        foreach ($this->firingModes as $dmgMode => $modeName) {
            $variance = $this->maxDamageArray[$dmgMode] - $this->minDamageArray[$dmgMode];
            $mod = $dp * max(2, 0.2 * $variance);
        
            $avgDmg = ($this->maxDamageArray[$dmgMode] + $this->minDamageArray[$dmgMode]) / 2;
        
            if ($avgDmg > 0) {
                $this->dpArray[$dmgMode] = $mod / $avgDmg;
            } else {
                $this->dpArray[$dmgMode] = 1;
            }
        
            $this->dpArray[$dmgMode] = min(0.9, $this->dpArray[$dmgMode]);
        
            $this->minDamageArray[$dmgMode] = floor($this->minDamageArray[$dmgMode] * (1 - $this->dpArray[$dmgMode]));
            $this->maxDamageArray[$dmgMode] = floor($this->maxDamageArray[$dmgMode] * (1 - $this->dpArray[$dmgMode]));
        }
    }

    public function getNormalLoad()
    {
        if ($this->normalload == 0) {
            return $this->getLoadingTime();
        }
        return $this->normalload;
    }

    public function getLoadingTime()
    {
        return $this->loadingtime;
    }

    public function getTurnsloaded()
    {
        return $this->turnsloaded;
    }

    /*if this weapon was to be used for interception of indicated shot - how high intercept mod would be?
        assume that intercept itself is legal
    */
    public function getInterceptionMod($gamedata, $intercepted)
    {
        $interceptMod = 0;
        $shooter = $gamedata->getShipById($intercepted->shooterid);
        $interceptedWeapon = $shooter->getSystemById($intercepted->weaponid);
        if ($interceptedWeapon->hextarget) return 0;//can't intercept uninterceptable or hextarget weapon!
        if ($this->ballisticIntercept && (!($interceptedWeapon->ballistic))) return 0;//can't intercept non-ballistic if weapon can intercept only ballistics!

        $interceptMod = $this->getInterceptRating($gamedata->turn);

// Commenting out this fragmant as it's is duplicated below, and seemed to be causing intercept degradation to apply twice - DK - 4 Jan 24        
 /*       if (!($interceptedWeapon->ballistic || $interceptedWeapon->noInterceptDegradation)) {//target is neither ballistic weapon nor has lifted degradation, so apply degradation!
            for ($i = 0; $i < $intercepted->numInterceptors; $i++) {
                $interceptMod -= 1; //-1 for each already intercepting weapon
            }
        }  */

		if( ($interceptedWeapon->doInterceptDegradation) || (!($interceptedWeapon->ballistic || $interceptedWeapon->noInterceptDegradation)) ) {//target is neither ballistic weapon nor has lifted degradatoin, so apply degradation!
            for ($i = 0; $i < $intercepted->numInterceptors; $i++) {
                $interceptMod -= 1; //-1 for each already intercepting weapon
			}
		}
 
        $interceptMod = max(0, $interceptMod) * 5;//*5: d20->d100
        return $interceptMod;
    }//endof  getInterceptionMod


    public function firedOnTurn($turn)
    {
        foreach ($this->fireOrders as $fire) {
            if ($fire->type != "selfIntercept" && $fire->weaponid == $this->id && $fire->turn == $turn) {
                return true;
            } else if ($fire->type == "selfIntercept" && checkForSelfInterceptFire::checkFired($this->id, $turn)) {
                return true;
            }
        }
        return false;
    }
	
	/*mode of fire used - needed to determine whether fire can be sustained*/
    public function firedUsingMode($turn)
    {
        foreach ($this->fireOrders as $fire) {
            if ($fire->type != "selfIntercept" && $fire->weaponid == $this->id && $fire->turn == $turn) {
                return $fire->firingMode;
            } else { //should not happen
                return 0;
            }
        }
        return false;
    }
	
    public function firedOffensivelyOnTurn($turn)
    {
        //if ($this instanceof DualWeapon && isset($this->turnsFired[$turn])) return true;
        foreach ($this->fireOrders as $fire) {
            if ( (strpos($fire->type, 'ntercept') == false) && $fire->weaponid == $this->id && $fire->turn == $turn) {
                return true;
            } 
        }
        return false;
    }

    public function formatFCValue($fc)
    {
        if ($fc === null)
            return "-";

        return number_format(($fc * 5), 0);
    }

    public function setSystemDataWindow($turn)
    {			
		//re-create damage arrays, so they reflect loading time...
		foreach ($this->firingModes as $i => $modeName) {
			$this->changeFiringMode($i);
			$this->setMinDamage();
			$this->minDamageArray[$i] = $this->minDamage;
			$this->setMaxDamage();
			$this->maxDamageArray[$i] = $this->maxDamage;
			//set AF priority, too!
			$this->setPriorityAF(); 
			$this->priorityAFArray[$i] = $this->priorityAF;
		} 

        //re-apply damage penalties, if any!
        $dp = 0;
        foreach ($this->criticals as $crit) {
            if ($crit instanceof ReducedDamage) $dp++;
        }
        if ($dp > 0) {
            $this->effectCriticalDamgeReductions($dp);
        }

		$this->changeFiringMode(1); //reset mode to basic
            
        if ($this->damageType != '') $this->data["Damage type"] = $this->damageType;
        if ($this->weaponClass != '') $this->data["Weapon type"] = $this->weaponClass;
		
		//this is shown visually, and showing current values would require overriding cache - I'll disable it
        //$this->data["Loading"] = $this->getTurnsloaded() . "/" . $this->getNormalLoad();

        $dam = $this->minDamage . "-" . $this->maxDamage;
        if ($this->minDamage == $this->maxDamage)
            $dam = $this->maxDamage;

        $this->data["Damage"] = $dam;

        if ($this->rangePenalty > 0) {
            $this->data["Range penalty"] = number_format(($this->rangePenalty * 5), 2) . " per hex";
        } 
		if ($this->range > 0) {
            $this->data["Range"] = $this->range;
            if ($this->distanceRange > $this->range) $this->data["Range"] .= '/' . $this->distanceRange;
        }

        $fcfight = $this->formatFCValue($this->fireControl[0]);
        $fcmed = $this->formatFCValue($this->fireControl[1]);
        $fccap = $this->formatFCValue($this->fireControl[2]);
        $this->data["Fire control (fighter/med/cap)"] = "$fcfight/$fcmed/$fccap";
		
        if ($this->guns > 1) {
            $this->data["Number of guns"] = $this->guns;
        }

        if (!($this instanceof Pulse) && $this->shots > 1) {
            $this->data["Number of shots"] = $this->shots;
        }

        if ($this->intercept > 0) {
            $this->data["Intercept"] = "-" . $this->intercept * 5;
        }
		
		if($this->exclusive){
			$this->data["Exclusive"] = 'Yes';
		}
        $this->data["Resolution Priority (ship/fighter)"] = $this->priority . '/' . $this->priorityAF;

/*no longer used
        $misc = array();

        if ($this->overloadturns > 0) {
            $misc[] = " OVERLOADABLE";
        }
        if ($this->uninterceptable)
            $misc[] = " UNINTERCEPTABLE";

        //if (sizeof($misc)>0)
        //$this->data["Misc"] = $misc;
*/		

        parent::setSystemDataWindow($turn);
    }

    public function onAdvancingGamedata($ship, $gamedata)
    {
        $data = $this->calculateLoading($gamedata);
        if ($data)
            SystemData::addDataForSystem($this->id, 0, $ship->id, $data->toJSON());
    }


    public function setSystemData($data, $subsystem)
    {
        $array = json_decode($data, true);
        if (!is_array($array))
            return;

        foreach ($array as $i => $entry) {
            if ($i == "loading") {
                if (sizeof($entry) == 4) {
                    $loading = new WeaponLoading(
                        $entry[1],
                        $entry[2],
                        $entry[3],
                        $entry[4],
                        $this->loadingtime,
                        $this->firingMode
                    );
                } elseif (sizeof($entry) == 5) {
                    $loading = new WeaponLoading(
                        $entry[1],
                        $entry[2],
                        $entry[3],
                        $entry[4],
                        $entry[5],
                        $this->firingMode
                    );
                } else {
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
/*
    public function getStartLoading()
    {
//        return new WeaponLoading($this->getNormalLoad(), 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
        return new WeaponLoading($this->getNormalLoad(), $this->overloadshots, 0, $this->overloadturns, $this->getLoadingTime(), $this->firingMode);
    }
*/
public function getStartLoading()
{
    $overloadTurns = $this->overloadturns;

    // At game start, no power entries exist yet.
    // If this weapon can overload and we're initializing (Turn 1 setup),
    // start overloadturns at 1 so that Turn 1 displays correctly.
    if ($overloadTurns === 0 && $this->overloadable) {
        $overloadTurns = 1;
    }

    return new WeaponLoading(
        $this->getNormalLoad(),
        $this->overloadshots,
        0,
        $overloadTurns,
        $this->getLoadingTime(),
        $this->firingMode
    );
}

    public function setLoading($loading)
    {
        if (!$loading)
            return;

        $this->overloadturns = $loading->overloading;
        $this->overloadshots = $loading->extrashots;
	   
	  
        // turnsloaded is set by boostable weapons that are ready.
        // Keep your hands of in that case.
        if (!($this->boostable && ($this->loadingtime <= $this->getTurnsloaded()))) {
            $this->turnsloaded = $loading->loading;
        }else{ //Marcin Sawicki, January 2019: phase 4 loading is now skipped, moved to calculateLoadingFromLastTurn - was just called... so there should be no difference?
	  //so - boostable weapons... behaving the same!
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


    public function calculateLoading(TacGamedata $gamedata)
    {
        $phase = $gamedata->phase;
        $normalload = $this->getNormalLoad();
        if ($phase == 2) {
            if ($this->isOfflineOnTurn(TacGamedata::$currentTurn)) {
                return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            } else if ($this->ballistic && $this->firedOnTurn(TacGamedata::$currentTurn)) {
                return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            } else if (!$this->isOverloadingOnTurn(TacGamedata::$currentTurn)) {
                return new WeaponLoading($this->getTurnsloaded(), 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            }
        } else if ($phase == 3) {
            if ($this->preFires && $this->firedOnTurn(TacGamedata::$currentTurn)) {
                return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            }
        } else if ($phase == -1) {
            $weaponLoading = $this->calculateLoadingFromLastTurn($gamedata);                   
            $this->setLoading($weaponLoading);

            if ($this->overloadshots === -1) {
                return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            } else {
                $newloading = $this->getTurnsloaded() + 1;            
                if ($newloading > $normalload)
                    $newloading = $normalload;
 
                $newExtraShots = $this->overloadshots;
                $overloading = $this->overloadturns + 1;
                if ($overloading >= $normalload && $newExtraShots == 0)
                    $newExtraShots = $this->extraoverloadshots;

                if ($overloading > $normalload)
                    $overloading = $normalload;

                return new WeaponLoading($newloading, $newExtraShots, $this->getLoadedAmmo(), $overloading, $this->getLoadingTime(), $this->firingMode);
            }

        }

        return new WeaponLoading($this->getTurnsloaded(), $this->overloadshots, $this->getLoadedAmmo(), $this->overloadturns, $this->getLoadingTime(), $this->firingMode);
    }


    private function calculateLoadingFromLastTurn($gamedata)
    {
        if ($this->ballistic && !(checkForSelfInterceptFire::checkFired($this->id, $gamedata->turn -1)))
            return null;


        if ($this->firedOnTurn($gamedata->turn -1)) {
            if ($this->firedUsingMode($gamedata->turn -1)==1) {//only basic mode counts for sustaining! otherwise shot was just not sustained 
                if ($this->overloadshots > 0) {
                    $newExtraShots = $this->overloadshots - 1;
                    //if you have extra shots use them
                    if ($newExtraShots === 0) {
                        //if extra shots are reduced to zero, go to cooldown
                        //return new WeaponLoading(0, -1, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
                        //...which SHOULD mean - go to normal loading and throw a crit forcing shutdown ! 	
                        //proper loading calculation below, critical here would not be saved - it needs to be thrown right after firing
                        return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
                    } else {
                        //if you didn't use the last extra shot, keep on going.
                        return new WeaponLoading($this->getTurnsloaded(), $newExtraShots, $this->getLoadedAmmo(), $this->overloadturns, $this->getLoadingTime(), $this->firingMode);
                    }
                } else {
                    //Situation normal, no overloading -> lose loading
                    return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
                }
            } else {
                //Situation normal, no overloading -> lose loading
                return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
            }
        } else {
            //cannot save the extra shots from everload -> lose loading and cooldown
            if ($this->overloadshots > 0 && $this->overloadshots < $this->extraoverloadshots) {
                if ($this->isOverloadingOnTurn(TacGamedata::$currentTurn)) {
                    return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 1, $this->getLoadingTime(), $this->firingMode);
                } else {
                    return new WeaponLoading(0, 0, $this->getLoadedAmmo(), 0, $this->getLoadingTime(), $this->firingMode);
                }
            }
        }

        return new WeaponLoading($this->getTurnsloaded(), $this->overloadshots, $this->getLoadedAmmo(), $this->overloadturns, $this->getLoadingTime(), $this->firingMode);
    }

	
	//returns $turnsloaded if >0 and weapon is enabled; 
	//if weapon is disabled, returns 0
	//if $turnsloaded is 0 and weapon is enabled (meaning it has just been fired) - returns time that passed from most recent shot (or being disabled)
	//..for some reason does not work - possibly loaded date does not contain proper historical firing references...
	/*
	public function getLoadingBeforeCurrentShot()
	{
		if ($this->turnsloaded >=1 ) return $this->turnsloaded;
		if ($this->isOfflineOnTurn(TacGamedata::$currentTurn)) return 0;
		//weapon is enabled and has just been fired... calculate time that passed from most recent shot
		$turnsArmed = 1; //current turn, at the very least
		for($turnToCheck = TacGamedata::$currentTurn -1; $turnToCheck >= 0; $turnToCheck--){
			if ($turnToCheck == 0) { //start of game - meaning weapon is fully charged
				$turnsArmed = $this->loadingtime;
				break;
			}
			if ($this->isOfflineOnTurn($turnToCheck)) break; //weapon was disabled
			if ($this->firedOnTurn($turnToCheck)) break; //weapon was fired
			$turnsArmed++; //weapon was armed, increase loading time
			if($turnsArmed >= $this->loadingtime) break; //full loading time reached, no point checking further
		}
		return $turnsArmed;
	} //endof function getLoadingBeforeCurrentShot
	*/
	
    public function beforeTurn($ship, $turn, $phase)
    {
        parent::beforeTurn($ship, $turn, $phase);
    }

    public function getDamage($fireOrder)
    {
        return 0;
    }

    public function setMinDamage()
    {

    }

    public function setMaxDamage()
    {

    }
    
     public function getBonusDamage(){
		$bonusDamage = 0;	
		//Hyach Specialists sometimes require additional info to be sent to front end.
		$ship = $this->unit;			
		if ($ship->getSystemByName("HyachSpecialists")){ //Does ship have Specialists system?
			$specialists = $ship->HyachSpecialists;
			$specAllocatedArray = $specialists->specAllocatedCount;
			foreach ($specAllocatedArray as $specsUsed=>$specValue){
				if ($specsUsed == 'Weapon'){ //Weapon Specialists add 3 damage.
				$bonusDamage = 3;									
				}		
			}
		}	
		return $bonusDamage;
    }   

/*replacing this with function just accepting distance as parameter and returning penalty!
    public function calculateRangePenalty(OffsetCoordinate $pos, BaseShip $target)
    {
        $targetPos = $target->getHexPos();
        $dis = mathlib::getDistanceHex($pos, $targetPos);

        $rangePenalty = ($this->rangePenalty * $dis);
        $notes = "shooter: " . $pos->q . "," . $pos->r . " target: " . $targetPos->q . "," . $targetPos->r . " dis: $dis, rangePenalty: $rangePenalty";
        return Array("rp" => $rangePenalty, "notes" => $notes);
    }
*/
    public function calculateRangePenalty($distance)
    {
        $rangePenalty = $this->rangePenalty * $distance;
        return $rangePenalty;
    }


	protected function isFtrFiringNonBallisticWeapons($shooter, $fireOrder)
    {
        // first get the fighter that is armed with this weapon
        // We have to go looking for it because the shooter is a flight,
        // not an individual fighter.
        $fighterSys = $shooter->getFighterBySystem($fireOrder->weaponid);

        // now recheck all the fighter's weapons
	///but do NOT count defensive fire!
        foreach ($fighterSys->systems as $weapon) {
			if ($weapon instanceOf Weapon){
				//if (!$weapon->ballistic && $weapon->firedOnTurn(TacGamedata::$currentTurn)) {
				if (!$weapon->ballistic && $weapon->firedOffensivelyOnTurn(TacGamedata::$currentTurn)) { //defensive fire is out of player control, mostly, so should not be penalized
					return true;
				}
			}
        }

        return false;
    } //endof function isFtrFiringNonBallisticWeapons

    public function getFiringHex($gamedata, $fireOrder){
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
		$pos = $shooter->getHexPos();
		$launchPos = null;		
		
        if ($this->ballistic) {
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $launchPos = $movement->position;
        } else {
            $launchPos = $pos;
        }
       return $launchPos; 
	}//endof getFiringHex

    /*Marcin Sawicki: is there a chance that defender has choice of target section? */
    public function isTargetAmbiguous($gamedata, $fireOrder)
    {
        if ($fireOrder->calledid != -1) return false; //no choice for called shot
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);


        if ($target == null) return true; //target is a hex rather than unit, probability of ambiguosness is relatively high
        if ($target instanceof FighterFlight) return false; //shot at fighter may be ambiguous, but there's no point in postponing the decision!

        $pos = $shooter->getCoPos();
        $ambiguous = false;

		$launchHex = $this->getFiringHex($gamedata, $fireOrder);	
		$launchPos = mathlib::hexCoToPixel($launchHex);
/*
		if($this->ballistic){
			$movement = $shooter->getLastTurnMovement($fireOrder->turn);
			$launchPos = mathlib::hexCoToPixel($movement->position);
		}else{
			$launchPos = $pos;
		}
*/		
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

		//half-phased and non-half-phased ship cannot ram each other
		$shooterHalfphased = Movement::isHalfPhased($shooter, $gamedata->turn);
		$targetHalfphased = Movement::isHalfPhased($target, $gamedata->turn);
		if ($shooterHalfphased != $targetHalfphased) return 0;

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

		//range penalty - based on ramming units' speed (typical ramming has no range penalty, but HKs do!
		$ownSpeed = abs($shooter->getSpeed()); 
		$speedPenalty = $this->rangePenalty * $ownSpeed;
		$hitChance -= $speedPenalty;
		
		$hitChance = round($hitChance * 5); //convert d20->d100

		$hitLoc = null;
		$hitLoc = $target->getHitSection($shooter, $fireOrder->turn);
	    	$target->setExpectedDamage($hitLoc, $hitChance, $this, $shooter);


		$notes = $fireOrder->notes . "RAMMING, final hit chance: $hitChance";
		$fireOrder->chosenLocation = $hitLoc;
		$fireOrder->needed = $hitChance;
		$fireOrder->notes = $notes;
		$fireOrder->updated = true;
	}//endof function calculateHitBaseRam



 /*calculate base chance to hit (before any interception is applied) - Marcin Sawicki*/
    public function calculateHitBase(TacGamedata $gamedata, FireOrder $fireOrder){
		$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too! - certainly important for calculating hit chance...
		if ($this->isRammingAttack) return $this->calculateHitBaseRam($gamedata, $fireOrder);
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);

        if($target == null){ //Somehow a hex targeted weapon made it to the normal fire function, don't proceed as it'll bug out.
                    $fireOrder->needed = 0; // Auto-miss!
                    $fireOrder->updated = true;
                    $this->uninterceptable = true;
                    $this->doNotIntercept = true;
                    $fireOrder->pubnotes .= "<br>ERROR: Null target shot attempted in normal fire routines.";
    
                    return;
                }

        $pos = $shooter->getHexPos();
		$targetPos = $target->getHexPos();
        $jammermod = 0;
        $jinkSelf = 0;
        $jinkTarget = 0;
        $defence = 0;
        $mod = 0;
        $oew = 0;
		
		$launchPos = $this->getFiringHex($gamedata, $fireOrder);
/*			
        if ($this->ballistic) {
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $launchPos = $movement->position;

        } else {
            $launchPos = $pos;
        }
*/
        if (!$this->isInDistanceRange($shooter, $target, $fireOrder)) {
            // Target is not in distance range. Auto-miss.
            $notes = ' Target moved out of range. ';
            $fireOrder->needed = 0; //auto-miss
            $fireOrder->notes .= $notes;
            $fireOrder->updated = true;
            return;
        }

        /*replaced by different calculation later
		$rp = $this->calculateRangePenalty($launchPos, $target);		
        $rangePenalty = $rp["rp"];
		*/

        /*//Old Jinking logic
        if ($shooter instanceof FighterFlight) $jinkSelf = Movement::getJinking($shooter, $gamedata->turn);  //count own jinking always

        if ($target instanceof FighterFlight) {
            if ((!($shooter instanceof FighterFlight)) || $this->ballistic) //non-fighters and ballistics always affected by jinking
            {
                $jinkTarget = Movement::getJinking($target, $gamedata->turn);
            } elseif ($jinkSelf > 0 || mathlib::getDistance($shooter->getCoPos(), $target->getCoPos()) > 0) { //fighter direct fire unaffected at range 0
                $jinkTarget = Movement::getJinking($target, $gamedata->turn);
            }
        }
		// if jinking is ignored - make it so
		if($this->ignoreJinking){
			$jinkTarget = 0;
			$jinkSelf = 0;
		}	
        */
        //New block to take into account jinking ships!
        if ($shooter instanceof FighterFlight || $shooter->jinkinglimit > 0) $jinkSelf = Movement::getJinking($shooter, $gamedata->turn);  //count own jinking always

        if ($target instanceof FighterFlight || $target->jinkinglimit > 0) {
            if ((!($shooter instanceof FighterFlight)) || $this->ballistic) //non-fighters and ballistics always affected by jinking
            {
                $jinkTarget = Movement::getJinking($target, $gamedata->turn);
            } elseif ($jinkSelf > 0 || mathlib::getDistance($shooter->getCoPos(), $target->getCoPos()) > 0) { //fighter direct fire unaffected at range 0
                $jinkTarget = Movement::getJinking($target, $gamedata->turn);
            }
        }
		// if jinking is ignored - make it so
		if($this->ignoreJinking){
			$jinkTarget = 0;
			$jinkSelf = 0;
		}	


        $dew = $target->getDEW($gamedata->turn);
        $bdew = EW::getBlanketDEW($gamedata, $target);
        $sdew = EW::getSupportedDEW($gamedata, $target);
        if ($this->useOEW) {
            $oew = $shooter->getOEW($target, $gamedata->turn);
            $soew = EW::getSupportedOEW($gamedata, $shooter, $target);
			$dist = EW::getDistruptionEW($gamedata, $shooter);
            $oew -= $dist;
            if ($oew < 1) { //less than required for a lock-on
				$oew = max(0,$oew); //OEW cannot be negative
				$soew = 0; //no lock-on negates SOEW, if any
			}	
        } else {
            $soew = 0;
            $oew = 0;
        }

		if (!($shooter instanceof FighterFlight) && !$shooter->ignoreManoeuvreMods) {//Mindriders ignore pivot and roll penalties - DK 17.7.24
            if ((!$shooter->agile) && Movement::isRolling($shooter, $gamedata->turn)) { //non-agile ships suffer as long as they're ROLLING
                $mod -= 3;
            } else if ($shooter->agile && Movement::hasRolled($shooter, $gamedata->turn)) { //Agile ships suffer on the turn they actually rolled!
				$mod -= 3;
			}
            if (Movement::hasPivoted($shooter, $gamedata->turn) /*&& !$this->ballistic*/) {
                $mod -= 3;
            }
        }

        if ($fireOrder->calledid != -1) {
            $mod += $this->getCalledShotMod();
            if ($target->base) $mod += $this->getCalledShotMod();//called shots vs bases suffer double penalty!
			//Add bonus to hit on Called Shots for certain systems, like Aegis Sensor Pod
            $calledSystem = $target->getSystemById($fireOrder->calledid);
            $mod += $calledSystem->checkforCalledShotBonus();             	
        }

        if ($shooter instanceof OSAT && Movement::hasTurned($shooter, $gamedata->turn)) { //leaving instanceof OSAT here - assuming MicroSATs will not suffer this penalty (Dovarum seems to be able to turn/pivot like a superheavy fighter it's based on)
            $mod -= 1;
        }

        if ($this->ballistic) { //getHitChanceMod should get explicit position only if it cannot be derived from shooter - otherwise results at rng 0 are incorrect!
            $posmod = $launchPos; //$pos;
        } else {
            $posmod = null;
        }
        $mod += $target->getHitChanceMod($shooter, $posmod, $gamedata->turn, $this);
        $mod += $this->getWeaponHitChanceMod($gamedata->turn);
		$mod += $shooter->toHitBonus;//Some ships have bonuses or minuses to hit on all weapons e.g. Elite Crew, Poor Crew and Markab Fervor 

        $ammo = $this->getAmmo($fireOrder);
        if ($ammo !== null) {
            $mod += $ammo->getWeaponHitChanceMod($gamedata->turn);
        }

        //Fighters direct fire ignore all defensive EW, be it DEW, SDEW or BDEW
        //and use OB instead of OEW
        if ($shooter instanceof FighterFlight) {
            if (Movement::getCombatPivots($shooter, $gamedata->turn) > 0) {
            	 if(!$shooter->ignoreManoeuvreMods) $mod -= 1;
            }

            $effectiveOB = $shooter->offensivebonus;
            $firstFighter = $shooter->getSampleFighter();
            $OBcrit = $firstFighter->hasCritical("tmpsensordown");
            if ($OBcrit > 0) {
                $effectiveOB = $shooter->offensivebonus - $OBcrit;
                $effectiveOB = max(0, $effectiveOB); //cannot bring OB below 0!
            }
  
            if (!$this->ballistic) {
                $dew = 0;
                $bdew = 0;
                $sdew = 0;
                $oew = $effectiveOB;
                //$soew = 0; //fighters CAN receive SOEW (fractional, SOEW calculation takes this into account)
            } else { //ballistics use of OB is more complicated
                $oew = 0;
                $shooterlosBlocked  = $this->isLoSBlocked($pos, $targetPos, $gamedata); //Defaults false e.g. line of sight NOT blocked.
                //$soew = 0; //fighters CAN receive SOEW (fractional, SOEW calculation takes this into account)
                if (!($shooter->isDestroyed() || $shooter->getFighterBySystem($fireOrder->weaponid)->isDestroyed())) {

                    // Check if skindancing is Active (True) or Failed. (Aborted counts as inactive).
                    $isSkindancing = false;
                    foreach ($shooter->skinDancing as $status) {
                        if ($status === true || $status === 'Failed') {
                            $isSkindancing = true;
                            break;
                        }
                    }

                    if ($shooter->hasNavigator && !$shooterlosBlocked && !$isSkindancing) {// Fighter has navigator and Line of Sight. Flight always benefits from offensive bonus.
                        $oew = $effectiveOB;
                    } else { // Check if target is in current weapon arc
                        $relativeBearing = $shooter->getBearingOnUnit($target);
                        if (mathlib::isInArc($relativeBearing, $this->startArc, $this->endArc) && !$shooterlosBlocked && !$isSkindancing) {
                            // Target is in current launcher arc and has Line of Sight and is NOT skindancing. Flight benefits from offensive bonus.
                            // Now check if the fighter is not firing any non-ballistic weapons
                            if (!$this->isFtrFiringNonBallisticWeapons($shooter, $fireOrder)) {
								$oew = $effectiveOB;
							}
                        }
                    }
                }
            }
			if($oew <1) $soew = 0;//if OEW is negated, so is SOEW
        }
	
		/* replaced by code allowing partial locks
			if (($oew < 1) && (!($shooter instanceof FighterFlight))) {
				$rangePenalty = $rangePenalty * 2;
			} elseif ($shooter->faction != $target->faction) {
		*/
		$noLockPenalty = 0;
		$noLockMod = 0;
		if ($oew < 0.5){
			$noLockPenalty = 1;
		}else if ($oew < 1){ //OEW beteen 0.5 and 1 is achievable for targets of Distortion EW
			$noLockPenalty = 0.5;
		}
		//$noLockMod =  $rangePenalty * $noLockPenalty; //moved lower!
			
		$jammerValue = 0;
		//if ($shooter->faction != $target->faction) { //checked by ability itself now!
			//jammerValue takes Jammer state, criticals, Advanced and Improved sensors into account
			$jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter" => $shooter, "target" => $target));
			
			if ($jammerValue>0) {
				$soew = 0; //no lock-on negates SOEW, if any
			}
			
			/*no longer needed, Advanced/Improved Sensors moved to Jammer special ability itself
			$jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter" => $shooter, "target" => $target));
			//Improved/Advanced Sensors
			if ($jammerValue > 0){ //else no point
				if ($shooter->hasSpecialAbility("AdvancedSensors")) {
					$jammerValue = 0; //negated
				} else if ($shooter->hasSpecialAbility("ImprovedSensors")) {
					$jammerValue = $jammerValue * 0.5; //halved
				}
			}
			*/		
		//}			
		//$jammermod = $rangePenalty * max(0,($jammerValue-$noLockPenalty));//no lock and jammer work on the same thing, but they still need to be separated (for jinking). //moved lower!

		//new calculation of range penalty - with different way of counting for weapons that double range itself (...antimatter...)
		$distanceForPenalty = mathlib::getDistanceHex($launchPos, $targetPos);
		$rangePenalty = $this->calculateRangePenalty($distanceForPenalty);
		$noLockMod = 0;
		$jammermod = 0; //no lock and jammer work on tI havehe same thing, but they still need to be separated (for jinking).
		
		// if EW is ignored - make it so (and also Jammer and no lock modifier, which are derived from EW as well)
		if($this->ignoreAllEW){
			$noLockPenalty = 0;
			$jammerValue = 0;
			$dew = 0;
			$bdew = 0;
			$sdew = 0;
			$oew = 0;
			$soew = 0;
		}		
		
		$rngPenaltyMultiplier = max($noLockPenalty,$jammerValue);
		if(!$this->noLockPenalty) $rngPenaltyMultiplier = 0;
		if($rngPenaltyMultiplier > 0){			
			if($this->doubleRangeIfNoLock){//double range (mostly Antimatter weapons)
				$modifiedDistance = $distanceForPenalty * (1+$noLockPenalty);
				$noLockMod = $this->calculateRangePenalty($modifiedDistance)-$rangePenalty;//modifier: difference between penalty at modified range and original range
				$modifiedDistance = $distanceForPenalty * (1+max(0,($jammerValue-$noLockPenalty)));
				$jammermod = $this->calculateRangePenalty($modifiedDistance)-$rangePenalty;	//modifier: difference between penalty at modified range and original range			
			}else{//double penalty (standard)
				$noLockMod =  $rangePenalty * $noLockPenalty;
				$jammermod = $rangePenalty * max(0,($jammerValue-$noLockPenalty));
			}
		}

        if (!($shooter instanceof FighterFlight) && !($shooter instanceof OSAT)) {//leaving instanceof OSAT here - MicroSATs will be omitted as they're SHFs
            $CnC = $shooter->getSystemByName("CnC");
            $mod -= ($CnC->hasCritical("PenaltyToHit", $gamedata->turn - 1));
            $mod -= ($CnC->hasCritical("tmphitreduction", $gamedata->turn, $gamedata->turn));
            $mod -= ($CnC->hasCritical("ShadowPilotPain", $gamedata->turn));
        }
        $firecontrol = $this->fireControl[$target->getFireControlIndex()];
        
		if ($shooter->hasSpecialAbility("HyachComputer")) { //Does ship have a Hyach Computer that might add bonus FC?
			$bonusFireControl = 0; //initialise
			$computer = $shooter->getSystemByName("HyachComputer"); //Find computer.
			$FCIndex = $target->getFireControlIndex(); //Find out FC category of the target.
			$bonusFireControl = $computer->getFCBonus($FCIndex, $gamedata->turn);  //Use FCIndex to check if Computer has any BFCP allocated to that FC category.
			$firecontrol += $bonusFireControl; //Add to $firecontrol.
		}

        //Check Line of Sight has been maintained by ship for Ballistic weapons after launch (Fighters checked separately above).
        if($this->ballistic && (!$shooter instanceof FighterFlight) && !$this->hasSpecialLaunchHexCalculation){
            if(!$firecontrol <= 0){ //No point checking for LoS if FC is a 0 or lower anyway!
                $losBlocked  = $this->isLoSBlocked($pos, $targetPos, $gamedata); //Defaults false e.g. line of sight NOT blocked.
               
                if($losBlocked ){ //Line of Sight is blocked!
                    if($this instanceof AmmoMissileRackS) { //Only zero LAUNCHER FC on AmmoMissileLaunchers, missiles have own guidance e.g. bonus. 
                        if (property_exists($this, 'basicFC') && is_array($this->basicFC) && !empty($this->basicFC)) {                         
                            $firecontrol -= $this->basicFC[$target->getFireControlIndex()];
                        }                       
                    } else { //Everything else e.g. torpedoes, just has it's FC zeroed
                        $firecontrol = 0; //Null weapon firecontrol when no Line of Sight.                       
                    }
                }
            }    
        }        

		if (TargetingArrayHandler::targetingArraysExist()){ //Do Targeting Array exist in game?		
			$mod += TargetingArrayHandler::getHitBonus($gamedata, $fireOrder, $shooter, $target);
		}
        		
		//advanced sensors: negates BDEW and SDEW, unless target is unit of advanced race
		if ( ($target->factionAge < 3) && ($shooter->hasSpecialAbility("AdvancedSensors")) ){
			$bdew = 0;
			$sdew = 0;
		}			
		
		//half-phasing: +4 vs gunfire, +8 vs ballistics, -10 to own fire
		$halfphasemod = 0;
		$shooterHalfphased = Movement::isHalfPhased($shooter, $gamedata->turn);
		$targetHalfphased = Movement::isHalfPhased($target, $gamedata->turn);
		if ($shooterHalfphased) $halfphasemod = 10;
		if ($targetHalfphased) {
			if($this->ballistic){
				$halfphasemod += 8;
			}else{
				$halfphasemod += 4;
			}
		}
		


        $hitPenalties = $dew + $bdew + $sdew + $rangePenalty + $jinkSelf + max($jammermod, $jinkTarget) + $noLockMod + $halfphasemod;
        $hitBonuses = $oew + $soew + $firecontrol + $mod;
        $hitLoc = null;

        if ($this->ballistic) {
            $hitLoc = $target->getHitSectionPos(mathlib::hexCoToPixel($launchPos), $fireOrder->turn);
            $defence = $target->getHitSectionProfilePos(mathlib::hexCoToPixel($launchPos));
        } else {
            $hitLoc = $target->getHitSection($shooter, $fireOrder->turn);
            $defence = $target->getHitSectionProfile($shooter);
        }
        
        //This section added to count new critical that raises Defence profiles and add to hit chance - June 2024 DK
        if(!$target instanceof FighterFlight){
	        $defenceMod = 0;
	        $targetCnC = $target->getSystemByName("CnC");        
	        $defenceMod = $targetCnC->hasCritical("ProfileIncreased");
	        $defence += $defenceMod;
		}else{             
            if ($target->hasSpecialAbility("Petals")){ //Does ship have Specialists system?
                $petals = $target->getSystemByName("FtrPetals");
                if($petals->isActive()){
	                $defence += 1;
                } 
            }           
        }    
     
        $goal = $defence + $hitBonuses - $hitPenalties;

		
        $change = round($goal * 5); //d20 to d100: ($goal/20)*100
		$target->setExpectedDamage($hitLoc, $change, $this, $shooter);

        //range penalty already logged in calculateRangePenalty... rpenalty: $rangePenalty,
        //interception penalty not yet calculated, will be logged later
        //$notes = $rp["notes"] . ", defence: $defence, DEW: $dew, BDEW: $bdew, SDEW: $sdew, Jammermod: $jammermod, no lock: $noLockMod, jink: $jinkSelf/$jinkTarget, OEW: $oew, SOEW: $soew, F/C: $firecontrol, mod: $mod, goal: $goal, chance: $change";
		$notes = $distanceForPenalty . ", defence: $defence, DEW: $dew, BDEW: $bdew, SDEW: $sdew, Jammermod: $jammermod, no lock: $noLockMod, jink: $jinkSelf/$jinkTarget, OEW: $oew, SOEW: $soew, F/C: $firecontrol, mod: $mod, goal: $goal, chance: $change, ";
        
		//update by arc - this caused some trouble and I want it logged...		
        $relativeBearing = $target->getBearingOnUnit($shooter);
		$notes .= 'bearing from target ' . $relativeBearing . ', ';

        $fireOrder->chosenLocation = $hitLoc;
        $fireOrder->needed = $change;
        $fireOrder->notes = $notes;
        $fireOrder->updated = true;
    } //endof calculateHitBase


    public function getIntercept($gamedata, $fireOrder)
    {
        $count = 0;
        $intercept = 0;
        if ($this->uninterceptable)
            return 0;

        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        foreach ($gamedata->ships as $ship) {
            $fireOrders = $ship->getAllFireOrders();
            foreach ($fireOrders as $fire) {

                if ($fire->type == "intercept" && $fire->targetid == $fireOrder->id) {

                    $deg = $count;
                    if ($this->ballistic || $this->noInterceptDegradation)
                        $deg = 0;

                    $interceptWeapon = $ship->getSystemById($fire->weaponid);
                    if (!$interceptWeapon instanceof Weapon) {
                        //debug::log("DING. You cant intercept with a non-weapon....");
                        //debug::log($interceptWeapon->displayName);
                        continue;
                    }
                    $i = $interceptWeapon->getInterceptRating(TacGamedata::$currentTurn) - $deg;

                    if ($i < 0
                        || $interceptWeapon->destroyed
                        || $interceptWeapon->isOfflineOnTurn(TacGamedata::$currentTurn)) {
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


    public function getInterceptRating($turn)
    {
        return $this->intercept;
    }


    public function getNumberOfIntercepts($gamedata, $fireOrder)
    {
        $count = 0;
        foreach ($gamedata->ships as $ship) {
            $fireOrders = $ship->getAllFireOrders();
            foreach ($fireOrders as $fire) {
                if ($fire->type == "intercept" && $fire->targetid == $fireOrder->id) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /*some weapons have different hit chance for further shots*/
    /*default - use grouping*/
    public function getShotHitChanceMod($shotInSequence)
    {
        $mod = $this->grouping * $shotInSequence;
        return $mod;
    }

    /*Marcin Sawicki - September 2019 - method called when weapon is firing defensively;
    	basically doing nothing, but some weapon may need to put some special effects here
	called in firing.php addToInterceptionTotal - if a weapon is for some reason NOT automatically assigned to intercept, 
		does not use this method and needs a backlash - it should call fireDefensively on its own (or devise own method of adding said backlash)
    */
    public function fireDefensively($gamedata, $interceptedWeapon)
    {
    	$this->firedDefensivelyAlready++; //may be used to check if weapon was already fired, in case of multiple defensive shots but only single backlash effect
    }
	
	
    /*Marcin Sawicki - October 2017 - new version of firing procedure - assuming all data is already prepared*/
    public function fire($gamedata, $fireOrder)
    {
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        if($target == null) {
            $rolled = Dice::d(100);
            $fireOrder->notes .= " FIRING SHOT: rolled: $rolled, needed: $$fireOrder->needed\n";
            $fireOrder->rolled = $rolled; //I think this is needed to generate a combat log note.           
            return; //Somehow a hex targeted weapon made it to the normal fire function, don't proceed.
        }    


        $fireOrder->needed -= $fireOrder->totalIntercept;
        $notes = "Interception: " . $fireOrder->totalIntercept . " sources:" . $fireOrder->numInterceptors . ", final to hit: " . $fireOrder->needed;
        $fireOrder->notes .= $notes;

        $pos = null; //functions will properly calculate from firing unit, which is important at range 0
        //$pos = $shooter->getCoPos();
        if ($this->ballistic) {
//            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
//            $pos = $movement->position;
            $pos = $this->getFiringHex($gamedata, $fireOrder);	
        }

        $shotsFired = $fireOrder->shots; //number of actual shots fired
        if ($this->damageType == 'Pulse') {//Pulse mode always fires one shot of weapon - while 	$fireOrder->shots marks number of pulses for display purposes
            $shotsFired = 1;
			$fireOrder->shots = $this->maxpulses;
        }
        for ($i = 0; $i < $shotsFired; $i++) {
            $needed = $fireOrder->needed;
            if ($this->damageType != 'Pulse') {//non-Pulse weapons may use $grouping, too!
                $needed = $fireOrder->needed - $this->getShotHitChanceMod($i);
            }

            //for linked shot: further shots will do the same as first!
            if ($i == 0) { //clear variables that may be relevant for further shots in line
                $fireOrder->linkedHit = null;
            }
            $rolled = Dice::d(100);
            if ($this->isLinked && $i > 0) { //linked shot - number rolled (and effect) for furthr shots will be just the same as for first
                $rolled = $fireOrder->rolled;
            }

            //interception?
            if ($rolled > $needed && $rolled <= $needed + ($fireOrder->totalIntercept * 5)) { //$fireOrder->pubnotes .= "Shot intercepted. ";
                if ($this->damageType == 'Pulse') {
                    $fireOrder->intercepted += $this->maxpulses;
                } else {
                    $fireOrder->intercepted += 1;
                }
            }

            //If skin-dancing shots which have a front arc automatically hit.
            if (!$this->ballistic && isset($shooter->skinDancing[$target->id]) && $shooter->skinDancing[$target->id] === true) {
                $inFrontArc = mathlib::isInArc(0, $this->startArc, $this->endArc);

                if ($inFrontArc) {
                    $rolled = 1;//Automatically roll best result when skindancing
                }
            }            

            $fireOrder->notes .= " FIRING SHOT " . ($i + 1) . ": rolled: $rolled, needed: $needed\n";
            $fireOrder->rolled = $rolled; //might be useful for weapon itself, too - like counting damage for Anti-Matter



            //hit?
            if ($rolled <= $needed) {
                $hitsRemaining = 1;

                if ($this->damageType == 'Pulse') { //possibly more than 1 hit from a shot
                    $hitsRemaining = $this->rollPulses($gamedata->turn, $needed, $rolled); //this takes care of all details
                }

                while ($hitsRemaining > 0) {
                    $hitsRemaining--;
                    $fireOrder->shotshit++;
					//19.12.2024 - clear any previous location for Vree-layout ships; this will be for entire volley - eg. single Pulse, but entire Raking shot
					$target->clearVreeHitSectionChoice($shooter->id, $fireOrder);
					
                    $this->beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
                }
            }
        }
	    /*
        //MOVED TO CRITICALS.PHP to CAPTURE SUSTAINED WEAPONS THAT DIDN@T FIRE THEIR LAST SHOT - DK - Dec 2025
		//for last segment of Sustained shot - force shutdown!
		$newExtraShots = $this->overloadshots - 1; 	
		if( $newExtraShots == 0 ) {
			$crit = new ForcedOfflineOneTurn(-1, $this->unit->id, $this->id, "ForcedOfflineOneTurn", $gamedata->turn);
			$crit->updated = true;
			$crit->newCrit = true; //force save even if crit is not for current turn
			$this->criticals[] =  $crit;
		}
        */
        $fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
		TacGamedata::$lastFiringResolutionNo++;    //note for further shots
		$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;//mark order in which firing was handled!
	    
    } //endof fire

    protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata)
    {
        $damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);        
        $this->damage($target, $shooter, $fireOrder, $gamedata, $damage);
    }

    protected function getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location = null)
    {
        /*Location only relevant for Flash damage, which overkills to a new roll on hit table rather than to Structure*/
        /*$damageWasDealt=true indicates this is actual overkill, instead of just passing through previously destroyed system that nevertheless was chosen as target*/
        $okSystem = null;
        $noOverkill = (!$this->doOverkill) && ($this->noOverkill || ($this->damageType == 'Piercing'));//either explicitly stated or Piercing mode shot

        if ($target instanceof FighterFlight) {
			return null;
        }

        if ($noOverkill && $damageWasDealt) {  //weapon trait: no overkill (if this is true overkill only!)
            return null;
        }

        if ($this->damageType == 'Flash') {// If overkill comes from flash damage, pick a new target in default way instead of typicaloverkill!
            $okSystem = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $location); //for Flash it won't return destroyed system other than PRIMARY Structure
        }

        if (($okSystem == null) || $okSystem->isDestroyed() || ($okSystem->getRemainingHealth() == 0)) { //overkill to Structure system is mounted on
            $okSystem = $target->getStructureSystem($system->location);
        }

        if (($okSystem == null) || $okSystem->isDestroyed() || ($okSystem->getRemainingHealth() == 0)) { //overkill to PRIMARY Structure
            if ($this->damageType == 'Piercing') { //Piercing does not overkill to PRIMARY
                return null;
            } else {
                $okSystem = $target->getStructureSystem(0);
            }
        }

        if (($okSystem == null) || $okSystem->isDestroyed() || ($okSystem->getRemainingHealth() == 0)) { //nowhere to overkill to
            return null;
        }

        return $okSystem;
    }//endof function getOverkillSystem


    /*collateral damage from a Flash explosion (if any), called from function damage*/
    public function doCollateralDamage($target, $shooter, $fireOrder, $gamedata, $flashDamageAmount)
    {
        $explosionPos = $target->getCoPos();
        $ships1 = $gamedata->getShipsInDistance($target, 0);
        foreach ($ships1 as $ship) {
            if ($ship === $target) continue;// make certain the target doesn't get the damage twice
            if ($ship->isDestroyed()) continue; //no point allocating
            $relativeBearing = $ship->getBearingOnUnit($target);//actual direction the damage comes from is from unit directly hit!
            if ($ship instanceof FighterFlight) {
                foreach ($ship->systems as $fighter) {
                    if ($fighter == null || $fighter->isDestroyed()) {
                        continue;
                    }
                    //this will not be entirely correct (may allocate to inappropriate armor) - but to allocate to appropriate armor, function accepting bearing (or section hit as array) would be needed
                    $this->doDamage($ship, $shooter, $fighter, $flashDamageAmount, $fireOrder, $explosionPos, $gamedata, false);
                }
            } else {
                $loc = $ship->doGetHitSectionBearing($relativeBearing); //full array
                $tmpLocation = $loc["loc"];
                $system = $ship->getHitSystem($target, $fireOrder, $this, $gamedata, $tmpLocation);
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
//			$movement = $shooter->getLastTurnMovement($fireOrder->turn);
//			$launchPos = mathlib::hexCoToPixel($movement->position);			
			$launchHex = $this->getFiringHex($gamedata, $fireOrder);	
			$launchPos = mathlib::hexCoToPixel($launchHex);
			if((!($tmpLocation > 0)) && (!$forcePrimary)){ //location not yet found or PRIMARY (reassignment causes no problem)
				$tmpLocation = $target->getHitSectionPos($launchPos, $fireOrder->turn);
			}
		}else{
			if((!($tmpLocation > 0)) && (!$forcePrimary)){ //location not yet found or PRIMARY (reassignment causes no problem)
				$tmpLocation = $target->getHitSection($shooter, $fireOrder->turn);
			}
		}
		
		//let's recognize "MCV sized" by number of structures rather than technical target size! (important for eg. Shadows, which are laid out damaged as MCVs even if they use larger ship sizes)
		$noOfStructures = 0;		
		if(($this->damageType == 'Piercing') && (!$target instanceOf FighterFlight)){ //irrelevant for non-Piercing shots!
			foreach ($target->systems as $struct) if ($struct instanceOf Structure) $noOfStructures++;
		}
        
        //if (($target->shipSizeClass > 1) && ($this->damageType == 'Piercing')) { //Piercing damage will be split into 3 parts vs units larger thgan MCVs
		if (($noOfStructures > 1) && ($this->damageType == 'Piercing')) {//special handling of Piercing on ships with 2 or more Structures - otherwise it will degenerate to Standard (no overkill)
            if($this->ballistic){
                $facingLocation = $target->getHitSectionPos($launchPos, $fireOrder->turn, true);
            }else{
                $facingLocation = $target->getHitSection($shooter, $fireOrder->turn, true); //do accept destroyed section as location
            }
            
            //find out opposite section...
            if($this->ballistic){ //firing position is explicitly declared
				$relativeBearing = $target->getBearingOnPos($launchPos);
			}else{ //check from shooter...
                $relativeBearing = $target->getBearingOnUnit($shooter);
			}
            
            //Rules update: piercing shots on HCVs coming from the side should split into 2 parts, not 3.
            //Check for HCV / HCVLeftRight and modify outLocation to match facingLocation if angle is from the side.
            $forceTwoWaySplit = false; 
            if ($target instanceof HeavyCombatVesselLeftRight) {
                //HCV Left/Right have 3-way split on 60-120 and 240-300 arcs. Everything else is 2-way.
                if (!Mathlib::isInArc($relativeBearing, 30, 150) && !Mathlib::isInArc($relativeBearing, 210, 330)) {
                    $forceTwoWaySplit = true;
                }
            } elseif ($target instanceof HeavyCombatVessel) { //Standard HCV
                //Standard HCV have 3-way split on Forward (300-30) and Aft (150-210) arcs. Everything else is 2-way.
                if (!Mathlib::isInArc($relativeBearing, 330, 30) && !Mathlib::isInArc($relativeBearing, 150, 210)) {
                    $forceTwoWaySplit = true;
                }
            }

            if ($forceTwoWaySplit){
                $outLocation = $facingLocation;
            }else{
                //Standard logic: Find opposite section
                $oppositeBearing = Mathlib::addToDirection($relativeBearing, 180);
                $outLocation = $target->doGetHitSectionBearing($oppositeBearing); 
                $outLocation = $outLocation["loc"];
            }


            //find how big damage is done - split to 3 equal parts; if can't be equal, bigger portions will go to PRIMARY and facing parts
            if ($outLocation == $facingLocation) { //shot enters and exits through the same section - narrow point - split into 2 parts only!
                $damageOut = 0;
                $damagePRIMARY = floor($damage / 2);
                $damageEntry = ceil($damage / 2);
            } else { //standard split to 3 parts; defender allocates, so protecting Primary
                $damageEntry = ceil($damage / 3);
                $damagePRIMARY = floor($damage / 3);
                $damageOut = $damage - $damageEntry - $damagePRIMARY;
            }
            //first part: facing structure
            $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $facingLocation);
            $this->doDamage($target, $shooter, $system, $damageEntry, $fireOrder, $launchPos, $gamedata, false, $facingLocation);
            //second part: PRIMARY Structure
            $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, 0);
            $this->doDamage($target, $shooter, $system, $damagePRIMARY, $fireOrder, $launchPos, $gamedata, false, 0);
            //last part: opposite Structure
            $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $outLocation);
            $this->doDamage($target, $shooter, $system, $damageOut, $fireOrder, $launchPos, $gamedata, false, $outLocation);
        } elseif (($this->damageType == 'Raking') && (!($target instanceof FighterFlight))) { //Raking hit... but not at fighters - that's effectively Standard shot!
            //split into rakes; armor will not need to be penetrated twice!
			$fireOrder->armorIgnored = array();//reset info about pierced armor 
            while ($damage > 0) {
                $rake = min($damage, $this->getRakeSize());
                $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $tmpLocation);
                $this->doDamage($target, $shooter, $system, $rake, $fireOrder, $launchPos, $gamedata, false, $tmpLocation);
                $damage = $damage - $rake;
            }
        } else { //standard mode of dealing damage
            if ($fireOrder->linkedHit == null) {
                $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $tmpLocation);
            } else {
                $system = $fireOrder->linkedHit;
            }
            if ($this->isLinked) { //further linked weapons will hit the exact same system!
                $fireOrder->linkedHit = $system;
            }			
            $this->doDamage($target, $shooter, $system, $damage, $fireOrder, $launchPos, $gamedata, false, $tmpLocation);
			//Flash weapon will cause collateral damage to other fighters in flight hit (collateral damage to other units was already handled) 
			if( ($this->damageType=='Flash') && ($target instanceof FighterFlight) ){ 
				foreach ($target->systems as $otherFighter) {
					if ($otherFighter == null || $otherFighter->isDestroyed() || $otherFighter->id==$system->id) {//do not damage destroyed fighter, or fighter hit directly
						continue;
					}
					$this->doDamage($target, $shooter, $otherFighter, $flashDamageAmount, $fireOrder, $launchPos, $gamedata, false, $tmpLocation);
				}		    
			}
        }
    }//endof function damage


    public function isInLaunchRange($shooter, $target, $fireOrder)
    {
        // gameData and fireOrder is needed to check if target has jammers
        return true;
    }

	
    /* returns armor protection of system 
    */
    public function getSystemArmourComplete($target, $system, $gamedata, $fireOrder, $pos = null)
    { 
		$armor = $this->getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
		//modify by AdvancedArmor if relevant
		$armor = $this->applyAdvancedArmor($system, $armor);
		$armor += $this->getSystemArmourAdaptive($target, $system, $gamedata, $fireOrder, $pos);
        return $armor;
    }//endof function getSystemArmourComplete
		
    public function getSystemArmourAdaptive($target, $system, $gamedata, $fireOrder, $pos = null)
    { 
		$shooter = $gamedata->getShipById($fireOrder->shooterid);
/*$ss = $this->weaponClass;
throw new Exception("getSystemArmourAdaptive! $ss");	*/			
		$armor = $system->getArmourAdaptive($target, $shooter, $this->weaponClass, $pos);
        return $armor;
    }//endof function getSystemArmourAdaptive
	
	
    public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos = null)
    { 
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $armor = 0;
        if (($pos == null) && ($this->ballistic)) { //source of attack not explicitly defined, and weapon is ballistic
 //           $movement = $shooter->getLastTurnMovement($fireOrder->turn);
 //           $pos = mathlib::hexCoToPixel($movement->position);
				$launchHex = $this->getFiringHex($gamedata, $fireOrder);	
				$pos = mathlib::hexCoToPixel($launchHex);
        }
        $armor = $system->getArmourBase($target, $shooter, $this->weaponClass, $pos);

        //$mod = $system->hasCritical("ArmorReduced", $gamedata->turn - 1);
		$mod = $system->hasCritical("ArmorReduced", $gamedata->turn ); //$inEffect variable should distinguish effect that are not immediately in effect
        $armor -= $mod;

        $armor = max(0, $armor); //at least 0

        return $armor;
    }//endof function getSystemArmourBase


    /*returns modified damage, NOT damage modifier*/
    /*this is different function that getDamageMod of unit!*/
    protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata)
    {               
        $damage = $damage - $damage * $this->dp; //$dp is fraction of shot that gets wasted!

        if ($this->rangeDamagePenalty > 0) {
            if ($pos != null) {
                $sourcePos = $pos;
            } else {
                $sourcePos = $shooter->getHexPos();
            }
            $dis = mathlib::getDistanceHex($sourcePos, $target);
            $damage -= round($dis * $this->rangeDamagePenalty); //round to avoid damage loss at minimal ranges!
        }

        //for Piercing shots at small targets (MCVs and smaller) - reduce damage by ~10% (by rules: -2 per die)
		//actually recognize this by number of structures instead of formal ship size - Shadow HCvs and MCVs are damaged as MCVs would have!
        //if (($this->damageType == 'Piercing') && ($target->shipSizeClass < 2)) $damage = $damage * 0.9;
		if ($this->damageType == 'Piercing'){
			$noOfStructures=0;
			if(!$target instanceOf FighterFlight){
				foreach ($target->systems as $struct) if ($struct instanceOf Structure) $noOfStructures++;
			}
			if($noOfStructures<2){ //damaged as MCV (or smaller)
				$damage = $damage * 0.9; 
			}				
		}

		$damage += $this->getBonusDamage();//For bonus damage such as Weapon Specialists.   

        $damage = max(0, $damage); //at least 0	    
        $damage = floor($damage); //drop fractions, if any were generated
        return $damage;
    } //endof function getDamageMod


    protected function getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder)
    {          
        $damage = $this->getDamage($fireOrder);      
        $damage = $this->getDamageMod($damage, $shooter, $target, $pos, $gamedata);                
        $damage -= $target->getDamageMod($shooter, $pos, $gamedata->turn, $this);
		
		/* first attempt of StarTrek shield
		$impactProtectionSystem = $target->getSystemProtectingFromImpactDamage($shooter, $pos, $gamedata->turn, $this, $damage);//damage-reducing system activating at weapon impact - other than shield (eg. Star Trek shield)
		if($impactProtectionSystem){ //some system can actually affect damage at this stage
			$damage = $impactProtectionSystem->doReduceImpactDamage($gamedata, $fireOrder, $target, $shooter, $this, $damage);
		}
		*/
		
        return $damage;
    }

/*
full Advanced Armor effects (by rules) for reference:
 - !!!Weapons fired by other advanced races ignore all of these advantages. 
 - !!!effective armor cannot get lower than 0 (before applying Adaptive part, if any)
 - Plasma weapons do not ignore half the value of advanced armor.
 - Matter weapons treat advanced armor as though it were two points less than listed. 
 - Armor-damaging weapons (e.g., molecular disruptors) do not use these abilities against advanced armor.
 - Electromagnetic weapons that cause effects other than damage do not affect a ship or fighter protected by advanced armor. 
 - EM weapons that cause damage still score this damage normally, but if they are listed as ignoring armor, they ignore only half of advanced armor (rounded up).
 - Breaching pods and docking clamps cannot attach to advanced armor. 
 - Tractor beams, gravitic shifters and the like will still function normally.
 - Ballistic weapons are anticipated by advanced armor due to their slower rate of approach. The armor’s value is considered 2 points higher versus any ballistic device (missile, torpedo, energy mine, etc.).
*/
	protected function applyAdvancedArmor($system, $armour){
		$returnArmour = $armour;
		//only do this if target is actually protected by advanced armor
		//and firing WEAPON age is not very advanced (<3 - less than Ancient)
		//Ancients themselves do not care!
		if( ($this->factionAge < 3) && ($system->advancedArmor)){
			if($this->ballistic){ //extra protection against ballistics
				$returnArmour += 2;
			}
			if($this->weaponClass == 'Matter'){ //slight vulnerability vs Matter
				$returnArmour += -2;
			}
			if($this->damageType == 'Flash' && ($system->hardAdvancedArmor)){
				$returnArmour = floor($returnArmour*2); 
			}
		}elseif($this->factionAge >= 3 && $system->hardAdvancedArmor){
			if($this->ballistic){ //extra protection against ballistics
				$returnArmour += 2;
			}
			if($this->weaponClass == 'Matter'){ //slight vulnerability vs Matter
				$returnArmour = floor($returnArmour/2);
			}
			if($this->damageType == 'Flash'){
				$returnArmour = floor($returnArmour*2);
			}   
//			if($this->weaponClass == 'Molecular'){
//				$returnArmour = floor($returnArmour/2);
//			}
		}else{ //NO ADVANCED ARMOR (effectively) - apply effect explicitly tied to damage type
			if($this->weaponClass == 'Matter'){ //Matter weapons ignore armor
				$returnArmour = 0;
			}
			if($this->weaponClass == 'Plasma'){ //Plasma weapons ignore (better) half of armor
				$returnArmour = floor($returnArmour/2);
			}
		}
		$returnArmour = max(0,$returnArmour);
		return $returnArmour;
	}

    protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location = null)
    {
        /*$pos ONLY relevant for FIGHTER armor if damage source position is different than one from weapon itself*/
        /*otherwise best leave null BUT fill $location!*/
        /*damageWasDealt indicates whether this hit already caused damage - important for overkill for some damage modes*/
        //if (!$system->isDestroyed()) { //else system was already destroyed, proceed to overkill
		if ($system->getRemainingHealth() > 0) { //Vree Structure systems are considered not there despite not being formally destroyed
            $damage = floor($damage);//make sure damage is a whole number, without fractions!
            $armour = $this->getSystemArmourComplete($target, $system, $gamedata, $fireOrder, $pos); //handles standard and Adaptive armor, as well as Advanced armor and weapon class modifiers
			// ...if armor-related modifications are needed, they should extend appropriate method (Complete or Base, as Adaptive should not be affected)
			// ...and doDamage should always call Complete


            //armor may be ignored for some reason... usually because of Raking mode :)
            $armourIgnored = 0;
            if (isset($fireOrder->armorIgnored[$system->id])) {
                if ($system->hardAdvancedArmor){
					$armourEffective = floor($system->armour/2);
					$armour = max(0,$armourEffective-$armourIgnored);
                }else{
                    $armourIgnored = $fireOrder->armorIgnored[$system->id];
                    $armour = $armour - $armourIgnored;
                }
            }
            $armour = max($armour, 0);

			//returned array: dmgDealt, dmgRemaining, armorPierced	
			$damage = $this->beforeDamagedSystem($target, $system, $damage, $armour, $gamedata, $fireOrder);
			$effects = $system->assignDamageReturnOverkill($target, $shooter, $this, $gamedata, $fireOrder, $damage, $armour, $pos, $damageWasDealt); //Also applies armor pierced for sustained shots  - DK 02.25
			$this->onDamagedSystem($target, $system, $effects["dmgDealt"], $effects["armorPierced"], $gamedata, $fireOrder);//weapons that do effects on hitting something
			$damage = $effects["dmgRemaining"];
			if ($this->damageType == 'Raking'){ //note armor already pierced so further rakes have it easier. All Sustained weapons are currently raking, so they can use standard method here - DK
				$armourIgnored = $armourIgnored + $effects["armorPierced"]; 
				$fireOrder->armorIgnored[$system->id] = $armourIgnored;
			}			
			
            $damageWasDealt = true; //actual damage was done! might be relevant for overkill allocation
        }

        if (($damage > 0) || (!$damageWasDealt)) {//overkilling!
            $overkillSystem = $this->getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $damageWasDealt, $location);
            if ($overkillSystem != null)
                $this->doDamage($target, $shooter, $overkillSystem, $damage, $fireOrder, $pos, $gamedata, $damageWasDealt, $location);
        }

    }

	/*weapons with special effects affecting damage done to system will redefine this - if effect should happen just before actual damage dealing*/
    protected function beforeDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder)
    {
        return $damage;
    }

	/*weapons with special effects affecting system hit will redefine this*/
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder)
    {
        return;
    }

	/*allow limited ammo */
	public function setAmmo($firingMode, $amount){
		if (isset($this->ammunition)){
			$this->ammunition = $amount;
		}
	}


        public function isInDistanceRange($shooter, $target, $fireOrder)
        {
			if(!$this->ballistic) return true; //non-ballistic weapons don't risk target moving out of range
            $distanceRange = max($this->range, $this->distanceRange); //just in case distanceRange is not filled! Then it's assumed to be the same as launch range
            if($distanceRange <=0 ) return true; //0 means unlimited range


            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            if(mathlib::getDistanceHex($movement->position,  $target) > $distanceRange )
            {
                $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
                return false;
            }

            return true;
        }


    /*allow changing of basic parameters for different firing modes...
        called in method fire()
    */
    public function changeFiringMode($newMode)
    { //change parameters with mode change
        //to display correctly in GUI, shipSystem.js changeFiringMode function also needs to be redefined
        $this->firingMode = $newMode;
        $i = $newMode;
        if (isset($this->priorityArray[$i])) $this->priority = $this->priorityArray[$i];
        if (isset($this->priorityAFArray[$i])){
			$this->priorityAF = $this->priorityAFArray[$i];
		}else{ //this means appropriate AF priority is not set yet - it is generated ans so must always be set! 
			$this->priorityAF = 0;
		}

		/*not used any more!
        if (isset($this->animationArray[$i])) $this->animation = $this->animationArray[$i];
        if (isset($this->animationImgArray[$i])) $this->animationImg = $this->animationImgArray[$i];
        if (isset($this->animationImgSpriteArray[$i])) $this->animationImgSprite = $this->animationImgSpriteArray[$i];
        if (isset($this->animationColor2Array[$i])) $this->animationColor2 = $this->animationColor2Array[$i];
        if (isset($this->animationWidthArray[$i])) $this->animationWidth = $this->animationWidthArray[$i];
        if (isset($this->animationExplosionTypeArray[$i])) $this->animationExplosionType = $this->animationExplosionTypeArray[$i];
        if (isset($this->explosionColorArray[$i])) $this->explosionColor = $this->explosionColorArray[$i];
        if (isset($this->trailLengthArray[$i])) $this->trailLength = $this->trailLengthArray[$i];
        if (isset($this->trailColorArray[$i])) $this->trailColor = $this->trailColorArray[$i];
        if (isset($this->projectilespeedArray[$i])) $this->projectilespeed = $this->projectilespeedArray[$i];
		*/

        if (isset($this->animationColorArray[$i])) $this->animationColor = $this->animationColorArray[$i];
        if (isset($this->animationExplosionScaleArray[$i])) $this->animationExplosionScale = $this->animationExplosionScaleArray[$i];

        if (isset($this->rangePenaltyArray[$i])) $this->rangePenalty = $this->rangePenaltyArray[$i];
        if (isset($this->rangeDamagePenaltyArray[$i])) $this->rangeDamagePenalty = $this->rangeDamagePenaltyArray[$i];
        if (isset($this->rangeArray[$i])) $this->range = $this->rangeArray[$i];
        if (isset($this->distanceRangeArray[$i])) $this->distanceRange = $this->distanceRangeArray[$i];
        if (isset($this->fireControlArray[$i])) $this->fireControl = $this->fireControlArray[$i];
        if (isset($this->loadingtimeArray[$i])) $this->loadingtime = $this->loadingtimeArray[$i];
        if (isset($this->turnsloadedArray[$i])) $this->turnsloaded = $this->turnsloadedArray[$i];
        if (isset($this->extraoverloadshotsArray[$i])) $this->extraoverloadshots = $this->extraoverloadshotsArray[$i];
        if (isset($this->uninterceptableArray[$i])) $this->uninterceptable = $this->uninterceptableArray[$i];
        if (isset($this->doNotInterceptArray[$i])) $this->uninterceptable = $this->doNotInterceptArray[$i];
        if (isset($this->shotsArray[$i])) $this->shots = $this->shotsArray[$i];
        if (isset($this->defaultShotsArray[$i])) $this->defaultShots = $this->defaultShotsArray[$i];
        if (isset($this->maxpulsesArray[$i])) $this->maxpulses = $this->maxpulsesArray[$i];
        if (isset($this->groupingArray[$i])) $this->grouping = $this->groupingArray[$i];
        if (isset($this->gunsArray[$i])) $this->guns = $this->gunsArray[$i];

        if (isset($this->damageTypeArray[$i])) $this->damageType = $this->damageTypeArray[$i];
        if (isset($this->weaponClassArray[$i])) $this->weaponClass = $this->weaponClassArray[$i];
        if (isset($this->minDamageArray[$i])) $this->minDamage = $this->minDamageArray[$i];
        if (isset($this->maxDamageArray[$i])) $this->maxDamage = $this->maxDamageArray[$i];
        if (isset($this->dpArray[$i])) $this->dp = $this->dpArray[$i];

        if (isset($this->noOverkillArray[$i])) $this->noOverkill = $this->noOverkillArray[$i];
				
        if (isset($this->rakingArray[$i])) $this->raking = $this->rakingArray[$i];
        
        if (isset($this->hextargetArray[$i])) $this->hextarget = $this->hextargetArray[$i];	
	    
        if (isset($this->startArcArray[$i])) $this->startArc = $this->startArcArray[$i];
        if (isset($this->endArcArray[$i])) $this->endArc = $this->endArcArray[$i];
	    
		if (isset($this->ignoreAllEWArray[$i])) $this->ignoreAllEW = $this->ignoreAllEWArray[$i];	
		if (isset($this->ignoreJinkingArray[$i])) $this->ignoreJinking = $this->ignoreJinkingArray[$i];	
	    
        if (isset($this->startArcArray[$i])) $this->startArc = $this->startArcArray[$i];
        if (isset($this->endArcArray[$i])) $this->endArc = $this->endArcArray[$i];
		
		if (isset($this->hidetargetArray[$i])) $this->hidetarget = $this->hidetargetArray[$i];  // GTS
		if (isset($this->noLockPenaltyArray[$i])) $this->noLockPenalty = $this->noLockPenaltyArray[$i];  // DK
				
		if (isset($this->calledShotModArray[$i])) $this->calledShotMod = $this->calledShotModArray[$i];  // DK		
		if (isset($this->specialRangeCalculationArray[$i])) $this->specialRangeCalculation = $this->specialRangeCalculationArray[$i];  // DK
		if (isset($this->specialHitChanceCalculationArray[$i])) $this->specialHitChanceCalculation = $this->specialHitChanceCalculationArray[$i];  // DK
			
		if (isset($this->interceptArray[$i])) $this->intercept = $this->interceptArray[$i];  // DK		
		if (isset($this->ballisticInterceptArray[$i])) $this->ballisticIntercept = $this->ballisticInterceptArray[$i];  // DK
        
		if (isset($this->canSplitShotsArray[$i])) $this->canSplitShots = $this->canSplitShotsArray[$i];  // DK
		if (isset($this->autoFireOnlyArray[$i])) $this->autoFireOnly = $this->autoFireOnlyArray[$i];  // DK  
		if (isset($this->canTargetAlliesArray[$i])) $this->canTargetAllies = $this->canTargetAlliesArray[$i];  // DK                          
											    
    }//endof function changeFiringMode


	public function switchModeForIntercept()
	{
		return;
	}

	public function notActuallyHexTargeted($fireOrder)
	{
		return;
	}

    //Called from core firing routines to check if any armour should be bypassed by a sustained shot.
    public function getsustainedSystemsHit()
    {
        return null;    
    }     

    public function isLoSBlocked($shooterPos, $targetPos, $gamedata) {
        $blockedLosHex = $gamedata->getBlockedHexes();

        $noLoS = false;
        if (!empty($blockedLosHex)) {            
            $noLoS = Mathlib::isLoSBlocked($shooterPos, $targetPos, $blockedLosHex);
        }
        
        return $noLoS;
    }

} //end of class Weapon




class checkForSelfInterceptFire
{
    private static $fired = array();

    public static function setFired($id, $turn)
    {
        if ($turn != TacGamedata::$currentTurn) {
            $fired = array();
        }
        checkForSelfInterceptFire::$fired[] = $id;
    }

    public static function checkFired($id, $turn)
    {
        if ($turn != TacGamedata::$currentTurn) {
            $fired = array();
        }
        foreach (checkForSelfInterceptFire::$fired as $weapon) {
            if ($weapon == $id) {
                return true;
            }
        }
        return false;
    }
} //endof class checkForSelfInterceptFire


 