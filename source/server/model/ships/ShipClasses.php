<?php
class BaseShip {

    public $shipSizeClass = 3; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
    public $Enormous = false; //size class 4 is NOT implemented!!! for semi-Enormous unit, set this variable to True
    public $imagePath, $shipClass;
    public $systems = array();
    public $EW = array();
    public $fighters = array();
    public $hitChart = array();
    public $notes = '';//notes to be displayed on fleet selection screen

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
    public $pointCost = 0;
    public $faction = null;
	public $factionAge = 1; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial 
    public $slot;
    public $unavailable = false;
    public $minesweeperbonus = 0;
    public $base = false;
    public $smallBase = false;
	public $nonRotating = false; //some bases do not rotate - this attribute is used in combination with $base or $smallBase
	public $osat = false; //true if object is OSAT (this includes MicroSATs and mines)
	
    public $critRollMod = 0; //penalty tu critical damage roll: positive means crit is more likely, negative less likely (for all systems)

	
    

    public $jinkinglimit = 0; //just in case there will be a ship actually able to jink; NOT SUPPORTED!

    public $enabledSpecialAbilities = array();

    public $canvasSize = 200;

    public $outerSections = array(); //for determining hit locations in GUI: loc, min, max, call (loc is location id, min/max is for arc, call is true if location systems can be called)

    protected $activeHitLocations = array(); //$shooterID->targetSection ; no need for this to go public! just making sure that firing from one unit is assigned to one section
    //following values from DB
    public $id, $userid, $name, $campaignX, $campaignY;
    public $rolled = false;
    public $rolling = false;
	public $EMHardened = false; //EM Hardening (Ipsha have it) - some weapons would check for this value!

    public $team;
    private $expectedDamage = array(); //loc=>dam; damage the unit is expected to take this turn (at outer locations), to decide where to take ambiguous shots
    
    public $slotid;

    public $movement = array();
    	    
		//unit enhancements
		public $enhancementOptions = array(); //ID,readableName,numberTaken,limit,price,priceStep
		public $enhancementOptionsEnabled = array(); //enabled non-standard options - just IDs
		public $enhancementOptionsDisabled = array(); //disabled standard options - jsut IDs
		public $enhancementTooltip = ""; //to be displayed with ship name / class	
	
    public $advancedArmor = false; //set to true if ship is equipped with advanced armor!
	
	
	public $hangarRequired = ''; //usually empty, but some ships (LCVs primarily) do require hangar space!	
	public $unitSize = 1; //typically ships are berthed in dedicated space, 1 per slot - but other arrangements are certainly possible.
	    
	    public function getAdvancedArmor(){
		return $this->advancedArmor;    
	    }
	    
        function __construct($id, $userid, $name, $slot){
            $this->id = (int)$id;
            $this->userid = (int)$userid;
            $this->name = $name;
            $this->slot = $slot;
			$this->fillLocationsGUI();//so called shots work properly
        }
        
        public function getCommonIniModifiers( $gamedata ){ //common Initiative modifiers: speed, criticals
            $mod = 0;
            $speed = $this->getSpeed();
        
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
        $strippedShip->systems = array_map( function($system) {return $system->stripForJson();}, $this->systems);
		
		//unit enhancements
		if($this->enhancementTooltip !== ''){ //enhancements exist!			
			$strippedShip->enhancementTooltip = $this->enhancementTooltip; 
			$strippedShip = Enhancements::addUnitEnhancementsForJSON($this, $strippedShip);//modifies $strippedShip  object
		}
		
		$strippedShip->enhancementOptions = array(); //no point in sending options information...
			
        return $strippedShip;
    }
	    
        public function getInitiativebonus($gamedata){
            if($this->faction == "Centauri"){
                return $this->doCentauriInitiativeBonus($gamedata);
            }
            if($this->faction == "Yolu"){
                return $this->doYoluInitiativeBonus($gamedata);
            }
            if($this->faction == "Dilgar"){
                return $this->doDilgarInitiativeBonus($gamedata);
            }
            return $this->iniativebonus;
        }
        
        private function doCentauriInitiativeBonus($gamedata){
            foreach($gamedata->ships as $ship){
                if(!$ship->isDestroyed()
                        && ($ship->faction == "Centauri")
                        && ($this->userid == $ship->userid)
                        && ($ship instanceof PrimusMaximus)
                        && ($this->id != $ship->id)){
                    return ($this->iniativebonus+5);
                }
            }
		return $this->iniativebonus;
        }
                
        private function doDilgarInitiativeBonus($gamedata){

        $mod = 0;

        if($gamedata->turn > 0 && $gamedata->phase >= 0 ){
            $pixPos = $this->getCoPos();
            //TODO: Better distance calculation
            $ships = $gamedata->getShipsInDistance($this, 9);

            foreach($ships as $ship){
                if( !$ship->isDestroyed()
                    && ($ship->faction == "Dilgar")
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

    private function doYoluInitiativeBonus($gamedata){
        foreach($gamedata->ships as $ship){
            if(!$ship->isDestroyed()
                && ($ship->faction == "Yolu")
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
            $this->enabledSpecialAbilities = $system->getSpecialAbilityList($this->enabledSpecialAbilities);
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

    public function isElint()
    {
        return $this->getSpecialAbilityValue("ELINT");
    }

    protected function addSystem($system, $loc){
        $i = sizeof($this->systems);
        $system->setId($i);
        $system->location = $loc;
        $system->setUnit($this);


            $this->systems[$i] = $system;
            
            if ($system instanceof Structure)
                $this->structures[$loc] = $system->id;
        
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
			//check whether game id is safe (can be safely be deleted lin May 2018 or so)
			///already safe enough, commenting out!
			//if ((TacGamedata::$currentGameID >= TacGamedata::$safeGameID) || (TacGamedata::$currentGameID<1)){
				if((!($this instanceof FighterFlight)) && (!($this->osat)) && (!$this->base) && (!$this->smallBase) ){
					$this->addPrimarySystem(new RammingAttack(0, 0, 360, 0, 0));
				}
			//}
		}

			$this->notesFill(); //add miscellanous info to notes!
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
			if (TacGamedata::$currentTurn >= 1){ //in later turns notes will be displayed from pre-compiled cache! no point generating them every time
				return;
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
				default: //should not happen!
					$this->notes .= 'Unit size not identified!';	
					break;
			}//unit size described, which also guarantees existence of previous entries!
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
			//Improved/Advanced Sensors
			/*hasSpecialAbility relies on data created in system->onConstructed, so not available here. Need to manually look for Sensors...
			if($this->hasSpecialAbility("ImprovedSensors")) $this->notes .= '<br>Improved Sensors';
			if($this->hasSpecialAbility("AdvancedSensors")) $this->notes .= '<br>Advanced Sensors';
			*/
			if($this->critRollMod != 0){
				$plus = '';
				if($this->critRollMod > 0) $plus = '+';
				if($this instanceof FighterFlight){					
					$this->notes .= '<br>Dropout roll modifier: ' . $plus . $this->critRollMod;
				}else{
					$this->notes .= '<br>Critical roll modifier: ' . $plus . $this->critRollMod;
				}
			}
			if(!($this instanceof FighterFlight)) foreach($this->systems as $sensor) if ($sensor instanceof Scanner){
				foreach($sensor->specialAbilities as $ability){
					if ($ability=='AdvancedSensors'){
						$this->notes .= '<br>Advanced Sensors';
					}else if ($ability=='ImprovedSensors'){
						$this->notes .= '<br>Improved Sensors';
					}else if ($ability=='StarWarsSensors'){
						$this->notes .= '<br>Star Wars Sensors';
					}else if ($ability=='LCVSensors'){ 
						$this->notes .= '<br>LCV Sensors';
					}
				}
				if ($sensor instanceof ElintScanner) {
					$this->notes .= '<br>ElInt Sensors';
				}
				break; //checking one Scanner is enough
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
            else{
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
            }
            
        return null;
    }

    public function getSystemByName($name){
        foreach ($this->systems as $system){
            if ($system instanceof $name){
                return $system;
            }
            else{
                if($system instanceof Weapon && $system->duoWeapon){
                    foreach($system->weapons as $weapon){
                        if($weapon instanceof $name){
                            return $weapon;
                        }
                    }
                }
            }
        }

        return null;
    }


    public function getSystemsByNameLoc($name, $location, $acceptDestroyed = false){ /*get list of required systems on a particular location*/
        /*name may indicate different location?...*/
        /*'destroyed' means either destroyed as of PREVIOUS turn, OR reduced to health 0*/
        $location_different_array = explode (':' , $name);
        if(sizeof($location_different_array)==2){ //indicated different section: exactly 2 items - first location, then name
            return $this->getSystemsByNameLoc($location_different_array[1], $location_different_array[0], $acceptDestroyed);
        }else{
            $returnTab = array();
            if($name=='Structure'){ //Structure is special, as it might actually belong to a different section! (on MCVs)
                $system = $this->getStructureSystem($location);
                if( ($acceptDestroyed == true) || (!$system->isDestroyed()) ){
                    $returnTab[] = $system;
                }
            }else{
                foreach ($this->systems as $system){
                    if ( ($system->displayName == $name) && ($system->location == $location) ){
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


    public function getSystemsByName($name, $acceptDestroyed = false){ /*get list of required systems anywhere on a ship*/
        /*'destroyed' means either destroyed as of PREVIOUS turn, OR reduced to health 0*/
        $returnTab = array();
        foreach ($this->systems as $system){
            if ( ($system->displayName == $name) ){
                if( ($acceptDestroyed == true) || (!$system->isDestroyed()) ){
                    $returnTab[] = $system;
                }
            }
        }
        return $returnTab;
    } //end of function getSystemsByName



    public function getHitChanceMod($shooter, $pos, $turn, $weapon){
        if ($pos !== null) {
            $pos = Mathlib::hexCoToPixel($pos);
        }
        $affectingSystems = array();
        foreach($this->systems as $system){
            if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)) continue;
            $mod = $system->getDefensiveHitChangeMod($this, $shooter, $pos, $turn, $weapon);
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
            if($pos!=null){ //firing position is explicitly declared
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
            if($move->type == "start") continue; //not a real move
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
        }else{
            return null;
        }
    }


    public function getFireControlIndex(){
        return 2;
    }


    public function isDestroyed($turn = false){
        foreach($this->systems as $system){
            if ($system instanceof Reactor && $system->isDestroyed($turn)){
                return true;
            }

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
        if (!$CnC || $CnC->destroyed || $CnC->hasCritical("ShipDisabledOneTurn", TacGamedata::$currentTurn))
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


    public function getBearingOnPos($pos){ //returns relative angle from this unit to indicated coordinates
        $tf = $this->getFacingAngle(); //ship facing
        $compassHeading = mathlib::getCompassHeadingOfPos($this, $pos); //absolute bearing
        $relativeBearing =  Mathlib::addToDirection($compassHeading, -$tf);//relative bearing
        if( Movement::isRolled($this) ){ //if ship is rolled, mirror relative bearing
            if( $relativeBearing <> 0 ) { //mirror of 0 is 0
                $relativeBearing = 360-$relativeBearing;
            }
        }
        return $relativeBearing;
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
        return $relativeBearing;
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



    public function getHitSectionChoice($shooter, $fireOrder, $weapon, $returnDestroyed = false){ //returns value - location! chooses method based on weapon and fire order!
        $foundLocation = 0;
        if($weapon->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn); //turn - 1?...
            $posLaunch = mathlib::hexCoToPixel($movement->position);
            $foundLocation = $this->getHitSectionPos($posLaunch, $fireOrder->turn);
        }else{
            $foundLocation = $this->getHitSection($shooter, $fireOrder->turn, $returnDestroyed);
        }
        return $foundLocation;
    }
    public function getHitSection($shooter, $turn, $returnDestroyed = false){ //returns value - location! DO NOT USE FOR BALLISTICS!
        $foundLocation = 0;
        if(isset($this->activeHitLocations[$shooter->id])){
            $foundLocation = $this->activeHitLocations[$shooter->id]["loc"];
        }else{
            $loc = $this->doGetHitSection($shooter); //finds array with relevant data!
            $this->activeHitLocations[$shooter->id] = $loc; //save location for further hits from same unit
            $foundLocation = $loc["loc"];
        }

        if($foundLocation > 0 && $returnDestroyed == false){ //return it only if not destroyed as of previous turn
            $structure = $this->getStructureSystem($foundLocation); //this always returns appropriate structure
            if($structure->isDestroyed($turn-1)) $foundLocaton = 0;
        }
        return $foundLocation;
    }
    public function getHitSectionPos($pos, $turn, $returnDestroyed = false){ //returns value - location! THIS IS FOR BALLISTICS!
        $foundLocation = 0;
        $loc = $this->doGetHitSectionPos($pos); //finds array with relevant data!
        $foundLocation = $loc["loc"];
        if($foundLocation > 0 && $returnDestroyed == false){ //return it only if not destroyed as of previous turn
            $structure = $this->getStructureSystem($foundLocation); //this always returns appropriate structure
            if($structure->isDestroyed($turn-1)) $foundLocaton = 0;
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
                    if($name != 'Primary'){ //no PRIMARY penetrating hits for Flash!
                        $systemsArray = $this->getSystemsByNameLoc($name, $location, false);//undestroyed ystems of this name
                        if(sizeof($systemsArray)>0){ //there actually are such systems!
                            $rngTotal+= $rngCurr;
                            $hitChart[$rngTotal] = $name;
                        }
                    }
                    $rngCurr = 0;
                }
            }
            if($rngTotal ==0) return $this->getStructureSystem(0);//there is nothing here! penetrate to PRIMARY...
        }
        $noPrimaryHits = ($weapon->noPrimaryHits || ($weapon->damageType == 'Piercing'));
        if($noPrimaryHits){ //change hit chart! - no PRIMARY hits!
            $hitChart = array();
            //use only non-destroyed systems on section hit
            $rngTotal = 0; //range of current system
            $rngCurr = 0; //total range of live systems
            for($roll = 1;$roll<=20;$roll++){
                $rngCurr++;
                if (isset($this->hitChart[$location][$roll])){
                    $name = $this->hitChart[$location][$roll];
                    if($name != 'Primary'){ //no PRIMARY penetrating hits
                        $systemsArray = $this->getSystemsByNameLoc($name, $location, true);//accept destroyed systems too
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
        $isSystemKiller = $weapon->systemKiller;
        while ($name == ''){
            if (isset($hitChart[$roll])){
                $name = $hitChart[$roll];
                if($name == 'Structure' && $isSystemKiller) { //for systemKiller weapon, reroll Structure hits
                    $isSystemKiller = false; //don't do that again
                    $name = ''; //reset
                    $roll = Dice::d($rngTotal); //new location roll
                }
            }else{
                $roll++;
                if($roll>$rngTotal)//out of range already! return facing Structure... Should not happen.
                {
                    return $this->getStructureSystem($location);
                }
            }
        }

        if($name == 'Primary'){ //redirect to PRIMARY!
            return $this->getHitSystemByTable($shooter, $fire, $weapon, 0);
        }
        $systems = $this->getSystemsByNameLoc($name, $location, false); //do NOT accept destroyed systems!
        if(sizeof($systems)==0){ //if empty, damage is done to Structure
            $struct = $this->getStructureSystem($location);
            return $struct;
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

          
		$hitChart = array(); //$hitChart will contain system names, as usual!
		//use only non-destroyed systems on section hit
		$rngTotal = 0; //range of current system
		$rngCurr = 0; //total range of live systems
		
		foreach ($this->systems as $system){ //ok, do use actual systems...
			if (($system->location == $location) && (!($system instanceof Structure))){ 
				//Flash - undestroyed only
				if(($weapon->damageType != 'Flash') || (!$system->isDestroyed())) {
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
		$noPrimaryHits = ($weapon->noPrimaryHits || ($weapon->damageType == 'Piercing') || ($weapon->damageType == 'Flash'));
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
		$isSystemKiller = $weapon->systemKiller;
		while ($name == ''){
			if (isset($hitChart[$roll])){
				$name = $hitChart[$roll];
				if($name == 'Structure' && $isSystemKiller) { //for systemKiller weapon, reroll Structure
					$isSystemKiller = false; //don't do that again
					$name = '';
					$roll = Dice::d($rngTotal); //new location roll
				}
			}else{
				$roll++;
				if($roll>$rngTotal)//out of range already!
				{
					return $this->getStructureSystem(0);
				}
			}
		}
		
		if($name == 'Primary'){ //redirect to PRIMARY!
			return $this->getHitSystemByDice($shooter, $fire, $weapon, 0);
		}
		$systems = $this->getSystemsByNameLoc($name, $location, false); //do NOT accept destroyed systems!
		if(sizeof($systems)==0){ //if empty, just return Structure - whether destroyed or not
			$struct = $this->getStructureSystem($location);
			return $struct;
		}
		
		//now choose one of equal eligible systems (they're already known to be undestroyed)
                $roll = Dice::d(sizeof($systems));
                $system = $systems[$roll-1];
		return $system;
		
	} //end of function GetHitSystemByDice
		
        
        public static function hasBetterIniative($a, $b){
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
            
            return false;
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
	public function setExpectedDamage($hitLoc, $hitChance, $weapon){
		//add to table private $expectedDamage = array(); //loc => dam; damage the unit is expected to take this turn
		if(($hitLoc==0) || ($hitChance<=0)) return; //no point checking, PRIMARY damage not relevant for this decision; same when hit chance is less than 0
		if(!isset($this->expectedDamage[$hitLoc])){
			$this->expectedDamage[$hitLoc] = 0;
		}		
		$structureSystem = $this->getStructureSystem($hitLoc);
		$armour = $structureSystem->getArmour($this, null, $weapon->damageType); //shooter relevant only for fighters - and they don't care about calculating ambiguous damage!
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
        $locs[] = array("loc" => 3, "min" => 180, "max" => 210, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 3, "min" => 210, "max" => 360, "profile" => $this->forwardDefense);

        return $locs;
    }
}



class MediumShip extends BaseShip{
    public $shipSizeClass = 1;

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name, $slot);
    }

    public function getFireControlIndex(){
        return 1;
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

} //end of class MediumShip

/* essentially treated as medium ship, except all 3 hit charts should be the same and point to PRIMARY systems. 
A lot of systems are technically present on LCV but not really there by rules, hence are made unhittable.
LCVs typically require hagar space, too.
*/
class LCV extends MediumShip{
	public $hangarRequired = 'LCVs';
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

        $locs[] = array("loc" => 3, "min" => 180, "max" => 210, "profile" => $this->forwardDefense);
        $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->sideDefense);
        $locs[] = array("loc" => 3, "min" => 330, "max" => 360, "profile" => $this->forwardDefense);

        return $locs;
    }
}



class LightShip extends BaseShip{ //is this used anywhere?...

    public $shipSizeClass = 0;

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name, $slot);
    }

    public function getFireControlIndex(){
        return 1;

    }

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

        if ($CnC->hasCritical("ShipDisabledOneTurn", TacGamedata::$currentTurn)){
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

        debug::log("killing section: ".$locToDestroy);
        foreach ($this->systems as $system){
            if ($system->location == $reactor->location){
                if (! $system->destroyed){
                    $sysArray[] = $system;
                }
            }
        }

        foreach ($sysArray as $system){

            $remaining = $system->getRemainingHealth();
            $armour = $system->armour;
            $toDo = $remaining + $armour;

            $damageEntry = new DamageEntry(-1, $this->id, -1, $gamedata->turn, $system->id, $toDo, $armour, 0, -1, true, "", "plasma");
            $damageEntry->updated = true;

            $system->damage[] = $damageEntry;
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


?>
