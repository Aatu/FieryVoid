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
    public $dp = 0; //damage penalty per dice
    public $range = 0;
	public $rangeArray = array();
    public $fireControl =  array(0, 0, 0); // fighters, <mediums, <capitals
	public $fireControlArray = array();


    public $loadingtime = 1;
	public $loadingtimeArray = array();
    public $turnsloaded;
	public $turnsloadedArray = array();

    public $overloadable = false;

    public $normalload = 0;
    public $alwaysoverloading = false;
    public $overloadturns = 0;
    public $overloadshots = 0;
    public $extraoverloadshots = 0;

    public $uninterceptable = false;
	public $uninterceptableArray = array();
    public $noInterceptDegradation = false; //if true, this weapon will be intercepted without degradation!
    public $intercept = 0;
    public $freeintercept = false;
    public $ballistic = false;
    public $hextarget = false;
    public $hidetarget = false;
    public $duoWeapon = false;
    public $dualWeapon = false;
    public $canChangeShots = false;


    public $shots = 1;
	public  $shotsArray = array();
    public $defaultShots = 1;
	public  $defaultShotsArray = array();

    public $rof = 2; //??? I do not see any use of this variable, besides one point in .js checking if it's 0...
	//public Array = array();

    public $grouping = 0;
	public $groupingArray = array();
    public $guns = 1;
	public $gunsArray = array();




    // Used to indicate a parent in case of dualWeapons
    public $parentId = -1;

    public $firingMode = 1;
    public $firingModes = array( 1 => "Standard"); //just a convenient name for firing mode
    public $damageType = ""; //actual mode of dealing damage (standard, flash, raking...) - overrides $this->data["Damage type"] if set!
	public $damageTypeArray = array();
    public $weaponClass = ""; //weapon class - overrides $this->data["Weapon type"] if set!
	public $weaponClassArray = array();

	//damage type-related variables
	    public $piercing = false; //this weapons deal Piercing damage - to be deleted once damageType takes over
	public $flashDamage = false; //this weapon deal Flash damage - to be deleted once damageType takes over...
	
	public $systemKiller = false;	//for custom weapons - increased chance to hit system and not Structure
	public $noOverkill = false; //this will let simplify entire Matter line enormously!
	
	
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
	    
	if($this->damageType != '') {$this->data["Damage type"] = $this->damageType;}else{$this->damageType= $this->data["Damage type"];}
	if($this->weaponClass != '') {$this->data["Weapon type"] = $this->weaponClass;}else{$this->weaponClass = $this->data["Weapon type"];}

	    //things that are calculated and can change with mode (and are displayed in GUI) - for all modes...
	    for($i = 1; $i <= $firingModes; $i++){
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

    public function getAvgDamage(){
        $min = $this->minDamage;
        $max = $this->maxDamage;
        $avg = round(($min+$max)/2);
        return $avg;
    }


    public function effectCriticals(){
        parent::effectCriticals();
        foreach ($this->criticals as $crit){
            if ($crit instanceof ReducedRange){

                if ($this->rangePenalty != 0){
                    if ($this->rangePenalty >= 1){
                        $this->rangePenalty += 1;
                    }else{
                        $this->rangePenalty = 1/(round(1/$this->rangePenalty)-1);
                    }

                }

                if ($this->range != 0){
                    $this->range = round($this->range *0.75);
                }

            }

            if ($crit instanceof ReducedDamage){
                $min = $this->minDamage * 0.25;
                $max = $this->maxDamage * 0.25;
                $avg = round(($min+$max)/2);
                $this->dp = $avg;
            }
        }


        $this->setMinDamage();
        $this->setMaxDamage();

    }

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
    }
	
	
	
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


        if ($this->piercing && $this->firingMode == 2 && $this->firingModes[1] == "Standard"){
            $mod -= 4;
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
        }

        if ($shooter instanceof OSAT && Movement::hasTurned($shooter, $gamedata->turn)){
            $mod -= 1;
        }

        $mod += $target->getHitChanceMod($shooter, $pos, $gamedata->turn);
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

        $notes = $rp["notes"] . ", DEW: $dew, BDEW: $bdew, SDEW: $sdew, Jammermod: $jammermod, OEW: $oew, SOEW: $soew, defence: $defence, intercept: $intercept, F/C: $firecontrol, mod: $mod, goal: $goal, chance: $change, jink: $jinkSelf $jinkTarget";
        $fireOrder->needed = $change;
        $fireOrder->notes = $notes;
        $fireOrder->updated = true;
    }

	
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
                        debug::log("DING. You cant intercept with a non-weapon....");
                        debug::log($interceptWeapon->displayName);
                        continue;
                    }
                    $i = $interceptWeapon->getInterceptRating(TacGamedata::$currentTurn) - $deg;

                    if ($i<0
                     || $interceptWeapon->destroyed
                     || $interceptWeapon->isOfflineOnTurn(TacGamedata::$currentTurn)){
                        $i = 0;
                     }

                    if ($shooter instanceof FighterFlight)
						$deg--;

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

	
	
    public function fire($gamedata, $fireOrder){
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
	
        //$this->firingMode = $fireOrder->firingMode;
	$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!

        $pos = $shooter->getCoPos();
        if ($this->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
	}

        $this->calculateHit($gamedata, $fireOrder);
        $intercept = $this->getIntercept($gamedata, $fireOrder);

        for ($i=0;$i<$fireOrder->shots;$i++){
            // Check if weapon is in distance range.
            if (!$this->isInDistanceRange($shooter, $target, $fireOrder))
            {
                // Target is not in distance range. Move to next shot.
                continue;
            }

            $needed = $fireOrder->needed - ($this->grouping*$i);
            $rolled = Dice::d(100);
            if ($rolled > $needed && $rolled <= $needed+($intercept*5)){
                //$fireOrder->pubnotes .= "Shot intercepted. ";
                $fireOrder->intercepted += 1;
            }

            $fireOrder->notes .= " FIRING SHOT ". ($i+1) .": rolled: $rolled, needed: $needed\n";
            if ($rolled <= $needed){
                $fireOrder->shotshit++;
                $this->beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);		
            }
        }

        $fireOrder->rolled = 1;//Marks that fire order has been handled
    }

	
    protected function beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata){
	//Debug::Log($this->firingMode);
        if ($this->piercing && $this->firingMode == 2 || $this->firingModes[1] == "Piercing"){
            $this->piercingDamage($target, $shooter, $fireOrder, $pos, $gamedata);
        }else{
            $damage = $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
            $this->damage($target, $shooter, $fireOrder, $pos, $gamedata, $damage);
        }
    }

	
    protected function piercingDamage($target, $shooter, $fireOrder, $pos, $gamedata)
    {

        if ($target->isDestroyed())
            return;

        $damage = $target->getPiercingDamagePerLoc(
                $this->getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder)
            );
        $locs = $target->getPiercingLocations($shooter, $pos, $gamedata->turn, $this);

        foreach ($locs as $loc){
            $system = $target->getHitSystem($shooter, $fireOrder, $this, $loc);

            if (!$system)
                continue;

            $this->doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $loc);
        }
    }

	
    protected function getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $location=null)  {
	    /*Location only relevant for Flash damage, which overkills to a new roll on hit table rather than to Structure*/
	    
	    
        $okSystem = null;

        if ($this->piercing && $this->firingMode == 2){
            return null;
        }

	if ($target instanceof FighterFlight){
            return null;
        }
	    
	if($this->noOverkill){  //weapon trait: no overkill
		return null;	
	}

        if ($this->flashDamage){// If overkill comes from flash damage, pick a new target in default way instead of overkill!
            $okSystem = $target->getHitSystem($shooter, $fireOrder, $this, $location); //for Flash it won't return destroyed system other than PRIMARY Structure
        }

        if ( $okSystem == null || $okSystem->isDestroyed()){
            $okSystem = $target->getStructureSystem($system->location);
        }

        if ($okSystem == null || $okSystem->isDestroyed())        {
            $okSystem = $target->getStructureSystem(0);
        }

        if ($okSystem == null || $okSystem->isDestroyed())        {
            return null;
        }

        return $okSystem;
    }

	

    public function damage($target, $shooter, $fireOrder, $pos, $gamedata, $damage){
	    /*$pos is never actually used, but _may_ be still useful for redefinitions...*/
        if($this->flashDamage){ //damage units other than base target
            $flashDamageAmount = $damage/4;
		$explosionPos = $target->getCoPos();
		

            $ships1 = $gamedata->getShipsInDistance($target->getCoPos());
            foreach($ships1 as $ship){
                if($ship === $target){
                    // make certain the target doesn't get the damage twice
                    continue;
                }
		    
		if ($ship->isDestroyed()) continue; //no point allocating

                if ($ship instanceof FighterFlight){
                    foreach ($ship->systems as $fighter){
                        if ($fighter == null || $fighter->isDestroyed()){
                            continue;
			}
                        $this->doDamage($ship, $shooter, $fighter, $flashDamageAmount, $fireOrder, $explosionPos, $gamedata);
                    }
                }else{
		    $tmpLocation = $ship->getHitSectionPos($explosionPos, $fireOrder->turn);
                    $system = $ship->getHitSystem($target, $fireOrder, $this, $tmpLocation);
                    if ($system == null ){
                        continue;
                    }

                    $this->doDamage($ship, $shooter, $system, $flashDamageAmount, $fireOrder, null, $gamedata, $tmpLocation);
                }
            }
        }

        if ($target->isDestroyed()) return;
	    
        if ($this->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $launchPos = mathlib::hexCoToPixel($movement->x, $movement->y);
		$tmpLocation = $target->getHitSectionPos($launchPos, $fireOrder->turn);
	}else{
 		$tmpLocation = $target->getHitSection($shooter, $fireOrder->turn);
	}
	$system = $target->getHitSystem($shooter, $fireOrder, $this, $tmpLocation);

        if ($system == null || $system->isDestroyed()) return; //there won't be destroyed system here other than PRIMARY Structure

        $this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, $tmpLocation);
    }

	
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

    protected function getSystemArmour($system, $gamedata, $fireOrder, $pos=null){

    	$shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);

	$armor = 0;
	if($pos!==null){ //attack comes from position not directly related to fire order
		$armor = $system->getArmourPos($gamedata, $pos);
	}
        elseif($this->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $posLaunch = mathlib::hexCoToPixel($movement->x, $movement->y);
	    $armor = $system->getArmourPos($gamedata, $posLaunch);
        }else{
            $armor = $system->getArmour($target, $shooter, $fireOrder->damageclass);
        }


        $mod = $system->hasCritical("ArmorReduced", $gamedata->turn-1);
        $armor -= $mod;
        if ($armor<0)
            $armor = 0;

        return $armor;
    }

    protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata){
        if ($this->rangeDamagePenalty > 0){
            $targetPos = $target->getCoPos();
            $dis = round(mathlib::getDistanceHex($pos, $targetPos));

            //print ("damage: $damage dis: $dis damagepen: " . $this->rangeDamagePenalty);
            $damage -= ($dis * $this->rangeDamagePenalty);
            //print ("damage: $damage \n\n");
            if ($damage < 0)
                return 0;
        }

        $damage -= $this->dp;
        if ($damage < 0)
            return 0;

        return $damage;
    }

    protected function getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder){
        $damage = $this->getDamage($fireOrder);
          //debug::log($damage);
        $damage = $this->getDamageMod($damage, $shooter, $target, $pos, $gamedata);
        $damage -= $target->getDamageMod($shooter, $pos, $gamedata->turn);

        return $damage;
    }

	
    protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata, $location = null){
	    /*$pos ONLY relevant for FIGHTER armor if damage source position is different than one from weapon itself*/
	    /*otherwise best leave null BUT fill $location!*/
    
	$damage = floor($damage);//make sure damage is a whole number, without fractions!
        $armour = $this->getSystemArmour($system, $gamedata, $fireOrder, $pos);
        $systemHealth = $system->getRemainingHealth();
        $modifiedDamage = $damage;
		
        $destroyed = false;
        if ($damage-$armour >= $systemHealth){ //target will be destroyed
            $destroyed = true;
            $modifiedDamage = $systemHealth + $armour;
        }

        $damageEntry = new DamageEntry(-1, $target->id, -1, $fireOrder->turn, $system->id, $modifiedDamage, $armour, 0, $fireOrder->id, $destroyed, "", $fireOrder->damageclass);
        $damageEntry->updated = true;
        $system->damage[] = $damageEntry;
        $this->onDamagedSystem($target, $system, $modifiedDamage, $armour, $gamedata, $fireOrder);
	
	$damage = $damage-$modifiedDamage;//reduce remaining damage by what was just dealt...
        if ($damage > 0){//overkilling!
             $overkillSystem = $this->getOverkillSystem($target, $shooter, $system, $fireOrder, $gamedata, $location);
             if ($overkillSystem != null)
                $this->doDamage($target, $shooter, $overkillSystem, $damage, $fireOrder, $pos, $gamedata, $location);
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
		if(isset($priorityArray[$i])) $this->priority = $priorityArray[$i];
		
		if(isset($animationArray[$i])) $this->animation = $animarionArray[$i];
		if(isset($animationImgArray[$i])) $this->animationImg = $animationImgArray[$i];
		if(isset($animationImgSpriteArray[$i])) $this->animationImgSprite = $animationImgSpriteArray[$i];
		if(isset($animationColorArray[$i])) $this->animationColor = $animationColorArray[$i];
		if(isset($animationColor2Array[$i])) $this->animationColor2 = $animationColor2Array[$i];
		if(isset($animationWidthArray[$i])) $this->animationWidth = $animationWidthArray[$i];
		if(isset($animationExplosionScaleArray[$i])) $this->animationExplosionScale = $animationExplosionScaleArray[$i];
		if(isset($animationExplosionTypeArray[$i])) $this->animationExplosionType = $animationExplosionTypeArray[$i];
		if(isset($animationExplosionScaleArray[$i])) $this->animationExplosionScale = $animationExplosionScaleArray[$i];
		if(isset($explosionColorArray[$i])) $this->explosionColor = $explosionColorArray[$i];
		if(isset($trailLengthArray[$i])) $this->trailLength = $trailLengthArray[$i];
		if(isset($trailColorArray[$i])) $this->trailColor = $trailColorArray[$i];
		if(isset($projectilespeedArray[$i])) $this->projectilespeed = $projectilespeedArray[$i];
		
		if(isset($rangePenaltyArray[$i])) $this->rangePenalty = $rangePenaltyArray[$i];
		if(isset($rangeDamagePenaltyArray[$i])) $this->rangeDamagePenalty = $rangeDamagePenaltyArray[$i];
		if(isset($rangeArray[$i])) $this->range = $rangeArray[$i];
		if(isset($fireControlArray[$i])) $this->fireControl = $fireControlArray[$i];
		if(isset($loadingtimeArray[$i])) $this->loadingtime = $loadingtimeArray[$i];
		if(isset($turnsloadedArray[$i])) $this->turnsloaded = $turnsloadedArray[$i];
		if(isset($uninterceptableArray[$i])) $this->uninterceptable = $uninterceptableArray[$i];
		if(isset($shotsArray[$i])) $this->shots = $shotsArray[$i];
		if(isset($defaultShotsArray[$i])) $this->defaultShots = $defaultShotsArray[$i];
		if(isset($groupingArray[$i])) $this->grouping = $groupingArray[$i];
		if(isset($gunsArray[$i])) $this->guns = $gunsArray[$i];
		
		if(isset($damageTypeArray[$i])) $this->damageType = $damageTypeArray[$i];
		if(isset($weaponClassArray[$i])) $this->weaponClass = $weaponClassArray[$i];
		if(isset($minDamageArray[$i])) $this->minDamage = $minDamageArray[$i];
		if(isset($maxDamageArray[$i])) $this->maxDamage = $maxDamageArray[$i];
		
	}
	
	
	
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
    }


 
