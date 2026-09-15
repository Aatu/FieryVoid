<?php
require_once("ShipClasses.php");

class FighterFlight extends BaseShip
{
    public $shipSizeClass = -1; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
    public $imagePath = "img/ships/null.png";
    public $iconPath, $shipClass;
    public $systems = array();
    public $agile = true;
    public $turncost;
    public $turndelay; 
    public $turndelaycost = 0;
    public $accelcost = 1;
    public $rollcost = 1;
    public $pivotcost = 1;
    public $currentturndelay = 0;
    public $iniative = "N/A";
    public $iniativebonus = 0;
    public $gravitic = false;
    public $phpclass;
    public $forwardDefense, $sideDefense;
    public $destroyed = false;
    public $pointCost = 0;
    public $faction = null;
    public $flight = true;
    public $hasNavigator = false;
    public $superheavy = false;
    public $flightSize = 1;
    public $maxFlightSize = 0; //maximum size of flight; for single fighter that should be 1 (set in SUperHeavyFighter class)
	//for regular flight of fighters that should be 0 (auto-choose, 12 for light fighters and 9 for others)
	//or set explicitly; guidelines:
	//light fighters: 12
	//medium/heavy: 9
	//particularly tough heavy (Pikitos, Tzymm): 6
	//superheavy (implemented as flight): 3
    protected $flightLeader = null;

    public $offensivebonus, $freethrust;
    public $jinkinglimit = 0;
	public $hangarRequired = 'fighters'; //if left 'fighters', will be classified based on Ini; fleet check only
	public $unitSize = 1; //most fighters are taken one per slot - but some are not
	//B5Wars example are some ultralight fighters, that can be carried two per hangar slot
	//and some superheavies that can use hangars (eg. Vorlon SHF) but take more slots
	//custom StarWars fighters are carried on squadron basis - allowing different squadron sizes for diffeerent craft
	
	public $customFtrName = ""; //to be filled if fighter has special hangar requirements - see Balvarix/Rutarian for usage
	public $dockRegeneration = 0; //full turns spent docked after which the flight FULLY regenerates (destroyed craft
		//regrown, all damage healed) - Kirishiac Warrior projectiles (5). 0 = no regeneration. Launching before the
		//dwell completes forfeits the regeneration entirely. See HangarOps::applyDockedRegeneration.
	public $deploysInHangar = false; //Some fighters like HK's MUST deploy in Hangars
    public $minesweeper = false;
	public $remoteControl = false; //true for remotely-controlled flights (Orieni Hunter-Killers); enables ELINT Jamming disruption.
		//Static blueprint property: travels to client via static-ship JSON, no stripForJson handling needed.


    public $canvasSize = 200;

    public $fireOrders = array();

    //following values from DB
    public $id, $userid, $name;
    protected $campaignX, $campaignY; //Not used as far as I can tell, just null entries in db.  
    public $rolled = false;
    public $rolling = false;
    public $team;

    protected $dropOutBonus = 0;
	protected $specialDropout = false; //Has special rules for dropout.    

    public $movement = array();

    function __construct($id, $userid, $name, $slot)
    {
        $this->id = (int)$id;
        $this->userid = (int)$userid;
        $this->name = $name;
        $this->slot = $slot;
		
		//set flight size limit if not explicitly set! - AT THIS POINT JINKING LIMIT IS 0!!!! so for now let's leave it for front end to determine after all...
		/*
		if($this->maxFlightSize < 1){
		    if ($this->jinkinglimit > 9) { //Medium and smaller
				$this->maxFlightSize = 12;
		    } else { //Heavy fighters
				$this->maxFlightSize = 9;			
			}
		}
		*/
    }


	/*calculates current combat value of the fighter flight, as a perentage of original value
	current algorithm: active fighters are worth their full value (except when damaged over 50%, then they get 3/4), inactive are worth nothing
	*/
	public function calculateCombatValue() {
		/* JUMP_POINTS_PLAN.md Stage 6 - a flight that LEFT through a jump point keeps the value it
		   had when it went, exactly as a hull does (BaseShip::calculateCombatValue has the same
		   branch; this class overrides the whole method, so it needs its own). Without it the
		   craft-by-craft count below reads every craft as destroyed - which is how a flight is
		   taken off the board - and the flight would be scored as a kill for the enemy. */
		if ($this->isDestroyed() && $this->hasJumpedToHyperspace()) return $this->getCVBeforeJump();

		$effectiveValue = 100;
		//combat value of flight: combat value of craft remaining in flight. Destroyed or dropped out craft have no value, still fighting craft have full value even if damaged
		$craftActive = 0;
		$craftTotal = 0;
		foreach($this->systems as $craft){
			$craftTotal++;
			if (!$craft->isDestroyed()) {
				//if damage is more than 50%: call it 75% combat value!
				if ( ($craft->getRemainingHealth()*2) < $craft->maxhealth ) { //>50% damage - 3/4 value
					$craftActive += 0.75;
				}else{ //damage up to 50% - full value
					$craftActive += 1;
				}
			}
		}
		if($craftTotal>0){
			$effectiveValue = round(100*($craftActive/$craftTotal));
		}
		return $effectiveValue;
	} //endOf function calculateCombatValue
	

    //Generic "this flight was changed mid-game, force it to serialize" flag. Set true by
    //Gravitic Augmenter Mode 2 (Warrior Enhancement) so the buffed offensivebonus / freethrust
    /// dropOutBonus reach the client (they're normally blueprint-only values from staticShips).
    public $isModified = false;

    /* WALKERS OF SIGMA-957 - Energy Draining Field thrust drain on a FLIGHT
       (WALKERS_OF_SIGMA_PLAN.md 2.2 + decision D1). A flight has no Engine, so the drain crit
       rides its sample fighter and comes off freethrust instead - which is the number both the
       client's movement budget (movement.js: `rem = ship.freethrust`) and AutomatedMovement
       spend from.
       Summed, not counted: one EdfThrustDrain crit is a whole roll. Never negative.
       ⚠️ Gated on the sample fighter having criticals at all, so an ordinary flight pays one
       property read - NOT on TacGamedata::$edfPresent, because the field that caused the drain
       may already be destroyed while its last drain is still in effect. */
    public function getEffectiveFreeThrust($turn = null){
        /* ⚠️ sumCriticalParam() defaults to the current turn on === false, NOT on null, so a null
           passed straight through makes every turn comparison fail and the drain silently reads 0.
           Resolve it here. (The test that caught this asserted the PUBLISHED number, not the
           getter with an explicit turn - which is the only way the two could disagree.) */
        if ($turn === null) $turn = TacGamedata::$currentTurn;
        $thrust = (int)$this->freethrust;
        $sample = $this->getSampleFighter();
        if (!$sample || empty($sample->criticals)) return $thrust;
        return max(0, $thrust - (int)$sample->sumCriticalParam("EdfThrustDrain", $turn));
    }

    public function stripForJson() {
        $strippedShip = parent::stripForJson();

        $strippedShip->flightSize = $this->flightSize;

        //Only emit the buffed combat stats when a system has modified them, so the
        //client overrides its static blueprint values (Ship ctor copies JSON over static).
        /* An EDF drain also has to reach the client, and the only channel is this block - the
           client spends ship.freethrust directly. Publishing a drained value means setting
           isModified, which then also publishes offensivebonus and dropOutBonus at their real
           (unchanged) values, which is correct by definition. */
        $effectiveThrust = $this->getEffectiveFreeThrust();
        if ($effectiveThrust !== (int)$this->freethrust) $this->isModified = true;

        if ($this->isModified) {
            $strippedShip->offensivebonus = $this->offensivebonus;
            $strippedShip->freethrust = $effectiveThrust;
            $strippedShip->dropOutBonus = $this->dropOutBonus;
            $strippedShip->isModified = $this->isModified;
        }

        return $strippedShip;
    }

    private $autoid = 1;


    public function getInitiativebonus($gamedata)
    {
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);

        if ($this->hasNavigator) {
            $initiativeBonusRet += 5;
        }

        return $initiativeBonusRet;
    }

    public function getDropOutBonus()
    {
        return $this->dropOutBonus;
    }

    /* Adjusts the protected dropout bonus from outside the class (e.g. Gravitic Augmenter's
     * Warrior Enhancement applies -4). Negative values lower the dropout threshold. */
    public function addDropOutBonus($amount)
    {
        $this->dropOutBonus += $amount;
    }

    public function getSpecialDropout()
    {
        return $this->specialDropout;
    }    

    public function getDropOutBonusSpecial($gamedata)
    {
        $dropOutBonus = 0;

        if ($this->faction === "Torvalus Speculators") {
            $dropOutBonus = -100; // Artificially high as Stilettos don't drop out if still controlled.
            $fighters = 0;
            $controlCapacity = 0;

            foreach ($gamedata->ships as $ship) {
                if ($ship->userid !== $this->userid) continue; // Only friendly
                if ($ship->faction !== "Torvalus Speculators") continue; // Only Torvalus
                if ($ship->isDestroyed()) continue; // Skip destroyed

                if ($ship instanceof FighterFlight) {
                    foreach ($ship->systems as $ftr) {
                        if ($ftr->isDestroyed()) continue;
                        $fighters += 1;
                    }
                } else {
                    $CnC = $ship->getSystemByName("CnC");
                    if ($CnC && !$CnC->isDestroyed()) {
                        if (!empty($ship->fighters["normal"])) {
                            $controlCapacity += $ship->fighters["normal"];
                        }
                    }
                }
            }

            if ($controlCapacity < $fighters) {
                $dropOutBonus = 0; // No bonus if not enough control available
            }
        }

        return $dropOutBonus;
    }

    public function getSystemById($id)
    {
        foreach ($this->systems as $system) {
            if ($system->id == $id) {
                return $system;
            }
            foreach ($system->systems as $fs) {
                if ($fs->id == $id) {
                    return $fs;
                }
            }
        }

        return null;
    }


    /*returns a sample fighter, if one needs to review example of what's in flight*/
    public function getSampleFighter()
    {
        return $this->systems[1];
    }

    /* ================ JUMP_POINTS_PLAN.md Stage 6 - A FLIGHT CAN LEAVE THROUGH A JUMP POINT =====
     *
     * BaseShip answers both of these off the ship's PRIMARY STRUCTURE, and a flight has none
     * (getStructureSystem returns null). Movement::applyJumpOut therefore takes a flight out by
     * destroying every craft in it with a HyperspaceJump damage entry - which is what "the flight
     * is gone" means for a flight - and hangs the CV note on the sample fighter. These two read
     * that back.
     *
     * hasJumpedToHyperspace is the same shape as BaseShip::hasHyperspaceJumpDamage, asked of the
     * craft instead of the hull: SOME craft carries a jump entry, and the damage that is NOT jump
     * damage left at least one craft alive - i.e. the flight was still flying when it jumped. A
     * flight shot to pieces and then handed a jump entry (which cannot happen, but the test should
     * not depend on that) reads as destroyed, exactly as a hull would. */
    public function hasJumpedToHyperspace(){
        $jumped   = false;
        $survivor = false;

        foreach ($this->systems as $craft){
            if (!is_array($craft->damage)) continue;

            $craftJumped = false;
            $nonJump     = 0;
            foreach ($craft->damage as $entry){
                if ($entry->damageclass === 'HyperspaceJump'){
                    $craftJumped = true;
                    continue;
                }
                $nonJump += max(0, $entry->damage - $entry->armour);
            }

            if ($craftJumped){
                $jumped = true;
                if ($nonJump < $craft->maxhealth) $survivor = true;
            }
        }

        return $jumped && $survivor;
    }

    public function getCVBeforeJump(){
        $sample = $this->getSampleFighter();
        return $sample ? $sample->getCVBeforeJump() : 0;
    }


    /*redefinition - as defensive systems will be on actual fighters*/
    /*assuming all fighters are equal, it's enough to get system from first fighter, whether it's alive or not!*/
    public function getDamageMod($shooter, $pos, $turn, $weapon)
    {
        if ($pos !== null) {
            $pos = Mathlib::hexCoToPixel($pos);
        }
        $affectingSystems = array();
        $fighter = $this->systems[1];
        foreach ($fighter->systems as $system) {
            if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)) continue;
            $mod = $system->getDefensiveDamageMod($this, $shooter, $pos, $turn, $weapon);
			//weapon might have something to say about that as well...
			$mod = $weapon->shieldInteractionDamage($this, $shooter, $pos, $turn, $system, $mod);
            if (!isset($affectingSystems[$system->getDefensiveType()])
                || $affectingSystems[$system->getDefensiveType()] < $mod) {
                $affectingSystems[$system->getDefensiveType()] = $mod;
            }
        }
        //Chromatic Pulse Driver adaptation (WALKERS_OF_SIGMA_PLAN.md 3.4) - the flight mirror of
        //BaseShip::getDamageMod. A flight carries its shields on the individual fighters, but the
        //bucket it aggregates them into is the same shape. Gated on the one static boolean, for the
        //reason spelled out at BaseShip::getHitChanceMod.
        if (TacGamedata::$cpdAdaptationPresent) {
            $affectingSystems = CpdScanRegistry::applyToShieldBucket($affectingSystems, $this, $shooter);
        }
        return array_sum($affectingSystems);
    }

    /*redefinition - as defensive systems will be on actual fighters*/
    /*assuming all fighters are equal, it's enough to get system from first fighter, whether it's alive or not!*/
    public function getHitChanceMod($shooter, $pos, $turn, $weapon)
    {
        if ($pos !== null) {
            $pos = Mathlib::hexCoToPixel($pos);
        }
        $affectingSystems = array();
        $fighter = $this->systems[1];
        foreach ($fighter->systems as $system) {
            if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)) continue;
            $mod = $system->getDefensiveHitChangeMod($this, $shooter, $pos, $turn, $weapon);
			//weapon might have something to say about that as well...
			$mod = $weapon->shieldInteractionDefense($this, $shooter, $pos, $turn, $system, $mod);
            if (!isset($affectingSystems[$system->getDefensiveType()]) //no system of this kind is taken into account yet, or it is but it's weaker
                || $affectingSystems[$system->getDefensiveType()] < $mod) {
                $affectingSystems[$system->getDefensiveType()] = $mod;
            }
        }
        //Chromatic Pulse Driver adaptation - the flight mirror of BaseShip::getHitChanceMod, gated
        //the same way.
        if (TacGamedata::$cpdAdaptationPresent) {
            $affectingSystems = CpdScanRegistry::applyToShieldBucket($affectingSystems, $this, $shooter);
        }
        return (-array_sum($affectingSystems));
    }


    /*redefinition; for fighter, don't check whether system is destroyed - it doesn't matter as long as entire flight isn't!*/
    /*also, fighter systems don't get disabled :)*/
    private function checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)
    {
        if (!($system instanceof DefensiveSystem)) return false; //this isn't a defensive system at all

        //if the system has arcs, check that the position is on arc
        if (is_int($system->startArc) && is_int($system->endArc)) {
            //get bearing on incoming fire...
            //if ($weapon->ballistic) {
			if($pos!==null){ //firing position is explicitly declared
                $relativeBearing = $this->getBearingOnPos($pos);
            } else { //direct fire weapon - check from shooter...
                $relativeBearing = $this->getBearingOnUnit($shooter);
            }
            //if not on arc, continue!
            if (!mathlib::isInArc($relativeBearing, $system->startArc, $system->endArc)) {
                return false;
            }
        }

        return true;
    }//endof function checkIsValidAffectingSystem


    public function getSystemByName($name)
    {          
        foreach ($this->systems as $fighter) {
            foreach ($fighter->systems as $fs) {
                if ($fs->name == $name) return $fs;
            }
        }
    }

    public function getFighterBySystem($id)
    {
        foreach ($this->systems as $fighter) {
            foreach ($fighter->systems as $fs) {
                if ($fs->id == $id) return $fighter;
            }
        }
    }

    protected function addSystem($fighter, $loc = null)
    {
        $fighter->setUnit($this);
        $fighter->id = $this->autoid;
        $fighter->location = sizeof($this->systems);		

        $this->autoid++;
        $fighterSys = array();
        foreach ($fighter->systems as $system) {
            $system->setUnit($this);
            $system->id = $this->autoid;
            $this->autoid++;
            $fighterSys[$system->id] = $system;
        }
        $fighter->systems = $fighterSys;
        $this->systems[$fighter->id] = $fighter;
		
		
		//add to Notes information about miscellanous attributes - with first fighter being added
		//if($fighter->id == 1){
		//	$this->notesFill($fighter);
		//}
		//add ramming attack if not equipped already$rammingExists = false;
		$rammingExists = false;
		foreach($fighter->systems as $sys)  if ($sys instanceof RammingAttack){
			$rammingExists = true;
		}
		if(!$rammingExists){
			/* breaks games :(
			//add ramming attack
			//check whether game id is safe (can be safely be deleted in March 2020 or so)
			if ((TacGamedata::$currentGameID >= TacGamedata::$safeGameID) || (TacGamedata::$currentGameID<1)){
				if((($this instanceof FighterFlight)) && (!$this.osat)  ){
					$fighter->addAftSystem(new RammingAttack(0, 0, 360, $this->getRammingFactor(), 0));
				}
			}
			*/
		}
    } //endof function addSystem


	/*for fighter flights - notes must be saved for fighters themselves as well as their subsystems!*/
	/*saves individual notes systems might have generated*/
	public function saveIndividualNotes(DBManager $dbManager) {
		foreach ($this->systems as $fighter) if ($fighter->fighter){ //only for actual fighters - and then handle the rest as subsystems!
            $fighter->saveIndividualNotes($dbManager);
				foreach ($fighter->systems as $subsystem){
					$subsystem->saveIndividualNotes($dbManager);
				}
        }
	}
	
	/*calls systems to generate notes if necessary*/
	public function generateIndividualNotes($gamedata, $dbManager) {
		foreach ($this->systems as $fighter) if ($fighter->fighter){ //only for actual fighters - and then handle the rest as subsystems!
            $fighter->generateIndividualNotes($gamedata, $dbManager);
				foreach ($fighter->systems as $subsystem){
					$subsystem->generateIndividualNotes($gamedata, $dbManager);
				}
        }		
	}
	
	
	/*calls systems to act on notes just loaded if necessary*/
	public function onIndividualNotesLoaded($gamedata) {
		foreach ($this->systems as $fighter) if ($fighter->fighter){ //only for actual fighters - and then handle the rest as subsystems!
            $fighter->onIndividualNotesLoaded($gamedata);
			foreach ($fighter->systems as $subsystem){
				$subsystem->onIndividualNotesLoaded($gamedata);
			}
        }	
	}

    /* getPreviousCoPos deliberately not overridden - this was a byte-for-byte copy of BaseShip's,
    and getCoPos is not overridden either, so inheriting keeps one implementation. It has to
    compensate for forced Pre-Firing movement and a second copy would only drift out of step. 
    public function getPreviousCoPos()
    {
        $pos = $this->getCoPos();

        for ($i = sizeof($this->movement) - 1; $i >= 0; $i--) {
            $move = $this->movement[$i];
            $pPos = $move->getCoPos();

            if ($pPos["x"] != $pos["x"] || $pPos["y"] != $pos["y"])
                return $pPos;
        }

        return $pos;
    }
    */

    public function getDEW($turn)
    {

        foreach ($this->EW as $EW) {
            if ($EW->type == "DEW" && $EW->turn == $turn)
                return $EW->amount;
        }

        return 0;

    }

    public function getOEW($target, $turn)
    {

        foreach ($this->EW as $EW) {
            if ($EW->type == "OEW" && $EW->targetid == $target->id && $EW->turn == $turn)
                return $EW->amount;
        }

        return 0;
    }

    public function getFacingAngle()
    {
        $movement = null;

        foreach ($this->movement as $move) {
            $movement = $move;
        }

        return $movement->getFacingAngle();
    }

    /*returns number of still active craft in flight*/
    public function countActiveCraft($turn)
    {
        $countActive = 0;
        foreach ($this->systems as $ftr) {
            if (!$ftr->isDestroyed($turn)) $countActive++;
        }
        return $countActive;
    }//endof function countActiveCraft

    public function getLocations()
    {
        $locs = array();
        foreach ($this->systems as $fighter) {
            $exampleFtr = $fighter; //whether still alive or not; any fighter in flight will do, as they're all the same!
        }
        $health = $exampleFtr->maxhealth;

        $locs[] = array("loc" => 0, "min" => 330, "max" => 30, "profile" => $this->forwardDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[0]);
        $locs[] = array("loc" => 0, "min" => 30, "max" => 150, "profile" => $this->sideDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[3]);
        $locs[] = array("loc" => 0, "min" => 150, "max" => 210, "profile" => $this->forwardDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[1]);
        $locs[] = array("loc" => 0, "min" => 210, "max" => 330, "profile" => $this->sideDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[2]);

        return $locs;
    }

    public function fillLocations($locs)
    { //for fighters, armour and health are already defined by getLocations
        return $locs;
    }


    public function getStructureSystem($location)
    {
        return null;
    }

    public function getFireControlIndex()
    {
		
        return 0;

    }

    public function isDestroyed($turn = false)
    {
        //Hangar Ops Stage 7: a docked flight is "removed" — treat it as
        //destroyed for filtering purposes (target lists, fleet iteration,
        //weapon scans like PulsarMine, etc.) so the broad isDestroyed
        //call surface transparently skips docked flights. See
        //BaseShip::isDestroyed for the full rationale.
        if ($this->removed && ($turn === false || $turn >= $this->removedTurn)) return true;

        foreach ($this->systems as $system) {
            if (!$system->isDestroyed($turn) && !$system->isDisengaged($turn)) {
                return false;
            }
        }
        return true;
    }

    public function isPowerless()
    {
        return false;
    }


    public function getHitSystem($shooter, $fire, $weapon, $gamedata, $location = null, $sourceOverride = null)
    {
        //$sourceOverride is unused for fighter flights (no directional armor by section), accepted for parent signature parity
        $skipStandard = false;
        $systems = array();
        if ($fire->calledid != -1) {
            $system = $this->getSystemById($fire->calledid);
			//if system is not actual fighter - redirect to fighter it's mounted on!
			if(!$system instanceof Fighter){
				$system = $this->getFighterBySystem($system->id);
			}
			
            if (!$system->isDestroyed()) { //called shot at particular fighter, which is still living
                $systems[] = $system;
                $skipStandard = true;
            }
        }

        if (!$skipStandard) {
            foreach ($this->systems as $system) {
                if (!$system->isDestroyed()) {
                    $systems[] = $system;
                }
            }
        }

        if (sizeof($systems) == 0) return null;
	    
	/* AF fire is normally allocated by player, and it's very important for fighter toughness
	in FV there is no information about actual amount of incoming damage
	but let's try to make an algorithm based on damage _potential_ of incoming shot - won't be as good, but far better than random allocation
	priority of allocation:
	 1. no chance of forcing dropout (prefer destroyed fighter to two fighters dropping out)
	 2. no chance of being destroyed
	 3. being more damaged already
	 4. having higher ID (last craft in flight first - if any craft is special, it'll be the first one)
	*/
	//fill data about eligible craft...
	$craftWithData = array();
	foreach ($systems as $craft){
		$dmgPotential = 0;
		//actually vs fighters Raking degenerates into Standard, rake size is irrelevant!
			$dmgPotential = $weapon->maxDamage; //potential = maximum damage weapon can do	
		//modify by armor properties
		$armor = $weapon->getSystemArmourComplete($this, $craft, $gamedata, $fire);		
		//modify by defensive system (like Diffuser)! 
		$protection=0;
		
		$protectingSystem = $this->getSystemProtectingFromDamage($shooter, null, $gamedata->turn, $weapon, $craft,$dmgPotential);//let's find biggest one!
		if($protectingSystem){ //may be unavailable, eg. already filled
			$shots = ($weapon && $weapon->isLinked) ? $weapon->shots : 1;
			$protection = $protectingSystem->doesProtectFromDamage($dmgPotential, $craft, false, $shots, false, $shooter);
		}
		$armor += $protection;		
		
		$dmgPotential = max(0, $dmgPotential-$armor);//never negative damage ;)
		/*for linked weapons - multiply by number of shots!*/
		if ($weapon->isLinked){
			$dmgPotential = $dmgPotential*$weapon->shots;
		}
		$remainingHP = $craft->getRemainingHealth();
		$damagedThisTurn = false;
		foreach($craft->damage as $dmgEntry){
			if($dmgEntry->turn == $gamedata->turn){
				$damagedThisTurn = true;	
			}
		}
		$alreadyDropoutSubject = false;
		//dropout threshold for this particular fighter - include dropout bonuses/penalties!
		$dropoutThreshold = 10;
		$dropoutThreshold += $this->dropOutBonus + $this->critRollMod + $craft->critRollMod;//negative values will lower the threshold, positive will increase it		
		$dropoutThreshold = min($dropoutThreshold,10); //never higher than 10!
		//subject to dropout if damaged this turn and brought below 10 points
		if (($damagedThisTurn==true) && ($remainingHP<$dropoutThreshold)) $alreadyDropoutSubject = true;
		//can be dropped out if can be brought below 10 hp by this shot and NOT subject to dropout already
		$minRemainingHP = $remainingHP-$dmgPotential;
		$canBeDroppedOut = false;
		if (($minRemainingHP<$dropoutThreshold) && ($alreadyDropoutSubject==false)) $canBeDroppedOut = true;
		$canBeKilled = false;
		if ($minRemainingHP<1) $canBeKilled = true;
		//$singleCraft = array("id"=>$craft->id, "hp"=>$remainingHP, "canDrop"=>$canBeDroppedOut, "canDie"=>$canBeKilled, "fighter"=>$craft);
		//let's add non-armor resistance to list of priorities - these are usually depletable but do regenerate...
		$singleCraft = array("id"=>$craft->id, "hp"=>$remainingHP, "canDrop"=>$canBeDroppedOut, "canDie"=>$canBeKilled, "fighter"=>$craft, "protection"=>$protection);
		$craftWithData[] = $singleCraft;
	}
	    
	//sort by priorities, return first one on list!
	usort($craftWithData, function($a, $b){
		if (($a["canDrop"] == true) && ($b["canDrop"] == false)){ //prefer craft with no dropout chance
		    return 1;
		}else if (($b["canDrop"] == true) && ($a["canDrop"] == false)){
		    return -1;	
		} else if (($a["canDie"] == true) && ($b["canDie"] == false)){ //prefer craft with no death chance
		    return 1;	
		} else if (($b["canDie"] == true) && ($a["canDie"] == false)){ 
		    return -1;
			
		//prefer better protected craft - even before more durable one (as long as there is no threat of dropout...)
		} else if ($a["protection"] < $b["protection"]){ //prefer best protected craft!
		    return 1;
		} else if ($b["protection"] < $a["protection"]){ 
		    return -1;	
			
		} else if ($a["hp"] < $b["hp"]){ //prefer most durable craft!
		    return 1;
		} else if ($b["hp"] < $a["hp"]){ 
		    return -1;	
		} else if ($a["id"] < $b["id"]){ //lastly - prefer one that's further in order of IDs
		    return 1;
		} else if ($b["id"] < $a["id"]){ 
		    return -1;	
		}	
		else return 0; //should never happen, IDs are different!
	});
	    
	return $craftWithData[0]["fighter"];

        //return $systems[(Dice::d(sizeof($systems)) - 1)];
    }//endof function 


    public function getAllFireOrders($turn = -1)
    {
        $orders = array();

        foreach ($this->systems as $fighter) {
            foreach ($fighter->systems as $system) {
                $orders = array_merge($orders, $system->getFireOrders($turn));
                //$orders = array_merge($orders, $system->fireOrders); //old version
            }
        }

        return $orders;
    }
	
	public function getFighterFireOrders($craft, $turn = -1)
    {
        $orders = array();

        foreach ($this->systems as $fighter) if ($fighter->id == $craft->id){//only for indicated craft
            foreach ($fighter->systems as $system) {
                $orders = array_merge($orders, $system->getFireOrders($turn));
            }
        }

        return $orders;
    }


    /*always nothing to do for fighters*/
    public function setExpectedDamage($hitLoc, $hitChance, $weapon, $shooter)
    {
        return;
    }


    /*returns calculated ramming factor for fighter (so will never use explosive charge if, say, Delegor or HK is rammed instead of ramming itself!*/
    /*approximate raming factor as Structure + all Armors of example fighter (so always full ramming factor is used, not reduced by damage received) */
    public function getRammingFactor()
    {
        $dmg = 0;
        $ftr = $this->getSampleFighter();
        $dmg += $ftr->maxhealth;
        foreach ($ftr->armour as $armorvalue) {
            $dmg += $armorvalue;
        }
        return $dmg;
    } //endof function getRammingFactor

}//endof class FighterFlight

class SuperHeavyFighter extends FighterFlight
{
    public $superheavy = true;
	public $maxFlightSize = 1;
    public $jinkinglimit = 4; //SHF standard

    function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }
}

class MicroSAT extends SuperHeavyFighter{
	public $osat = true;
	public $accelcost = 100; //not supposed to actually move anywhere, may move/pivot normally
    public $jinkinglimit = 4; //they are actually allowed to jink!!! Can't imagine how (without being able to seriously accelerate away), but sure useful in game
	
	function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }
}


class MineClass extends SuperHeavyFighter{
//    public $shipSizeClass = 1; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
	public $mine = true;
	public $accelcost = 100; //not supposed to actually move anywhere, may move/pivot normally
    public $jinkinglimit = 0; //they cannot jink

    public function getFireControlIndex()
    {
        return 1;
    }
	
	function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }
}


?>
