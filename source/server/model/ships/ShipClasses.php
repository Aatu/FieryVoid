<?php
class BaseShip {

    public $shipSizeClass = 3; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
    public $Enormous = false; //size class 4 is NOT implemented!!! for semi-Enormous unit, set this variable to True
    public $Huge = 0; //For massive terrain units, denotes how many hexes radius they have from centre hex.
    public $imagePath, $shipClass;
    public $systems = array();
    public $EW = array();
    public array $structures = [];
    public array $locations = [];
    public $fighters = array();
	public $customFighter = array(); //array for fighters with special hangar requirements - see Balvarix/Rutarian for usage
    public $hitChart = array();
    public $notes = '';//notes to be displayed on fleet selection screen
	public $unofficial = false; //false - official AoG design; true - custom design; 'S' - semi-custom - design that isn't quite official (and so is appropriately marked), but enjoys similar status in Fiery Void

    public $occurence = "common";
    public $variantOf = ''; //variant of what? - MUST be the same as $shipClass of base unit, or this unit will not be displayed on fleet selection screen!
    public $limited = 0;
    public $agile = false;
    public $turncost, $turndelaycost, $accelcost, $rollcost, $pivotcost;
    public $currentturndelay = 0;
    public $iniative = "N/A";
    public $unmodifiedIniative = null;
    public $iniativebonus = 0;
    public $iniativeadded = 0; //Initiative bonus difference - compared to base bonus! Just for display to player.
    public $gravitic = false;
    public $phpclass;
    public $forwardDefense, $sideDefense;
    public $destroyed = false;
    //public $deploysOnTurn = 1; //Default turn to deploy.
    public $pointCost = 0;
    public $pointCostEnh = 0; //points spent on enhanements (in addition to crafts' own price), DOES NOT include cost of items being only technically enhancements (special missiles, Navigators...)
	public $pointCostEnh2 = 0; //points spent on non-enhancements - separation actuallly exists only at fleet selection, afterwards it will be always 0 with points added to $pointCostEnh
	/* THIRD cost bucket - points spent on PER-SYSTEM enhancements (WEAPON_ENHANCEMENTS_PLAN.md D5).
	   It cannot share either bucket above: readBulkPurchase and doEditShip REWRITE pointCostEnh and
	   pointCostEnh2 from zero off the buy dialog's spinners every time they run, and system
	   enhancements are not in that dialog - so folding them in means an Edit silently REFUNDS every
	   refit while leaving it applied. A separate field is the only version that survives an edit.
	   ⚠️ getPristinePointCost must peel all three. */
	public $pointCostSysEnh = 0;
	public $combatValue = 100; //current combat value, as percentage of original
    public $spawned = -1; //To denote if a unit was spawned by DURING the game, e.g. doesn't count for CPV etc, show in Replay prior to it spawning
    public $removed = false; //Hangar Ops (B5W §10.1): set when a flight has docked. Hides from board/target lists without triggering destruction; record stays in DB for replay history.
    public $removedTurn = null; //Turn the ship docked into a hangar. Lets replay show the flight up to and including this turn.
    /* JUMP_POINTS_PLAN.md Stage 4 x Hangar Ops: this unit was sitting in a carrier's hangar when
       that carrier left through a jump vortex, so it is in hyperspace too. Set per LOAD by
       TacGamedata::markJumpedDockedFlights (never persisted - it is derived from the carrier), and
       only for the viewers allowed to know. Exists because the carrier->flight link lives in the
       hangar's own-team-only $hangarUsage, which an opponent's client never receives. */
    public $jumpedWithCarrier = false;
    public $dockCoalesceDone = false; //Hangar Ops Stage 21: transient once-per-carrier guard for the whole-flight dock coalescer (no-split docking). Not persisted/serialized — fresh false each load; first non-catapult hangar's criticalPhaseEffects runs the coalescer, the rest skip it.
    public $launchCoalesceDone = false; //Hangar Ops Stage 21: transient once-per-carrier guard for the whole-flight launch coalescer. Same lifetime as dockCoalesceDone.
    public $dockRegenSweepDone = false; //Kirishiac Warrior regeneration: transient once-per-carrier guard for HangarOps::applyDockedRegeneration. Same lifetime as dockCoalesceDone.
    public $faction = null;
	public $factionAge = 1; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
    public $isd = 0; 
    public $slot;
    /* REINFORCEMENTS_PLAN.md §3.1 — the three tac_ship columns that say where a unit is in the
       hyperspace-to-board journey. PUBLIC on purpose: the client needs all three, and they are
       PER-INSTANCE rather than blueprint, so the static ship bundle and ShipCompactor never see
       them.
         $reinforcement  bought as a reinforcement. Fixed at purchase, never changes.
         $arrivalTurn    null = still in hyperspace; N = arrives and places on turn N. Written
                         ONLY by the server's end-of-formation-turn deviation sweep, never from
                         a POST.
         $arrivalVia     the OPENER unit's id (a reinforcement ship, or a gate) whose exit
                         this unit is riding through. null = unassigned. */
    public $reinforcement = false;
    public $arrivalTurn   = null;
    public $arrivalVia    = null;
    /* REINFORCEMENTS_PLAN.md §4 Stage 1 — THE LOBBY'S CLAIM, AND DELIBERATELY NOT $reinforcement.
       Carried RAW by Manager::getShipsFromJSON and consumed at exactly one place,
       BuyingGamePhase::process, which checks the game rule and promotes it. Same idiom as
       $preBattleDamage and $systemEnhancements below, and for a stronger reason than either:
       $reinforcement is NOT inert. getTurnDeployed/getTurnPlaced read it, a POST-side ship never
       carries $arrivalTurn, and so a POSTed ship with $reinforcement = true would answer 999 to
       both accessors in every phase - which Hangar::generateIndividualNotes and
       HangarOps::validateDeployBayOrders both ask of POST-side ships (plan trap 3). Keeping the
       client's claim in its own field means $reinforcement always means "the DB said so", which is
       what all ~80 getTurnDeployed call sites assume. NOT a tac_ship column. */
    public $reinforcementClaim = false;
    public $unavailable = false;
    public $minesweeperbonus = 0;
    public $base = false;
    public $smallBase = false;
    //LCV Rails: set true in addSystem() when a DockingCollar (LCV rail) is mounted.
    //Very few ships carry LCV rails, so this flag lets the per-ship/per-turn LCV
    //checks (getInitiativebonus penalty, stripForJson docked count, the carrier-
    //destruction sweep) short-circuit on the overwhelming majority of ships
    //without iterating their systems via HangarOps::shipHasLCVRail.
    public $LCVCarrier = false;
    //LCV Rails: per-request memo for the 2d10 rail-fragment roll a forced-launched
    //LCV takes (rail/carrier destroyed). Set on the LCV ship in
    //HangarOps::loadOrRollLCVFrag so both destruction handlers can't double-roll in
    //one in-memory sweep. Transient (not persisted/serialized) — declared here so
    //PHP 8.2+ doesn't deprecate the dynamic-property assignment.
    public $lcvFragRolledTurn = null;
    public $lcvFragRolledValue = 0;
	public $nonRotating = false; //some bases do not rotate - this attribute is used in combination with $base or $smallBase
	public $osat = false; //true if object is OSAT (this includes MicroSATs and mines)
	public $mine = false;
    public $SixSidedShip = false;
	public $isCombatUnit = true; //is this a combat unit (as opposed to non-combat - transport, freighter, civilian, explorer, diplomatic ship, yacht...)
    public $bulkBuy = 1; //Variable to track mass purchases in Fleet Selection.
	public $triadOrder = false; // Used to flag Triad: Order units for immunity from their Flare Generators   GTS_Triad

	/* ⭐ THE OUTER-STRUCTURE-RING RULE (Vree saucers). On most hulls a Structure block IS
	   the compartment its systems sit in, so losing the block takes the systems with it
	   (ShipSystem::isDestroyed's cascade). On a Vree saucer the blocks are an outer RING
	   around a disc whose systems are elsewhere, so a breached block does NOT destroy the
	   systems shown in it - which is why a Xill that lost both Port structures kept firing
	   its Port Antiproton Guns on the tabletop but not here (user report, game 4285).

	   Set it on the HULL, not on 30 systems one at a time: addSystem below stamps
	   ShipSystem::$survivesStructureDestruction on everything the hull mounts, so it is
	   baked into the blueprint and therefore into the static ship bundle
	   (ShipCompactor::annotateSystems), the crit-catalogue endpoint's `ssd`, the lobby's
	   damage preview and every live ship, from ONE line per hull class.

	   PROTECTED on purpose: this must not become a public ship property, or it rides every
	   ship of every gamedata poll for the ~1% of hulls that set it. Nothing on the client
	   needs the hull-level flag - the per-system one already has two delivery routes.

	   Structures themselves are skipped: a Structure's own destruction rule is the
	   PRIMARY-structure test in isDestroyed, and this flag has nothing to say about it. */
	protected $systemsSurviveStructureLoss = false;

	//non-combat ships cannot be taken in pickup battles by standard tourtnament rules
	//rule of thumb is that if it has cargo bays, then it's not a combat ship - but it's far from proof
	//eg. Pak'ma'ra and Orieni capital ships (combat ones) do have cargo bays, while eg. Emperor's transport or Grey Sharlin (non-combat ships) do not
	//by core definition, combat ship is one that is intended to be present in fleet sent into combat zone.

	public $toHitBonus = 0; //Used to increase hit chance of all weapons fired by a ship e.g. Elite Crew / Markab enhancements.		
    public $critRollMod = 0; //penalty to critical damage roll: positive means crit is more likely, negative less likely (for all systems)

	
	public $halfPhaseThrust = 0; //needed for half phasing; equal to thrust from two BioThrusters on a given ship; 0 for ships that cannot half phase, eg. vast majority
    

    public $jinkinglimit = 0; //Some ships can jink, e.g. Torvalus MCVs
	
    public $enabledSpecialAbilities = array();

	//Chameleon Sensor Suite: phpclass of the simulacrum this ship projects, chosen at purchase.
	//null / '' means "None" - no disguise - which is the default and the fallback for anything
	//unresolvable. NEVER serialize this: it is the secret the whole feature protects.
	public $chameleonDisguiseClass = null;
	//Per-load cache of the simulacrum's pristine blueprint (see getChameleonBlueprint). Transient -
	//declared here so PHP 8.2+ doesn't deprecate the dynamic-property assignment.
	public $chameleonBlueprint = null;
	//Per-VIEWER marker: does the player this payload is being built for still see the simulacrum?
	//Set by TacGamedata::applyChameleonDisguise() during prepareForPlayer() and consumed by
	//stripForJson(). Transient and never persisted - the model is rebuilt per request, and the
	//outgoing JSON is already cached per user (game_{id}_user_{uid}_json).
	public $chameleonDisguisedForViewer = false;
	//The phantom SHEET (D1/D2): a live ship of the simulacrum's class that accumulates its own
	//damage under shipid = -realId, so the enemy sees a plausibly damaged hull rather than a
	//pristine one. Distinct from chameleonBlueprint, which must stay pristine because every
	//plausibility threshold is measured against it. Deliberately NOT in $gamedata->ships (D1) -
	//it would then have to be excluded from initiative, movement, targeting, CPV and isFinished().
	public $chameleonPhantom = null;
	public $chameleonIsPhantom = false; //true on the phantom sheet itself, never on a real ship
	//Per-load cache of the real weapon id -> simulacrum weapon id map (D7, Stage 6). Transient.
	public $chameleonWeaponMap = null;

    public $canvasSize = 200;

	//Guard for Enhancements::addEnhancementSystems - the enhancement-mounted systems (Extra
	//Tendrils) must be built exactly once per ship object. One request can load gamedata more than
	//once (advanceGameState, then the phase's own load), and a second pass would mount a second
	//pair. Transient, per object, never persisted.
	public $enhancementSystemsAdded = false;

	//Pre-battle damage & fleet damage persistence (PREBATTLE_DAMAGE_PLAN.md).
	//Compact wire-format payload: {sys:{<systemid>:{d,k,c}}, ftr:{<ordinal>:{d,k,c}}}.
	//See PreBattleDamage for the format and all of its rules.
	//INVARIANT: read by BuyingGamePhase::process ONLY. Every other phase ignores the
	//field, so a client cannot inject damage mid-game by POSTing it in, say, Movement.
	public $preBattleDamage = array();
	//Display-only companion set by Manager::loadSavedFleet: {damage:bool, criticals:bool}
	//describing what the SAVED FLEET carried, as opposed to what the player chose to
	//load. Never submitted - construcGamedata must not copy it.
	public $preBattleAvailable = null;

    public $outerSections = array(); //for determining hit locations in GUI: loc, min, max, call (loc is location id, min/max is for arc, call is true if location systems can be called)
   
    protected $activeHitLocations = array(); //$shooterID->targetSection ; no need for this to go public! just making sure that firing from one unit is assigned to one section
    protected $VreeHitLocations = false; //Value to indicate that all gunfire from the same ship may not hit same side on Vree capital ships	
   
    //following values from DB
    public $id, $userid, $name;
    protected $campaignX, $campaignY; //Not used as far as I can tell, just null entries in db.    
    public $rolled = false;
    public $rolling = false;
	protected $EMHardened = false; //EM Hardening (Ipsha have it) - some weapons would check for this value!
	
	public $ignoreManoeuvreMods = false;//New marker for factions like Mindriders that don't take penalties for pivoting etc
    public $trueStealth = false; //For ships that can actually be hidden, not just jammer from range.  Important for Front End.	
    public $skinDancing = array();	//Holds target ids when there's a successful skin dance.
    public $hasAttached = array(); // Holds shooterid => location for attached boarding pods on this ship.
    public $attached = array(); // Holds targetid => location if this unit is attached to another unit.
    public $hasAttachedFacing = array(); // shooterid => entry-side hex offset 0-5 (facing offset relative to host's facing direction)
    public $attachedFacing = array(); // targetid  => entry-side hex offset 0-5 (facing offset relative to host's facing direction)
    protected $skinDancer = false; //Let';s ships of unusual size skin dance e.g. Toravlus capitals ships.   	

	public $isCloaked = false;  //Used for deactivating Trek shields when the Trek cloak is activated

    public $team;
    private $expectedDamage = array(); //loc=>dam; damage the unit is expected to take this turn (at outer locations), to decide where to take ambiguous shots
    
    public $slotid;
    public $canPreOrder = false; //Marker for ships equipped with systems that are used every turn in Deployment/Pre-Orders Phase.

    public $movement = array();
    	    
		//unit enhancements
		public $enhancementOptions = array(); //ID,readableName,numberTaken,limit,price,priceStep
		public $enhancementOptionsEnabled = array(); //enabled non-standard options - just IDs
		public $enhancementOptionsDisabled = array(); //disabled standard options - jsut IDs
		public $enhancementTooltip = ""; //to be displayed with ship name / class

		/* PER-SYSTEM enhancements (WEAPON_ENHANCEMENTS_PLAN.md §3.1). A SEPARATE array from
		   enhancementOptions on purpose (D2): confirm.js renders one buy-dialog row per
		   enhancementOptions INDEX and gamedata.readBulkPurchase walks .selectAmount.shpenh<N>
		   by that same index until the first gap, so a sparse index list silently zeroes every
		   enhancement after the gap. The two arrays also diverge at index 6 - isOption there,
		   systemid here.
		   Tuple: [enhID, humanName, count, limit, price, priceStep, systemid, sysname]
		   Indices 0-5 match enhancementOptions so describeTaken and the price helpers work on both.
		   ⚠️ Index 4 on a PURCHASE is the TOTAL points paid for the whole row (all `count` levels)
		   - that is what tac_sys_enhancements.enhvalue stores and what a refund pays back. On an
		   OFFER it is the price of ONE level. The two arrays never mix, and systemEnhancementOffers
		   uses a leaner 5-slot shape of its own (Enhancements::setSystemEnhancementOptions). */
		public $systemEnhancements = array();       //PURCHASED per-system refits
		public $systemEnhancementOffers = array();  //what MAY be bought - lobby only, never sent back (D3)

    public $advancedArmor = false; //set to true if ship is equipped with advanced armor!
	public $hardAdvancedArmor = false; // set to true if ship is equipped with hardented advanced armor - GTS
	
	
	public $hangarRequired = ''; //usually empty, but some ships (LCVs primarily) do require hangar space!	
	public $unitSize = 1; //typically ships are berthed in dedicated space, 1 per slot - but other arrangements are certainly possible.

	public $outOfTier = array(); //interpreted in gamelobby.js (fleet checker); indicates number of out-of-bounds elements, and their kind 
	//like: EMINE => 2
	//relevant entries and their limits before a warning is shown are listed in gamelobby.js

	//another approach to the same problem - commented out but not deleted
	//public $messageOP = array(); //Used by fleet checker to give specific warnings about some fleet choices e.g. Warlock, e-mines.
	
	protected $adaptiveArmorController = null; //Adaptive Armor Controller object (if present)
	protected $IFFSystem = false;  
    protected $commandControl = false;     
	    
        function __construct($id, $userid, $name, $slot){
            $this->id = (int)$id;
            $this->userid = (int)$userid;
            $this->name = $name;
            $this->slot = $slot;
			$this->fillLocationsGUI();//so called shots work properly
        }
		
		public function getAdvancedArmor(){
			return $this->advancedArmor;    
	    }
		
		public function getHardAdvancedArmor(){   // GTS hardened advanced armor
			return $this->hardAdvancedArmor;
		}

		public function getEMHardened(){
			return $this->EMHardened;    
	    }

		public function getIFFSystem(){
			return $this->IFFSystem || $this->commandControl;
	    }
       
		public function setIFFSystem(){
			$this->IFFSystem = true;    
	    }            
		
		public function getAdaptiveArmorController(){
			return $this->adaptiveArmorController;    
		}
		public function createAdaptiveArmorController($AAtotal, $AApertype, $AApreallocated){ //$AAtotal, $AApertype, $AApreallocated
			$this->adaptiveArmorController = new AdaptiveArmorController($AAtotal, $AApertype, $AApreallocated); 
			return $this->getAdaptiveArmorController();
		}
	
		public function getHyachSpecialists(){
			return $this->HyachSpecialists;    
		}

		public function createHyachSpecialists($specTotal){ //$specTotal
			$this->HyachSpecialists = new HyachSpecialists($specTotal); 
			return $this->getHyachSpecialists();
		}		

        
        /**
         * Checks if this ship is, or is a variant of, the specified hull.
         * Matches against phpclass (e.g., 'gaimMoas'), shipClass (e.g., 'Moas Gunship'),
         * and variantOf (e.g., 'Moas Gunship').
         * @param string $hullName The name of the hull or class to check against.
         * @return bool
         */
        public function isHull($hullName) {
            if ($this->phpclass === $hullName) return true;
            if ($this->shipClass === $hullName) return true;
            if ($this->variantOf === $hullName) return true;
            return false;
        }

        public function getCommonIniModifiers( $gamedata ){ //common Initiative modifiers: speed, criticals
            $mod = 0;
            $speed = $this->getSpeed();
			
			/*first turn, on deployment - use always speed 5 (without this modification speed 0 is used)*/
			if(($gamedata->turn <= 1) && ($gamedata->phase <= 1)) $speed = 5;
        
            if ( !($this instanceof OSAT) ){
                if ($speed < 5){
                    $mod = (5-$speed)*(-10);
                }
                $CnC = $this->getSystemByName("CnC");
                if ($CnC){
			    $mod += -5*($CnC->hasCritical("CommunicationsDisrupted", $gamedata->turn));
			    $mod += -10*($CnC->hasCritical("ReducedIniativeOneTurn", $gamedata->turn));
			    $mod += -10*($CnC->hasCritical("ReducedIniative", $gamedata->turn));		    
				//additional: SWTargetHeld (ship being held by Tractor Beam - reduces Initiative
	    		$mod += -20*($CnC->hasCritical("swtargetheld", $gamedata->turn)); //-4 Ini per hit
				//additional: tmpinidown (temporary Ini reduction - Abbai weapon scan do so!
				$mod += -5*($CnC->hasCritical("tmpinidown", $gamedata->turn)); //-1 Ini per crit
				//additional: ShadowPilotPain						
			    $mod += -5*($CnC->hasCritical("ShadowPilotPain", $gamedata->turn));
				$mod += -20*($CnC->hasCritical("HangarOperations", $gamedata->turn));
				$mod += -50*($CnC->hasCritical("LCVLaunchedThisTurn", $gamedata->turn));
			/* WALKERS OF SIGMA-957 - Energy Draining Field initiative drain
			   (WALKERS_OF_SIGMA_PLAN.md 2.2). ⚠️ The crit's param is in TABLETOP points and FV
			   initiative is d100, so it is multiplied by 5 - plan trap 5. The rules cap the
			   drain at -20 tabletop, which is -100 here, and the cap belongs on the EDF's own
			   accumulated total rather than on $mod (everything else in this method is a
			   separate effect and must not be squeezed by it).
			   Summed, not counted: one crit is a whole roll (see the crit classes). */
			$edfIni = (int)$CnC->sumCriticalParam("EdfIniDrain", $gamedata->turn);
			if ($edfIni > 0) $mod -= min($edfIni, EdfExposure::INI_CAP) * EdfExposure::INI_SCALE;
			}
		    if ($this instanceof FighterFlight){
			    $firstFighter = $this->getSampleFighter();
			    if ($firstFighter){
			    	$mod += -5* $firstFighter->hasCritical("tmpinidown", $gamedata->turn);
					$mod += -50* $firstFighter->hasCritical("LaunchedThisTurn", $gamedata->turn);
					//HK Jamming: disruption crits land on the flight's sample fighter (flights have no CnC).
					//ReducedIniativeOneTurn is -10 (=-2 tabletop) each; the table stacks up to ×2 for -4.
					$mod += -10* $firstFighter->hasCritical("ReducedIniativeOneTurn", $gamedata->turn);
					$mod += -10* $firstFighter->hasCritical("ReducedIniative", $gamedata->turn);
					//Uncontrolled = -3 tabletop ini for the lost-control turn (-15 in FV d100 units).
					$mod += -15* $firstFighter->hasCritical("Uncontrolled", $gamedata->turn);
					/* EDF initiative drain on a flight - the crit rides the sample fighter,
					   which is where every other flight initiative crit above lives. Same
					   x5 conversion and same -20-tabletop cap as the ship branch. */
					$edfIniFtr = (int)$firstFighter->sumCriticalParam("EdfIniDrain", $gamedata->turn);
					if ($edfIniFtr > 0) $mod -= min($edfIniFtr, EdfExposure::INI_CAP) * EdfExposure::INI_SCALE;
			    }
		    }
            if (!empty($this->attached)) $mod += -10;//Attached Pods get -10 to Iniative as if just launched.
	    }

	    //REINFORCEMENTS_PLAN.md STAGE 9 - a unit that has just come out of hyperspace off course
	    //is disordered for the turn it arrives on. Outside the OSAT guard above: it applies to
	    //everything that can ride a doorway, and an OSAT cannot be a reinforcement at all
	    //(alwaysDeploysTurnOne refuses the flag at the buy), so the answer is 0 there anyway.
	    $mod += $this->getReinforcementArrivalIniModifier($gamedata);

	    return $mod;
    }

    /* ⭐⭐ REINFORCEMENTS_PLAN.md STAGE 9 - THE ARRIVAL INITIATIVE PENALTY.
     *
     * "Scatter hexes + 2 per 60 degrees of facing shift, applied to units arriving through that
     * vortex, on their arrival turn only." A wave that comes out of hyperspace four hexes off
     * course and turned sideways spends the turn sorting itself out.
     *
     *     -5 per hex of scatter          (1 tabletop initiative point)
     *     -10 per 60 degrees of facing   (2 tabletop initiative points)
     *
     * ⚠️ FV INITIATIVE IS d100 AND EVERY MODIFIER IN THIS FILE IS FIVE TIMES ITS TABLETOP VALUE -
     * the crit lines above are the reference (-5 is written "-1 Ini per crit", -20 is "-4 Ini per
     * hit"), as is the -10 per point of speed below 5. Written x1 this rule would be worth a
     * fifth of a point and would do nothing at all, while still looking implemented.
     *
     * abs() ON THE FACING STEPS. openExitVortex stores them signed and shortest-way-round (-2..3)
     * because the log line reads better for it; turning left is exactly as disordering as turning
     * right.
     *
     * ⭐ THE ORDER OF THE FIRST TWO TESTS IS THE EFFICIENCY GATE (Stage 9, refinement 4), and it is
     * the cheap one first ON PURPOSE. getCommonIniModifiers runs for every ship at every turn
     * advance, so this method is on the hottest path reinforcements touch. $reinforcement is a
     * plain property read and is false for every unit of every ordinary game - so a game without
     * the feature pays one boolean per ship per turn, and only a genuinely flagged unit ever
     * reaches the rule lookup or the vortex walk.
     *
     * ⚠️ THE RULE TEST STILL HAS TO BE THERE, after it: the column survives in games whose rule was
     * removed, and in the pre-fix fleets §5 trap 25 describes.
     *
     * ⚠️ ARRIVAL TURN ONLY, which isArrivingReinforcement is (arrivalTurn === this turn). It is not
     * "has arrived": a unit that came through a badly-scattered doorway three turns ago is an
     * ordinary ship and must roll like one.
     *
     * ⚠️ ASKED OF A REAL gamedata LOAD. The scatter is rebuilt from an IndividualNote on the
     * opener's engine, which a POST-side object does not carry (plan trap 3). Both callers -
     * Manager::generateIniative and SimultaneousMovementRule - roll on a full server load, which is
     * also the only moment initiative is generated. */
    public function getReinforcementArrivalIniModifier($gamedata)
    {
        if (empty($this->reinforcement)) return 0;
        if (!$gamedata || !$gamedata->rules || !$gamedata->rules->hasRuleName('allowReinforcements')) return 0;

        $scatter = JumpEngine::getArrivalScatter($this, $gamedata);
        if ($scatter === null) return 0;

        return -5 * (int)$scatter['hexes'] - 10 * abs((int)$scatter['facingSteps']);
    }
    

	
	/*calculates current combat value of the ship, as a perentage of original value - algorithm modified by public discussion
	algorithm:
		- sum all current boxes, weighted by system class
  		- sum all max boxes, weighterd by system class
    		- current/mas is base percentage (<10% is 10%, >=95% is 100%)
      		- further modified by core system destruction
		- core systems affecting value:
  			- C&C - 0%
			- Sensors - 50%
     			- Engine - 50%
			- Thrusters - 5/20/40/50% for 1/2/3/4 sets missing (but counted only if engine is present)
   		- weight modifiers:
     			- core systems - 0 (simply do not count them. their destruction will be counted separately)
			- Thrusters - 0 (simply do not count them, their detruction will be counted separately)
			- Structure - 2
   			- Weapons - 3, unless no weapons are left then 5 (note: count ElInt Sensors as a weapon)
      			- everything else - 1
	 		- this basically means that point value is primarily derived from structure and weapons, with other systems being counted a little or not at all - but completely disabling relevant abilities will be counted extra)
	*/
	public function calculateCombatValue() {
		$effectiveValue = 100;
		$overallModifier = 1;

		$weaponCurr = 0; //remaining HP
		$weaponMax = 0; //full HP
		$weaponMultiplier = 3; //base value
		$weaponDmgMultiplier = 0.7; //at what rate should damaged boxes be added to healthy boxes; for weapon, damaged weapons retain most but not all of their value
		$weaponMultiplierMax = 5; //to be used if no weapons are left

		$structCurr = 0;
		$structMax = 0;
		$structMultiplier = 2;
		$structDmgMultiplier = 0; //for Structure, damaged is damaged - count damaged as destroyed
		
		$coreCurr = 0;
		$coreMax = 0;
		$coreDmgMultiplier = 0; //for core systems - count damage the same as destruction
		$coreMultiplier = 0; //for core systems - do not count their value at all (functionality loss of key systems is noted, and heavily so)
		
		$thrusterCurr = 0;
		$thrusterMax = 0;
		$thrusterMultiplier = 0; //for thrusters - do not count their value at all (functionality loss of thruster sets destruction is noted)
		$thrusterDmgMultiplier = 0; 
		
		$otherCurr = 0;
		$otherMax = 0;
		$otherMultiplier = 1;
		$otherDmgMultiplier = 0; //for other systems, we do not know how useful they are after being damaged... but examples are hangars and cargo bays - assume destroyed boxes have no value

		$cncPresent = false;
		$enginePresent = false;
		$scannerPresent = false;
		
		//LCV Rails: a docked LCV is $removed (so isDestroyed() returns true) but is
		//NOT actually destroyed — it's a full ship parked on a rail and should keep
		//its full combat value while docked (it relaunches intact), like a carried
		//parasite, not a wreck. Detect "removed-by-docking" (an LCV whose PRIMARY
		//structure is undamaged) and skip the destroyed-ship zeroing below. A
		//genuinely damage-destroyed LCV (primary structure gone) still zeroes.
		$dockedLCV = false;
		if ($this->removed
			&& (($this instanceof LCV) || (isset($this->hangarRequired) && strtolower(trim((string)$this->hangarRequired)) === 'lcvs'))) {
			$primaryStruct = $this->getStructureSystem(0);
			$dockedLCV = !($primaryStruct && $primaryStruct->isDestroyed());
		}

		//destroyed ship gets no value UNLESS it successfully jumped to Hyperspace
		if($this->isDestroyed() && !$dockedLCV){
            if(!$this instanceof FighterFlight && !$this->base && !$this->osat){
                /* A unit that LEFT through hyperspace keeps the value it had when it went.
                   hasJumpedToHyperspace asks the jump engine when the unit has one - the
                   boost path's behaviour, unchanged - and reads the primary structure's own
                   HyperspaceJump entry when it does not, because any unit may use an open jump
                   vortex, including one with no jump engine of its own
                   (JUMP_POINTS_PLAN.md Stage 4, section 2.5). */
                if($this->hasJumpedToHyperspace()){
                    //Do NOT zero $effectiveValue if ship has jumped.
                    $effectiveValue = $this->getCVBeforeJump();
                    return $effectiveValue;
                }
            }
            //Hasn't jumped, set value to 0 as normal.
            $effectiveValue = 0;
        }
        
        if($this instanceof Mine && $this->spawned !== -1){
            //Mines which have been created by weapons, don't count towards Fleet Value
            $effectiveValue = 0;   
        }

		/*moved
		$cnc = $this->getSystemByName("CnC");
		if($cnc){
			foreach($cnc->criticals as $critDisabled){
				if($critDisabled->phpclass == "ShipDisabled") $effectiveValue = 0;//Captured, no value!					
			}
		}
  		*/		
		
		if($effectiveValue>0){ //check for critical systems: Sensors, Engine, C&C - if none are active, reduce combat value appropriately
			
            if($this instanceof Mine) return $effectiveValue; //If mine exists at all, it's worth it's full value. 

            $cncPresent = false;
			$enginePresent = false;
			$scannerPresent = false;          

			foreach ($this->systems as $system) {
				if (!$system->isDestroyed()) {
					if ($system instanceOf Scanner) $scannerPresent = true;
					if ($system instanceOf Engine) $enginePresent = true;
					if ($system instanceOf CnC) {
						$cncPresent = true;
						foreach($system->criticals as $critDisabled){ //look for disabled ship! - 
							if($critDisabled->phpclass == "ShipDisabled") $effectiveValue = 0;//Captured, no value!					
						}
					}
				}
			}
			if ( (!$this->osat) && (!$cncPresent) ) $effectiveValue = 0; //ship disabled - no value - except OSATs which simpy don't have C&C!
			if (!$scannerPresent) $overallModifier = $overallModifier/2; //no Sensors: cut value in half
			if ( (!$this->base) && (!$this->osat) && (!$enginePresent)) $overallModifier = $overallModifier/2; //no Engine: cut value in half - except starbases which don't have any engine, and OSATs for which it's secondary anyway
		}	

		if(($effectiveValue>0) && ($enginePresent)){ //if engine is present - check for thruster sets (no engine present already skips check for bases and OSATs
			$set1 = false;
			$set2 = false;
			$set3 = false;
			$set4 = false;
			$thrusterList = $this->getSystemsByName('Thruster', false); //list of active thrusters on the ship						    
			foreach ($thrusterList as $thruster) {
				if ($thruster->direction == 1) $set1 = true;
				if ($thruster->direction == 2) $set2 = true;
				if ($thruster->direction == 3) $set3 = true;
				if ($thruster->direction == 4) $set4 = true;
			}
			$totalSets = 0;
			if ($set1) $totalSets += 1;
			if ($set2) $totalSets += 1;
			if ($set3) $totalSets += 1;
			if ($set4) $totalSets += 1;
			$setModifier = 1; //all 4 sets
			if ($totalSets == 3) $setModifier = 0.95; //one set missing, -5%
			else if ($totalSets == 2) $setModifier = 0.8; //two sets missing, -20%
			else if ($totalSets == 1) $setModifier = 0.6; //three set missing, -40%
			else if ($totalSets < 1) $setModifier = 0.5; //no thrusters left, -50%
			$overallModifier *= $setModifier;
	   	}
		
		
		if($effectiveValue>0){ //check for state of structures and systems; calculate total boxes and total remaining boxes 
			$currentStructure = 0;
			$totalStructure = 0;
				      
			foreach ($this->systems as $system) if($system->getCountForCombatValue()) { //skip technical systems
				$systemCurr = 0;
				$systemDmg = 0;
				$systemMax =  $system->maxhealth;
				$bump = ($system instanceOf Structure) ? $system->orbitalBump : 0; //docked Kirishiac Orbital boxes merged into this block - already counted on the orbital system itself
				$systemMax -= $bump;
				if (!$system->isDestroyed()) {
					$systemCurr = max(0, $system->getRemainingHealth() - $bump); //bump boxes are pristine (they belong to the orbital) - damage comes off the base pool
					$systemDmg = $systemMax - $systemCurr;
				}

				//classify system appropriately
				if ($system instanceOf Structure) { //Structure block
					$structCurr += $systemCurr + ($systemDmg * $structDmgMultiplier);
					$structMax += $systemMax;
				/* JumpEngine is a Weapon subclass (JUMP_POINTS_PLAN.md section 3.1) purely so it can
				   declare a hex-targeted vortex - it is not a gun and must not be valued as one. Two
				   things break if it lands in this bucket: a 40-box engine would add 120 to both the
				   current and total weapon pools on every jump-capable ship, and the "defanged" branch
				   below (weaponCurr == 0 -> weaponMultiplierMax) could never fire while the engine
				   lived. It is $primary, so falling through leaves it in the CORE bucket - exactly
				   where it sat before the conversion. */
				} else if ((($system instanceOf Weapon) && !($system instanceOf JumpEngine)) || ($system instanceOf ElintScanner)) { //weapon! (count ElInt Scanner as a weapn here)
					$weaponCurr += $systemCurr + ($systemDmg * $weaponDmgMultiplier);
					$weaponMax += $systemMax;
				} else if ($system instanceOf Thruster) { //Thruster
					$thrusterCurr += $systemCurr + ($systemDmg * $thrusterDmgMultiplier);
					$thrusterMax += $systemMax;
				//} else if (!$system->isPrimaryTargetable) { //core system} 
				} else if ($system->primary) { //core system - change the way of assessing this
					$coreCurr += $systemCurr + ($systemDmg * $coreDmgMultiplier);
					$coreMax += $systemMax;
			   	} else { //other systems - not listed in relevant categories, but not core either
					$otherCurr += $systemCurr + ($systemDmg * $otherDmgMultiplier);
					$otherMax += $systemMax;
				}
			}

			//add all boxes counted - with appropriate multipliers!	

			//weapons
			$multiplier = $weaponMultiplier;
			if ($weaponCurr == 0) $multiplier = $weaponMultiplierMax; //if there are no weapons left, ship is defanged - count weapons higher to push total value lower despite other systems being intact!
			$currentStructure += $multiplier * $weaponCurr;
			$totalStructure +=  $multiplier * $weaponMax;

			//structural integrity
			$multiplier = $structMultiplier;
			$currentStructure += $multiplier * $structCurr;
			$totalStructure +=  $multiplier * $structMax;

			//core systems
			$multiplier = $coreMultiplier;
			$currentStructure += $multiplier * $coreCurr;
			$totalStructure +=  $multiplier * $coreMax;
					
			//thrusters
			$multiplier = $thrusterMultiplier;
			$currentStructure += $multiplier * $thrusterCurr;
			$totalStructure +=  $multiplier * $thrusterMax;
	
			//everything else
			$multiplier = $otherMultiplier;
			$currentStructure += $multiplier * $otherCurr;
			$totalStructure +=  $multiplier * $otherMax;
				      
			if($totalStructure>0){
				$structureCombatEffectiveness = $currentStructure / $totalStructure;
				$structureCombatEffectiveness = max(0.1,$structureCombatEffectiveness); //let's say structural damage cannot reduce effectiveness below 20%!
				if($structureCombatEffectiveness >= 0.95) $structureCombatEffectiveness = 1; //let's first few damage points be free - at less than 5% damage ship retains full effectiveness!
				$effectiveValue = $effectiveValue * $structureCombatEffectiveness;
			}				
		}

		$effectiveValue = $effectiveValue * $overallModifier; //this may get total value below structural minimum all right	
		return $effectiveValue;
	} //endOf function calculateCombatValue

	
	/*calculates current combat value of the ship, as a perentage of original value
	current algorithm:
	 - base is remaining boxes, as a percentage of total boxes
	  -- THIS ONE IS COMMENTED OUT -maybe it's too much :) ! PRIMARY Structure and systems that cannot be called (eg. particularly important) is counted double, so damage to outer sections is less valuable
          -- weapons are counted double, as they're in general primary way of achieving combat value, barring specialized designs
	  -- ELINT Sensors are also counted double, due to their extra importance
	  -- Structure damage is counted proportionally, same for important systems
	  -- other systems are counted as either destroyed (0 value) or not (full value) - with reasoning that their damage usually results in little combat value loss
	  -- scratches are free - if total box count value is 95% or more, it's counted as 100%
	  -- total value due to box count cannot get below 20%
	 - on top of the above, critical system status is added:
	  -- no Engine: cut value in half (cannot maneuver, most likely it's on the way out of the game even if currently it can still contribute)
	  -- no Sensors: cut value in half (this means offensive fire is mostly ineffective (except point blank and ballistics), and ship is very easy target)
	  -- no C&C: reduce value to 0 (cannot contribute to current game at all)
	*/
	public function calculateCombatValueOld() {
				$effectiveValue = 100;
		
		//destroyed ship gets no value
		if($this->isDestroyed()) $effectiveValue = 0;
			
		$cnc = $this->getSystemByName("CnC");
		if($cnc){
			foreach($cnc->criticals as $critDisabled){
				if($critDisabled->phpclass == "ShipDisabled") $effectiveValue = 0;//Captured, no value!					
			}
		}		
		
		if($effectiveValue>0){ //check for critical systems: Sensors, Engine, C&C - if none are active, reduce combat value appropriately
			$cncPresent = false;
			$enginePresent = false;
			$scannerPresent = false;
			foreach ($this->systems as $system) {
				if (!$system->isDestroyed()) {
					if ($system instanceOf Scanner) $scannerPresent = true;
					if ($system instanceOf Engine) $enginePresent = true;
					if ($system instanceOf CnC) $cncPresent = true;
				}
			}
			if ( (!$this->osat) && (!$cncPresent) ) $effectiveValue = 0; //ship disabled - no value - except OSATs which simpy don't have C&C!
			if (!$scannerPresent) $effectiveValue = $effectiveValue/2; //no Sensors: cut value in half
			if ( (!$this->base) && (!$this->osat) && (!$enginePresent)) $effectiveValue = $effectiveValue/2; //no Engine: cut value in half - except starbases which don't have any engine, and OSATs for which it's secondary anyway
		}	
		
		if($effectiveValue>0){ //check for state of structures and systems; calculate total boxes and total remaining boxes 
			$totalStructure = 0;
			$currentStructure = 0;
			foreach ($this->systems as $system) if($system->getCountForCombatValue()) { //skip technical systems
				$multiplier = 1;
				$systemBoxes = $system->maxhealth;
				$systemState = 0;
				if (!$system->isDestroyed()) {				
					$systemState = $system->maxhealth;
					if (($system instanceOf Structure) || (!$system->isTargetable)) { //Structure and particularly important systems - actually count remaining boxes
						$systemState = $system->getRemainingHealth();
					}
				}
				/* maybe DON'T multiply PRIMARY after all; core systems are both counted for damage and have extra (and harsh) multipliers if destroyed, so that should be enough
				if (($system instanceOf Structure) && (!$system->location == 0)) $multiplier = 2; //PRIMARY structure - double value!
				if ( (!($system instanceOf Structure)) && (!$system->isTargetable)) $multiplier = 2; //particularly important systems (other than Structure) - double value!
				*/
				//DO multiply value of weapons and ElInt Scanner:
				if (($system instanceOf Weapon) || ($system instanceOf ElintScanner)) $multiplier = 2; //weapons and ElInt Sensors - double value!							   
				$totalStructure += $system->maxhealth * $multiplier;
				$currentStructure += $multiplier * $systemState;
				
			}
			if($totalStructure>0){
				$structureCombatEffectiveness = $currentStructure / $totalStructure;
				$structureCombatEffectiveness = max(0.2,$structureCombatEffectiveness); //let's say structural damage cannot reduce effectiveness below 20%!
				if($structureCombatEffectiveness >= 0.95) $structureCombatEffectiveness = 1; //let's first few damage points be free - at less than 5% damage ship retains full effectiveness!
				$effectiveValue = $effectiveValue*$structureCombatEffectiveness;
			}				
		}
		
		return $effectiveValue;
	} //endOf function calculateCombatValueOld


    public function isLoSBlocked($shooterPos, $targetPos, $gamedata) {
        //$blockedHexes = $gamedata->getBlockedHexes();
		$blockedHexes = $gamedata->blockedHexes; //Just do this once outside loop	        

        $noLoS = false;
        if (!empty($blockedHexes)) {            
            $noLoS = Mathlib::isLoSBlocked($shooterPos, $targetPos, $blockedHexes);
        }
        
        return $noLoS;
    }


	public function howManyMarines(){
		$marines = 0;
		$rammingFactor = $this->getRammingFactor();
		$marines = floor($rammingFactor/20);//TT rules suggest using Ramming factor and divding by 20.	
				
		$cnc = $this->getSystemByName("CnC");//$this should be CnC, but just in case.
		if($cnc){
			foreach($cnc->criticals as $critical){
				if($critical->phpclass == "DefenderLost")	$marines -= 1;	 												
			}
		}		
		
        //Add unused Marines from Grappling Claw to bolster defences.
        if($this->hasSpecialAbility("Attaches") && !$this instanceOf FighterFlight){
            foreach ($this->systems as $system){
                if ($system instanceof GrapplingClaw)  $marines += $system->ammunition;
            }
        }    

		// Bonus marines for bases (1 per section)
		if ($this->base) {
			$sections = [];
			foreach ($this->systems as $system) {
				if ($system instanceof Structure) {
					$sections[$system->location] = true;
				}
			}
			$marines += count($sections);
		}

		// Bonus marines for Assault ships
		if (strpos($this->shipClass, 'Assault') !== false) {
			if ($this->shipSizeClass == 3) {
				$marines += 4;
			} elseif ($this->shipSizeClass == 2) {
				$marines += 3;
			} elseif ($this->shipSizeClass == 1) {
				$marines += 2;
			}
		}

		//Stage 17 ext: unspent Extra Marine Contingents pool bolsters the
		//ship's defenders. capacity - spent = marines still in stores that
		//haven't been used to restock a docked Breaching Pod yet.
		if (!($this instanceof FighterFlight)) {
			$marines += HangarOps::marinePoolRemaining($this);
		}

		$totalMarines = max(0, $marines);

		return $totalMarines;
	}

	
    public function stripForJson() {
        //Chameleon Sensor Suite: for a viewer this ship is still hiding from, the whole payload is
        //somebody else's. Marked once per load by TacGamedata::applyChameleonDisguise().
        if ($this->chameleonDisguisedForViewer) return $this->stripForJsonDisguised();

        $strippedShip = new stdClass();
        $strippedShip->name = $this->name;
        $strippedShip->team = $this->team;
        $strippedShip->currentturndelay = $this->currentturndelay;
        $strippedShip->iniative = $this->iniative;
        $strippedShip->unmodifiedIniative = $this->unmodifiedIniative;
        $strippedShip->iniativeadded = $this->iniativeadded;
        $strippedShip->destroyed = $this->destroyed;
        $strippedShip->slot = $this->slot;
        $strippedShip->unavailable = $this->unavailable;
        $strippedShip->id = $this->id;
        $strippedShip->userid = $this->userid;
        $strippedShip->rolled = $this->rolled;
        $strippedShip->rolling = $this->rolling;
        $strippedShip->slotid = $this->slotid;
        if (isset($this->EW) && !empty($this->EW)) $strippedShip->EW = $this->EW; //Terrain/Mines don't have EW for example.
        $strippedShip->movement = $this->movement;
        $strippedShip->faction = $this->faction;
        $strippedShip->phpclass = $this->phpclass;
        //LCV Rails: the client dock/recover UI gates on hangarRequired === 'LCVs'
        //to route a whole-LCV dock down the LCV-rail path. Only emitted when set
        //(empty for ordinary ships) to keep the payload lean.
        if ($this->hangarRequired !== '') $strippedShip->hangarRequired = $this->hangarRequired;
        if ($this->skinDancing) $strippedShip->skinDancing = $this->skinDancing;
        if (!empty($this->hasAttached)) $strippedShip->hasAttached = $this->hasAttached;
        if (!empty($this->attached)) $strippedShip->attached = $this->attached;
        if (!empty($this->hasAttachedFacing)) $strippedShip->hasAttachedFacing = $this->hasAttachedFacing;
        if (!empty($this->attachedFacing)) $strippedShip->attachedFacing = $this->attachedFacing;
        if ($this->spawned !== null && $this->spawned !== -1) $strippedShip->spawned = $this->spawned;
        if ($this->removed) {
            $strippedShip->removed = true;
            if ($this->removedTurn !== null) $strippedShip->removedTurn = $this->removedTurn;
        }
        //Emitted only when true, so every other unit's payload is byte-identical to before (the
        //fleet list reads a plain falsy on anything that did not leave inside a carrier).
        if ($this->jumpedWithCarrier) $strippedShip->jumpedWithCarrier = true;

        /* REINFORCEMENTS_PLAN.md §3.1 — shipManager.getTurnDeployed/getTurnPlaced mirror the server
           pair and read all three, so a reinforcement that did not carry them would answer with its
           SLOT's deploy turn on the client and with its arrival turn on the server. Emitted only on
           a reinforcement, so every other unit's payload is byte-identical to before.
           arrivalTurn/arrivalVia go out even when null, because on the client "still in hyperspace"
           is a value and not an absence. A hyperspace unit belonging to another team never reaches
           this method at all - deleteHiddenData drops the whole ship first (§3.6). */
        if ($this->reinforcement) {
            $strippedShip->reinforcement = true;
            $strippedShip->arrivalTurn   = $this->arrivalTurn;
            $strippedShip->arrivalVia    = $this->arrivalVia;
        }

        $strippedShip->systems = array_map( function($system) {return $system->stripForJson();}, $this->systems);

        //Chameleon Sensor Suite, D11 (Stage 8) - the other half of the arming mask. The DISGUISED
        //payload has had this since Stage 3 (armChameleonSimulacrumWeapons); this is the same rule
        //applied to a CSS ship the enemy sees as ITSELF, which is a different code path entirely.
        if (TacGamedata::$chameleonSuitePresent) $this->maskChameleonArming($strippedShip);

        //With changes to how we cache ships, we sadly have to re-do this each time. DK - Dec 2025
        $this->notesFill();
		$strippedShip->notes = $this->notes;                

		$strippedShip->combatValue = $this->calculateCombatValue();
		$strippedShip->pointCostEnh = $this->pointCostEnh;
		
		//unit enhancements
		/* ✦ The per-system refit SUMMARY is OWN-TEAM ONLY (user request, 2026-08-15): "System
		   Enhancements (4)" on an enemy hull tells them far too much before a shot is fired. The
		   ship-LEVEL lines stay public exactly as they always were, so this trims one line rather
		   than hiding the box. If that leaves nothing, the ship falls back to sending no tooltip at
		   all and skipping addUnitEnhancementsForJSON - which is precisely what a ship carrying only
		   system enhancements did before this feature existed.
		   ⚠️ Cosmetic hiding only, and §6.3 says so out loud: the enhanced shield output, armour and
		   thrust still reach the enemy, because their own damage and hit-chance previews read them. */
		$enhancementTooltip = $this->enhancementTooltip;
		if($enhancementTooltip !== '' && !$this->isRevealedToCurrentViewer()){
			$enhancementTooltip = Enhancements::stripSystemEnhancementSummary($enhancementTooltip);
		}
		if($enhancementTooltip !== ''){ //enhancements exist!
			$strippedShip->enhancementTooltip = $enhancementTooltip;
			$strippedShip = Enhancements::addUnitEnhancementsForJSON($this, $strippedShip);//modifies $strippedShip  object
		}

		/* The PURCHASED per-system refits, for the ✦ badge on the system icons. SystemIcon reads
		   them through systemEnhancements.hasAny(), which is how the lobby has always lit the star -
		   in game the array simply never reached the browser, so the badge was lobby-only (user
		   report, 2026-08-15).
		   OWN TEAM ONLY (D8), which is also what lets SystemIcon stay free of any client-side userid
		   comparison: an enemy payload carries no rows, so hasAny() is false for the right reason.
		   Sent verbatim - the in-game tuples come from getEnhancementsForShips with their limit and
		   priceStep columns already zeroed, and nothing in game can buy or re-price them. */
		if(!empty($this->systemEnhancements) && $this->isRevealedToCurrentViewer()){
			$strippedShip->systemEnhancements = $this->systemEnhancements;
		}

		//Stage S (fleet-value attribution): for an integrated-fighter carrier, send the
		//number of integrated fighters it BOUGHT and their per-craft CP cost. The carrier's
		//enhValue covers all of them; the fleet list values LAUNCHED integrated fighters on
		//their own flight rows, so it nets the launched ones off the carrier (and credits
		//them back on dock). Held count is read client-side from the ShadowHangar hangarUsage.
		if (HangarOps::shipHasShadowHangar($this)) {
			list($intFtrCount, $intFtrPerCraft) = HangarOps::integratedFighterPurchase($this);
			if ($intFtrCount > 0) {
				$strippedShip->integratedFighterCount    = $intFtrCount;
				$strippedShip->integratedFighterPerCraft = $intFtrPerCraft;
			}
		}

		//Push Specialists updates to Ship variables when used
		if ($this->hasSpecialAbility("HyachSpecialists")){ //Does ship have Specialists system?
			$specialists = $this->HyachSpecialists;
			$specAllocatedArray = $specialists->specAllocatedCount;
			foreach ($specAllocatedArray as $specsUsed=>$specValue){
				if ($specsUsed == 'Defence'){
					$strippedShip->forwardDefense = $this->forwardDefense; 
        			$strippedShip->sideDefense = $this->sideDefense;					
				}
				if ($specsUsed == 'Targeting'){
					$strippedShip->toHitBonus = $this->toHitBonus; 				
				}
				if ($specsUsed == 'Maneuvering'){
					$strippedShip->turncost = $this->turncost;
					$strippedShip->turndelaycost = $this->turndelaycost;
				}
			}
		}

		//LCV Rails (B5W §10.1): each LCV docked on a rail makes the carrier less
		//manoeuvrable — a turn/turn-delay this turn costs +1 THRUST per docked LCV
		//(the ship's turncost/turndelaycost STATS are unchanged; only the per-turn
		//thrust cost rises, e.g. a turn-cost-1 carrier at speed 5 with 4 docked LCVs
		//pays 5+4=9 thrust). Send the docked count so the client movement engine can
		//add it to the per-turn turn/turn-delay thrust cost. Omitted (0) when none
		//docked to keep the payload lean. Gated on $LCVCarrier so the vast majority
		//of ships skip the system scan entirely.
		if ($this->LCVCarrier) {
			$dockedLCVs = HangarOps::dockedLCVCount($this);
			if ($dockedLCVs > 0) $strippedShip->dockedLCVs = $dockedLCVs;
		}

        //Pass increased defence profile to front end directly.
        $CnC = $this->getSystemByName("CnC");
        if ($CnC) {
            $defenceMod = $CnC->hasCritical("ProfileIncreased");
            if ($defenceMod) {
                $this->forwardDefense += $defenceMod;
                $this->sideDefense += $defenceMod;
                $strippedShip->forwardDefense = $this->forwardDefense; 
                $strippedShip->sideDefense = $this->sideDefense;
            }
        }

		if($this->hasSpecialAbility("MindriderEngine")){//Mind's Eye Contraction needs a few more values to got to Front End.
			$strippedShip->forwardDefense = $this->forwardDefense; 
        	$strippedShip->sideDefense = $this->sideDefense;
		    $strippedShip->Enormous = $this->Enormous; 
			$strippedShip->imagePath = $this->imagePath;
			$strippedShip->canvasSize = $this->canvasSize;			 		           	
		}				
	
		
		//$strippedShip->enhancementOptions = array(); // can remove - will be emptied in front end when ships are built - DK
        return $strippedShip;
    }

	/*Chameleon Sensor Suite - the payload a viewer who still believes the deception receives: the
	  SIMULACRUM's ship, wearing this ship's identity and position.

	  Built by asking the blueprint to strip ITSELF rather than by editing this ship's payload, which
	  is what makes the deception complete for nothing: phpclass, faction, systems (names, arcs,
	  armour, hit chart, ELINT-ness), notes, point cost and combat value all come out of a pristine
	  ship of the fake class, so the DEFAULT for any field is "fake" and nothing of the real ship can
	  leak through a line somebody forgot to write. It also gets finding #5 for free - game.php
	  builds window.staticShips from the phpclasses in this already-masked payload, so the enemy's
	  page preloads the simulacrum's blueprint and never sees this hull's.

	  Everything patched back in below is either observable with the naked eye or needed to interact
	  with the unit at all (the §3 leak audit).

	  STAGE 4 - the systems come from the PHANTOM SHEET when one exists: a live simulacrum-class ship
	  carrying its own damage (D1/D2), so the hull the enemy sees can be visibly shot up. It falls
	  back to the pristine blueprint whenever no phantom was built - a load that never reached
	  DBManager::getChameleonPhantoms, e.g. POST-side ship reconstruction - which is exactly the
	  Stage 3 behaviour and is always safe, just pristine.
	  Still open: a disguised ship's own shots do not reach the enemy's combat log until its fire
	  orders are remapped onto simulacrum weapons (D7, Stage 6), and nothing WRITES phantom damage
	  until the mirrored resolution lands (D3, Stage 5) - Stage 4 is the read path.
	  The one thing neither sheet states for itself is weapon arming - see
	  armChameleonSimulacrumWeapons(), which is D11 brought forward because a sheet nobody has
	  calculated loading for reads as literally null rather than as a plausible default.*/
	public function stripForJsonDisguised() {
		$blueprint = $this->getChameleonBlueprint();
		if ($blueprint === null){
			//Unresolvable simulacrum: tell the truth rather than invent something. Clearing the
			//marker first is what stops the fallback recursing back into here via stripForJson().
			$this->chameleonDisguisedForViewer = false;
			return $this->stripForJson();
		}

		//The phantom is the sheet the enemy reads; the blueprint stays pristine because every
		//plausibility threshold is measured against it.
		$sheet = $this->getChameleonSheet();

		$this->armChameleonSimulacrumWeapons($sheet);
		$disguised = $sheet->stripForJson();
		$this->reassignChameleonSheetIds($disguised);
		$this->remapChameleonFireOrders($disguised);

		//--- identity: needed to target it, team it and talk about it at all
		$disguised->id     = $this->id;
		$disguised->userid = $this->userid;
		$disguised->team   = $this->team;
		$disguised->slot   = $this->slot;
		$disguised->slotid = $this->slotid;
		$disguised->name   = $this->getChameleonMaskedName($blueprint);

		//--- observable with the naked eye
		$disguised->movement    = $this->movement;   //position and facing are not hideable
		$disguised->destroyed   = $this->destroyed;
		$disguised->unavailable = $this->unavailable;
		$disguised->rolled      = $this->rolled;
		$disguised->rolling     = $this->rolling;
		if ($this->spawned !== null && $this->spawned !== -1) $disguised->spawned = $this->spawned;
		else unset($disguised->spawned);
		unset($disguised->removed, $disguised->removedTurn);
		if ($this->removed) {
			$disguised->removed = true;
			if ($this->removedTurn !== null) $disguised->removedTurn = $this->removedTurn;
		}
		//Reinforcement state travels REAL, for the same reason initiative below does: the sheet is
		//a hull, not a history, and the client's getTurnDeployed reads these three. A unit still in
		//hyperspace is not on the board to be disguised in the first place, so in practice this only
		//ever carries an arrival that has already happened - which everyone watched happen.
		unset($disguised->reinforcement, $disguised->arrivalTurn, $disguised->arrivalVia);
		if ($this->reinforcement) {
			$disguised->reinforcement = true;
			$disguised->arrivalTurn   = $this->arrivalTurn;
			$disguised->arrivalVia    = $this->arrivalVia;
		}

		//--- D13: initiative and turn delay go out REAL. Faking the number while the ship still
		//moves in its true initiative order is a worse tell than the truth.
		$disguised->iniative           = $this->iniative;
		$disguised->unmodifiedIniative = $this->unmodifiedIniative;
		$disguised->iniativeadded      = $this->iniativeadded;
		$disguised->currentturndelay   = $this->currentturndelay;

		//--- EW goes out real: it is public from phase 2 onward (finding #22) and it is the evidence
		//the ELINT-plausibility reveal (D6c) acts on. Masking it would make the deception unbreakable.
		unset($disguised->EW);
		if (isset($this->EW) && !empty($this->EW)) $disguised->EW = $this->EW;

		//--- attachments are RELATIONAL - the viewer is usually the other half of the grapple, and
		//anything close enough to attach broke the disguise on proximity several turns ago.
		unset($disguised->hasAttached, $disguised->attached, $disguised->hasAttachedFacing, $disguised->attachedFacing);
		if (!empty($this->hasAttached))       $disguised->hasAttached       = $this->hasAttached;
		if (!empty($this->attached))          $disguised->attached          = $this->attached;
		if (!empty($this->hasAttachedFacing)) $disguised->hasAttachedFacing = $this->hasAttachedFacing;
		if (!empty($this->attachedFacing))    $disguised->attachedFacing    = $this->attachedFacing;

		//--- the spend and its breakdown name the real hull and its enhancements outright
		$disguised->pointCostEnh = 0;
		unset($disguised->enhancementTooltip);

		return $disguised;
	}

	/*The phantom's rows are stored under shipid = -realId (D2), which is a PERSISTENCE detail: it is
	  how two sheets share one tac_damage table without colliding. The client must never see it.

	  The combat log resolves each damage row with gamedata.getShip(d.shipid) before it can name the
	  system that was hit (combatLog.js:290), and there is no ship with a negative id on the page -
	  so an enemy who fired at a disguised ship got NO combat log entry for their own shot at all
	  (found in playtest, game 4273). From the enemy's point of view this damage belongs to the ship
	  they can see, which wears the real id.

	  Safe to rewrite in place: ShipSystem::stripForJson already hands back CLONES of the damage and
	  critical entries, so the phantom's own objects - the ones that get persisted - are untouched.*/
	private function reassignChameleonSheetIds($disguised)
	{
		if (!isset($disguised->systems) || !is_array($disguised->systems)) return;

		foreach ($disguised->systems as $system){
			if (!empty($system->damage)){
				foreach ($system->damage as $entry) $entry->shipid = $this->id;
			}
			if (!empty($system->criticals)){
				foreach ($system->criticals as $crit) $crit->shipid = $this->id;
			}
		}
	}

	/*D7 (Stage 6) - the disguised ship's OWN shots, moved onto the weapons the enemy can see.

	  Until this existed a disguised ship simply had no combat log on the enemy's screen: they are
	  served the simulacrum's systems, and fire orders live on the shooter's weapon, so every order
	  this ship wrote stayed behind on a system the viewer does not have.

	  Three things are rewritten on each order, and the order is CLONED before any of them: the
	  FireOrder objects reached here by reference off the real weapon, they are the same objects the
	  owner's payload serves and the ones that get persisted, so mutating them in place would corrupt
	  the real record for a masking pass that is supposed to be per-viewer.

	    weaponid   - the mapped simulacrum weapon (getChameleonWeaponMap)
	    firingMode - CLAMPED, see the trap below
	    notes      - rebuilt to the two fragments combatLog.js parses. The stored breakdown carries
	                 the REAL weapon's fire control and range penalty, which name the weapon class
	                 as surely as its id does. Thresholds are unchanged - they are true, and this
	                 ship's target is not the disguised party here.
	    pubnotes   - dropped. It is weapon-effect narrative ("Plasma cloud created on hex", "Beam is
	                 stowed", "A Targeting Array malfunctions") and there is no safe subset.

	  ⚠️ TRAP - firingMode must be clamped or the enemy's BROWSER HANGS. combatLog.js:93-98 does
	     while (modeIteration != weapon.firingMode) { weapon.changeFiringMode(); }
	  against the weapon it resolved from weaponid, and changeFiringMode cycles that weapon's own
	  declared modes. A Dargan Twin Array firing in mode 2 (Split) remapped onto a Plasma
	  Accelerator, which declares only mode 1, spins that loop forever. Clamping to mode 1 - which
	  every weapon has - is safe because the mode is a property of the weapon the viewer thinks
	  fired, not of the shot.*/
	private function remapChameleonFireOrders($disguised)
	{
		if (!isset($disguised->systems) || !is_array($disguised->systems)) return;

		$map = $this->getChameleonWeaponMap();
		if (empty($map)) return;

		//Where each simulacrum weapon ended up in the stripped payload, so orders can be dropped
		//onto it by id rather than by position.
		$byId = array();
		foreach ($disguised->systems as $system){
			if (isset($system->id)) $byId[$system->id] = $system;
		}

		foreach ($this->systems as $real){
			if (!($real instanceof Weapon)) continue;
			if (empty($real->fireOrders)) continue;
			if (!isset($map[$real->id])) continue;

			$fakeId = $map[$real->id];
			if (!isset($byId[$fakeId])) continue;
			$fake = $byId[$fakeId];

			foreach ($real->fireOrders as $order){
				$copy = clone $order;
				$copy->weaponid   = $fakeId;
				$copy->firingMode = $this->clampChameleonFiringMode($fakeId, $order->firingMode);
				$copy->notes      = TacGamedata::buildChameleonFireOrderNotes($order->notes, $order->needed, $order->needed);
				$copy->pubnotes   = '';
				//A called shot names a system on THIS hull; the viewer holds the simulacrum's.
				//There is no honest translation in this direction, so the call is simply not shown.
				$copy->calledid   = -1;

				if (!isset($fake->fireOrders) || !is_array($fake->fireOrders)) $fake->fireOrders = array();
				$fake->fireOrders[] = $copy;
			}
		}
	}

	/*Mode 1 exists on every weapon; anything else has to be declared by the substitute itself. See
	  the infinite-loop trap on remapChameleonFireOrders().*/
	private function clampChameleonFiringMode($fakeId, $mode)
	{
		$sheet = $this->getChameleonSheet();
		if ($sheet === null) return 1;
		$fake = $sheet->getSystemById($fakeId);
		if ($fake === null) return 1;
		if (empty($fake->firingModes) || !is_array($fake->firingModes)) return 1;
		return isset($fake->firingModes[$mode]) ? $mode : 1;
	}

	/*A ship's DEFAULT name is generated from its hull - "Dargan Strike Cruiser #2" - so an untouched
	  name hands the enemy the answer and the deception is over before it starts. The §3 audit called
	  for warning the player in the buy dialog; that is not enough when the leak is the default.

	  Swap any mention of the real hull for the simulacrum's, keeping everything the player actually
	  wrote, including their numbering: "Dargan Strike Cruiser #2" -> "Demos Heavy Warship #2", while
	  "Lord Kiro's Revenge" is passed through untouched. shipClass is tried before phpclass because
	  it is the longer, more specific string and consumes the shorter one.*/
	private function getChameleonMaskedName($blueprint){
		$name = $this->name;
		if ($name === null || $name === '') return $name;

		$mask = ($blueprint->shipClass !== '' && $blueprint->shipClass !== null)
			? $blueprint->shipClass : $blueprint->phpclass;

		foreach (array($this->shipClass, $this->phpclass) as $tell){
			if ($tell === null || $tell === '') continue;
			if (stripos($name, $tell) === false) continue;
			$name = str_ireplace($tell, $mask, $name);
		}
		return $name;
	}

	/*D11 - "Chameleon suites mask the arming status of weapons ... even after the deception is
	  revealed." The simulacrum is a pristine blueprint, and nothing ever calculates a blueprint's
	  loading (that runs off tac_loading against a real ship), so every gun would go out with
	  turnsloaded NULL and the enemy's system window would read "null/4".

	  Fully loaded is both the fix and the rule: it is the only arming state that is plausible on
	  every turn of every game and tells the enemy nothing. It must NOT mirror the real weapon -
	  that is exactly the information D11 exists to withhold.

	  "Fully loaded" is NOT loadingtime. An accelerator fires at one turn of charge but keeps
	  charging to a harder-hitting maximum, and it is normalload that records that maximum: a
	  Plasma Accelerator is loadingtime 1, normalload 3. The real ship's own charge cap is
	  getNormalLoad() (weapon.php:1074), and both client displays agree - SystemIcon uses
	  normalload outright as the denominator and weaponManager reads
	  Math.max(loadingtime, normalload) (weaponManager.js:2292). So the value here is that same
	  max, which is what makes an accelerator read 3/3 rather than 1/3. max() rather than
	  normalload alone because a handful of weapons declare a normalload BELOW their loading time.

	  Multi-mode weapons carry a loading time per firing mode, and the client re-reads turnsloaded
	  out of turnsloadedArray whenever that array is present (shipSystem.js:219), so the array has
	  to be mirrored key for key - keys are mode numbers, 1-based - or the masked scalar is
	  discarded the moment the viewer looks at any mode. There is no normalloadArray, so the one
	  normalload applies to every mode. Idempotent, so running it once per viewer against the
	  per-load cached blueprint costs nothing and cannot drift.*/
	private function armChameleonSimulacrumWeapons($blueprint)
	{
		//STAGE 6: a simulacrum weapon matched to a real one of the SAME CLASS mirrors that weapon's
		//arming instead of reading full - see mirrorChameleonWeaponArming() for why that is both
		//necessary and safe. Everything unmatched keeps the D11 default below.
		$mirrored = $this->getChameleonArmingMirror();

		foreach ($blueprint->systems as $system){
			if (!($system instanceof Weapon)) continue;

			if (isset($mirrored[$system->id])){
				$real = $mirrored[$system->id];
				$system->turnsloaded = $real->turnsloaded;
				if (!empty($system->loadingtimeArray) && !empty($real->turnsloadedArray)){
					$system->turnsloadedArray = $real->turnsloadedArray;
				}
				continue;
			}

			//The PROPERTY, not getNormalLoad(): boostable weapons override that method to return
			//loadingtime + maxBoostLevel, and claiming maximum boost on a phantom that shows no
			//power allocated at all (D12) would be a tell rather than a mask.
			$fullCharge = max($system->getLoadingTime(), (int)$system->normalload);
			$system->turnsloaded = $fullCharge;

			if (!empty($system->loadingtimeArray)){
				$perMode = array();
				foreach ($system->loadingtimeArray as $mode => $loadingtime){
					$perMode[$mode] = max($loadingtime, (int)$system->normalload);
				}
				$system->turnsloadedArray = $perMode;
			}
		}
	}

	/*STAGE 6, user's request - "if the phantom is equipped with the same type of weapon and it is in
	  arc of the shot just made, amend turnsloaded to simulate the phantom weapon recharging."

	  A simulacrum gun that is SEEN to fire and still reads fully loaded is a tell, so a matched
	  weapon has to show the recharge an honest simulacrum would. The implementation is one line
	  because of an identity worth stating outright:

	    a match is same-class by construction, so the two weapons have the same loadingtime and the
	    same normalload, and therefore the SAME charge curve - the simulacrum weapon can simply
	    mirror turnsloaded from the real weapon it is standing in for.

	  Measured rather than assumed (game 4273): the G'Quan's Heavy Laser id 9, loadingtime 4, fired
	  on turn 1 and read turnsloaded 1 on turn 2, while its unfired sibling id 6 read 4. So the
	  engine's curve is "fires on turn L, still reads full for the rest of turn L, then 1 on L+1, 2
	  on L+2, capped at full" - and mirroring reproduces it exactly, on every turn, across reloads
	  and replays, with no new state.

	  Why not derive it from firing history instead: fire orders are loaded for the CURRENT TURN
	  ONLY (verified - a turn-2 load shows an empty fireOrders on a weapon that fired on turn 1), so
	  "which turn did this weapon last fire" is not answerable without a new gated query over
	  tac_fireorder. The real weapon's own turnsloaded already IS that answer.

	  Twin Arrays need no special case, as the user noted: TwinArray and HeavyArray are both
	  loadingtime 1, so mirroring reads 1 either way. Mattercannon (2) and BattleLaser (3) are the
	  two that visibly recharge.

	  Does this breach D11 ("arming status is masked, permanently")? No. The enemy watched a
	  same-class weapon fire from that arc, so "this gun is reloading" is something they already saw,
	  and consistency with an observed event is not a leak. Everything unmatched stays pinned at full
	  charge, which is D11's default and still hides which of the ship's guns are actually hot.
	  Accepted residual: a matched weapon at partial charge for an UNOBSERVED reason - a
	  destroyed-then-repaired accelerator - also mirrors, and the enemy cannot tell that apart from a
	  shot. Smaller than the two tells already accepted (D13 initiative, D3c no phantom criticals).

	  Returns simulacrum weapon id => the real Weapon object it mirrors, same-class matches only.*/
	private function getChameleonArmingMirror()
	{
		$mirror = array();
		$map = $this->getChameleonWeaponMap();
		if (empty($map)) return $mirror;

		foreach ($this->systems as $real){
			if (!($real instanceof Weapon)) continue;
			if (!isset($map[$real->id])) continue;
			if ($real->turnsloaded === null) continue; //nothing calculated to mirror

			$fakeId = $map[$real->id];
			if (isset($mirror[$fakeId])) continue; //tier-4 doubling up: first claim wins

			$sheet = $this->getChameleonSheet();
			$fake = ($sheet === null) ? null : $sheet->getSystemById($fakeId);
			if ($fake === null) continue;
			//Same class is the whole justification - a looser match has a different charge curve and
			//mirroring it would produce a number no honest simulacrum could show.
			if (get_class($fake) !== get_class($real)) continue;

			$mirror[$fakeId] = $real;
		}
		return $mirror;
	}



        public function getInitiativebonus($gamedata){
            if($this instanceof Terrain) return 0;
            if($this instanceof Mine) return 0;

            $flagBridgeBonus = FlagBridge::getIniBonus($gamedata, $this);

            //LCV Rails (B5W §10.1): a carrier with LCVs docked on rails is less
            //responsive — -10 initiative per docked LCV. Folded into every return
            //below (the faction early-returns bypass the default tail). Gated on the
            //$LCVCarrier flag so every non-LCV-carrier ship (the vast majority) skips
            //the docked-count scan entirely. FighterFlights never set the flag.
            $lcvDockPenalty = 0;
            if ($this->LCVCarrier) {
                $lcvDockPenalty = 10 * HangarOps::dockedLCVCount($this, $gamedata);
            }

            /*
            if($this->faction == "Abbai Matriarchate"){
                return $this->doAbbaiInitiativeBonus($gamedata) + $flagBridgeBonus;
            }
            if($this->faction == "Centauri Republic"){
                return $this->doCentauriInitiativeBonus($gamedata) + $flagBridgeBonus;
            }
            if($this->faction == "Dilgar Imperium"){
                return $this->doDilgarInitiativeBonus($gamedata) + $flagBridgeBonus;
            }
            if($this->faction == "Narn Regime"){
                return $this->doNarnInitiativeBonus($gamedata) + $flagBridgeBonus;
            }
            if($this->faction == "Yolu Confederation"){
                return $this->doYoluInitiativeBonus($gamedata) + $flagBridgeBonus;
			}
			if ($this->faction == "Earth Alliance" || 
				$this->faction == "Earth Alliance (defenses)" || 
				$this->faction == "Earth Alliance (early)" ||
				$this->faction == "Earth Alliance (custom)"){
                return $this->doEAInitiativeBonus($gamedata) + $flagBridgeBonus;
            }
			if($this->faction == "Raiders"){
                return $this->doRaidersInitiativeBonus($gamedata) + $flagBridgeBonus;
            }
            */   
            //Pakmara have special Ini penalty
            if(($this->faction == "Pak'ma'ra Confederacy") && (!($this instanceof FighterFlight))	){
                return $this->doPakmaraInitiativeBonus($gamedata) - $lcvDockPenalty;
            }
			//Polaren have speial initiative bonus
            if((($this->faction == "Nexus Polaren Confederacy (early)") || ($this->faction == "Nexus Polaren Confederacy (early)")) && (!($this instanceof FighterFlight))	){
                return $this->doPolarenInitiativeBonus($gamedata) - $lcvDockPenalty;
            }
           /* 
		   if($this->faction == "Hyach Gerontocracy"){
		        return $this->doHyachInitiativeBonus($gamedata) + $flagBridgeBonus;
		    }            
			if(($this->faction == "Gaim Intelligence") && ($this instanceOf gaimMoas)){  //GTS
                return $this->doGaimInitiativeBonus($gamedata) + $flagBridgeBonus;
            }
            */
            return $this->iniativebonus + $flagBridgeBonus - $lcvDockPenalty;
        }
        
		/*
        private function doAbbaiInitiativeBonus($gamedata){
            foreach($gamedata->ships as $ship){
                if(!$ship->isDestroyed()
                        && ($ship->faction == "Abbai Matriarchate")
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof Nakarsa)
                        && ($this->id != $ship->id)){
                    return ($this->iniativebonus+5);
                }
            }
			return $this->iniativebonus;
        }
		
        private function doCentauriInitiativeBonus($gamedata){
            foreach($gamedata->ships as $ship){
                if(!$ship->isDestroyed()
                        && ($ship->faction == "Centauri Republic")
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof PrimusMaximus)
                        && ($this->id != $ship->id)){
                    return ($this->iniativebonus+5);
                }
            }
			return $this->iniativebonus;
        }
		
		
        private function doNarnInitiativeBonus($gamedata){
            foreach($gamedata->ships as $ship){
                if(!$ship->isDestroyed()
                        && ($ship->faction == "Narn Regime")
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof Gtal)
                        && ($this->id != $ship->id)){
                    return ($this->iniativebonus+5);
                }
            }
			return $this->iniativebonus;
        }

        
         private function doEAInitiativeBonus($gamedata){
            foreach($gamedata->ships as $ship){
                if(!$ship->isDestroyed()
                        && ($ship->faction == "Earth Alliance" || 
			                $ship->faction == "Earth Alliance (defenses)" || 
			                $ship->faction == "Earth Alliance (Early)" ||
							$ship->faction == "Earth Alliance (Custom)")
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof Poseidon)
                        && ($this->id != $ship->id)){
                    return ($this->iniativebonus+5);
                }
            }
			return $this->iniativebonus;
        }               


        private function doRaidersInitiativeBonus($gamedata){

        $mod = 0;

        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
            $pixPos = $this->getCoPos();
            //TODO: Better distance calculation
            $ships = $gamedata->getShipsInDistance($this, 5);

            foreach($ships as $ship){
                if( !$ship->isDestroyed()
                    && ($ship->faction == "Raiders")
                    && ($this->userid == $ship->userid)
                    && ($ship->shipSizeClass == 3)
                    && ($this->id != $ship->id)){
                    $cnc = $ship->getSystemByName("CnC");
                    $bonus = $cnc->output;
                    if ($bonus > $mod){
                        $mod = $bonus;
                    } else continue;
                }
            }
        }
        //    debug::log($this->phpclass."- bonus: ".$mod);
        return $this->iniativebonus + $mod*5;
    }
 
        
        private function doDilgarInitiativeBonus($gamedata){

	        $mod = 0;

	        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
	            $pixPos = $this->getCoPos();
	            //TODO: Better distance calculation
	            $ships = $gamedata->getShipsInDistance($this, 9);

	            foreach($ships as $ship){
	                if( !$ship->isDestroyed()
	                    && ($ship->faction == "Dilgar Imperium")
	                    && ($this->userid == $ship->userid)
	                    && ($ship->shipSizeClass == 3)
	                    && ($this->id != $ship->id)){
	                    $cnc = $ship->getSystemByName("CnC");
	                    $bonus = $cnc->output;
	                    if ($bonus > $mod){
	                        $mod = $bonus;
	                    } else continue;
	                }
	            }
	        }
        //    debug::log($this->phpclass."- bonus: ".$mod);
        return $this->iniativebonus + $mod*5;
    	} //end of doDilgarInitiativeBonus  
    */        
    
        private function doPakmaraInitiativeBonus($gamedata){
        	
	        $mod = 0;
			$alivePakShips = 0;
				
				foreach($gamedata->ships as $ship){
	                if(
	                     ($ship->faction == "Pak'ma'ra Confederacy") //Correct faction
	                    && ($this->userid == $ship->userid) //of same player
	                    && (!($ship instanceOf FighterFlight)) //actually a ship
	                    && (!$ship->isDestroyed())){
	                        $alivePakShips++;
	                }
					$mod = floor(($alivePakShips)/3); //Divide by three and round down
	            }
	                
	        //    debug::log($this->phpclass."- bonus: ".$mod);
	        return $this->iniativebonus - $mod*5;
    	} //end of doPakmaraInitiativeBonus    


        private function doPolarenInitiativeBonus($gamedata){
        	
	        $mod = 0;
			$alivePolarenShips = 0;
				
				foreach($gamedata->ships as $ship){
	                if(
	                     (($ship->faction == "Nexus Polaren Confederacy (early)") || ($ship->faction == "Nexus Polaren Confederacy (early)")) //Correct faction
	                    && ($this->userid == $ship->userid) //of same player
	                    && (!($ship instanceOf FighterFlight)) //actually a ship
	                    && (!$ship->isDestroyed())){
	                        $alivePolarenShips++;
	                }
					$mod = floor(($alivePolarenShips)/3); //Divide by three and round down
					if ($mod > 10) $mod = 10;
	            }
	                
	        //    debug::log($this->phpclass."- bonus: ".$mod);
	        return $this->iniativebonus + $mod*5;
    	} //end of doPolarenInitiativeBonus    


        /*    
        private function doHyachInitiativeBonus($gamedata){
            foreach($gamedata->ships as $ship){
                if(!$ship->isDestroyed()
                        && ($ship->faction == "Hyach Gerontocracy")
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof HyachIrokaiKal)
                        && ($this->id != $ship->id)){
                    return ($this->iniativebonus+5);
                }
            }
            return $this->iniativebonus;
        }


		//GTS
	private function doGaimInitiativeBonus($gamedata){
        $mod = 0;

        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
            $pixPos = $this->getCoPos();
            //TODO: Better distance calculation
            $ships = $gamedata->getShipsInDistance($this, 10);

            foreach($ships as $ship){
                if( !$ship->isDestroyed()
                    && ($ship->faction == "Gaim Intelligence")
                    && ($this->userid == $ship->userid)
                    && ($ship instanceOf gaimMearc)
                    && ($this->id != $ship->id)){
                    $cnc = $ship->getSystemByName("CnC");
                    $bonus = $cnc->output;
                    if ($bonus > $mod){
                        $mod = $bonus;
                    } else continue;
                }
            }
        }
        //    debug::log($this->phpclass."- bonus: ".$mod);
        return $this->iniativebonus + $mod*5;
    }//end of doGaimInitiativeBonus
    */  
    
    /*
    private function doYoluInitiativeBonus($gamedata){
        foreach($gamedata->ships as $ship){
            if(!$ship->isDestroyed()
                && ($ship->faction == "Yolu Confederation")
                && ($this->userid == $ship->userid)
                && ($ship instanceof Udran)
                && ($this->id != $ship->id)){
                $cnc = $ship->getSystemByName("CnC");
                $bonus = $cnc->output;
                return ($this->iniativebonus+$bonus*5);
            }
        }
        return $this->iniativebonus;
    }
    */       
	
	/*saves individual notes systems might have generated*/
	public function saveIndividualNotes(DBManager $dbManager) {
		foreach ($this->systems as $system){
            $system->saveIndividualNotes($dbManager);
        }
	}
	
	/*calls systems to generate notes if necessary*/
	public function generateIndividualNotes($gamedata, $dbManager) {
		foreach ($this->systems as $system){
            $system->generateIndividualNotes($gamedata, $dbManager);
        }
	}

    //Used in FireGamePhase->process to generate extra notes for Hyach Specialists, but could have other applications - DK - 27.12.25
	public function generateAdditionalNotes($gameData, $dbManager) {

        if($gameData->phase == 3){
            $specialists = $this->getSystemByName("HyachSpecialists");
            if ($specialists){ //Does ship have Specialists system?
                $specialists->generateIndividualNotes($gameData, $dbManager); //Generate notes for Specialists system
                $this->saveIndividualNotes($dbManager); //Save ship notes.
            }

            //Hangar Operations: persist any launch orders the player queued via
            //the launch dialog. doIndividualNotesTransfer (called during ship
            //reconstruction earlier in this request) stashed the payload into
            //each Hangar's $pendingLaunchTransfer; here we let it write the
            //hangarLaunchOrder note now that $gameData is hydrated.
            $hasHangar = false;
            foreach ($this->systems as $sys) {
                if ($sys instanceof Hangar) {
                    $sys->generateIndividualNotes($gameData, $dbManager);
                    $hasHangar = true;
                }
            }
            if ($hasHangar) {
                $this->saveIndividualNotes($dbManager);
            }

            //Kirishiac Orbitals: persist the player's dock/deploy orders (given via the
            //Dock/Deploy buttons in the Firing Phase; carried in individualNotesTransfer,
            //stashed during ship reconstruction). Takes effect next turn.
            $hasOrbital = false;
            foreach ($this->systems as $sys) {
                if ($sys instanceof KirishiacOrbital) {
                    $sys->generateIndividualNotes($gameData, $dbManager);
                    $hasOrbital = true;
                }
            }
            if ($hasOrbital) {
                $this->saveIndividualNotes($dbManager);
            }
        }
    }
	
	/*calls systems to act on notes just loaded if necessary*/
	public function onIndividualNotesLoaded($gamedata) {
		foreach ($this->systems as $system){
            $system->onIndividualNotesLoaded($gamedata);
        }
	}
     
    public function setEW($ew)
    {
        $this->EW[] = $ew;
    }

    public function setMovement($movement)
    {
        $this->movement[] = $movement;
    }

    public function setMovements($movements)
    {
        $this->movement = $movements;
    }



    public function onConstructed($turn, $phase, $gamedata)
    {	    
		//enhancements (in game, NOT fleet selection!)
		Enhancements::setEnhancements($this);
		/* PER-SYSTEM enhancements, immediately after the ship-level ones and - critically - BEFORE
		   the per-system onConstructed loop below. Shield::onConstructed derives tohitPenalty and
		   damagePenalty from getOutput(), so a Hardened Shields refit applied AFTER that loop would
		   absorb the bonus damage but not confer the bonus to-hit penalty: a half-working shield,
		   which is far worse than a broken one because nobody notices. */
		Enhancements::setSystemEnhancements($this);

        foreach ($this->systems as $system){
            $system->onConstructed($this, $turn, $phase);
            $abilities = $system->getSpecialAbilityList($this->enabledSpecialAbilities);
            if (is_array($abilities)) {
                $this->enabledSpecialAbilities = array_merge($this->enabledSpecialAbilities, $abilities);
            }
        }
        //fill $this->iniativeadded
        $modifiedbonus = $this->getInitiativebonus( $gamedata ) + $this->getCommonIniModifiers( $gamedata );
        $modifiedbonus = $modifiedbonus - $this->iniativebonus;
        $this->iniativeadded = $modifiedbonus;
    }

    public function hasSpecialAbility($ability)
    {
        return (isset($this->enabledSpecialAbilities[$ability]));
    }

    public function getSpecialAbilitySystem($ability)
    {
        if (isset($this->enabledSpecialAbilities[$ability]))
        {
            return $this->getSystemById($this->enabledSpecialAbilities[$ability]);
        }

        return null;
    }

    public function getSpecialAbilityValue($ability, $args = null)
    {
        $system = $this->getSpecialAbilitySystem($ability);
        if ($system)
            return $system->getSpecialAbilityValue($args);

        return false;
    }

	/*Ship-level twin of ShipSystem::isRevealedToCurrentViewer(). True when the player this gamedata
	  load is being built for may see this ship's private state: the owner and their teammates.
	  Returns REVEALED when there is no viewer context (server-side turn processing, static ship
	  generation) - so only ever use this to mask OUTGOING JSON, never in game logic.*/
	public function isRevealedToCurrentViewer()
	{
		if (TacGamedata::$currentForPlayer === null) return true; //no viewer context
		if ($this->userid == TacGamedata::$currentForPlayer) return true; //owner
		if (TacGamedata::$currentForPlayerTeam !== null && $this->team == TacGamedata::$currentForPlayerTeam) return true; //teammate
		return false;
	}

	/*D11, Stage 8 - "Chameleon suites mask the arming status of weapons ... even after the deception
	  is revealed."

	  This is the one CSS effect that is a property of the SUITE rather than of the deception, so it
	  is keyed on "this ship has a live array", not on "this ship is disguised". It therefore covers
	  the two cases the Stage 3 mask cannot reach, both of which serve the ship's REAL payload:

	    - a Chameleon ship left on the default "None" - an ordinary ELINT hull that still jams its
	      arming readout, which is what makes "None" a real choice rather than a wasted slot;
	    - a ship whose deception has already broken. The enemy now knows what it is looking at and
	      still cannot tell which of its guns are hot.

	  Applied to the STRIPPED clones, never to the live systems: $strippedShip->systems came from
	  ShipSystem::stripForJson(), which hands back fresh stdClass objects, so nothing here can reach
	  the objects the server resolves firing against or the ones the owner's own payload is built
	  from. (Editing in place would be the same class of bug as mutating a FireOrder during the
	  Stage 6 remap.)

	  "Full charge" is max(loadingtime, normalload), NOT loadingtime - the property, not
	  getNormalLoad(), which boostable weapons override to loadingtime + maxBoostLevel. Both of those
	  are the same traps armChameleonSimulacrumWeapons() documents; the value has to match, or a
	  revealed ship and a disguised one would mask to visibly different numbers.

	  overloadturns is DROPPED rather than zeroed: absent already means "not overloading" to the
	  client, and a masked ship that is quietly charging an overload must not advertise it.*/
	private function maskChameleonArming($strippedShip)
	{
		if (TacGamedata::$chameleonDisclosed) return;   //D15: a finished game hides nothing
		if ($this->isRevealedToCurrentViewer()) return; //owner and teammates always see the truth
		if (!$this->hasChameleonSensors()) return;      //destroyed or offline array masks nothing

		$realById = array();
		foreach ($this->systems as $system){
			if ($system instanceof Weapon) $realById[$system->id] = $system;
		}
		if (empty($realById)) return;

		foreach ($strippedShip->systems as $sys){
			if (!isset($realById[$sys->id])) continue;
			if (!property_exists($sys, 'turnsloaded')) continue;
			$real = $realById[$sys->id];

			$sys->turnsloaded = max($real->getLoadingTime(), (int)$real->normalload);

			//The client DISCARDS the scalar whenever turnsloadedArray is present
			//(shipSystem.js:219), so a multi-mode weapon has to be masked key for key or the mask
			//is silently thrown away the moment the viewer looks at any mode. There is no
			//normalloadArray - the one normalload applies to every mode.
			if (!empty($real->loadingtimeArray)){
				$perMode = array();
				foreach ($real->loadingtimeArray as $mode => $loadingtime){
					$perMode[$mode] = max($loadingtime, (int)$real->normalload);
				}
				$sys->turnsloadedArray = $perMode;
			}

			unset($sys->overloadturns);
		}
	}

	/*Does this ship carry a LIVE Chameleon Sensor Suite?
	  Rides on the special-ability list, which getSpecialAbilityList() refuses to fill for a system that is
	  destroyed or offline - so a shot-out suite drops the ability (and with it the disguise) for free.
	  Depends on onConstructed() having run, like every other hasSpecialAbility() caller.*/
	public function hasChameleonSensors()
	{
		return $this->hasSpecialAbility("ChameleonSensors");
	}

	/*Is this ship actually projecting a simulacrum right now? A Chameleon ship left on the default "None"
	  disguise is an ordinary ELINT ship and must stay on the common path - hence the disguise-class test.*/
	public function isChameleonDisguised()
	{
		if (empty($this->chameleonDisguiseClass)) return false;
		return $this->hasChameleonSensors();
	}

	/*The Chameleon suite itself, or null. Uses the special-ability index rather than a system scan,
	  so it costs a hash lookup on the ships that have one and nothing at all on the ships that don't.*/
	public function getChameleonSensors()
	{
		if (!$this->hasChameleonSensors()) return null;
		return $this->getSpecialAbilitySystem("ChameleonSensors");
	}

	/*A pristine instance of the simulacrum's class, built once per load and cached on the ship.
	  Every plausibility threshold reads from THIS rather than from a live copy: the limits a player
	  plans around must not wander as the deception accumulates mirrored damage.
	  Returns null when there is no disguise or the stored class no longer resolves.*/
	public function getChameleonBlueprint()
	{
		if (empty($this->chameleonDisguiseClass)) return null;
		if ($this->chameleonBlueprint !== null) return $this->chameleonBlueprint;
		if (!class_exists($this->chameleonDisguiseClass)) return null;

		$cls = $this->chameleonDisguiseClass;
		$blueprint = new $cls(-1, $this->userid, '', $this->slot);
		foreach ($blueprint->systems as $system){
			$system->beforeTurn($blueprint, 0, 0);
		}
		$this->chameleonBlueprint = $blueprint;
		return $this->chameleonBlueprint;
	}

	/*The phantom sheet (D1): a LIVE ship of the simulacrum's class, hung off this one, which carries
	  its own damage so an enemy sees a hull that has plausibly been shot at. Built once per load,
	  by DBManager after enhancements have been applied - the disguise class comes FROM an
	  enhancement, so anything earlier reads null and silently builds nothing.

	  id = -realId (D2). That one choice is what makes the whole existing damage machinery work
	  unchanged: assignDamageReturnOverkill stamps $target->id into every DamageEntry and
	  submitDamages writes it positionally, so the phantom persists through tac_damage/tac_critical
	  with no schema change. Negative ids cannot collide with real ones and are self-describing in
	  the table. The two ordinary loaders skip them (they are not in $gamedata->ships, and both
	  loaders null-guard); the phantoms are filled by the gated loaders instead.

	  Returns null - meaning "no phantom, fall back to the pristine blueprint" - for a ship with no
	  disguise or an unresolvable one.*/
	public function buildChameleonPhantom()
	{
		if (empty($this->chameleonDisguiseClass)) return null;
		if ($this->chameleonPhantom !== null) return $this->chameleonPhantom;
		if (!class_exists($this->chameleonDisguiseClass)) return null;

		$cls = $this->chameleonDisguiseClass;
		//The real name/slot: the phantom IS this unit as far as the enemy is concerned, and
		//stripForJsonDisguised() masks the name afterwards exactly as it does for the blueprint.
		$phantom = new $cls(-(int)$this->id, $this->userid, $this->name, $this->slot);
		$phantom->team = $this->team;

		/*⚠️ THE PHANTOM MUST CARRY THE REAL SHIP'S MOVEMENT, and it is not optional.
		  Every geometric question a ship is asked reads $this->movement: getFacingAngle(),
		  getCoPos(), getBearingOnUnit(), Movement::isRolled(). getFacingAngle() returns 0 for an
		  empty movement list rather than failing, so a phantom without this resolves every incoming
		  shot as though it were facing due north at the origin - and quietly picks a plausible but
		  WRONG hit section. That is how a laser that hit the real hull's starboard side landed on
		  the simulacrum's aft (found in playtest, game 4273).
		  The phantom occupies the same hex, facing the same way, by definition: it IS this ship as
		  far as the enemy is concerned, and position and facing are the two things a disguise can
		  never hide. Assigned after the movement load (getMovesForShips runs long before
		  getChameleonPhantoms) and never written back - phantoms are not in $gamedata->ships, so no
		  movement persistence sweep can see this.*/
		$phantom->movement = $this->movement;
		$phantom->rolled   = $this->rolled;
		$phantom->rolling  = $this->rolling;
		//Lets an allocation pass tell which sheet it is on without inspecting the negative id.
		//Weapon::damageOneSheet needs it: a Flash weapon's collateral splash hits the REAL ships
		//sharing the hex, so the mirrored pass must not deal it a second time.
		$phantom->chameleonIsPhantom = true;

		$this->chameleonPhantom = $phantom;
		return $this->chameleonPhantom;
	}

	/*A phantom is not a working sheet until its systems have been constructed the way a real ship's
	  are. ShipSystem::onConstructed() is what links each system to its Structure block, applies
	  criticals, and latches $destroyed off the damage list - and $destroyed is the only thing
	  stripForJson() sends, so without this a phantom system can absorb a fatal damage entry and
	  still be served to the enemy as intact.

	  Called by DBManager AFTER the phantom's criticals and damage are loaded, which is exactly the
	  order real ships get them (getTacShips loads both before $gamedata->onConstructed()).

	  Deliberately NOT $phantom->onConstructed(): that also runs Enhancements::setEnhancements - the
	  phantom has no enhancements of its own and must never inherit this ship's, since the whole
	  point is that it is a different, plainer vessel - and recomputes initiative, which is patched
	  from the real ship anyway (D13). The special-ability merge is kept because system stripForJson
	  paths consult it.*/
	public function finaliseChameleonPhantom($turn, $phase)
	{
		if ($this->chameleonPhantom === null) return;

		$phantom = $this->chameleonPhantom;
		foreach ($phantom->systems as $system){
			$system->onConstructed($phantom, $turn, $phase);
			$abilities = $system->getSpecialAbilityList($phantom->enabledSpecialAbilities);
			if (is_array($abilities)) {
				$phantom->enabledSpecialAbilities = array_merge($phantom->enabledSpecialAbilities, $abilities);
			}
		}
	}

	/*The sheet an enemy reads: the phantom once one has been built, otherwise the pristine blueprint.
	  One accessor so every masking site agrees about which object is "the simulacrum" - the fallback
	  matters on any load that never reached DBManager::getChameleonPhantoms (POST-side rebuilds),
	  where serving a pristine hull is safe and serving nothing is not.*/
	public function getChameleonSheet()
	{
		if ($this->chameleonPhantom !== null) return $this->chameleonPhantom;
		return $this->getChameleonBlueprint();
	}

	/*D7 (Stage 6) - the real weapon -> simulacrum weapon map, built once per load.

	  Why it has to exist: an enemy is served the SIMULACRUM's systems, so a fire order carrying a
	  real Dargan weapon id names a system that does not exist on the sheet they hold. The combat log
	  resolves it with shipManager.systems.getSystem(ship, fire.weaponid) (combatLog.js:90) and then
	  dereferences the result immediately, so an unmapped id is not a cosmetic gap - it is a
	  TypeError that kills the whole log, exactly like the negative-shipid bug Stage 5 hit.

	  Matching is GREEDY and INJECTIVE, in four descending tiers:
	    1. same weapon class, arc covers the real weapon's arc
	    2. same weapon class, any arc
	    3. any unused weapon in the same section/location
	    4. any weapon at all (the last tier may reuse a mount)
	  Injective through tiers 1-3 because two real guns firing must look like two simulacrum guns
	  firing; tier 4 gives up on that rather than emit an id the client cannot resolve, and a ship
	  reduced to tier 4 has already revealed itself under D6 anyway.

	  Tiers 1 and 2 are the ones that matter: a same-class match is what makes the shot plausible
	  (D6) and what lets the simulacrum weapon mirror the real one's recharge, which is only sound
	  BECAUSE the classes match and their loading curves are therefore identical.

	  Keyed and valued by system id, not by object, so it survives the clone that stripForJson makes.*/
	public function getChameleonWeaponMap()
	{
		if ($this->chameleonWeaponMap !== null) return $this->chameleonWeaponMap;

		$map = array();
		$sheet = $this->getChameleonSheet();
		if ($sheet === null){
			$this->chameleonWeaponMap = $map;
			return $map;
		}

		$fakeWeapons = array();
		foreach ($sheet->systems as $system){
			if ($system instanceof Weapon) $fakeWeapons[$system->id] = $system;
		}
		if (empty($fakeWeapons)){
			$this->chameleonWeaponMap = $map;
			return $map;
		}

		$realWeapons = array();
		foreach ($this->systems as $system){
			if ($system instanceof Weapon) $realWeapons[$system->id] = $system;
		}

		$taken = array();
		//Tiers 1-3 in order, each a full pass over the still-unmapped real weapons, so a weapon that
		//can be matched exactly is never consumed by a looser tier that ran first.
		foreach (array(1, 2, 3) as $tier){
			foreach ($realWeapons as $realId => $real){
				if (isset($map[$realId])) continue;
				foreach ($fakeWeapons as $fakeId => $fake){
					if (isset($taken[$fakeId])) continue;
					if (!$this->chameleonWeaponTierMatches($tier, $real, $fake)) continue;
					$map[$realId]   = $fakeId;
					$taken[$fakeId] = true;
					break;
				}
			}
		}

		//Tier 4: something renderable, even if it doubles up.
		$fallback = null;
		foreach ($fakeWeapons as $fakeId => $fake){ $fallback = $fakeId; break; }
		foreach ($realWeapons as $realId => $real){
			if (!isset($map[$realId])) $map[$realId] = $fallback;
		}

		$this->chameleonWeaponMap = $map;
		return $map;
	}

	private function chameleonWeaponTierMatches($tier, $real, $fake)
	{
		if ($tier == 3) return ($real->location === $fake->location);
		if (get_class($real) !== get_class($fake)) return false;
		if ($tier == 2) return true;
		//Tier 1: the simulacrum mount must cover the directions the real mount covers. Compared as
		//arc bounds rather than against a shot, because this map is built per LOAD and has no shot
		//to reason about - the per-shot arc test that drives the reveal lives in ChameleonSensors.
		return ($this->chameleonArcCovers($fake, $real->startArc, $real->endArc));
	}

	/*Does $fake's firing arc contain the whole of the arc [$start,$end]? Sampled every 30 degrees
	  rather than solved, because arcs wrap past 360 and a handful of mounts carry SPLIT arcs (Vree
	  turrets) that isInAnyArc already understands - reusing it keeps one definition of "in arc" in
	  the codebase instead of a second, subtly different one here.*/
	public function chameleonArcCovers($fake, $start, $end)
	{
		$startArcs = ($fake->splitArcs && !empty($fake->startArcArray)) ? $fake->startArcArray : array();
		$endArcs   = ($fake->splitArcs && !empty($fake->endArcArray))   ? $fake->endArcArray   : array();

		$span = (int)$end - (int)$start;
		while ($span < 0) $span += 360;
		if ($span == 0) $span = 360; //a 0..0 / 0..360 mount is all-round

		for ($offset = 0; $offset <= $span; $offset += 30){
			$bearing = ((int)$start + $offset) % 360;
			if (!mathlib::isInAnyArc($bearing, $fake->startArc, $fake->endArc, $startArcs, $endArcs)) return false;
		}
		return true;
	}

	/*D4 - "resolve the fire using the simulated ship's defense ratings." Returns the SIMULACRUM's
	  defence profile against a shot from $shooter, or null when this ship is not disguised from
	  them - which is every shot in almost every game, and the only cost that path pays.

	  The BEARING stays the real ship's: which way a hull points is observable, and the phantom has
	  no movement of its own to derive one from. Only the profile VALUE comes off the fake sheet.
	  doGetHitSectionBearing() is what makes that split possible - it takes a bearing and needs no
	  position at all.

	  Without this the server resolves against the real profile while the enemy's client previews
	  against the fake one (finding #10), so predicted and actual hit chance disagree on every shot
	  at a disguised ship - the one thing a player is guaranteed to notice.

	  $launchPos is the ballistic firing hex; direct fire passes null and bears on the shooter.*/
	public function getDisguisedProfileFor($shooter, $launchPos = null)
	{
		if (!TacGamedata::$chameleonPresent) return null;
		if ($shooter === null) return null;
		if (!$this->isChameleonDisguisedFrom($shooter->team)) return null;

		$sheet = $this->getChameleonSheet();
		if ($sheet === null) return null;

		$relativeBearing = ($launchPos !== null)
			? $this->getBearingOnPos($launchPos)
			: $this->getBearingOnUnit($shooter);

		$loc = $sheet->doGetHitSectionBearing($relativeBearing);
		if (!is_array($loc) || !isset($loc["profile"])) return null;
		return $loc["profile"];
	}

	/*D4, extended to DEW. Returns the defensive EW the SIMULACRUM appears to be running against a
	  shooter who still believes the deception, or null when this ship is not disguised from them.

	  Why this is needed at all: the client never reads the stored DEW entry. ew.getDefensiveEW() is
	  an alias for getEWLeft() (ew.js:86/252), which is "sensor capacity MINUS everything spent on
	  something else" - and the capacity an enemy sees belongs to the simulacrum. For an undisguised
	  ship the two agree, because a player always allocates their whole suite; for a disguised one
	  they cannot. Measured in playtest: a Dargan running OEW 8 + DEW 2 (10 of 10) inside a 9-EW
	  Demos displayed DEW 1 to the enemy while the server resolved against 2, and the hit chance
	  disagreed 103% vs 99% on every shot.

	  This mirrors getEWLeft() term for term: the simulacrum's scanner output, minus the REAL non-DEW
	  allocations (which the enemy receives unmasked and the client counts the same way). The boost
	  term getEWLeft() adds for EW-boosted systems is zero on both sides, since the enemy's copy of
	  this ship carries the simulacrum's systems with no power allocated to them.

	  Consequence worth knowing: disguising as a smaller-sensor hull genuinely costs defensive EW,
	  and a larger-sensor hull genuinely gains it. That is the same bargain D4 already strikes on the
	  defence profile - the enemy resolves against the ship they believe they are shooting at.*/
	public function getDisguisedDEWFor($shooter, $turn)
	{
		if (!TacGamedata::$chameleonPresent) return null;
		if ($shooter === null) return null;
		if (!$this->isChameleonDisguisedFrom($shooter->team)) return null;

		$sheet = $this->getChameleonSheet();
		if ($sheet === null) return null;

		$ceiling = EW::getScannerOutput($sheet, $turn);
		$spent   = $this->getAllEWExceptDEW($turn);
		return max(0, $ceiling - $spent);
	}

	/*D9 - the system a called shot NAMES, resolved on the sheet the shooter was actually looking at.

	  For an ordinary shot that is this ship's own system. For a shot called at a Chameleon
	  simulacrum it is the SIMULACRUM's, because Firing::withdrawChameleonCalledShots() has already
	  cleared calledid off the real hull (the call does not translate - it fails, and the hit rolls
	  on the ordinary chart for the bearing) while keeping the declared id on the order as
	  $chameleonCalledId.

	  The distinction matters because the to-hit maths must still see a called shot: the shooter
	  declared one, paid its penalty, and their own client previewed the number against this very
	  system on the false sheet. Resolving it here rather than on the real hull is also the only
	  correct reading - the real ship may mount nothing of the kind.

	  Returns null when the order is not a called shot at all.*/
	public function getCalledSystemAsAimed($fireOrder)
	{
		if ($fireOrder->calledid != -1) return $this->getSystemById($fireOrder->calledid);
		if ($fireOrder->chameleonCalledId === null) return null; //a genuinely uncalled shot

		$sheet = $this->getChameleonSheet();
		if ($sheet === null) return null;
		return $sheet->getSystemById($fireOrder->chameleonCalledId);
	}

	/*D3a - divergent destruction. The two sheets have different armour and structure totals, so the
	  phantom can die before the real ship does. A wreck that keeps flying, manoeuvring and shooting
	  is a worse tell than the truth, so the deception ends there and then.

	  The fatal entries are CLAMPED rather than left standing: the phantom must survive as a
	  coherent sheet for the rest of this turn's resolution (later shots in the same volley still
	  allocate against it), and the enemy learns the truth from the reveal instead. Undoing the
	  destroyed flag is safe because a phantom rolls no criticals (D3c), so its state is a pure
	  function of its damage list.

	  The converse - the real ship dying first - is moot: destruction ends the deception anyway.*/
	public function checkChameleonDivergentDestruction($gamedata)
	{
		if ($this->chameleonPhantom === null) return;
		if ($this->isDestroyed()) return;                    //real ship dead: nothing left to protect
		if (!$this->chameleonPhantom->isDestroyed()) return;

		$clamped = false;
		foreach ($this->chameleonPhantom->systems as $system){
			foreach ($system->damage as $entry){
				if (!$entry->destroyed) continue;
				if ($entry->turn != $gamedata->turn) continue; //only this turn's kill is ours to undo
				$entry->destroyed = false;
				$entry->updated = true;
				$clamped = true;
			}
			$system->destroyed = $system->isDestroyed();
		}
		if (!$clamped) return;

		$css = $this->getChameleonSensors();
		if ($css instanceof ChameleonSensors){
			$css->revealOnDivergentDestruction($gamedata);
		}
	}

	/*Reveal checkpoint, called from the Deployment and Movement advances. Gated twice over: the
	  per-load TacGamedata::$chameleonPresent boolean means a game with no disguised ship never gets
	  here at all, and isChameleonDisguised() means a Chameleon ship left on "None" doesn't either.*/
	public function checkChameleonReveal($gamedata, $checkpoint = 'movement')
	{
		if (empty($this->chameleonDisguiseClass)) return;
		//NOT isChameleonDisguised(): a destroyed or offlined array drops the ChameleonSensors special
		//ability, and that is exactly the case the shutdown check exists to record.
		$css = $this->getSystemByName("chameleonSensors");
		if ($css instanceof ChameleonSensors) $css->checkChameleonReveal($gamedata, $checkpoint);
	}

	/*Is this ship's true identity still hidden from $team? The single question every masking site
	  asks, so there is one place where all the ways a deception can end are accounted for.*/
	public function isChameleonDisguisedFrom($team)
	{
		if (!$this->isChameleonDisguised()) return false;
		//Own team always sees the truth - they have to, they are flying alongside it. This belongs
		//here rather than at each call site precisely because this is the one question every masking
		//site asks; a site that forgot the check would show a player their own ally as a stranger.
		if ($team !== null && (int)$team === (int)$this->team) return false;
		$css = $this->getChameleonSensors();
		if ($css === null) return false;
		return $css->isDisguisedFrom($team, TacGamedata::$currentTurn);
	}

    public function checkStealth($gamedata)
    {        
        //Check Torvalus at start of Initial Orders and end of Movement 
        if($this->faction == "Torvalus Speculators"){
            $shadingField = $this->getSystemByName("ShadingField");
            if($shadingField) $shadingField->checkStealthNextPhase($gamedata);
        }

        //Check Hyach subs at end of movement
        if($this->faction == "Hyach Gerontocracy" && $gamedata->phase == 2){
            $stealth = $this->getSystemByName("Stealth");
            if($stealth) $stealth->isDetectedMovement($this, $gamedata);
        }


        //Check Trek at start of Initial Orders and end of Movement 
        if($this->hasSpecialAbility("Cloaking")){           
            $cloakingDevice = $this->getSystemByName("CloakingDevice");
            if($cloakingDevice) $cloakingDevice->checkStealthNextPhase($gamedata);
        } 

        return;
    }   
    
    public function hasPreFireWeaponsReady($gamedata)
    {         
        $readyToFire = false;
        foreach($this->systems as $system){
            if($system instanceof Weapon){
                //A multi-mode weapon (e.g. Gravitic Augmenter) leaves its scalar autoFireOnly at the
                //default-mode value here, because no fire order has been submitted yet to trigger a
                //changeFiringMode(). If ANY mode in autoFireOnlyArray is player-fireable, the weapon can
                //still legitimately want the pre-fire phase, so treat it as manually-fireable in that case.
                $manuallyFireable = !$system->autoFireOnly
                    || (!empty($system->autoFireOnlyArray) && in_array(false, $system->autoFireOnlyArray, true));
                if($system->preFires && ($system->turnsloaded >= $system->loadingtime) && $manuallyFireable && !$system->stowed){ //ready to fire!
                    //Separate check for Prox Mines here.              
                    if($system instanceof ProximityMine){
                        if($this->commandControl && !$system->checkForPreFiringTargets($this, $gamedata)) continue; //No targets in range, don't trigger preFire phase.
                    }                
                    $readyToFire = true;
                    break; //At least one weapon can pre fire, exit loop.
                }    
            }
        }
        return $readyToFire;
    }        

    public function isElint()
    {
        //return $this->getSpecialAbilityValue("ELINT");
		return $this->hasSpecialAbility("ELINT");
    }

    protected function addSystem($system, $loc){
        $i = sizeof($this->systems);
        $system->setId($i);
        $system->location = $loc;
        $system->setUnit($this);
								 
		//LCV Rails: flag the ship as an LCV carrier the moment a DockingCollar is
		//mounted, so the per-turn LCV checks can short-circuit (see $LCVCarrier).
		if (!empty($system->isLCVRail)) $this->LCVCarrier = true;

		$this->systems[$i] = $system;


		if ($system instanceof Structure){
			$this->structures[$loc] = $system->id;
		} else if ($this->systemsSurviveStructureLoss){
			//Outer-structure-ring hull (Vree saucers): a breached block does not take its
			//systems with it. See $systemsSurviveStructureLoss above. Stamped here, at
			//construction, so it reaches the static bundle and the catalogue for free.
			$system->setSurvivesStructureDestruction(true);
		}

		//Structure arc indicator (STRUCTURE_ARCS_PLAN.md): Structures fall THROUGH to the same
		//auto-arc fill as every other system (they used to be excluded, which is why a normal
		//structure carried a null arc), so each section's Structure knows the facing range
		//getLocations() uses to allocate hits there. Display only - hovering a section's health
		//bar in the ship window draws that wedge on the icon (ShipIcon.showStructureArc), and the
		//value rides the STATIC ship bundle, so gamedata is untouched. Combat never reads a
		//structure's arc: "Structure" hit-chart entries resolve by location (getStructureSystem),
		//the protection paths drop structures before the arc gate (doesProtectFromDamage < 1), and
		//no hit chart uses TAG:Structure - the one arc-based structure selector. Hand-authored
		//structure arcs (Vree/Centauri, Structure::createAsOuter) are non-zero, so the gate below
		//leaves them exactly as written.
		if(($system->startArc ==0)&&($system->endArc ==0) && !($system instanceof Bulkhead)){ //20.01.2025 - add arc equal to section arc, if not set explicitly. Bulkheads protect by location only - giving them a section arc makes the arc gate in getSystemProtectingFromDamage falsely filter them out for shots from outside that arc.
			//if arc is not set - copy from location!
			//systems belonging to ANOTHER section's structure block (structureHomeLocation -
			//Kirishiac orbitals displayed on the L/R sections) take their HOME block's arc:
			//they can only be hit from the same directions as their associated structure
			$arcLoc = $system->getStructureLocation();
			if($arcLoc==0){ //PRIMARY
				$system->startArc = 0;
				$system->endArc = 360;
			} else {
				$locations = $this->getLocations();
				foreach($locations as $line) if ($line["loc"]==$arcLoc){
					if( ($system->startArc == 0) && ($system->endArc == 0) ){ //for initial values - accept anything
						$system->startArc = $line["min"];
						$system->endArc = $line["max"];
					} else if ($system->endArc == $line["min"]) { //accept end arc extension
						$system->endArc = $line["max"];
					} else if ($system->startArc == $line["max"]) { //accept start arc extension
						$system->startArc = $line["min"];
					}
				}
			}
		}
	}
        
        protected function addFrontSystem($system){
            $this->addSystem($system, 1);
        }
        protected function addAftSystem($system){
            $this->addSystem($system, 2);
        }
        protected function addPrimarySystem($system){
		//if system is Structure - first add Ramming Attack! assume we're nearing the end...
	   if($system instanceof Structure){
		//check whether ramming attack already exists (do not add another)
		$rammingExists = false;
		foreach($this->systems as $sys)  if ($sys instanceof RammingAttack){
			$rammingExists = true;
		}
		if(!$rammingExists){
			//add ramming attack
			//if((!($this instanceof FighterFlight)) && (!($this->osat)) && (!$this->base) && (!$this->smallBase) ){
			if(!($this instanceof FighterFlight)){
				$newRamming = new RammingAttack(0, 0, 360, 0, 0);
				//add Ramming to immobile objects too!
				if(($this->osat) || ($this->base) || ($this->smallBase)) {
					$newRamming->autoFireOnly = true; //do not allow manual attack!
				}
				$this->addPrimarySystem($newRamming);
			}
		}

			//$this->notesFill(); //add miscellanous info to notes! //Moved to strpForJson after cache changes - DK DEc 2025
	   }//endof adding PRIMARY Structure (with specials attached)
	   
            $this->addSystem($system, 0);
		}//endof addPrimarySystem

        protected function addLeftSystem($system){
            $this->addSystem($system, 3);
        }
        protected function addRightSystem($system){
            $this->addSystem($system, 4);
        }

		/*The single legitimate way to mount a system AFTER the hull's constructor has run - used
		  only by Enhancements::addEnhancementSystems (Extra Tendrils), which is documented there.
		  A named public method rather than making addSystem() public, so this exception stays
		  greppable and obvious: everything else must build its systems in the constructor.

		  Safe only because addSystem() APPENDS - ids are array indices, so every system already on
		  the hull keeps the id its stored damage, power and fire orders refer to.*/
		public function addEnhancementSystem($system, $loc){
			$this->addSystem($system, $loc);
		}

		/* fill notes with information contained in various attributes, not so readily accessible to player*/
		public function notesFill($sampleFighter = null){
			//if (TacGamedata::$currentTurn >= 1){ //in later turns notes will be displayed from pre-compiled cache! no point generating them every time
			//	return;
			//}
            if ($sampleFighter === null && $this instanceof FighterFlight) {
                $sampleFighter = $this->getSampleFighter();
            }
			//add to Notes information about miscellanous attributes
			if($this->notes!='')$this->notes .= '<br>';
				//faction age - if older than Young
				switch($this->factionAge){
				case 2:
					$this->notes .= 'Middleborn ';
					break;
				case 3:
					$this->notes .= 'Ancient ';
					break;
				case 4:
					$this->notes .= 'Primordial ';
					break;
			}
			//unit size
			switch($this->shipSizeClass){
				case 0: //fighters
				case -1:
					if($this->osat){				
						$this->notes .= 'MicroSAT';
					} else if(($this instanceof SuperHeavyFighter) || ($this->superheavy)){
						$this->notes .= 'Superheavy Fighter';
					}else{
						$this->notes .= 'Fighter';
					}
					break;			
				case 1: //MCV/LCV
					if($this->osat){	
                        if($this instanceof Mine){
                            if($this->spawned !== -1){
						        $this->notes .= 'Mine (Spawned)';
                            }else{
						        $this->notes .= 'Mine';
                            }    
                        }else{			
						    $this->notes .= 'OSAT';
                        }
					}else if($this instanceof LCV){
						$this->notes .= 'Light Ship';
					}else{
						$this->notes .= 'Medium Ship';
					}
					break;				  
				case 2: //HCV
					$this->notes .= 'Heavy Ship';
					break;       
				case 3: //Capital/Enormous
					if($this->Enormous){
						$this->notes .= 'Enormous ';
					}else{
						$this->notes .= 'Capital ';
					}
					if($this->base){
						if ($this->nonRotating) $this->notes .= 'non-rotating ';
						$this->notes .= 'Base';
					}else{
						$this->notes .= 'Ship';
					}
					break;
                case 5: //Terrain
                        if($this->Enormous) $this->notes .= 'Enormous Terrain';
                        break;                        
				default: //should not happen!
					$this->notes .= 'Unit size not identified!';	
					break;
			}//unit size described, which also guarantees existence of previous entries!
			//mark if not a combat unit!
			if(!$this->isCombatUnit) $this->notes .= '<br>Non-combatant!';
			//required hangar
			if($this->hangarRequired!='') { 
                $this->notes .= '<br>Requires hangar space: ' . ucfirst(strtolower($this->hangarRequired));		
				if($this->unitSize!=1){
                    $slotSize = 1 / $this->unitSize;
                    if($this->unitSize < 1){
                        $this->notes .= ' (Requires ' . $slotSize . ' hangar slots)';
                    }else{
                        $this->notes .= ' (' . $slotSize . ' per slot)';                        
                    }    
                } 
			}
			//Agile status
			if($this->agile) $this->notes .= '<br>Agile';	    
			//Gravitic Drive
			if($this->gravitic) $this->notes .= '<br>Gravitic Drive';	
			//Minesweeper
			if($this->minesweeperbonus > 0) $this->notes .= '<br>Minesweeper: ' . $this->minesweeperbonus;	
			if($this instanceof Mine && $this->signature > 0) $this->notes .= '<br>Signature: ' . $this->signature;	//Add signature value for mines here as well
			if($this instanceof Mine && $this->mineType == 'DEW') $this->notes .= '<br>Detected Signature: ' . $this->detectedSignature;	//Add signature value for mines here as well 

			//Advanced Armor
			if($this->advancedArmor) $this->notes .= '<br>Advanced Armor';
			if($this->hardAdvancedArmor) $this->notes .= '<br>Hardened Advanced Armor';   // GTS Hardened advanced armor
			//Improved/Advanced Sensors
			/*hasSpecialAbility relies on data created in system->onConstructed, so not available here. Need to manually look for Sensors...
			if($this->hasSpecialAbility("ImprovedSensors")) $this->notes .= '<br>Improved Sensors';
			if($this->hasSpecialAbility("AdvancedSensors")) $this->notes .= '<br>Advanced Sensors';
			*/
			$totalMod = $this->critRollMod;
			if($this instanceof FighterFlight){		//another variable with the same meaning exists for fighters, too! Both are used
				$totalMod += $this->dropOutBonus;
			}
			if($totalMod != 0){
				$plus = '';				
				if($totalMod > 0) $plus = '+';
				if($this instanceof FighterFlight){					
					$this->notes .= '<br>Dropout roll modifier: ' . $plus . $totalMod;
				}else{
					$this->notes .= '<br>Critical roll modifier: ' . $plus . $totalMod;
				}
			}
			if(!($this instanceof FighterFlight)){
				foreach($this->systems as $engine) if ($engine instanceof Engine){
					foreach($engine->specialAbilities as $ability){
						if ($ability=='EngineFlux'){
							$this->notes .= '<br>Engine Fluctuations';
						}
					}
					break; //checking one Engine is enough
				}
				foreach($this->systems as $sensor) if ($sensor instanceof Scanner){
					
					if ($sensor instanceof ElintScanner) {
						$this->notes .= '<br>ELINT Sensors';
					}				
					
					foreach($sensor->specialAbilities as $ability){
						if ($ability=='AdvancedSensors'){
							$this->notes .= '<br>Advanced Sensors';
						}else if ($ability=='ImprovedSensors'){
							$this->notes .= '<br>Improved Sensors';
						}else if ($ability=='StarWarsSensors'){
							$this->notes .= '<br>Star Wars Sensors';
						}else if ($ability=='LCVSensors'){ 
							$this->notes .= '<br>LCV Sensors';
						}else if ($ability=='SensorFlux'){ 
							$this->notes .= '<br>Sensor Fluctuations';
						}else if ($ability=='ConstrainedEW'){ 
							$this->notes .= '<br>Constrained ELINT';
						}
					}
					break; //checking one Scanner is enough
				}				
				foreach($this->systems as $reactor) if ($reactor instanceof Reactor){
					foreach($reactor->specialAbilities as $ability){
						if ($ability=='ReactorFlux'){
							$this->notes .= '<br>Power Fluctuations';
						}
					}if ($reactor instanceof MagGravReactor && !$this->isTerrain() && !$this->mine) {
						$this->notes .= '<br>Mag-Gravitic Reactor';
					}
					break; //checking one Reactor is enough
				}
			}

			//fighter-specific
			if($this instanceof FighterFlight){
				if($this->hasNavigator) $this->notes .= '<br>Navigator'; //Navigator		
				if($sampleFighter !== null){
					foreach($sampleFighter->systems as $ftrSys){
						foreach($ftrSys->specialAbilities as $ability){
							if ($ability=='AdvancedSensors'){
								$this->notes .= '<br>Advanced Sensors';
							}else if ($ability=='ImprovedSensors'){
								$this->notes .= '<br>Improved Sensors';
							}
						}
					}
				}
			}
			
		}//endof function notesFill
		
        
        public function addDamageEntry($damage){        
            $system = $this->getSystemById($damage->systemid);
            $system->damage[] = $damage;        
        }
        
        public function getLastTurnMoved(){
            $turn = 0;
            foreach($this->movement as $elementKey => $move) {
                if (!$move->preturn && $move->type != "deploy")
                    $turn = $move->turn;
            } 
            
            return $turn;
        }
        
        public function getMovementById($id){
			foreach ($this->movement as $move){
				if ($move->id === $id)
					return $move;
			}
			
			return null;
		}
        
        public function getLastMovement(){
            $m = null;

            if (!is_array($this->movement) || empty($this->movement))
                return null;

            foreach($this->movement as $elementKey => $move) {
                $m = $move;
            }

            return $m;
        }

        /* AutomatedMovement seam: describes how this unit should move when under server
         * (non-player) control this turn. Default is straight-line 'drift'. Units with
         * richer automated behaviour (future CPU ships; eventually Strategy-A seeking
         * HKs) override this to return e.g. ['type'=>'seek','targetSize'=>..,'jink'=>2].
         * Only consulted when AutomatedMovement::isUnderAutomatedControl() is true. */
        public function getAutomatedMovementIntent($gamedata){
            return array('type' => 'drift', 'jink' => 0);
        }
        
        public function getSpeed(){
            $m = $this->getLastMovement();
            if ($m == null)
                return 0;
                
            return $m->speed;
        }
        
        public function unanimatePreturnMovements($turn){
            foreach($this->movement as $elementKey => $move) {
                if ($move->turn == $turn && $move->type != "start" && $move->preturn){
                    if ($move->type == "pivotright" || $move->type == "pivotleft"){
                        $move->animated = false;
                    }
                }
            } 
        }
        
        public function unanimateMovements($turn){
        
            if (!is_array($this->movement))
                return;
            
            foreach($this->movement as $elementKey => $move) {
                if ($move->turn == $turn && $move->type != "start" && !$move->preturn){
                    if ($move->type == "move" || $move->type == "turnleft" || $move->type == "turnright" || $move->type == "slipright" || $move->type == "slipleft" || $move->type == "pivotright" || $move->type == "pivotleft"){
                        $move->animated = false;
                    }
                }
            } 
        }
        
        public function getSystemById($id){
            if (isset($this->systems[$id])){
                return $this->systems[$id];
            }
            else{/* no longer needed, duo/dual weapon is obsolete
                foreach($this->systems as $system){
					
                    if($system instanceof Weapon && ($system->duoWeapon || $system->dualWeapon)){
                        foreach($system->weapons as $weapon){
                            if($weapon->id == $id){
                                return $weapon;
                            }else{
                                if($weapon->duoWeapon){
                                    foreach($weapon->weapons as $subweapon){
                                        if($subweapon->id == $id){
                                            return $subweapon;
                                        }
                                    }
                                }
                            }
                        }
                    }					
                }
				*/
            }
            
			return null;
		}

	//by CLASS name
    public function getSystemByName($name){        
        foreach ($this->systems as $system){
            if ($system instanceof $name){
                return $system;
            }
        }

        return null;
    }

	//get systems by display name
	//15.09.2023 - bearing added, needed to get system by tag
    public function getSystemsByNameLoc($name, $location, $bearing, $acceptDestroyed = false){ /*get list of required systems on a particular location*/
        /*name may indicate different location?...*/
        /*'destroyed' means either destroyed as of PREVIOUS turn, OR reduced to health 0*/
        $location_different_array = explode (':' , $name);
        if(sizeof($location_different_array)==2){ //indicated different section: exactly 2 items - first location, then name
			$actualLocation = $location_different_array[0];
			$actualSystem = $location_different_array[1];
			if ($actualLocation == 'TAG'){ //search by tag and direction of impact, disregarding sections
				return $this->getSystemsByTag($actualSystem, $bearing, $acceptDestroyed);
			}else{ //standard search, just with redirected section
				return $this->getSystemsByNameLoc($actualSystem, $actualLocation, $bearing, $acceptDestroyed);
			}
        }else{
            $returnTab = array();
            if($name=='Structure'){ //Structure is special, as it might actually belong to a different section! (on MCVs)
                $system = $this->getStructureSystem($location);
                if( ($acceptDestroyed == true) || (!$system->isDestroyed()) ){
                    $returnTab[] = $system;
                }
            }else{
                foreach ($this->systems as $system){
			//change to case ignoring:
                    //if ( ($system->displayName == $name) && ($system->location == $location) ){
		    if ( ( (STRCASECMP($system->displayName, $name)==0) || (($system->hitChartName !== null) && (STRCASECMP($system->hitChartName, $name)==0)) ) && ($system->location == $location) ){
                        if( ($acceptDestroyed == true) || (!$system->isDestroyed()) ){
                            $returnTab[] = $system;
                        }
                    }
                }
            }
            return $returnTab;
        }
        return array(); //should never reach here
    } //end of function getSystemsByNameLoc



	//get systems by tag - anywhere on a ship, BUT only ones with arc covering indicated direction of impact
	//if there are differences - prioritize systems with lowest repair priority!
    public function getSystemsByTag($tag, $bearing, $acceptDestroyed = false){ /*get list of required systems on a particular location*/
        /*'destroyed' means either destroyed as of PREVIOUS turn, OR reduced to health 0*/
		$minUndestroyedPriority = 99; //lowest priority of undestroyed system found
		$undestroyedExists = false; //does an undestroyed system actually exist?
		$searchName = strtoupper($tag);
		
		$returnTab = array();
		
		foreach ($this->systems as $currSystem){
			$displayName = strtoupper( $currSystem->displayName );
			if(
				$currSystem->repairPriority <= $minUndestroyedPriority //priority fits
				and ( ($displayName == $searchName) || $currSystem->checkTag($searchName) ) //tag fits - either directly or to system name
				and mathlib::isInArc($bearing, $currSystem->startArc, $currSystem->endArc) //arc fits
			){
				//tag fits and arc fits - is it destroyed?
				$isDestroyed = $currSystem->isDestroyed();
				//...but treat health 0 as destroyed here, too!
				if(!$isDestroyed){
					$remHealth = $currSystem->getRemainingHealth();
					if($remHealth == 0) $isDestroyed = true;
				}
				if( (!$isDestroyed) || ($acceptDestroyed) ){ //either not destroyed, or destroyed systems are accepted
					if( (!$isDestroyed) && ($currSystem->repairPriority < $minUndestroyedPriority) ){ //is not destroyed and of lower repair priority than current best fit - clear earlier findings, new one should be prioritized!
						$returnTab = array();
						$minUndestroyedPriority = $currSystem->repairPriority;
					}
					$returnTab[] = $currSystem;
				}				
			}
		}
		
		return $returnTab;			
    } //end of function getSystemsByTag


    public function getSystemsByName($name, $acceptDestroyed = false){ /*get list of required systems anywhere on a ship*/
        /*'destroyed' means either destroyed as of PREVIOUS turn, OR reduced to health 0*/
        $returnTab = array();
        foreach ($this->systems as $system){
            //if ( ($system->displayName == $name) ){
            if ( (STRCASECMP($system->displayName, $name)==0 ) ){		
                if( ($acceptDestroyed == true) || (!$system->isDestroyed()) ){
                    $returnTab[] = $system;
                }
            }
        }
        return $returnTab;
    } //end of function getSystemsByName


	//defensive system that can affect damage dealing - only one (best) such system will be called
	//call overridden by FighterFlight to get only systems on a fighter actually hit
	public function getSystemProtectingFromDamage($shooter, $pos, $turn, $weapon, $systemhit, $expectedDmg, $damageWasDealt = false, $isUnderShield = false){ //$systemhit actually used by fighter flight
		$chosenSystem = null;
		$chosenValue=0;
		if($this instanceOf FighterFlight){ //only subsystems of a particular fighter
			//a flight keeps its defensive systems on the individual craft, so without a craft there is no
			//list to search - callers that ask before a craft has been picked simply get no protector
			//(rather than a fatal on $systemhit->systems)
			if($systemhit === null) return null;
			$listOfPotentialSystems = $systemhit->systems;
		}else{ //all systems of a ship
			$listOfPotentialSystems = $this->systems;
		}

		$shots = 1;
		if($weapon && $weapon->isLinked) $shots = $weapon->shots;
        if($weapon && $weapon->damagesUnderShield() && !$isUnderShield) $isUnderShield = true;  //Some weapon weapons might bypass shield-type protections, so only things like Diffsuers and Bulkheads would apply.

        //foreach($this->systems as $system){
		foreach($listOfPotentialSystems as $system){

			$value=$system->doesProtectFromDamage($expectedDmg, $systemhit, $damageWasDealt, $shots, $isUnderShield, $shooter);
            if ($value<1) continue;
			if ($system->isDestroyed($turn-1)) continue;
			if ($system->isOfflineOnTurn($turn)) continue;

			//if the system has arcs, check that the position is on arc
			if(is_int($system->startArc) && is_int($system->endArc)){
				//get bearing on incoming fire...
				if($pos!=null){ //firing position is explicitly declared
					$relativeBearing = $this->getBearingOnPos($pos);
				}else{ //check from shooter...
					$relativeBearing = $this->getBearingOnUnit($shooter);
				}
				//if not on arc, continue!
				if (!mathlib::isInArc($relativeBearing, $system->startArc, $system->endArc)){
					continue;
				}
			}
			if($value>$chosenValue){
				$chosenSystem = $system;
				$chosenValue=$value;
			}
        }
		return ($chosenSystem);
	} //endof getSystemProtectingFromDamage
	
	
	/*first attempt at StarTrek shield
	//defensive system that can affect damage dealing at the moment of impact - only one (best) such system will be called
	//Not relevant for fighters - in their case appropriate system may be simplified to regular damage absorbing system, as in their case system hit is either already chosen or being chosen 
	public function getSystemProtectingFromImpactDamage($shooter, $pos, $turn, $weapon, $expectedDmg){ //$systemhit actually used by fighter flight
		$chosenSystem = null;
		$chosenValue=0;
		if($this instanceOf FighterFlight){ //only subsystems of a particular fighter
			return ($chosenSystem);
		}else{ //all systems of a ship
			$listOfPotentialSystems = $this->systems;
		}
		foreach($listOfPotentialSystems as $system){
			$value=$system->doesReduceImpactDamage($expectedDmg);
            if ($value<1) continue;
			if ($system->isDestroyed($turn-1)) continue;
			if ($system->isOfflineOnTurn($turn)) continue;

			//if the system has arcs, check that the position is on arc
			if(is_int($system->startArc) && is_int($system->endArc)){
				//get bearing on incoming fire...
				if($pos!=null){ //firing position is explicitly declared
					$relativeBearing = $this->getBearingOnPos($pos);
				}else{ //check from shooter...
					$relativeBearing = $this->getBearingOnUnit($shooter);
				}
				//if not on arc, continue!
				if (!mathlib::isInArc($relativeBearing, $system->startArc, $system->endArc)){
					continue;
				}
			}
			if($value>$chosenValue){
				$chosenSystem = $system;
				$chosenValue=$value;
			}
        }
		return ($chosenSystem);
	} //endof getSystemProtectingFromImpactDamage
	*/
	

    public function getHitChanceMod($shooter, $pos, $turn, $weapon){
        if ($pos !== null) {
            $pos = Mathlib::hexCoToPixel($pos);
        }
        $affectingSystems = array();
        foreach($this->systems as $system){
            if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)) continue;
            $mod = $system->getDefensiveHitChangeMod($this, $shooter, $pos, $turn, $weapon);
			//weapon might have something to say about that as well...
			$mod = $weapon->shieldInteractionDefense($this, $shooter, $pos, $turn, $system, $mod);
			//Advanced Sensors negate positive (eg. reducing profile) defensive systems' effects operated by less advanced races
			if ( ($mod > 0) && ($this->factionAge < 3) && ($shooter->hasSpecialAbility("AdvancedSensors")) ){
				$mod = 0;
			}
            if ( !isset($affectingSystems[$system->getDefensiveType()]) //no system of this kind is taken into account yet, or it is but it's weaker
                || $affectingSystems[$system->getDefensiveType()] < $mod){
                $affectingSystems[$system->getDefensiveType()] = $mod;
            }
        }
        /* Walkers of Sigma-957 (WALKERS_OF_SIGMA_PLAN.md 3.4): a Chromatic Pulse Driver scan
           teaches the SHOOTER's team about this ship's RACE, and from the next turn its shields
           read weaker for everything that team fires. Applied to the aggregated bucket, not inside
           each shield class: the bucket already holds only the strongest single "Shield" source, so
           the reduction lands exactly once however many shields are mounted, and one edit covers
           every shield-type system in the game.
           ⚠️ This method runs for every shot in every game, so the feature hangs off ONE static
           boolean - see TacGamedata::$cpdAdaptationPresent. False until a scan note has actually
           been replayed, which means no method call, no argument passing and not even an autoload
           of CpdScanRegistry in an ordinary game. Do not turn this into an unconditional call. */
        if (TacGamedata::$cpdAdaptationPresent) {
            $affectingSystems = CpdScanRegistry::applyToShieldBucket($affectingSystems, $this, $shooter);
        }
        return (-array_sum($affectingSystems));
    }

    public function getDamageMod($shooter, $pos, $turn, $weapon){
        if ($pos !== null) {
            $pos = Mathlib::hexCoToPixel($pos);
        }
        $affectingSystems = array();
        foreach($this->systems as $system){
            if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)) continue;
            $mod = $system->getDefensiveDamageMod($this, $shooter, $pos, $turn, $weapon);
			//weapon might have something to say about that as well...
			$mod = $weapon->shieldInteractionDamage($this, $shooter, $pos, $turn, $system, $mod);
            if ( !isset($affectingSystems[$system->getDefensiveType()])
                || $affectingSystems[$system->getDefensiveType()] < $mod){
                $affectingSystems[$system->getDefensiveType()] = $mod;
            }
        }
        //Chromatic Pulse Driver adaptation - see the gated block in getHitChanceMod above for why
        //the boolean is there. The same points reduce BOTH halves of a shield, which is what "the
        //shields are weaker" means: damage absorption here, profile reduction there.
        if (TacGamedata::$cpdAdaptationPresent) {
            $affectingSystems = CpdScanRegistry::applyToShieldBucket($affectingSystems, $this, $shooter);
        }
        return array_sum($affectingSystems);
    }

    private function checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon){
        if (!($system instanceof DefensiveSystem)) return false; //this isn't a defensive system at all

        //If the system was destroyed last turn continue
        //(If it has been destroyed during this turn, it is still usable)
        if ($system->isDestroyed($turn-1)) return false;

        //If the system is offline either because of a critical or power management, continue
        if ($system->isOfflineOnTurn($turn)) return false;

        //if the system has arcs, check that the position is on arc
        if(is_int($system->startArc) && is_int($system->endArc)){
            //get bearing on incoming fire...
            if($pos!==null){ //firing position is explicitly declared
                $relativeBearing = $this->getBearingOnPos($pos);
            }else{ //check from shooter...
                $relativeBearing = $this->getBearingOnUnit($shooter);
            }			

            //if not on arc, continue!
            if (!mathlib::isInArc($relativeBearing, $system->startArc, $system->endArc)){
                return false;
            }
        }

        return true;
    }


    public function getLastTurnMovement($turn){
        /*new code - returns last move of turn previous to indicated*/
        $trgtTurn = $turn - 1;
        $movement =  null;
        foreach ($this->movement as $move){ //should be sorted from oldest to newest...
            if($move->type == "start" && $this->userid !== -5) continue; //not a real move, except for generated Terrain
            if( ($move->turn > $trgtTurn) && ($move->type!='deploy')) continue; //future move; but always include deployment!
            $movement = $move;
        }
        return $movement;
    }



    public function getCoPos(){

        $movement = null;
        if (!is_array($this->movement) || empty($this->movement)){
            return array("x"=>0, "y"=>0);
        }
        foreach ($this->movement as $move){
            $movement = $move;
        }
        return $movement->getCoPos();

    }

    public function getHexPos() : OffsetCoordinate{

        $movement = null;
        if (!is_array($this->movement) || empty($this->movement)){
            return new OffsetCoordinate(0, 0);
        }

        foreach ($this->movement as $move){
            $movement = $move;
        }

        return $movement->position;
    }



    /* Two units in one hex have no real bearing on each other, so mathlib::getCompassHeadingOfShip
       fakes one from direction of travel: it stands the unit back in the hex it came from and takes
       the bearing from there. Forced Pre-Firing movement - Gravitic Mine pull, Gravity Net,
       Transverse Drive, Warp Jump - appends a 'prefire' order that teleports the unit AFTER movement
       is done, and a plain walk back then answers with the hex it was DRAGGED out of instead of the
       hex it flew in from. That rotates the bearing by the drag angle and drops the target out of
       arc even though a drag moves everything in the hex together and changes nothing between them.
       So walk back from where movement itself left the unit, then slide that answer along the drag
       vector - a rigid translation, which is what a drag actually is. Note the translation is
       essential and not just tidiness: pairing a pre-drag origin with a post-drag destination is
       wrong by the drag angle, a whole hex facing. hexCoToPixel is affine over cube coordinates, so
       shifting the pixel pair is exactly shifting hexes and row parity looks after itself.
       Undragged units take the original path below, unchanged.
       Client twin: movement.js getPreviousLocation - keep the two in step. */
    public function getPreviousCoPos(){
        $pos = $this->getCoPos();

        //Where the unit's own movement left it, ignoring any forced Pre-Firing shift.
        $movedPos = null;
        for ($i = sizeof($this->movement)-1; $i>=0; $i--){
            if ($this->movement[$i]->type == "prefire") continue;
            $movedPos = $this->movement[$i]->getCoPos();
            break;
        }

        $dragged = ($movedPos !== null) && ($movedPos["x"] != $pos["x"] || $movedPos["y"] != $pos["y"]);
        $anchor = $dragged ? $movedPos : $pos;

        for ($i = sizeof($this->movement)-1; $i>=0; $i--){
            $move = $this->movement[$i];
            if ($dragged && $move->type == "prefire") continue;
            //'start' is the off-board pre-deployment marker (x=+-30), not a position the unit was
            //ever really at - the same row getLastTurnMovement skips, and real only for generated
            //Terrain. It matters only on the dragged path: there the walk-back is anchored to the
            //PRE-drag hex, so a unit that never left its deploy hex (mine, OSAT, base) matches every
            //row and would otherwise run off the end of its history and answer with the marker.
            if ($dragged && $move->type == "start" && $this->userid !== -5) continue;
            $pPos = $move->getCoPos();

            if ( $pPos["x"] != $anchor["x"] || $pPos["y"] != $anchor["y"]){
                if (!$dragged) return $pPos;
                return array("x" => $pPos["x"] + ($pos["x"] - $movedPos["x"]),
                             "y" => $pPos["y"] + ($pos["y"] - $movedPos["y"]));
            }
        }

        //Nothing to walk back to. A dragged unit that never moved under its own power (mine, OSAT,
        //base) has no direction of travel, so the drag is the only motion there is: answer with the
        //hex it was dragged out of, which is also what the old code did. Undragged, $pos as before.
        return $dragged ? $movedPos : $pos;
    }

    public function getEWbyType($type, $turn, $target = null){
        foreach ($this->EW as $EW)
        {
            if ($EW->turn != $turn)
                continue;

            if ($target && $EW->targetid != $target->id)
                continue;

            if ($EW->type == $type){
                return $EW->amount;
            }
        }

        return 0;

    }

    public function getDEW($turn){

        foreach ($this->EW as $EW){
            if ($EW->type == "DEW" && $EW->turn == $turn)
                return $EW->amount;
        }

        return 0;

    }

    public function getBlanketDEW($turn){
        foreach ($this->EW as $EW){
            if ($EW->type == "BDEW" && $EW->turn == $turn)
                return $EW->amount;
        }

        return 0;
    }

    public function getOEW($target, $turn){
	$totalAmount = 0;
        if ($target instanceof FighterFlight){
            foreach ($this->EW as $EW){
                if ($EW->type == "CCEW" && $EW->turn == $turn){
					//check range - CCEW works up to 10 hexes away!
					$targetPos = $target->getHexPos();
					$ownPos = $this->getHexPos();
					$dis = mathlib::getDistanceHex($ownPos, $targetPos);
					if ($dis <=10){					
						$totalAmount += $EW->amount;
					}
				}
            }
        }
	    //OEW vs fighters is now possible too! else{
            foreach ($this->EW as $EW){
                if ($EW->type == "OEW" && $EW->targetid == $target->id && $EW->turn == $turn)
                    //return $EW->amount;
					$totalAmount += $EW->amount;
            }
			//Added ability for Systems like Aegis Pod to give Bonus OEW - 18 Apr 2024 - DK
			if ($this->hasSpecialAbility("BonusOEW")) {//'$this' is shooter in this function.
			    // Initialize podEW to store maximum output
			    $podEW = 0;         	
			    foreach ($this->systems as $system) {
			        if ($system->isDestroyed($turn) || $system->isOfflineOnTurn($turn)) continue; // Do nothing if destroyed or deactivated
			        if ($system instanceof AegisSensorPod) {
			            // Initialize $podFireOrder to null
			            $podFireOrder = null;
			            // Get Aegis Pod fireOrders, if any
			            $podFireOrders = $system->getFireOrders($turn);
			            // Iterate through fire orders
				            foreach ($podFireOrders as $fireOrder) { 
				                if ($fireOrder->type == 'normal') { 
				                    $podFireOrder = $fireOrder;
				                    // Break the loop after finding the first 'normal' fire order
				                    break;
				                }
				            }
			            // If there is no fire order, continue to search for other Aegis Pods
			            if ($podFireOrder === null) continue;
			            // Check if $podFireOrder is not null and the target id matches				            
			            if ($podFireOrder->targetid == $target->id) {
			                // Update podEW if system output is greater
			                if ($system->output > $podEW) {
			                    $podEW = $system->output;
			                }
			            }
			        }
			    }
				    // Return podEW if it's greater than the total amount				    
				    if ($podEW > $totalAmount) return $podEW;	    
			}		    
			    
        //}
        return $totalAmount;
    }

    public function getOEWTargetNum($turn){
        $amount = 0;
        foreach ($this->EW as $EW){
            //JAM (Hunter-Killer Jamming) counts as OEW for the purpose of disrupting ELINT (DIST):
            //each JAM allocation is a disruptable offensive-EW target slot, same as OEW.
            if ( ($EW->type == "OEW" || $EW->type == "JAM" || ($EW->type == "CCEW" && $EW->amount>0)) && $EW->turn == $turn)
                $amount++;
        }

        return $amount;
    }

 public function getAllOffensiveEW($turn){
    $amount = 0;
    foreach ($this->EW as $EW){
        if ($EW->type == "OEW" && $EW->turn == $turn) {
            $amount += $EW->amount;
        }
        // Move this part inside the loop
        else if ($EW->type == "CCEW" && $EW->turn == $turn) {
            // Check range - CCEW works up to 10 hexes away!
            $amount += $EW->amount;
        }
    }
    return $amount;
}

public function getAllEWExceptDEW($turn){
    $amount = 0;
    foreach ($this->EW as $EW){
        if ($EW->turn != $turn) continue;
        if ($EW->type == "DEW") continue;
        $amount += $EW->amount;
    }
    return $amount;
}

    public function getFacingAngle(){
        $movement = null;

        if (!is_array($this->movement) || empty($this->movement)){
            return 0;
        }

        foreach ($this->movement as $move){
            $movement = $move;
        }

        return $movement->getFacingAngle();
    }


    /* ===== LEAVING THE BATTLE THROUGH HYPERSPACE (JUMP_POINTS_PLAN.md Stage 4) ==============
     *
     * "Did this unit LEAVE, rather than die?" Asked wherever a destroyed unit has to be told
     * apart from one that jumped out - the combat value (below) and the hangar escape roll
     * (HangarOps::processCarrierDestructionEscapes).
     *
     * Two storage sites, one question. A unit WITH a jump engine keeps its record on the engine,
     * exactly where JumpEngine::doHyperspaceJump has always written it - that path is untouched.
     * A unit WITHOUT one can still use somebody else's open vortex (plan section 2.5), so the
     * fallback reads the primary structure directly: Movement::applyJumpOut hangs the combat-value
     * note there instead, and the HyperspaceJump damage entry is on it either way.
     */
    public function hasJumpedToHyperspace(){
        $jumpEngine = $this->getSystemByName("JumpEngine");
        if ($jumpEngine) return $jumpEngine->hasJumped();

        return $this->hasHyperspaceJumpDamage();
    }

    /* The combat value this unit had at the moment it jumped. Meaningless unless
       hasJumpedToHyperspace() is true. */
    public function getCVBeforeJump(){
        $jumpEngine = $this->getSystemByName("JumpEngine");
        if ($jumpEngine) return $jumpEngine->getCVBeforeJump();

        $primaryStruct = $this->getStructureSystem(0);
        return $primaryStruct ? $primaryStruct->getCVBeforeJump() : 0;
    }

    /* Stricter than JumpEngine::hasJumped, deliberately: with no jump engine to act as a first
       filter, an actual HyperspaceJump damage entry has to be present. Without that test any
       unit destroyed by something OTHER than damage to its primary structure - a collision, a
       captured hull - would read as having jumped. The second half is the engine version's test
       verbatim: the damage that is NOT jump damage must be short of destroying the ship, or the
       unit was already dead when the vortex took it. */
    private function hasHyperspaceJumpDamage(){
        $primaryStruct = $this->getStructureSystem(0);
        if (!$primaryStruct || !is_array($primaryStruct->damage)) return false;

        $jumped = false;
        $totalDamage = 0;
        foreach ($primaryStruct->damage as $entry){
            if ($entry->damageclass === 'HyperspaceJump'){
                $jumped = true;
                continue;
            }
            $totalDamage += max(0, $entry->damage - $entry->armour);
        }

        return $jumped && ($totalDamage < $primaryStruct->maxhealth);
    }

    public function getStructureSystem($location){
        foreach ($this->systems as $system){
            if ($system instanceof Structure  && $system->location == $location){
                return $system;
            }
        }
        if($location!=0){ //if there is no appropriate structure for a section, then it must be PRIMARY Structure!
            return $this->getStructureSystem(0);
        }else{ //should never happen!
            return null;
        }
    }


    public function getFireControlIndex(){
		//actually derive fire control index from ship size, like front end!
		if ($this->shipSizeClass < 2){ //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
			return 1; //MCV fire control
		}else{
			return 2; //Capital fire control
		}
		//original version:
        //return 2;
    }


    public function isDestroyed($turn = false){
        //Hangar Ops Stage 7: a docked flight has $removed=true; treat as
        //destroyed for filtering purposes so the 379+ isDestroyed callsites
        //(target lists, fleet iteration, hex occupancy, weapon scans like
        //PulsarMine, etc.) transparently skip docked flights without each
        //needing a !$removed check. Destruction explosions are gated on
        //damageManager::getTurnDestroyed (turn-of-damage record), not on
        //isDestroyed(), so no false explosions fire for docked flights.
        //Mirrors the client-side shipManager.isDestroyed which has done the
        //same since Stage 5; the server was the outlier.
        if ($this->removed && ($turn === false || $turn >= $this->removedTurn)) return true;

        foreach($this->systems as $system){
			/*18.02.2023: now dying Reactor will destroy PRIMARY Structure as well, so no point in checking directly for Reactor destruction (this avoids infinite loops, too)
            if ($system instanceof Reactor && $system->isDestroyed($turn)){
                return true;
            }
			*/
            if ($system instanceof Structure && $system->location == 0 && $system->isDestroyed($turn)){
                return true;
            }

        }

        return false;
    }

    /* Returns true when the unit is still in play (not destroyed, not removed-by-docking).
     * Stage 5 alias retained for self-documenting call sites; isDestroyed() now folds in the
     * $removed check (Stage 7), so this is just `!isDestroyed($turn)` — kept as a positive
     * predicate for readability where "is this ship on the board?" is the question being asked.
     */
    public function isOnBoard($turn = false){
        return !$this->isDestroyed($turn);
    }


    public function isDisabled(){
        if ($this->isPowerless())
            return true;

        $CnC = $this->getSystemByName("CnC");
        if (!$CnC || $CnC->destroyed || $CnC->hasCritical("ShipDisabledOneTurn", TacGamedata::$currentTurn ) || $CnC->hasCritical("ShipDisabled", TacGamedata::$currentTurn ))
            return true;

        return false;
    }


    public function isPowerless(){
        $output = 0;
        foreach($this->systems as $system){
            if ($system->isDestroyed())
                continue;

            if ($system instanceof Reactor){
                $output += $system->outputMod;
            }else if ($system->powerReq > 0){
                $output += $system->powerReq;
            }

        }

        if ($output >= 0)  return false;
        return true;
    }

	public function isTerrain(){
        //If any of these conditions is true, indicates Terrain.
        if($this instanceof Terrain || $this->userid == -5 || $this->shipSizeClass == 5) return true;
		return false;
	}

	/* ⭐ Is this unit BOUGHT IN BULK - ONE lobby row carrying bulkBuy = N, minted into N
	   separate ships by BuyingGamePhase::process? Mines always have been; OSATs joined
	   them (user request 2026-08-10).

	   THE MIRROR: gamedata.isBulkRow() in gamelobby.js answers the same question on the
	   client. The two must agree - the lobby decides which buy dialog a store entry gets
	   and how the fleet row is priced, this side decides whether a saved fleet's `bulkbuy`
	   column is honoured and whether the minted copies are numbered. Edit both or neither.

	   ⚠️ `osat` is not only the ship-shaped OSAT hull: MicroSAT extends SuperHeavyFighter
	   extends FighterFlight and sets osat = true. It is a FLIGHT with a flight size the
	   bulk dialog cannot express, so flights are excluded - EXCEPT the flight-shaped mines
	   (MineClass), which have been bulk-bought all along. */
	public function isBulkBought(){
		if ($this->mine) return true;
		return $this->osat && !($this instanceof FighterFlight);
	}

	/* REINFORCEMENTS_PLAN.md §3.1 — "this unit was bought as a reinforcement and is STILL in
	   hyperspace". Once the exit it is riding forms, $arrivalTurn is stamped and it stops
	   answering true: from then on it is an ordinary unit with a late deploy turn. */
	public function isReinforcement(){
		return $this->reinforcement && $this->arrivalTurn === null;
	}

	/* ⭐ IS THIS UNIT ON THE BOARD FROM TURN 1 NO MATTER WHAT THE SLOT OR THE FLAG SAYS?
	   Bases, OSATs and Terrain never 'jump in' - getTurnDeployed has always answered 1 for them
	   before it looks at anything else, and hideHyperspaceReinforcements already carries the ⚠️
	   that says so. This NAMES that rule so the other two places that need it can ask instead of
	   re-listing the three properties (user report 2026-08-29, game 4319):

	     - BuyingGamePhase::process refuses the reinforcement flag on such a unit outright. A gate
	       bought while the lobby's REINFORCEMENTS group was selected took the flag, and the flag
	       can never come true for it - the gate is on the board on turn 1 regardless.
	     - JumpEngine::stampArrivingReinforcements refuses to give it an arrival turn. That was the
	       live bug: a signalled gate is in $opened as the doorway's HOLDER, not as a unit riding
	       it, so a gate carrying the flag stamped ITSELF as arriving and won its owner a phantom
	       Deployment phase on every turn its jump point stood.

	   ⚠️ NOT the same question as isReinforcement(). This is a property of the HULL; that one is a
	   property of where the unit currently is. */
	public function alwaysDeploysTurnOne(){
		return $this->osat || $this->base || $this->isTerrain();
	}

    public function getTurnDeployed($gamedata){

        if ($this->alwaysDeploysTurnOne()) return 1; //Bases, Terrain and OSATs never 'jump in'.

        $slot = $gamedata->getSlotById($this->slot);
        $depTurn = $slot->depavailable;

        /* REINFORCEMENTS_PLAN.md §3.2 — THE ONE LOAD-BEARING CHANGE. A reinforcement's arrival
           turn is decided IN PLAY (by the jump point exit it comes through), not by its
           slot, so the slot's depavailable says nothing about it. null means it is still in
           hyperspace, which reads here as "not on the board" - and that single sentence is what
           makes it inert to firing, movement, EW, power, masking, the fleet list's deployable
           filter and the unavailable flag all at once, with no further change anywhere.
           999 is the existing surrender sentinel, reused deliberately: both mean exactly
           "not on the board". */
        if ($this->reinforcement) $depTurn = ($this->arrivalTurn === null) ? 999 : $this->arrivalTurn;

        if($slot->surrendered !== null){
            if($slot->surrendered <= $gamedata->turn){ //Surrendered on this turn or before, no longer present in game.
                $depTurn = 999; //Artifically high number, so surrendered ships are no longer considered by game! - DK
            }
        }    
        
        return $depTurn;
	}


    /*The turn this unit picks its ENTRY HEX, as opposed to the turn it is physically on the
      board (getTurnDeployed above). LATE-SLOT arrivals place a turn EARLY: the player commits the
      entry hexes during the Deployment phase of turn depTurn-1, those hexes show to everyone as
      a blue "Jump Point" ballistic marker for the whole of that turn, and only then do the ships
      arrive. Without the early placement an opponent got no warning whatsoever - a fresh fleet
      simply materialised.

      ONLY code answering "is this unit being placed right now?" may use this. Everything asking
      "is this unit on the board?" - firing, movement, EW, masking, the unavailable flag - must
      keep reading getTurnDeployed, which is unchanged.

      Bases/OSATs/Terrain place and arrive together on turn 1, so they fall out of getTurnDeployed
      already. The 999 surrender sentinel becomes 998, still far beyond any real turn.*/
    public function getTurnPlaced($gamedata){
        /* REINFORCEMENTS_PLAN.md §3.2 / trap 2 — a REINFORCEMENT (the jump point exit kind,
           not a late slot) places and arrives on the SAME turn. Its early warning is the blue
           jump point that formed LAST turn, not an early placement. Subtracting one here would
           grant it a Deployment phase a turn before its vortex exists, with nowhere legal for it
           to stand - a silent break either way round, and the most expensive mistake available
           in this feature. */
        if ($this->reinforcement) return $this->getTurnDeployed($gamedata);

        $depTurn = $this->getTurnDeployed($gamedata);
        return ($depTurn > 1) ? ($depTurn - 1) : $depTurn;
    }


    public function getBearingOnPos($pos){ //returns relative angle from this unit to indicated coordinates
        $tf = $this->getFacingAngle(); //ship facing
        $compassHeading = mathlib::getCompassHeadingOfPos($this, $pos); //absolute bearing
        $relativeBearing =  Mathlib::addToDirection($compassHeading, -$tf);//relative bearing
        if( Movement::isRolled($this) ){ //if ship is rolled, mirror relative bearing
            if( $relativeBearing <> 0 ) { //mirror of 0 is 0
                $relativeBearing = 360-$relativeBearing;
            }
        }
        return round($relativeBearing); //round to full degrees - otherwise there were sometimes problems!!!
    }

    public function getBearingOnUnit($unit){ //returns relative angle from this unit to indicated unit
        $tf = $this->getFacingAngle(); //ship facing
        $compassHeading = mathlib::getCompassHeadingOfShip($this, $unit); //absolute bearing
        $relativeBearing =  Mathlib::addToDirection($compassHeading, -$tf);//relative bearing
        if( Movement::isRolled($this) ){ //if ship is rolled, mirror relative bearing
            if( $relativeBearing <> 0 ) { //mirror of 0 is 0
                $relativeBearing = 360-$relativeBearing;
            }
        }
        return round($relativeBearing); //round to full degrees - otherwise there were sometimes problems!!!
    }


    public function doGetHitSectionBearing($relativeBearing){ //pick section hit from given bearing; return array with all data!
        $locs = $this->getLocations();
        $valid = array();
        foreach ($locs as $loc){
            if(mathlib::isInArc($relativeBearing, $loc["min"], $loc["max"])){
                $valid[] = $loc;
            }
        }
        $valid = $this->fillLocations($valid);

        //New Ambiguous hit resolution - DK 12.1.26
        //If we have multiple valid sections (ambiguous shot), randomize based on profile.
        //Original logic sticked to the 'toughest' section deterministically.
        if (count($valid) > 1) { //Only if multiple valid locations
            $liveSections = array();
            //Calculated REAL predicted health (fillLocations clamps it to 1, causing dead sections to look alive)
            foreach ($valid as $loc){
                
                //We need to check if it's actually dead (Health - Expected <= 0). 
                //fillLocations already subtracted expectedDamage but maxed it to 1.
                //So we have to check the raw numbers.
                $structure = $this->getStructureSystem($loc["loc"]);
                if($structure){
                    $trueRem = $structure->getRemainingHealth();
                    $expected = 0;
                    if(isset($this->expectedDamage[$loc["loc"]])) $expected = $this->expectedDamage[$loc["loc"]];
                    
                    if( ($trueRem - $expected) > 0 ){
                         $liveSections[] = $loc;
                    }
                }
            }

            if(count($liveSections) > 1){
                $totalProfile = 0;
                foreach($liveSections as $loc) $totalProfile += $loc["profile"];

                if($totalProfile > 0){
                    $roll = Dice::d($totalProfile);
                    $current = 0;
                    foreach($liveSections as $loc){
                        $current += $loc["profile"];
                        if($roll <= $current) return $loc;
                    }
                }
            }
        }
        //End of new block - DK 12.1.26

        $pick = $this->pickLocationForHit($valid);
        return $pick;
    }


    public function doGetHitSectionPos($pos){ //pick section hit from given coordinates; return array with all data!
        $relativeBearing =  $this->getBearingOnPos($pos);
        $result = $this->doGetHitSectionBearing($relativeBearing);
        return $result;
    }


    public function doGetHitSection($shooter){   //pick section hit from given unit; return array with all data!
        $relativeBearing =  $this->getBearingOnUnit($shooter);
        $result = $this->doGetHitSectionBearing($relativeBearing);
        return $result;
    }


    public function isHitSectionAmbiguous($shooter, $turn){ //for a shot from indicated unit - would there be choice of target section?
        $locs = $this->getLocations();
        $relativeBearing =  $this->getBearingOnUnit($shooter);
        $valid = array();
        foreach ($locs as $loc){
            if(mathlib::isInArc($relativeBearing, $loc["min"], $loc["max"])){
                $valid[] = $loc;
            }
        }
        $valid = $this->fillLocations($valid);
        //count non-destroyed locations...
        $numValidLocs = 0;
        foreach ($valid as $loc){
            if($loc["remHealth"]>0) $numValidLocs++;
        }
        //ambiguous: if there is more than 1 valid choice
        if($numValidLocs>1){
            return true;
        }else{
            return false;
        }
    }

    public function isHitSectionAmbiguousPos($pos, $turn){ //for a shot from indicated position - would there be choice of target section?
        $locs = $this->getLocations();
        $relativeBearing =  $this->getBearingOnPos($pos);
        $valid = array();
        foreach ($locs as $loc){
            if(mathlib::isInArc($relativeBearing, $loc["min"], $loc["max"])){
                $valid[] = $loc;
            }
        }
        $valid = $this->fillLocations($valid);
        //count non-destroyed locations...
        $numValidLocs = 0;
        foreach ($valid as $loc){
            if($loc["remHealth"]>0) $numValidLocs++;
        }
        //ambiguous: if there is more than 1 valid choice
        if($numValidLocs>1){
            return true;
        }else{
            return false;
        }
    }


    /*outer locations of unit and their arcs, used for GUI called shots*/
    public function fillLocationsGUI(){
        $call = ($this->shipSizeClass>1); //MCVs are one big PRIMARY
        $this->outerSections = array();
        $allOuter = $this->getLocations();
        foreach($allOuter as $curr){
            if($curr['loc']!=0){
                $outer = array("loc" => $curr['loc'], "min" => $curr['min'], "max" => $curr['max'], "call" => $call);
                $this->outerSections[] = $outer;
            }
        }
    }


    /*outer locations of unit and their arcs, used for assigning incoming fire*/
    public function getLocations(){
        $locs = array();
        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->sideDefense);
        return $locs;
    }
	

    public function fillLocations($locs){
        foreach ($locs as $key => $loc){
            $structure = $this->getStructureSystem($locs[$key]["loc"]);
            if ($structure){
                $locs[$key]["remHealth"] = $structure->getRemainingHealth();
                if($locs[$key]["remHealth"]>0){ //else section is destroyed anyway!
				    if(isset($this->expectedDamage[$locs[$key]["loc"]])){
                        $locs[$key]["remHealth"] -= round($this->expectedDamage[$locs[$key]["loc"]]);
                        $locs[$key]["remHealth"] = max(1,$locs[$key]["remHealth"]);
                    }
				/*
                    if(isset($expectedDamage[$locs[$key]["loc"]])){
                        $locs[$key]["remHealth"] -= round($expectedDamage[$locs[$key]["loc"]]);
                        $locs[$key]["remHealth"] = max(1,$locs[$key]["remHealth"]);
                    }
					*/
                }
                $locs[$key]["armour"] = $structure->armour;
            }
            else {
                return null; //should never happen!
            }
        }
        return $locs;
    }


    public function pickLocationForHit($locs){   //return array! ONLY OUTER LOCATIONS!!! (unless PRIMARY can be hit directly and is on hit table)
        $pick = array("loc"=>0, "profile"=>1000, "remHealth"=>0, "armour"=>0);
        foreach ($locs as $loc){
            //compare current best pick with current loop iteration, change if new pick is better
            $toughnessPick = $pick["remHealth"]+round($pick["remHealth"]*$pick["armour"]*0.15);//toughness: remaining structure toughened by armor
            $toughnessLoc = $loc["remHealth"]+round($loc["remHealth"]*$loc["armour"]*0.15);//every point of armor increases toughness by 15%

            //now, depending on which profile is larger - modify toughness of smaller profile
            //every point of size difference increases perceived toughness by 12 points
            //that's a lot if remaining structure is low, but not all that much if it's high
            $profileImpact = 17; //equiv. to almost 10 Str boxes at armor 5, or 11 at 4
            if($pick["profile"]<$loc["profile"]){ //old profile smaller
                $profileDiff = $loc["profile"] - $pick["profile"];
                if($toughnessPick>0)// profile shouldn't cause destroyed section to be chosen
                    $toughnessPick = $toughnessPick + ($profileDiff*$profileImpact);
            }elseif($pick["profile"]>$loc["profile"]){ //old profile larger
                $profileDiff = $pick["profile"] - $loc["profile"];
                if($toughnessLoc>0)// profile shouldn't cause destroyed section to be chosen
                    $toughnessLoc = $toughnessLoc + ($profileDiff*$profileImpact);
            }


            if($toughnessLoc>$toughnessPick){ //if new toughness is better, it wins (already takes profile into account)
                $pick = $loc;
            }elseif(($toughnessLoc==$toughnessPick) && ($loc["profile"]<=$pick["profile"])){ //if toughness is equal, better profile wins
                $pick = $loc;
            }//else old choice stays
        }

        return $pick;
    }


	/*19.12.2024 clear Vree section choice - to be called from firing routine*/
	public function clearVreeHitSectionChoice($shooter_id, $fireOrder) {
		if ($this->VreeHitLocations != true) return false; //no Vree layout - do nothing
		
		if (isset($this->activeHitLocations[$shooter_id])) { //unset location already chosen for this ship
			unset($this->activeHitLocations[$shooter_id]);
		}
		
		//unset location already stored in fire order, too
		$fireOrder->chosenLocation = 0;
		
		//...and unset expected damage - so allocation is based on _current actual_ damage only (as further incoming shots will be re-assigned as well!)
		foreach($this->expectedDamage as $key => $value){
			$this->expectedDamage[$key] = 0;
		}
		
		return true;
	}

    public function getHitSectionChoice($shooter, $fireOrder, $weapon, $returnDestroyed = false){ //returns value - location! chooses method based on weapon and fire order!
        $foundLocation = 0;
        if($weapon->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn); //turn - 1?...
            $posLaunch = mathlib::hexCoToPixel($movement->position);
            $foundLocation = $this->getHitSectionPos($posLaunch, $fireOrder->turn, $returnDestroyed);
        }else{
            $foundLocation = $this->getHitSection($shooter, $fireOrder->turn, $returnDestroyed);
        }
        return $foundLocation;
    }
    public function getHitSection($shooter, $turn, $returnDestroyed = false){ //returns value - location! DO NOT USE FOR BALLISTICS!
        $foundLocation = 0;
		/*19.12.2024: set up Vree hit locations like for anyone else; but clear them after shot (separate call from firing routine!)*/
        if(isset($this->activeHitLocations[$shooter->id]) /*&& ($this->VreeHitLocations != true)*/){		
            $foundLocation = $this->activeHitLocations[$shooter->id]["loc"];
        }else{
            $loc = $this->doGetHitSection($shooter); //finds array with relevant data!
            $this->activeHitLocations[$shooter->id] = $loc; //save location for further hits from same unit
            $foundLocation = $loc["loc"];
        }
        if(($foundLocation > 0) && ($returnDestroyed == false)){ //return it only if not destroyed as of previous turn
            $structure = $this->getStructureSystem($foundLocation); //this always returns appropriate structure
            if($structure->isDestroyed($turn-1)) $foundLocation = 0;
        }
        return $foundLocation;
    }
    /* Which section a BOARDING unit attaches to - breaching pods and grappling claws only.
       DO NOT use for weapon fire.

       Ordinary fire takes the arcs that CONTAIN the shooter's bearing and, where they overlap,
       doGetHitSectionBearing rolls profile-weighted between them. On a six-section hull every
       hex-edge bearing is ambiguous three ways, so which block a boarder grabbed was a roll the
       player could neither predict nor plan around - and with per-section attachment limits a
       clash now costs the whole attempt (and, once §3 of BOARDING_ATTACHMENT_PLAN lands, rams
       the boarder into an Enormous base). So boarding gets a deterministic rule.

       The rule (user ruling 2026-08-13): attach to the section whose arc is CENTRED on the hex
       edge the unit crossed. A same-hex bearing is always a multiple of 60 degrees - hex facings
       and hex neighbours both are - and every section arc is centred on one of those bearings,
       so this maps the six approach directions straight onto 1/41/42/2/32/31 on a six-section
       hull. Verified a NO-OP on every hull whose arcs do not overlap: there the containing arc
       is already the nearest-centred one, so nothing about existing hulls changes.

       Two sections equidistant from the entry edge is a genuine boundary rather than something
       to invent an answer for - dead astern of a hull with no aft section, or head-on to a
       port/starboard-only one - and falls through to getHitSection's existing roll.

       Shares activeHitLocations with getHitSection, so a boarder's section and its own gunfire
       agree exactly as two shots from one shooter already do, and so both fire orders of a
       two-pod flight resolve to one section. */
    public function getAttachSection($shooter, $turn){
        if (!isset($this->activeHitLocations[$shooter->id])){
            $pick = $this->doGetAttachSectionBearing($this->getBearingOnUnit($shooter));
            if ($pick !== null) $this->activeHitLocations[$shooter->id] = $pick;
        }

        //Reads the pick back (or rolls the ordinary way when the bearing tied) and applies the
        //destroyed-structure -> Primary redirect.
        return $this->getHitSection($shooter, $turn);
    }

    //Section whose arc is centred nearest $relativeBearing; null if none contains the bearing
    //or if two different sections are equally centred on it. See getAttachSection.
    protected function doGetAttachSectionBearing($relativeBearing){
        $best = null;
        $bestDist = null;
        $tied = false;

        foreach ($this->getLocations() as $loc){
            if (!mathlib::isInArc($relativeBearing, $loc["min"], $loc["max"])) continue;

            $dist = mathlib::getAngleDistance($relativeBearing,
                        mathlib::getArcCentre($loc["min"], $loc["max"]));

            if ($bestDist === null || $dist < $bestDist - 0.001){
                $bestDist = $dist;
                $best = $loc;
                $tied = false;
            }elseif ($dist < $bestDist + 0.001 && (int)$loc["loc"] !== (int)$best["loc"]){
                $tied = true; //two distinct sections equally centred - let the roll decide
            }
        }

        if ($best === null || $tied) return null;

        //Enrich with remHealth/armour to match what doGetHitSectionBearing returns. fillLocations
        //answers NULL if a location has no Structure of its own, so fall back to the raw entry
        //rather than losing the pick - the only fields activeHitLocations consumers read are
        //"loc" and "profile", and getLocations already carries both.
        $filled = $this->fillLocations(array($best));
        if (!is_array($filled) || !isset($filled[0])) return $best;

        return $filled[0];
    }

    public function getHitSectionPos($pos, $turn, $returnDestroyed = false){ //returns value - location! THIS IS FOR BALLISTICS!
        $foundLocation = 0;
        $loc = $this->doGetHitSectionPos($pos); //finds array with relevant data!
        $foundLocation = $loc["loc"];
        if(($foundLocation > 0) && ($returnDestroyed == false)){ //return it only if not destroyed as of previous turn
            $structure = $this->getStructureSystem($foundLocation); //this always returns appropriate structure
            if($structure->isDestroyed($turn-1)) $foundLocation = 0;
        }
        return $foundLocation;
    }


    public function getHitSectionProfileChoice($shooter, $fireOrder, $weapon){ //returns value - profile! chooses method based on weapon and fire order!
        $foundProfile = 0;
        if($weapon->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn); //turn-1?...
            $posLaunch = mathlib::hexCoToPixel($movement->position);
            $foundProfile = $this->getHitSectionProfilePos($posLaunch);
        }else{
            $foundProfile = $this->getHitSectionProfile($shooter);
        }
        return $foundProfile;
    }
    public function getHitSectionProfile($shooter){ //returns value - profile! DO NOT USE FOR BALLISTICS!
        $foundProfile = 0;
        if(isset($this->activeHitLocations[$shooter->id]) ){
            $foundProfile = $this->activeHitLocations[$shooter->id]["profile"];
        }else{
            $loc = $this->doGetHitSection($shooter); //finds array with relevant data!
            $this->activeHitLocations[$shooter->id] = $loc; //save location for further hits from same unit
            $foundProfile = $loc["profile"];
        }
        return $foundProfile;
    }
    public function getHitSectionProfilePos($pos){ //returns value - profile! THIS IS FOR BALLISTICS!
        $foundProfile = 0;
        $loc = $this->doGetHitSectionPos($pos); //finds array with relevant data!
        $foundProfile = $loc["profile"];
        return $foundProfile;
    }



    public function getHitSystemPos($pos, $shooter, $fireOrder, $weapon, $gamedata, $location = null){
        /*find target section (based on indicated position) before finding location*/
        if($location==null){
            $location = $this->getHitSectionPos($pos, $fireOrder->turn);
        }
        $foundSystem = $this->getHitSystem($shooter, $fireOrder, $weapon, $gamedata, $location);
        return $foundSystem;
    }


    public function getHitSystem($shooter, $fireOrder, $weapon, $gamedata, $location = null, $sourceOverride = null){
        /*if something has to choose system by firing position, use getHitSystemPos instead*/
        /*$sourceOverride (pixel coords) forces a synthetic source for in-arc system filtering - used by Piercing 3rd-part*/
        if (isset($this->hitChart[0])){
            $system = $this->getHitSystemByTable($shooter, $fireOrder, $weapon, $location, $sourceOverride);
        }
        else {
            $system = $this->getHitSystemByDice($shooter, $fireOrder, $weapon, $location, $sourceOverride);
        }
        if ($system !== null){ //system may redirect the hit (own sub-chart roll, or divert to Structure while stowed) - Kirishiac Orbitals
            $system = $system->resolveSubHitChart();
        }
        return $system;
    }



    public function getHitSystemByTable($shooter, $fire, $weapon, $location, $sourceOverride = null){
        /*DOES NOT take care of overkill!!! returns section structure if no system can be hit, whether that section is still alive or not*/
        /*$sourceOverride (pixel coords) forces synthetic source - used by Piercing 3rd-part to filter exit-side systems*/
        $system = null;
        $name = false;
        //$location_different = false; //target system may be on different location?
        //$location_different_array = array(); //array(location,system) if so indicated
        $systems = array();

        if ($fire->calledid != -1){
            $system = $this->getSystemById($fire->calledid);
        }

        if ($system != null && !$system->isDestroyed()) return $system; //if destroted, allocate s if it wasn't called
        if ($location === null) {
            $location = $this->getHitSectionChoice($shooter, $fire, $weapon);
        }
		//15.09.2023 - moved bearing calculation here, as it will be needed earlier than previously
		$bearing = 0;
		//this will ignore non-standard direction of impact - like with Flash collateral damage. This information is simply not available here, and IMO not important enough to rewrite entire chain if calls to pass
		if($sourceOverride !== null){ //synthetic source - Piercing 3rd-part exit slug
			$bearing = $this->getBearingOnPos($sourceOverride);
		}else if($weapon->ballistic){
			$movement = $shooter->getLastTurnMovement($fire->turn);
            $pos = mathlib::hexCoToPixel($movement->position);
			$bearing = $this->getBearingOnPos($pos);
		}else{
			$bearing = $this->getBearingOnUnit($shooter);
		}
        $hitChart = $this->hitChart[$location];             
        $rngTotal = 20; //standard hit chart has 20 possible locations
        if($weapon->damageType == 'Flash'){ //Flash - change hit chart! - only undestroyed systems
            $hitChart = array();
            //use only non-destroyed systems on section hit
            $rngTotal = 0; //range of current system
            $rngCurr = 0; //total range of live systems
            for($roll = 1;$roll<=20;$roll++){
                $rngCurr++;
                if (isset($this->hitChart[$location][$roll])){
                    $name = $this->hitChart[$location][$roll];
			$name=strtoupper($name); //to ensure working no matter the spelling!
                    if($name != 'PRIMARY'){ //no PRIMARY penetrating hits for Flash!
                        $systemsArray = $this->getSystemsByNameLoc($name, $location, $bearing, false);//undestroyed sytems of this name
                        if(sizeof($systemsArray)>0){ //there actually are such systems!
                            $rngTotal+= $rngCurr;
                            $hitChart[$rngTotal] = $name;
                        }
                    }
                    $rngCurr = 0;
                }
            }
            if($rngTotal ==0) return $this->getStructureSystem(0);//there is nothing here! assign to Structure...
        }
        // $noPrimaryHits = ($weapon->noPrimaryHits || ($weapon->damageType == 'Piercing')); //Original Logic - DK 13.01.26
        $noPrimaryHits = ($weapon->damageType == 'Piercing'); //New logic: Only Piercing removes PRIMARY from table. $noPrimaryHits trait keeps it but redirects result.    
        if($noPrimaryHits){ //change hit chart! - no PRIMARY hits!
            $hitChart = array();
            //use only non-destroyed systems on section hit
            $rngTotal = 0; //range of current system
            $rngCurr = 0; //total range of live systems
            for($roll = 1;$roll<=20;$roll++){
                $rngCurr++;
                if (isset($this->hitChart[$location][$roll])){
                    $name = $this->hitChart[$location][$roll];
			$name=strtoupper($name); //to ensure working no matter the spelling!
                    if($name != 'PRIMARY'){ //no PRIMARY penetrating hits
                        $systemsArray = $this->getSystemsByNameLoc($name, $location, $bearing, true);//accept destroyed systems too
                        if(sizeof($systemsArray)>0){ //there actually are such systems!
                            $rngTotal+= $rngCurr;
                            $hitChart[$rngTotal] = $name;
                        }
                    }
                    $rngCurr = 0;
                }
            }
            if($rngTotal ==0) return $this->getStructureSystem($location);//there is nothing here! return facing Structure anyway, overkill methods will handle it
        }

        //now choose system from chart...
        $roll = Dice::d($rngTotal);
        $name = '';
        //$isSystemKiller = $weapon->systemKiller;
        while ($name == ''){
            if (isset($hitChart[$roll])){
                $name = $hitChart[$roll];
				/* this ability was never used, I comment it out!
                if($name == 'Structure' && $isSystemKiller) { //for systemKiller weapon, reroll Structure hits
                    $isSystemKiller = false; //don't do that again
                    $name = ''; //reset
                    $roll = Dice::d($rngTotal); //new location roll
                }
				*/
            }else{
                $roll++;
                if($roll>$rngTotal)//out of range already! return facing Structure... Should not happen.
                {
                    return $this->getStructureSystem($location);
                }
            }
        }
 
        if($name == 'Primary'){ //redirect to PRIMARY!
            if($weapon->noPrimaryHits) return $this->getStructureSystem($location); //If weapon treats Primary as facing Structure - DK 13.01.26
            return $this->getHitSystemByTable($shooter, $fire, $weapon, 0, $sourceOverride);
        }
        $systems = $this->getSystemsByNameLoc($name, $location, $bearing, false); //do NOT accept destroyed systems!
        if(sizeof($systems)==0){ //if empty, damage is done to Structure
            $struct = $this->getStructureSystem($location);
            return $struct;
        }
 		
		//prioritize in-arc systems - 13.09.2021
		$systemsInArc = array();
		
		/*15.09.2023 - moved bearing calculatioon earlier, it will be needed to pass it!
		$bearing = 0;
		//this will ignore on-standard direction of impact - like with Flash collateral damage. This information is simply not available here, and IMO not important enough to rewrite entire chain if calls to pass
		if($weapon->ballistic){
			$movement = $shooter->getLastTurnMovement($fire->turn);
            $pos = mathlib::hexCoToPixel($movement->position);
			$bearing = $this->getBearingOnPos($pos);
		}else{
			$bearing = $this->getBearingOnUnit($shooter);	
		}		
		*/
		
		foreach($systems as $systemInArc){
			if(mathlib::isInArc($bearing, $systemInArc->startArc, $systemInArc->endArc)){ //actually this system is in relevant arc!
				$systemsInArc[] = $systemInArc;
			}
		}
		if(sizeof($systemsInArc)>0) $systems = $systemsInArc; //some of indicated systems are in arc - they have to be targeted as priority!
		
		//Prefer Port/Stbd thrusters if on Primary and no thruster in arc
		else if ( ($location == 0) && (sizeof($systems)>0) && ($systems[0] instanceof Thruster) ){
			$preferredSystems = array();
            
            //Resolve bearing to 0-359 range just in case
            $relBearing = $bearing;
            while($relBearing < 0) $relBearing += 360;
            while($relBearing >= 360) $relBearing -= 360;

			foreach($systems as $currSys){
				$center = ($currSys->startArc + $currSys->endArc) / 2;
                if($currSys->startArc > $currSys->endArc){
                     $center = ($currSys->startArc + $currSys->endArc + 360) / 2;
                }
                while($center >= 360) $center -= 360;
                                
                $isPortThruster = ($center > 180) && ($center < 360); //Strictly Port side
                $isStbdThruster = ($center > 0) && ($center < 180); //Strictly Stbd side

                //Shot from Port?
                if( ($relBearing > 180) && ($relBearing < 360) ){ 
                    if($isPortThruster) $preferredSystems[] = $currSys;
                }
                //Shot from Stbd?
                else if( ($relBearing > 0) && ($relBearing < 180) ){
                    if($isStbdThruster) $preferredSystems[] = $currSys;
                }
			}
            
            if(sizeof($preferredSystems)>0) $systems = $preferredSystems;
            else if ( ($relBearing > 0) && ($relBearing < 360) && ($relBearing != 180) ) {
                // Targeted specific side, but no matching thrusters found (e.g. destroyed).
                // Do not fall back to wrong-side thrusters. Return Structure.
                return $this->getStructureSystem($location);
            }
		}

        // Exterior-first: when a tag pool mixes Primary-mounted weapons (location 0) with
        // hull-section weapons (TAG:Weapon on Vree etc.), spare the Primary ones - they are
        // only hit when no non-Primary weapon of the same tag is in arc, matching the B5W
        // principle that Primary systems stay protected until the exterior is stripped.
        if ($systems[0] instanceof Weapon) {
            $nonPrimaryWeapons = array();
            foreach ($systems as $sys) if ($sys->location != 0) $nonPrimaryWeapons[] = $sys;
            if (sizeof($nonPrimaryWeapons) > 0) $systems = $nonPrimaryWeapons;
        }

        // Prefer destroying weapons that need the longest to recharge:
        //   tier 1: weapons that fired this turn (turnsloaded resets to 1 next turn)
        //   tier 2: weapons recharging and not ready next turn either
        if ($systems[0] instanceof Weapon) {
            $firedThisTurn = array();
            $rechargingSystems = array();
            foreach ($systems as $sys) {
                if ($sys->loadingtime <= 1) continue; //1-turn weapons always ready, ignore
                if ($sys->firedOnTurn(TacGamedata::$currentTurn)) {
                    $firedThisTurn[] = $sys;
                } else if ($sys->turnsloaded < ($sys->loadingtime - 1)) {
                    $rechargingSystems[] = $sys;
                }
            }
            if (sizeof($firedThisTurn) > 0) $systems = $firedThisTurn;
            else if (sizeof($rechargingSystems) > 0) $systems = $rechargingSystems;
        }

        //now choose one of equal eligible systems (they're already known to be undestroyed... well, they may be destroyed, but then they're to be returned anyway)
        $roll = Dice::d(sizeof($systems));
        $system = $systems[$roll-1];

        return $system;

    } //end of function getHitSystemByTable


    public function getHitSystemByDice( $shooter, $fire, $weapon, $location, $sourceOverride = null){
        /*same as by table, but prepare table out of available systems...*/
        /*$sourceOverride (pixel coords) forces synthetic source - used by Piercing 3rd-part to filter exit-side systems*/
        $system = null;
        $name = false;
        //$location_different = false; //target system may be on different location?
        //$location_different_array = array(); //array(location,system) if so indicated
        $systems = array();

        if ($fire->calledid != -1){
            $system = $this->getSystemById($fire->calledid);
        }

        if ($system != null && !$system->isDestroyed()) return $system; //if destroted, allocate s if it wasn't called

        if ($location === null) {
            $location = $this->getHitSectionChoice($shooter, $fire, $weapon);
        }

		//15.09.2023 - moved bearing calculation here, as it will be needed
		$bearing = 0;
		//this will ignore non-standard direction of impact - like with Flash collateral damage. This information is simply not available here, and IMO not important enough to rewrite entire chain if calls to pass
		if($sourceOverride !== null){ //synthetic source - Piercing 3rd-part exit slug
			$bearing = $this->getBearingOnPos($sourceOverride);
		}else if($weapon->ballistic){
			$movement = $shooter->getLastTurnMovement($fire->turn);
            $pos = mathlib::hexCoToPixel($movement->position);
			$bearing = $this->getBearingOnPos($pos);
		}else{
			$bearing = $this->getBearingOnUnit($shooter);
		}
          
		$hitChart = array(); //$hitChart will contain system names, as usual!
		//use only non-destroyed systems on section hit
		$rngTotal = 0; //range of current system
		$rngCurr = 0; //total range of live systems
		
		foreach ($this->systems as $system){ //ok, do use actual systems...
			if (($system->location == $location) && (!($system instanceof Structure))){ 
				//Flash - undestroyed only
				if(($weapon->damageType != 'Flash') || (!$system->isDestroyed())) {
                    if(!$system->isTargetable) continue; //cannot be targeted!
					//Structure and C&C will get special treatment...
					$multiplier = 1;
					if($system->displayName == 'C&C' ) $multiplier = 0.5; //C&C should have relatively low chance to be hit!
					$rngCurr =  ceil($system->maxhealth * $multiplier);
					$rngCurr+=1; //small systems usually have relatively high chance of being hit
					$rngTotal = $rngTotal+$rngCurr;
					$hitChart[$rngTotal] = $system->displayName;
				}
			}
		}
		//add Structure
		$system =  $this->getStructureSystem($location);
		if(($weapon->damageType != 'Flash') || (!$system->isDestroyed() )) {
			if($location == 0){
				$multiplier = 2; //PRIMARY has relatively low Structure, increase chance
			}else{
				$multiplier = 0.5; //non-PRIMARY have relatively high structure, reduce chance
			}
			$rngCurr =  ceil($system->maxhealth * $multiplier);
			$rngCurr+=1; //small systems usually have relatively high chance of being hit
			$rngTotal = $rngTotal+$rngCurr;
			$hitChart[$rngTotal] = $system->displayName;
		} 
		//is there anything to be hit? if not, just return facing Structure...
		if($rngTotal==0){
			$struct = $this->getStructureSystem($location); //if Structure destroyed, overkill to PRIMARY Structure
			return $struct;
		}
			
		//for non-Flash/Piercing, add PRIMARY to hit table...
		// $noPrimaryHits = ($weapon->noPrimaryHits || ($weapon->damageType == 'Piercing') || ($weapon->damageType == 'Flash')); //Original Logic - DK 13.01.26
        $noPrimaryHits = (($weapon->damageType == 'Piercing') || ($weapon->damageType == 'Flash')); //New Logic: Only Flash and Piercing remove functionality. $noPrimaryHits trait redirects logic later.
		if(!$noPrimaryHits){ 
			$multiplier = 0.1; //10% chance for PRIMARY penetration
			if($this->shipSizeClass<=1) $multiplier = 0.15;//for MCVs - 15%...
			$rngCurr =  ceil($rngTotal * $multiplier);
			$rngTotal = $rngTotal+$rngCurr;
			$hitChart[$rngTotal] = 'Primary';
		}	
			
		//now choose system from chart...
		$roll = Dice::d($rngTotal);
		$name = '';
		//$isSystemKiller = $weapon->systemKiller;
		while ($name == ''){
			if (isset($hitChart[$roll])){
				$name = $hitChart[$roll];
				/* this ability was never used, commenting out
				if($name == 'Structure' && $isSystemKiller) { //for systemKiller weapon, reroll Structure
					$isSystemKiller = false; //don't do that again
					$name = '';
					$roll = Dice::d($rngTotal); //new location roll
				}
				*/
			}else{
				$roll++;
				if($roll>$rngTotal)//out of range already!
				{
					return $this->getStructureSystem(0);
				}
			}
		}
		
		if($name == 'Primary'){ //redirect to PRIMARY!
            if($weapon->noPrimaryHits) return $this->getStructureSystem($location); //If weapon treats Primary as facing Structure - DK 13.01.26
			return $this->getHitSystemByDice($shooter, $fire, $weapon, 0, $sourceOverride);
		}
		$systems = $this->getSystemsByNameLoc($name, $location, $bearing, false); //do NOT accept destroyed systems!
		if(sizeof($systems)==0){ //if empty, just return Structure - whether destroyed or not
			$struct = $this->getStructureSystem($location);
			return $struct;
		}

        //prioritize in-arc systems - copied from byTable 16.01.2026
        $systemsInArc = array();
        foreach($systems as $systemInArc){
            if(mathlib::isInArc($bearing, $systemInArc->startArc, $systemInArc->endArc)){ //actually this system is in relevant arc!
                $systemsInArc[] = $systemInArc;
            }
        }
        if(sizeof($systemsInArc)>0) $systems = $systemsInArc; //some of indicated systems are in arc - they have to be targeted as priority!

		//Prefer Port/Stbd thrusters if on Primary and no thruster in arc
		else if ( ($location == 0) && (sizeof($systems)>0) && ($systems[0] instanceof Thruster) ){
			$preferredSystems = array();
            
            //Resolve bearing to 0-359 range just in case
            $relBearing = $bearing;
            while($relBearing < 0) $relBearing += 360;
            while($relBearing >= 360) $relBearing -= 360;

			foreach($systems as $currSys){
				$center = ($currSys->startArc + $currSys->endArc) / 2;
                if($currSys->startArc > $currSys->endArc){
                     $center = ($currSys->startArc + $currSys->endArc + 360) / 2;
                }
                while($center >= 360) $center -= 360;
                                
                $isPortThruster = ($center > 180) && ($center < 360); //Strictly Port side
                $isStbdThruster = ($center > 0) && ($center < 180); //Strictly Stbd side

                //Shot from Port?
                if( ($relBearing > 180) && ($relBearing < 360) ){ 
                    if($isPortThruster) $preferredSystems[] = $currSys;
                }
                //Shot from Stbd?
                else if( ($relBearing > 0) && ($relBearing < 180) ){
                    if($isStbdThruster) $preferredSystems[] = $currSys;
                }
			}
            
            if(sizeof($preferredSystems)>0) $systems = $preferredSystems;
            else if ( ($relBearing > 0) && ($relBearing < 360) && ($relBearing != 180) ) {
                // Targeted specific side, but no matching thrusters found (e.g. destroyed).
                // Do not fall back to wrong-side thrusters. Return Structure.
                return $this->getStructureSystem($location);
            }
		}
		
		// Prefer destroying weapons that need the longest to recharge:
		//   tier 1: weapons that fired this turn (turnsloaded resets to 1 next turn)
		//   tier 2: weapons recharging and not ready next turn either
		if ($systems[0] instanceof Weapon) {
			$firedThisTurn = array();
			$rechargingSystems = array();
			foreach ($systems as $sys) {
				if ($sys->loadingtime <= 1) continue; //1-turn weapons always ready, ignore
				if ($sys->firedOnTurn(TacGamedata::$currentTurn)) {
					$firedThisTurn[] = $sys;
				} else if ($sys->turnsloaded < ($sys->loadingtime - 1)) {
					$rechargingSystems[] = $sys;
				}
			}
			if (sizeof($firedThisTurn) > 0) $systems = $firedThisTurn;
			else if (sizeof($rechargingSystems) > 0) $systems = $rechargingSystems;
		}

		//now choose one of equal eligible systems (they're already known to be undestroyed)
        $roll = Dice::d(sizeof($systems));
        $system = $systems[$roll-1];
		return $system;

	} //end of function GetHitSystemByDice
		
        
        public static function hasBetterIniative($a, $b){
			if ($a->iniative > $b->iniative) return true;
			if ($a->iniative < $b->iniative) return false;

				if ($a->iniativebonus > $b->iniativebonus) return true;
				if ($b->iniativebonus > $a->iniativebonus) return false;

			if ($a->id > $b->id) {
				return true;
			} else{
				return false;    
			}
		
			/* replaced by changed logic above, to unify among various places in game
            if ($a->iniative > $b->iniative)
                return true;
            
            if ($a->iniative < $b->iniative)
                return false;

            if ($a->unmodifiedIniative != null && $b->unmodifiedIniative != null) {
                if ($a->unmodifiedIniative > $b->unmodifiedIniative)
                    return true;
            
                if ($a->unmodifiedIniative < $b->unmodifiedIniative)
                    return false;
            }
                
            if ($a->iniative == $b->iniative){
                if ($a->iniativebonus > $b->iniativebonus)
                    return true;
                
                if ($b->iniativebonus > $a->iniativebonus)
                    return false;
                
                if ($a->id > $b->id)
                    return true;
            }
            */
            return true; //should never reach here
        }
        
        public function getAllFireOrders($turn = -1)
        {	
            $orders = array();
            
            foreach ($this->systems as $system){
                $orders = array_merge($orders, $system->getFireOrders($turn));
            }
            
            return $orders;
        }
        
        protected function getUndamagedSameSystem($system, $location){
            foreach ($this->systems as $sys){
                // check if there is another system of the same class on this location.
                if($sys->location == $location && get_class($system) == get_class($sys) && !$sys->isDestroyed()){
                    return $sys;
                }
            }
            return null;
        } 
        
	/*note expected damage - important for deciding ambiguous shots!*/
	public function setExpectedDamage($hitLoc, $hitChance, $weapon, $shooter){
		//add to table private $expectedDamage = array(); //loc => dam; damage the unit is expected to take this turn
		if(($hitLoc==0) || ($hitChance<=0)) return; //no point checking, PRIMARY damage not relevant for this decision; same when hit chance is less than 0
		if(!isset($this->expectedDamage[$hitLoc])){
			$this->expectedDamage[$hitLoc] = 0;
		}		
		$structureSystem = $this->getStructureSystem($hitLoc);
		$armour = $structureSystem->getArmourComplete($this, $shooter, $weapon->weaponClass); 
		$expectedDamageMax = $weapon->maxDamage-$armour;
		$expectedDamageMin = $weapon->minDamage-$armour;
		$expectedDamageMax = max(0,$expectedDamageMax);
		$expectedDamageMin = max(0,$expectedDamageMin);
		$expectedDamage = ($expectedDamageMin+$expectedDamageMax)/4; //halve damage as not all would go to Structure! - hence /4 and not /2
		//reduce damage for non-Standard modes...
		switch($weapon->damageType) {
		    case 'Raking': //Raking damage gets reduced multiple times
			$expectedDamage = $expectedDamage * 0.9;
			break;
		    case 'Piercing': //Piercing does little damage to actual outer section...
			$expectedDamage = $expectedDamage * 0.4;
			break;
		    case 'Pulse': //multiple hits - assume half of max pulses hit!
			$expectedDamage = 0.5 * $expectedDamage * max(1,$weapon->maxpulses);
			break;			
		    default: //something else: can't be as good as Standard!
			$expectedDamage = $expectedDamage * 0.9;
			break;
		}
		//multiply by hit chance!
		$expectedDamage = $expectedDamage * min(100,$hitChance) /100;
		$this->expectedDamage[$hitLoc] += $expectedDamage;
	}//endof function setExpectedDamage
	    
	    
	    /*returns calculated ramming factor for ship (so will never use explosive charge if, say, Delegor or HK is rammed instead of ramming itself!*/
	    /*approximate raming factor as full Structure of undestroyed sections *110% */
	public function getRammingFactor(){
		$structuretotal = 0;
		$prevturn = max(0,TacGamedata::$currentTurn-1);
		$activeStructures = $this->getSystemsByName("Structure",true);//list of all Structure blocks (check for being destroyed will come later)
		foreach($activeStructures as $struct){
			if (!$struct->isDestroyed($prevturn)){ //if structure is not destroyed AS OF PREVIOUS TURN
				$structuretotal += $struct->maxhealth;
			}
		}
		$multiplier = 1.1;
		if ($this->shipSizeClass == 1) $multiplier = 1.2; //MCVs seem to use a bit larger multiplier...
		$dmg = ceil($structuretotal * $multiplier);
		return $dmg;
	} //endof function getRammingFactor      

    public function isSkinDancer(){
        return $this->skinDancer;
    }    


} //endof class BaseShip
    
class BaseShipNoAft extends BaseShip{
    //public $draziCap = true;//no longer used
    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name,$slot);
    }

    public function getLocations(){
        //debug::log("getLocations");         
        $locs = array();
        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 3, "min" => 180, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 150, "max" => 180, "profile" => $this->forwardDefense);

        return $locs;
    }
}

/*reversed Drazi capital ship - used in some scustom designs*/
class BaseShipNoFwd extends BaseShip{
    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name,$slot);
    }

    public function getLocations(){      
        $locs = array();
        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense); //Aft
        $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->sideDefense); //Port actual
        $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense); //Stbd actual
        $locs[] = array("loc" => 3, "min" => 330, "max" => 0, "profile" => $this->forwardDefense); //Port - from front
        $locs[] = array("loc" => 4, "min" => 0, "max" => 30, "profile" => $this->forwardDefense); //Stbd - from Front

        return $locs;
    }
}


class HeavyCombatVessel extends BaseShip{
    public $shipSizeClass = 2;

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name,$slot);
    }

    protected function addLeftFrontSystem($system){
        $this->addSystem($system, 31);
    }
    protected function addLeftAftSystem($system){
        $this->addSystem($system, 32);
    }
    protected function addRightFrontSystem($system){
        $this->addSystem($system, 41);
    }
    protected function addRightAftSystem($system){
        $this->addSystem($system, 42);
    }

    public function getLocations(){
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 1, "min" => 30, "max" => 90, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 90, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 210, "max" => 270, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 1, "min" => 270, "max" => 330, "profile" => $this->sideDefense);

        return $locs;
    }

}


class HeavyCombatVesselLeftRight extends BaseShip{

    //public $draziHCV = true; //no longer used
    public $shipSizeClass = 2;

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name,$slot);
    }


    public function getLocations(){
        $locs = array();
        $locs[] = array("loc" => 4, "min" => 0, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 4, "min" => 150, "max" => 180, "profile" => $this->forwardDefense);

        $locs[] = array("loc" => 3, "min" => 330, "max" => 360, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 3, "min" => 180, "max" => 210, "profile" => $this->forwardDefense);

        return $locs;
    }
}



class MediumShip extends BaseShip{
    public $shipSizeClass = 1;

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name, $slot);
    }

/* not actually needed, BaseShip routine will now handle it
    public function getFireControlIndex(){
        return 1;
    }
*/	

    public function getLocations(){
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);

        $locs[] = array("loc" => 1, "min" => 30, "max" => 90, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 90, "max" => 150, "profile" => $this->sideDefense);

        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);

        $locs[] = array("loc" => 2, "min" => 210, "max" => 270, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 1, "min" => 270, "max" => 330, "profile" => $this->sideDefense);

        return $locs;
    }

} //end of class MediumShip

/* essentially treated as medium ship, except all 3 hit charts should be the same and point to PRIMARY systems. 
A lot of systems are technically present on LCV but not really there by rules, hence are made unhittable.
LCVs typically require hagar space, too.
*/
class LCV extends MediumShip{
	public $hangarRequired = 'LCVs';
}

class Terrain extends MediumShip{
    public $shipSizeClass = 5; //5 is used to identify Terrain is certain Front End functions.
    public $Enormous = true;
    public $hexOffsets = []; //For irregular-shaped terrain, this lest's you specifiy specific hexes occupied in relation to terrain unit's hex.

    public function stripForJson() {
        $strippedShip = parent::stripForJson();
        //$strippedShip->hexOffsets = $this->hexOffsets;
        return $strippedShip;
    }
}


class MediumShipLeftRight extends MediumShip{

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name, $slot);
    }


    public function getLocations(){
        $locs = array();

        $locs[] = array("loc" => 4, "min" => 0, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 4, "min" => 150, "max" => 180, "profile" => $this->forwardDefense);

        $locs[] = array("loc" => 3, "min" => 330, "max" => 360, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 3, "min" => 180, "max" => 210, "profile" => $this->forwardDefense);

        return $locs;
    }
}



class LightShip extends BaseShip{ //is this used anywhere?...

    public $shipSizeClass = 0;

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name, $slot);
    }
	
/* not actually needed, BaseShip routine will now handle it
    public function getFireControlIndex(){
        return 1;
    }
*/	

} //end of class LightShip



class OSAT extends MediumShip{
    public $osat = true;
    public $canvasSize = 100;  

    public function isDisabled(){
        return false;
    }


    public function getLocations(){
        $locs = array();

        $locs[] = array("loc" => 0, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 0, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 0, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 0, "min" => 210, "max" => 330, "profile" => $this->sideDefense);

        return $locs;
    }
}


class Mine extends OSAT{
    public $mine = true;
    public $canvasSize = 80;  
    public $trueStealth = true;
    public $mineType = ''; //Captor, DEW or Proximity
    public $signature = 0;
    public $activated = false;
    public $detectedSignature = 0; //Adjusted signature for detected DEW mines, also seves as a way to identift these type of mines.
    public $spawned = -1; //To denote the turn a unit was spawned by DURING the game, e.g. doesn't count for CPV etc, show in Replay prior to it spawning
    public $canPreOrder = true;//Needed to set ranges for spawned Mines in Pre-Turn phase.
    protected $variableDamage = 0; //Amount by which mine set damage can vary, looked for in Enhancements
    protected $commandControl = false;
    public $multiSettings = false;


    public function isDisabled(){
        return false;
    }

    public function getVariableDamage(){
        return $this->variableDamage;
    }

    public function setCommandControl($setting){
        $this->commandControl = $setting;
    }

    public function getCommandControl(){
        return $this->commandControl;
    }


    //Mines: signature reduces effective defense (more visible = easier to hit).
    //Negative signature improves it (subtracting a negative). Floored at 0.
    public function getEffectiveForwardDefense(){
        return max(0, $this->forwardDefense - $this->signature);
    }

    public function getEffectiveSideDefense(){
        return max(0, $this->sideDefense - $this->signature);
    }

    public function getLocations(){
        $effFwd  = $this->getEffectiveForwardDefense();
        $effSide = $this->getEffectiveSideDefense();

        $locs = array();

        $locs[] = array("loc" => 0, "min" => 330, "max" => 30,  "profile" => $effFwd);
        $locs[] = array("loc" => 0, "min" => 30,  "max" => 150, "profile" => $effSide);
        $locs[] = array("loc" => 0, "min" => 150, "max" => 210, "profile" => $effFwd);
        $locs[] = array("loc" => 0, "min" => 210, "max" => 330, "profile" => $effSide);

        return $locs;
    }

    public function stripForJson() {
        $strippedShip = parent::stripForJson();
        $strippedShip->forwardDefense = $this->getEffectiveForwardDefense();
        $strippedShip->sideDefense    = $this->getEffectiveSideDefense();

        if($this->detectedSignature !== -1){
            $strippedShip->signature = $this->signature; //Need to send updated Signature values for DEW mine weapons.
            if ($this->commandControl) $strippedShip->commandControl = $this->commandControl; //If true front end needs to know for firing checks.
            //$strippedShip->multiSettings = $this->multiSettings;
        }
        return $strippedShip;
    }

}


class StarBase extends BaseShip{
    public $base = true;
    public $Enormous = true;

    public function isDisabled(){
        if ($this->isPowerless())
            return true;

        $cncs = $this->getControlSystems();



        if (sizeof($cncs) > 0){
            $intact = sizeof($cncs);

            foreach ($cncs as $cnc){
                if ($cnc->destroyed){
                    $intact--;
                }
            }
            if ($intact == 0){
                return true;
            }

            usort($cncs, function($a, $b){
                if ($a->getRemainingHealth() > $b->getRemainingHealth()){
                    return 1;
                }
                else return -1;
            });

            $CnC = $cncs[0];
        }

        if ($CnC->hasCritical("ShipDisabledOneTurn", TacGamedata::$currentTurn) || $CnC->hasCritical("ShipDisabled", TacGamedata::$currentTurn)){
            debug::log("is effeictlvy PHP Disabled due to ".$CnC->id);
            return true;
        }

        return false;
    }


    public function getControlSystems(){
        $array = array();

        foreach ($this->systems as $system){
            if ($system instanceof CnC){
                $array[] = $system;

            }
        }

        return $array;
    }


    protected function addLeftFrontSystem($system){
        $this->addSystem($system, 31);
    }
    protected function addLeftAftSystem($system){
        $this->addSystem($system, 32);
    }
    protected function addRightFrontSystem($system){
        $this->addSystem($system, 41);
    }
    protected function addRightAftSystem($system){
        $this->addSystem($system, 42);
    }


    public function isDestroyed($turn = false){
        //Hangar Ops Stage 7: see BaseShip::isDestroyed for rationale — bases
        //don't dock, so this branch is essentially dead, but stays consistent
        //with the parent contract in case a future base-class carrier appears.
        if ($this->removed && ($turn === false || $turn >= $this->removedTurn)) return true;

        foreach($this->systems as $system){
            if ($system instanceof Reactor && $system->location == 0 &&  $system->isDestroyed($turn)){
                return true;
            }
            if ($system instanceof Structure && $system->location == 0 && $system->isDestroyed($turn)){
                return true;
            }
        }
        return false;
    }

    public function getMainReactor(){
        foreach ($this->systems as $system){
            if ($system instanceof Reactor && $system->location == 0){
                return $system;
            }
        }
    }

    public function destroySection($reactor, $gamedata){
        $locToDestroy = $reactor->location;
        $sysArray = array();

        //debug::log("killing section: ".$locToDestroy);
        foreach ($this->systems as $system){
            if ($system->location == $reactor->location){
                if (! $system->destroyed){
                    $sysArray[] = $system;
                }
            }
        }
		
		//try to make actual attack to show in log - use Ramming Attack system!				
		$rammingSystem = $this->getSystemByName("RammingAttack");
		if($rammingSystem){ //actually exists! - it should on every ship!				
			$newFireOrder = new FireOrder(
				-1, "normal", $this->id, $this->id,
				$rammingSystem->id, -1, $gamedata->turn, 1, 
				100, 100, 1, 1, 0,
				0,0,'Plasma',10000
			);
			$newFireOrder->pubnotes = "Sub-reactor explosion - section destroyed.";
			$newFireOrder->addToDB = true;
			$rammingSystem->fireOrders[] = $newFireOrder;
		}else{
			$newFireOrder=null;
		}

        foreach ($sysArray as $system){
            $remaining = $system->getRemainingHealth();
            $damageEntry = new DamageEntry(-1, $this->id, -1, $gamedata->turn, $system->id, $remaining, 0, 0, -1, true, false, "", "Plasma");
            $damageEntry->updated = true;
            $system->damage[] = $damageEntry;			
			if($rammingSystem){ //add extra data to damage entry - so firing order can be identified!
					$damageEntry->shooterid = $this->id; //additional field
					$damageEntry->weaponid = $rammingSystem->id; //additional field
			}
        }
    }
}


class StarBaseSixSections extends StarBase{

    /* no longer needed, keeping code just in case
    public function getPiercingLocations($shooter, $pos, $turn, $weapon){
    $location = $this->getHitSection($shooter, $turn, true);

        $locs = array();
        $finallocs = array();

        if ($location == 1 || $location == 2){
            $locs[] = 1;
            $locs[] = 0;
            $locs[] = 2;
        }
        else if ($location == 31 || $location == 42){
            $locs[] = 31;
            $locs[] = 0;
            $locs[] = 42;
        }
        else if ($location == 32 || $location == 41){
            $locs[] = 32;
            $locs[] = 0;
            $locs[] = 41;
        }

        foreach ($locs as $loc){
            $structure = $this->getStructureSystem($loc);
            if ($structure != null && !$structure->isDestroyed()){
                $finallocs[] = $loc;
            }
        }

        return $finallocs;

    }
*/


    public function getLocations(){
        //debug::log("getLocations");         
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 300, "max" => 60, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 41, "min" => 0, "max" => 120, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 42, "min" => 60, "max" => 180, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 120, "max" => 240, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 32, "min" => 180, "max" => 300, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 31, "min" => 240, "max" => 360, "profile" => $this->forwardDefense);

        return $locs;
    }
}



class StarBaseFiveSections extends StarBase{
    /* no longer needed, keeping code just in case
public function getPiercingLocations($shooter, $pos, $turn, $weapon){
    $location = $this->getHitSection($shooter, $turn, true);

        $locs = array();
        $finallocs = array();
        if ($location == 1 ){
            $locs[] = 1;
            $locs[] = 0;
            $locs[] = 41; //should be choice, let's go for '3 sections further'
        }
        else if ($location == 41){
            $locs[] = 41;
            $locs[] = 0;
            $locs[] = 31;
        }
        else if ($location == 42){
            $locs[] = 42;
            $locs[] = 0;
            $locs[] = 1;
        }
        else if ($location == 32){
            $locs[] = 32;
            $locs[] = 0;
            $locs[] = 41;
        }
        else if ($location == 31){
            $locs[] = 31;
            $locs[] = 0;
            $locs[] = 42;
        }


        foreach ($locs as $loc){
            $structure = $this->getStructureSystem($loc);
            if ($structure != null && !$structure->isDestroyed()){
                $finallocs[] = $loc;
            }
        }

        return $finallocs;

    }
*/

    public function getLocations(){
        //debug::log("getLocations");         
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 270, "max" => 90, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 41, "min" => 330, "max" => 150, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 42, "min" => 30, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 32, "min" => 90, "max" => 270, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 31, "min" => 150, "max" => 330, "profile" => $this->forwardDefense);

        return $locs;
    }
} //end of StarBaseFiveSections

//Llort Base
class UnevenStarBaseEightSections extends StarBase{


    public function getLocations(){
        //debug::log("getLocations");         
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 300, "max" => 60, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 120, "max" => 240, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 180, "max" => 360, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 31, "min" => 240, "max" => 60, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 32, "min" => 180, "max" => 300, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 4, "min" => 0, "max" => 180, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 41, "min" => 0, "max" => 120, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 42, "min" => 60, "max" => 240, "profile" => $this->sideDefense);        

        return $locs;
    }
} //end of StarBaseEightSections



class SmallStarBaseFourSections extends BaseShip{ //just change arcs of sections...
	public $enhancementOptionsDisabled = array('DEPLOY'); //Base cannot jump into a scenario!     
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->base = true;
        $this->smallBase = true;

        $this->shipSizeClass = 3;
        $this->iniativebonus = -200; //no voluntary movement anyway
        $this->turncost = 0;
        $this->turndelaycost = 0;
    }

    protected function addLeftFrontSystem($system){
        $this->addSystem($system, 31);
    }
    protected function addLeftAftSystem($system){
        $this->addSystem($system, 32);
    }
    protected function addRightFrontSystem($system){
        $this->addSystem($system, 41);
    }
    protected function addRightAftSystem($system){
        $this->addSystem($system, 42);
    }

    public function getLocations(){
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 270, "max" => 90, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 90, "max" => 270, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 180, "max" => 0, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 0, "max" => 180, "profile" => $this->forwardDefense);

        return $locs;
    }
} //end of SmallStarBaseFourSections


class SmallStarBaseThreeSections extends SmallStarBaseFourSections{

    public function getLocations(){
        $locs = array();
		//I settled for exactly 120 degrees between sections, accepting arc going through half-hex
        $locs[] = array("loc" => 1, "min" => 270, "max" => 90, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 150, "max" => 330, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 30, "max" => 210, "profile" => $this->forwardDefense);

        return $locs;
    }
} //end of StarBaseThreeSections

class UnevenBaseFourSections extends BaseShip{ //4-sided base which has differend fwd and side profile
	public $enhancementOptionsDisabled = array('DEPLOY'); //Base cannot jump into a scenario!   
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->base = true;
        $this->smallBase = true;

        $this->shipSizeClass = 3;
        $this->iniativebonus = -200; //no voluntary movement anyway
        $this->turncost = 0;
        $this->turndelaycost = 0;
    }

    public function getLocations(){
        $locs = array();
		//fwd: 270..90, incl. fwd profile at 330..30
		//aft: 90..270, incl. fwd profile at 150..210
		//Port: 180..0, incl. fwd profile at 180..210 and 330..0
		//Stbd: 0..180, incl. fwd profile at 0..30 and 150..180

        $locs[] = array("loc" => 1, "min" => 270, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 1, "min" => 30, "max" => 90, "profile" => $this->sideDefense);
		
		
        $locs[] = array("loc" => 2, "min" => 90, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 210, "max" => 270, "profile" => $this->sideDefense);
		
		
        $locs[] = array("loc" => 3, "min" => 180, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 3, "min" => 330, "max" => 0, "profile" => $this->forwardDefense);
		
        $locs[] = array("loc" => 4, "min" => 0, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 4, "min" => 150, "max" => 180, "profile" => $this->forwardDefense);

        return $locs;
    }
} //end of UnevenBaseFourSections


class SixSidedShip extends BaseShip{
    public $SixSidedShip = true;
//	public $mindrider = false; 
     
    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name,$slot);
    }
    
    protected function addLeftFrontSystem($system){
        $this->addSystem($system, 31);
    }
    protected function addLeftAftSystem($system){
        $this->addSystem($system, 32);
    }
    protected function addRightFrontSystem($system){
        $this->addSystem($system, 41);
    }
    protected function addRightAftSystem($system){
        $this->addSystem($system, 42);
    }

    public function getLocations(){
        //debug::log("getLocations");         
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 41, "min" => 30, "max" => 90, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 42, "min" => 90, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 32, "min" => 210, "max" => 270, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 31, "min" => 270, "max" => 330, "profile" => $this->sideDefense);

        return $locs;
    } 
    		
} //end of SixSidedShip


class SixSidedHCV extends SixSidedShip{
    public $shipSizeClass = 2;

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name,$slot);
    }

    //Locations are same as HCV, because we use setStructureHome() to assign front and aft location to any system place on 31, 32, 41, 42 etc.
    public function getLocations(){
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 1, "min" => 30, "max" => 90, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 90, "max" => 150, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 210, "max" => 270, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 1, "min" => 270, "max" => 330, "profile" => $this->sideDefense);

        return $locs;
    }
    
}


//Vorlon Capital Ships are made using 6-sided layout - with side-aft being actual sides, and side-front a pseudo-section to fit Lightning Cannons that do not fall off
class VorlonCapitalShip extends SixSidedShip{	

    protected function addLeftSystem($system){//Left = Left Aft
        $this->addLeftAftSystem($system);
    }
    protected function addRightSystem($system){//Right = Right Aft
        $this->addRightAftSystem($system);
    }

    public function getLocations(){
        //debug::log("getLocations");         
        $locs = array();

		//locations 41 and 31 CANNOT be targeted, because it would be picked if PRIMARY Structure was more healthy than side
		///41 and 42 systems can be targeted as MCV systems would have been (eg. weapons from their arc)
        $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 32, "min" => 210, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 42, "min" => 30, "max" => 150, "profile" => $this->sideDefense);

        return $locs;
    }		
} //end of VorlonCapitalShip

class VreeCapital extends SixSidedShip{

    protected $VreeHitLocations = true; //Value to indicate that all gunfire from the same ship may not hit same side on Vree capital ships

    //Vree saucer: the six "Outer Structure" blocks are a RING, not the compartments the
    //systems live in, so a breached block does not destroy what is shown in it.
    //See $systemsSurviveStructureLoss on BaseShip.
    protected $systemsSurviveStructureLoss = true;

    public function getLocations(){
        //debug::log("getLocations");         
        $locs = array();

        $locs[] = array("loc" => 1, "min" => 300, "max" => 60, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 41, "min" => 0, "max" => 120, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 42, "min" => 60, "max" => 180, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 2, "min" => 120, "max" => 240, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 32, "min" => 180, "max" => 300, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 31, "min" => 240, "max" => 360, "profile" => $this->sideDefense);

        return $locs;
    }
} //end of VreeCapital


class VreeHCV extends HeavyCombatVessel{

    protected $VreeHitLocations = true; //Value to indicate that all gunfire from the same ship may not hit same side on Vree capital ships

    //Same outer-structure ring as the capitals — see VreeCapital above.
    protected $systemsSurviveStructureLoss = true;

    public $shipSizeClass = 2;
        
} //end of VreeHCV


class MindriderCapital extends SixSidedShip{
	
	public $ignoreManoeuvreMods = true;
	public $mindrider = true;

    public function getLocations(){
        $locs = array();
        $locs[] = array("loc" => 31, "min" => 270, "max" => 360, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 41, "min" => 0, "max" => 90, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 32, "min" => 180, "max" => 270, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 42, "min" => 90, "max" => 180, "profile" => $this->sideDefense);
        return $locs;
    }

}

class MindriderHCV extends SixSidedShip{

	public $shipSizeClass = 2;	
	public $ignoreManoeuvreMods = true;
	public $mindrider = true;	
	

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name,$slot);
    }

    protected function addLeftSystem($system){//Left = Left Front
        $this->addLeftFrontSystem($system);
    }
    protected function addRightSystem($system){//Right = Right Front
        $this->addRightFrontSystem($system);
    }

    public function getLocations(){
        //debug::log("getLocations");         
        $locs = array();

		//locations 42 and 32 CANNOT be targeted, because it would be picked if PRIMARY Structure was more healthy than side
        $locs[] = array("loc" => 2, "min" => 120, "max" => 240, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 31, "min" => 240, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 41, "min" => 30, "max" => 120, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 31, "min" => 330, "max" => 0, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 41, "min" => 0, "max" => 30, "profile" => $this->sideDefense);        

        return $locs;
    }

}//endof MindriderHCV


class MindriderMCV extends MediumShip{
	
	public $ignoreManoeuvreMods = true;
	public $mustPivot = true;
	public $mindrider = true;		

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name,$slot);
    }

}//endof MindriderMCV







    
?>
