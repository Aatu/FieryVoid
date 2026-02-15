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
	public $combatValue = 100; //current combat value, as percentage of original
    public $faction = null;
	public $factionAge = 1; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
    public $isd = 0; 
    public $slot;
    public $unavailable = false;
    public $minesweeperbonus = 0;
    public $base = false;
    public $smallBase = false;
	public $nonRotating = false; //some bases do not rotate - this attribute is used in combination with $base or $smallBase
	public $osat = false; //true if object is OSAT (this includes MicroSATs and mines)
	public $mine = false;
    public $SixSidedShip = false;
	public $isCombatUnit = true; //is this a combat unit (as opposed to non-combat - transport, freighter, civilian, explorer, diplomatic ship, yacht...)

	//non-combat ships cannot be taken in pickup battles by standard tourtnament rules
	//rule of thumb is that if it has cargo bays, then it's not a combat ship - but it's far from proof
	//eg. Pak'ma'ra and Orieni capital ships (combat ones) do have cargo bays, while eg. Emperor's transport or Grey Sharlin (non-combat ships) do not
	//by core definition, combat ship is one that is intended to be present in fleet sent into combat zone.

	public $toHitBonus = 0; //Used to increase hit chance of all weapons fired by a ship e.g. Elite Crew / Markab enhancements.		
    public $critRollMod = 0; //penalty to critical damage roll: positive means crit is more likely, negative less likely (for all systems)

	
	public $halfPhaseThrust = 0; //needed for half phasing; equal to thrust from two BioThrusters on a given ship; 0 for ships that cannot half phase, eg. vast majority
    

    public $jinkinglimit = 0; //Some ships can jink, e.g. Torvalus MCVs
	
    public $enabledSpecialAbilities = array();

    public $canvasSize = 200;

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
    protected $skinDancer = false; //Let';s ships of unusual size skin dance e.g. Toravlus capitals ships.   	

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
	
    public $advancedArmor = false; //set to true if ship is equipped with advanced armor!
	public $hardAdvancedArmor = false; // set to true if ship is equipped with hardented advanced armor - GTS
	
	
	public $hangarRequired = ''; //usually empty, but some ships (LCVs primarily) do require hangar space!	
	public $unitSize = 1; //typically ships are berthed in dedicated space, 1 per slot - but other arrangements are certainly possible.
	
	protected $adaptiveArmorController = null; //Adaptive Armor Controller object (if present)
	protected $IFFSystem = false;   
	    
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
			return $this->IFFSystem;    
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
			}
		    if ($this instanceof FighterFlight){
			    $firstFighter = $this->getSampleFighter();
			    if ($firstFighter){
			    	$mod += -5* $firstFighter->hasCritical("tmpinidown", $gamedata->turn);				    
			    }
		    }
	    }
	    return $mod;
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
		
		//destroyed ship gets no value UNLESS it successfully jumped to Hyperspace
		if($this->isDestroyed()){
            if(!$this instanceof FighterFlight && !$this->base && !$this->osat){            
                $jumpEngine = $this->getSystemByName("JumpEngine");
                // Check if the ship has a jump engine                
                if ($jumpEngine) {                
                    //Check if it's jumped, instead of being destroyed.
                    if($jumpEngine->hasJumped()){                   
                        //Do NOT zero $effectiveValue if ship has jumped.               
                        $effectiveValue = $jumpEngine->getCVBeforeJump();                    
                        return $effectiveValue;
                    }    
                }
            }     
            //No jump engine, or hasn't jumped, set value to 0 as normal.
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
				if (!$system->isDestroyed()) {				
					$systemCurr = $system->getRemainingHealth();
					$systemDmg = $systemMax - $systemCurr;
				}

				//classify system appropriately
				if ($system instanceOf Structure) { //Structure block
					$structCurr += $systemCurr + ($systemDmg * $structDmgMultiplier);
					$structMax += $systemMax;
				} else if (($system instanceOf Weapon) || ($system instanceOf ElintScanner)) { //weapon! (count ElInt Scanner as a weapn here)
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
        foreach ($this->systems as $system){
            if ($system instanceof GrapplingClaw)  $marines += $system->ammunition;
        }

		$totalMarines = max(0, $marines);
		
		return $totalMarines;
	}

	
    public function stripForJson() {
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
        $strippedShip->EW = $this->EW;
        $strippedShip->movement = $this->movement; 
        $strippedShip->faction = $this->faction; 
        $strippedShip->phpclass = $this->phpclass;
        $strippedShip->skinDancing = $this->skinDancing;
        
        $strippedShip->systems = array_map( function($system) {return $system->stripForJson();}, $this->systems);

        //With changes to how we cache ships, we sadly have to re-do this each time. DK - Dec 2025
        $this->notesFill();
		$strippedShip->notes = $this->notes;                

		$strippedShip->combatValue = $this->calculateCombatValue();
		$strippedShip->pointCostEnh = $this->pointCostEnh;
		
		//unit enhancements
		if($this->enhancementTooltip !== ''){ //enhancements exist!			
			$strippedShip->enhancementTooltip = $this->enhancementTooltip; 
			$strippedShip = Enhancements::addUnitEnhancementsForJSON($this, $strippedShip);//modifies $strippedShip  object
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
	
		
		$strippedShip->enhancementOptions = array(); //no point in sending options information...
        return $strippedShip;
    }
	    
        public function getInitiativebonus($gamedata){
            if($this->faction == "Abbai Matriarchate"){
                return $this->doAbbaiInitiativeBonus($gamedata);
            }
            if($this->faction == "Centauri Republic"){
                return $this->doCentauriInitiativeBonus($gamedata);
            }
            if($this->faction == "Dilgar Imperium"){
                return $this->doDilgarInitiativeBonus($gamedata);
            }
            if($this->faction == "Narn Regime"){
                return $this->doNarnInitiativeBonus($gamedata);
            }
            if($this->faction == "Yolu Confederation"){
                return $this->doYoluInitiativeBonus($gamedata);
			}
			if ($this->faction == "Earth Alliance" || 
				$this->faction == "Earth Alliance (defenses)" || 
				$this->faction == "Earth Alliance (early)" ||
				$this->faction == "Earth Alliance (custom)"){
                return $this->doEAInitiativeBonus($gamedata);
            }
			if($this->faction == "Raiders"){
                return $this->doRaidersInitiativeBonus($gamedata);
            }
            if(($this->faction == "Pak'ma'ra Confederacy") && (!($this instanceof FighterFlight))	){
                return $this->doPakmaraInitiativeBonus($gamedata);
            }
		   if($this->faction == "Hyach Gerontocracy"){
		        return $this->doHyachInitiativeBonus($gamedata);
		    }            
			if(($this->faction == "Gaim Intelligence") && ($this instanceOf gaimMoas)){  //GTS
                return $this->doGaimInitiativeBonus($gamedata);
            }
            return $this->iniativebonus;
        }
        
		
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
    
    
        private function doPakmaraInitiativeBonus($gamedata){
        	
	        $mod = 0;
			$alivePakShips = 0;
				
				foreach($gamedata->ships as $ship){
	                if(
	                     ($ship->faction == "Pak'ma'ra Confederacy") //Correct faction
	                    && ($this->userid == $ship->userid) //of same player
	                    && (!($ship instanceOf FighterFlight)) //actually a ship
	                    && (!$ship->isDestroyed()) //alive
	                   ){
	                            $alivePakShips++;
	                    }
						$mod = floor(($alivePakShips)/3); //Divide by three and round down
	                    }
	                
	        //    debug::log($this->phpclass."- bonus: ".$mod);
	        return $this->iniativebonus - $mod*5;
    	} //end of doPakmaraInitiativeBonus    


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
        }
    }                
	
	/*calls systems to act on notes just loaded if necessary*/
	public function onIndividualNotesLoaded($gamedata) {
		foreach ($this->systems as $system){
            $system->onIndividualNotesLoaded($gamedata);
        }
	}

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

    public function checkStealth($gamedata)
    {         
        if($this->faction == "Torvalus Speculators"){
            $shadingField = $this->getSystemByName("ShadingField");
            if($shadingField) $shadingField->checkStealthNextPhase($gamedata);
        }

        //Trek block.
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
                if($system->preFires && ($system->turnsloaded >= $system->loadingtime) && !$system->autoFireOnly){ //ready to fire!
                    $readyToFire = true;
                    break; //At least one weapon can pre fire, exit loop.
                }    
            }
            /*else if($system->preFires){ //Only weapons in game atm
                    $readyToFire = true;
                    break; //At least one non-weapon system can pre fire, exit loop.            
            }*/
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
								 
		$this->systems[$i] = $system;            

		
		if ($system instanceof Structure){
			$this->structures[$loc] = $system->id;	
		}else if(($system->startArc ==0)&&($system->endArc ==0)){ //20.01.2025 - add arc equal to section arc, if not set explicitly
			//if arc is not set - copy from location!
			if($loc==0){ //PRIMARY
				$system->startArc = 0;
				$system->endArc = 360;
			} else {
				$locations = $this->getLocations();
				foreach($locations as $line) if ($line["loc"]==$loc){
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
		
		/* fill notes with information contained in various attributes, not so readily accessible to player*/
		protected function notesFill($sampleFighter = null){
			//if (TacGamedata::$currentTurn >= 1){ //in later turns notes will be displayed from pre-compiled cache! no point generating them every time
			//	return;
			//}
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
						$this->notes .= 'OSAT';
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
                        $this->notes .= 'Enormous Terrain';
                        break;                        
				default: //should not happen!
					$this->notes .= 'Unit size not identified!';	
					break;
			}//unit size described, which also guarantees existence of previous entries!
			//mark if not a combat unit!
			if(!$this->isCombatUnit) $this->notes .= '<br>Non-combatant!';
			//required hangar
			if($this->hangarRequired!='') { 
				$this->notes .= '<br>Requires hangar space: ' . $this->hangarRequired;			
				if($this->unitSize!=1) $this->notes .= ' (' . $this->unitSize . ' per slot)';
			}
			//Agile status
			if($this->agile) $this->notes .= '<br>Agile';	    
			//Gravitic Drive
			if($this->gravitic) $this->notes .= '<br>Gravitic Drive';	
			//Minesweeper
			if($this->minesweeperbonus > 0) $this->notes .= '<br>Minesweeper: ' . $this->minesweeperbonus;	
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
					}if ($reactor instanceof MagGravReactor && !$this->isTerrain()) {
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
            $m = 0;
            
            if (!is_array($this->movement))
                return null;
            
            foreach($this->movement as $elementKey => $move) {
                $m = $move;
            } 
            
            return $m;
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
		    if ( (STRCASECMP($system->displayName, $name)==0) && ($system->location == $location) ){
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
			$listOfPotentialSystems = $systemhit->systems;
		}else{ //all systems of a ship
			$listOfPotentialSystems = $this->systems;
		}		

		$shots = 1;
		if($weapon && $weapon->isLinked) $shots = $weapon->shots;
        if($weapon && $weapon->damagesUnderShield() && !$isUnderShield) $isUnderShield = true;  //Some weapon weapons might bypass shield-type protections, so only things like Diffsuers and Bulkheads would apply.

        //foreach($this->systems as $system){
		foreach($listOfPotentialSystems as $system){

			$value=$system->doesProtectFromDamage($expectedDmg, $systemhit, $damageWasDealt, $shots, $isUnderShield);
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
        if (!is_array($this->movement)){
            return array("x"=>0, "y"=>0);
        }
        foreach ($this->movement as $move){
            $movement = $move;
        }
        return $movement->getCoPos();

    }

    public function getHexPos() : OffsetCoordinate{

        $movement = null;
        if (!is_array($this->movement)){
            return new OffsetCoordinate(0, 0);
        }

        foreach ($this->movement as $move){
            $movement = $move;
        }

        return $movement->position;
    }



    public function getPreviousCoPos(){
        $pos = $this->getCoPos();

        for ($i = sizeof($this->movement)-1; $i>=0; $i--){
            $move = $this->movement[$i];
            $pPos = $move->getCoPos();

            if ( $pPos["x"] != $pos["x"] || $pPos["y"] != $pos["y"])
                return $pPos;
        }

        return $pos;
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
            if ( ($EW->type == "OEW" || ($EW->type == "CCEW" && $EW->amount>0)) && $EW->turn == $turn)
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

        foreach ($this->movement as $move){
            $movement = $move;
        }

        return $movement->getFacingAngle();
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

    public function getTurnDeployed($gamedata){

        if ($this->osat || $this->base || $this->isTerrain()) return 1; //Bases, Terrain and OSATs never 'jump in'.

        $slot = $gamedata->getSlotById($this->slot);
        $depTurn = $slot->depavailable;

        if($slot->surrendered !== null){
            if($slot->surrendered <= $gamedata->turn){ //Surrendered on this turn or before, no longer present in game.
                $depTurn = 999; //Artifically high number, so surrendered ships are no longer considered by game! - DK
            }
        }    
        
        return $depTurn;           
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


    public function getHitSystem($shooter, $fireOrder, $weapon, $gamedata, $location = null){
        /*if something has to choose system by firing position, use getHitSystemPos instead*/           
        if (isset($this->hitChart[0])){
            $system = $this->getHitSystemByTable($shooter, $fireOrder, $weapon, $location);             
        }
        else {        	
            $system = $this->getHitSystemByDice($shooter, $fireOrder, $weapon, $location);            
        }          
        return $system;
    }



    public function getHitSystemByTable($shooter, $fire, $weapon, $location){
        /*DOES NOT take care of overkill!!! returns section structure if no system can be hit, whether that section is still alive or not*/
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
		if($weapon->ballistic){
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
            return $this->getHitSystemByTable($shooter, $fire, $weapon, 0);
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

        //now choose one of equal eligible systems (they're already known to be undestroyed... well, they may be destroyed, but then they're to be returned anyway)
        $roll = Dice::d(sizeof($systems));
        $system = $systems[$roll-1];
	        
        return $system;

    } //end of function getHitSystemByTable


    public function getHitSystemByDice( $shooter, $fire, $weapon, $location){
        /*same as by table, but prepare table out of available systems...*/
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
		if($weapon->ballistic){
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
			return $this->getHitSystemByDice($shooter, $fire, $weapon, 0);
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
	public $enhancementOptionsDisabled = array('DEPLOY'); //Base cannot jump into a scenario!    

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




class StarBase extends BaseShip{
    public $base = true;
    public $Enormous = true;
	public $enhancementOptionsDisabled = array('DEPLOY'); //Base cannot jump into a scenario! 

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
        
    public $shipSizeClass = 2;
        
} //end of VreeHCV


class MindriderCapital extends SixSidedShip{
	
	public $ignoreManoeuvreMods = true;
	public $mindrider = true;

    public function getLocations(){
        $locs = array();
        $locs[] = array("loc" => 31, "min" => 270, "max" => 360, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 41, "min" => 0, "max" => 90, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 32, "min" => 90, "max" => 180, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 42, "min" => 180, "max" => 270, "profile" => $this->sideDefense);
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
