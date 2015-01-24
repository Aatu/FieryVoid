<?php


class Weapon extends ShipSystem{

    public $weapon = true;

    public $name = null;
    public $displayName ="";
    public $priority = 1;

    public $animation = "none";
    public $animationImg = null;
    public $animationImgSprite = 0;
    public $animationColor = null;
    public $animationColor2 = array(255, 255, 255);
    public $animationWidth = 3;
    public $animationExplosionScale = 0.25;
    public $animationExplosionType = "normal";
    public $duoWeapon = false;
    public $dualWeapon = false;
    public $explosionColor = array(250, 230, 80);
    public $trailLength = 40;
    public $trailColor = array(248, 216, 65);

    public $rangePenalty = 0;
    public $rangeDamagePenalty = 0;
    public $dp = 0; //damage penalty per dice
    public $range = 0;
    public $fireControl =  array(0, 0, 0); // fighters, <mediums, <capitals
    public $piercing = false;

    public $loadingtime = 1;
    public $turnsloaded;

    public $overloadable = false;

    public $normalload = 0;
    public $alwaysoverloading = false;
    public $overloadturns = 0;
    public $overloadshots = 0;
    public $extraoverloadshots = 0;

    public $uninterceptable = false;
    public $intercept = 0;
    public $freeintercept = false;

    public $ballistic = false;
    public $hextarget = false;
    public $hidetarget = false;
    public $targetImmobile = false;


    public $shots = 1;
    public $defaultShots = 1;
    public $canChangeShots = false;

    public $grouping = 0;
    public $guns = 1;
    public $projectilespeed = 17;

    public $rof = 2;

    // Used to indicate a parent in case of dualWeapons
    public $parentId = -1;

    public $firingMode = 1;
    public $firingModes = array( 1 => "Standard");

    public $flashDamage = false;
    public $damageType = "standard";
    public $minDamage, $maxDamage;

    public $exclusive = false;

    public $useOEW = true;
    public $calledShotMod = -8;

    public $possibleCriticals = array(14=>"ReducedRange", 19=>"ReducedDamage", 25=>array("ReducedRange","ReducedDamage"));

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output = 0){
        parent::__construct($armour, $maxhealth, $powerReq, $output );

        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;

        $this->setMinDamage();
        $this->setMaxDamage();

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

        if ($this instanceof DualWeapon && isset($this->turnsFired[$turn]))
            return true;

        foreach ($this->fireOrders as $fire){
            if ($fire->weaponid == $this->id && $fire->turn == $turn){
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

        if ($this->shots > 1){
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
            /* if overloading ja ampuu:

                    JOS ON EXTRASHOTTEJA laske extrashotteja. Jos extrashotit menee nollaan, pistä -1 (cooldown)
                    ja loading ja overloading 0
             *
             *      JOS EI OLE EXTRASHOTTEJA: laske overloading ja loading 0
           */

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

    public function calculateHit($gamedata, $fireOrder){
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        $pos = $shooter->getCoPos();
        $jammermod = 0;
        $jink = 0;
        $defence = 0;

        if ($this->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
            $defence = $target->getDefenceValuePos($pos);
        }else{
            $defence = $target->getDefenceValue($shooter);
        }

        $rp = $this->calculateRangePenalty($pos, $target);
        $rangePenalty = $rp["rp"];

        $dew = $target->getDEW($gamedata->turn);
        if ($shooter instanceof FighterFlight){
            $dew = 0;
        }

        $bdew = EW::getBlanketDEW($gamedata, $target);

        if ($target instanceof FighterFlight){
            if (!($shooter instanceof FighterFlight))
            {
                $jink = Movement::getJinking($target, $gamedata->turn);
            }
            elseif( mathlib::getDistance($shooter->getCoPos(),  $target->getCoPos()) > 0
                    ||  Movement::getJinking($shooter, $gamedata->turn) > 0){
                $jink = Movement::getJinking($target, $gamedata->turn);
            }
        }

        $mod = 0;

        $sdew = EW::getSupportedDEW($gamedata, $target);
        $soew = EW::getSupportedOEW($gamedata, $shooter, $target);
        $dist = EW::getDistruptionEW($gamedata, $shooter);

        $oew = 0;

        if ($this->useOEW)
        {
            $oew = $shooter->getOEW($target, $gamedata->turn);
            $oew -= $dist;

            if ($oew < 0){
                $oew = 0;
            }
        }


        if ($shooter instanceof FighterFlight){
            $oew = $shooter->offensivebonus;
            $mod -= Movement::getJinking($shooter, $gamedata->turn);

            if (Movement::getCombatPivots($shooter, $gamedata->turn)>0){
               $mod -= 1;
            }
        }

        if ($this->piercing && $this->firingMode == 2 && $this->firingModes[1] == "Standard"){
            $mod -= 4;
        }

        if (!($shooter instanceof FighterFlight))
        {
            if (Movement::isRolling($shooter, $gamedata->turn) && !$this->ballistic){
                debug::log("apllying malus for rolling");
                $mod -=3;
            }

            if (Movement::hasPivoted($shooter, $gamedata->turn) && !$this->ballistic){
                $mod -=3;
            }
        }
        if ($fireOrder->calledid != -1){
            $mod += $this->getCalledShotMod();
        }

        $mod += $target->getHitChanceMod($shooter, $pos, $gamedata->turn);
        $mod += $this->getWeaponHitChanceMod($gamedata->turn);

        $ammo = $this->getAmmo($fireOrder);
        if ($ammo !== null)
        {
            $mod += $ammo->getWeaponHitChanceMod($gamedata->turn);
        }

        if ($oew < 1)
        {
            $rangePenalty = $rangePenalty*2;
        }
        else
        {
            $jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter"=>$shooter, "target"=>$target));
            if ($jammerValue > 0 && $shooter->faction != $target->faction)
            {
                $jammermod = $rangePenalty*$jammerValue;
                if ($target instanceof FighterFlight){
                    if ( $dew > $jammermod){
                        $jammermod = 0;
                    }
                    else{
                        $dew = 0;
                    }
                }
            }
        }

        if (!($shooter instanceof FighterFlight)){
			$CnC = $shooter->getSystemByName("CnC");
			$mod -= ($CnC->hasCritical("PenaltyToHit", $gamedata->turn-1));
		}
        $firecontrol =  $this->fireControl[$target->getFireControlIndex()];

        $intercept = $this->getIntercept($gamedata, $fireOrder);

        // Fighters ignore all defensive EW, be it DEW, SDEW or BDEW
        if ($shooter instanceof FighterFlight && !$this->ballistic){
            $dew = 0;
            $bdew = 0;
            $sdew = 0;
        }

        $goal = ($defence - $dew - $bdew - $sdew - $jammermod - $rangePenalty - $intercept - $jink + $oew + $soew + $firecontrol + $mod);

        $change = round(($goal/20)*100);

        $notes = $rp["notes"] . ", DEW: $dew, BDEW: $bdew, SDEW: $sdew, Jammermod: $jammermod, OEW: $oew, SOEW: $soew, defence: $defence, intercept: $intercept, F/C: $firecontrol, mod: $mod, goal: $goal, chance: $change, jink: $jink";

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
                    if ($this->ballistic)
                        $deg = 0;

                    $interceptWeapon = $ship->getSystemById($fire->weaponid);
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
        $this->firingMode = $fireOrder->firingMode;

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
            $system = $target->getHitSystem($pos, $shooter, $fireOrder, $this, $loc);

            if (!$system)
                continue;


            $this->doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata);
        }




    }

    protected function getOverkillSystem($target, $shooter, $system, $pos, $fireOrder, $gamedata)
    {
        $okSystem = null;

        if ($this->piercing && $this->firingMode == 2)
        {
            return null;
        }

	if ($target instanceof FighterFlight)
        {
            return null;
        }

        if ($this->flashDamage)
        {
            // If overkill comes from flash damage, first go through all
            // other systems before overkilling into structure.
            $okSystem = $target->getHitSystem($pos, $shooter, $fireOrder, $this);
        }

        if ( $okSystem == null )
        {
            $okSystem = $target->getStructureSystem($system->location);
        }

        if ($okSystem == null || $okSystem->isDestroyed())
        {
            $okSystem = $target->getStructureSystem(0);
        }

        if ($okSystem == null || $okSystem->isDestroyed())
        {
            return null;
        }

        return $okSystem;
    }


    public function damage($target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null){

        if($this->flashDamage){
            $flashDamageAmount = $damage/4;

            $ships1 = $gamedata->getShipsInDistance($target->getCoPos());
            foreach($ships1 as $ship){
                if($ship === $target){
                    // make certain the target doesn't get the damage twice
                    continue;
                }

                if ($ship instanceof FighterFlight){

                    foreach ($ship->systems as $fighter){
                        if ($fighter == null || $fighter->isDestroyed()){
                            continue;
			}
                        $this->doDamage($ship, $shooter, $fighter, $flashDamageAmount, $fireOrder, $pos, $gamedata);
                    }
                }
                else{
                    $system = $ship->getHitSystem($target->getCoPos(), $target, $fireOrder, $this);

                    if ($system == null){
                        continue;
                    }

                    $this->doDamage($ship, $shooter, $system, $flashDamageAmount, $fireOrder, $pos, $gamedata);
                }
            }
        }

        if ($target->isDestroyed())
            return;

	$system = $target->getHitSystem($pos, $shooter, $fireOrder, $this, $location);

        if ($system == null)
            return;

        $this->doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata);
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

    protected function getSystemArmour($system, $gamedata, $fireOrder){

	$shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);

		$armor = 0;
        if ($this->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
            $armor = $system->getArmourPos($gamedata, $pos);
        }else{
            $armor = $system->getArmour($target, $shooter);
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
        $damage = $this->getDamageMod($damage, $shooter, $target, $pos, $gamedata);
        $damage -= $target->getDamageMod($shooter, $pos, $gamedata->turn);

        return $damage;
    }

    protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata){

        $armour = $this->getSystemArmour($system, $gamedata, $fireOrder );
        $systemHealth = $system->getRemainingHealth();
        $modifiedDamage = $damage;



        //print("damage: $damage armour: $armour\n");

        $destroyed = false;
        if ($damage-$armour >= $systemHealth){
            $destroyed = true;
            $modifiedDamage = $systemHealth + $armour;
            //print("destroying! rem: ".$system->getRemainingHealth()."\n");
        }


        $damageEntry = new DamageEntry(-1, $target->id, -1, $fireOrder->turn, $system->id, $modifiedDamage, $armour, 0, $fireOrder->id, $destroyed, "");
        $damageEntry->updated = true;
        $system->damage[] = $damageEntry;
        $this->onDamagedSystem($target, $system, $modifiedDamage, $armour, $gamedata, $fireOrder);
        //print("damage: $damage armour: $armour destroyed: $destroyed \n");
        if ($damage-$armour > $systemHealth){
            //print("overkilling!\n\n");
             $damage = $damage-$modifiedDamage;
             $overkillSystem = $this->getOverkillSystem($target, $shooter, $system, $pos, $fireOrder, $gamedata);
             if ($overkillSystem != null)
                $this->doDamage($target, $shooter, $overkillSystem, $damage, $fireOrder, $pos, $gamedata);
        }




    }

    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){
        return;
    }
}
