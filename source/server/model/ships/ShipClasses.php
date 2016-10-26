<?php
    class BaseShip{

        public $shipSizeClass = 3; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
	public $Enormous = false; //size class 4 is NOT implemented!!! for semi-Enormous unit, set this variable to True
        public $imagePath, $shipClass;
        public $systems = array();
        public $EW = array();
        public $fighters = array();
        public $hitChart = array();

        public $occurence = "common";
        public $limited = 0;
        public $agile = false;
        public $turncost, $turndelaycost, $accelcost, $rollcost, $pivotcost;
        public $currentturndelay = 0;
        public $iniative = "N/A";
        public $iniativebonus = 0;
        public $gravitic = false;
        public $phpclass;
        public $forwardDefense, $sideDefense;
        public $destroyed = false;
        public $pointCost = 0;
        public $faction = null;
        public $slot;
        public $unavailable = false;
        public $minesweeperbonus = 0;
        public $base = false;
        public $smallBase = false;
	    
	public $jinkinglimit = 0; //just in case there will be a ship actually able to jink
        
        public $enabledSpecialAbilities = array();
        
        public $canvasSize = 200;

        public $activeHitLocations = array(); //$shooterID->targetSection
        //following values from DB
        public $id, $userid, $name, $campaignX, $campaignY;
        public $rolled = false;
        public $rolling = false;
        public $team;
        
        public $slotid;

        public $movement = array();
        
        function __construct($id, $userid, $name, $slot){
            $this->id = (int)$id;
            $this->userid = (int)$userid;
            $this->name = $name;
            $this->slot = $slot;

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
                $ships = $gamedata->getShipsInDistance($pixPos, ((9*mathlib::$hexWidth) + 1));

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
        
        public function onConstructed($turn, $phase)
        {
            foreach ($this->systems as $system){
                $system->onConstructed($this, $turn, $phase);
                
                $this->enabledSpecialAbilities = $system->getSpecialAbilityList($this->enabledSpecialAbilities);
            }
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
            $this->addSystem($system, 0);
        }
        protected function addLeftSystem($system){
            $this->addSystem($system, 3);
        }
        protected function addRightSystem($system){
            $this->addSystem($system, 4);
        }
        
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
		$location_different_array = explode (':' , $name);
		if(sizeof($location_different_array)==2){ //indicated different section: exactly 2 items - first location, then name
			return $this->getSystemsByNameLoc($location_different_array[1], $location_different_array[0], $acceptDestroyed);
		}else{
			$returnTab = array();
			foreach ($this->systems as $system){
				if ( ($system->displayName == $name) && ($system->location == $location) ){
				    if( ($acceptDestroyed == true) || (!$system->isDestroyed()) ){
					    $returnTab[] = $system;
				    }
				}
			}            
			return $returnTab;
		}
		return array(); //should never reach here
	} //end of function getSystemsByNameLoc
	    

        
        public function getHitChanceMod($shooter, $pos, $turn){
            $affectingSystems = array();
            
            foreach($this->systems as $system){
                
                if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn))
                    continue;
                
                $mod = $system->getDefensiveHitChangeMod($this, $shooter, $pos, $turn);
                
                if ( !isset($affectingSystems[$system->getDefensiveType()])
                    || $affectingSystems[$system->getDefensiveType()] < $mod){
                    $affectingSystems[$system->getDefensiveType()] = $mod;
                }
                
            }
            return (-array_sum($affectingSystems));
    	}
        
        public function getDamageMod($shooter, $pos, $turn){
	    $affectingSystems = array();
            foreach($this->systems as $system){
                
                if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn))
                    continue;
                
                $mod = $system->getDefensiveDamageMod($this, $shooter, $pos, $turn);
                
                if ( !isset($affectingSystems[$system->getDefensiveType()])
                    || $affectingSystems[$system->getDefensiveType()] < $mod){
                    $affectingSystems[$system->getDefensiveType()] = $mod;
                }
                
            }
            return array_sum($affectingSystems);
		}
        
        private function checkIsValidAffectingSystem($system, $shooter, $pos, $turn){
            if (!($system instanceof DefensiveSystem))
                return false;
                
            //If the system was destroyed last turn continue 
            //(If it has been destroyed during this turn, it is still usable)
            if ($system->isDestroyed($turn-1))
               return false;

            //If the system is offline either because of a critical or power management, continue
            if ($system->isOfflineOnTurn($turn))
                return false;

            //if the system has arcs, check that the position is on arc
            if(is_int($system->startArc) && is_int($system->endArc)){

                $tf = $this->getFacingAngle();

                //get the heading of position, not ship (in case ballistic)
		    $relativeBearing = $this->getBearingOnPos($pos);

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
		
		
	    /*original code - returns first move of indicated turn*/
		/*
            $movement = null;
            if (!is_array($this->movement)){
                return array("x"=>0, "y"=>0);
            }
            foreach ($this->movement as $move){
                if ($move->type == "start")
                    continue;
                
                if ($move->turn == $turn){
                    if (!$movement)
                        $movement = $move;
                    
                    break;
                }
                $movement = $move;
            }
            return $movement;
 		*/
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
        

        public function getHexPos(){
        
            $movement = null;
            if (!is_array($this->movement)){
                return array("x"=>0, "y"=>0);
            }
            foreach ($this->movement as $move){
                $movement = $move;
            }

            return array($movement->x, $movement->y);
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
        
			if ($target instanceof FighterFlight){
				foreach ($this->EW as $EW){
					if ($EW->type == "CCEW" && $EW->turn == $turn)
						return $EW->amount;
				}
			}else{
				foreach ($this->EW as $EW){
					if ($EW->type == "OEW" && $EW->targetid == $target->id && $EW->turn == $turn)
						return $EW->amount;
				}
			}
        
            
            
            return 0;
        }
        
        public function getOEWTargetNum($turn){
        
			$amount = 0;
            foreach ($this->EW as $EW){
                if ($EW->type == "OEW" && $EW->turn == $turn)
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
                    $locs[$key]["armour"] = $structure->armour;
                }
                else {
                    return null; //should never happen!
                }
            }
            return $locs;
        }


        public function pickLocationForHit($locs){   //return array! ONLY OUTER LOCATIONS!!! (unless PRIMARY can be hit directly and is on hit table)        
		$pick = array("loc"=>0, "profile"=>40, "remHealth"=>0, "armour"=>0);
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
			}elseif(($toughnessLoc==$toughnessPick) && ($loc["profile"]<$pick["profile"])){ //if toughness is equal, better profile wins
				$pick = $loc;
			}//else old choice stays
		}

            return $pick;
        }



	public function getHitSectionChoice($shooter, $fireOrder, $weapon, $returnDestroyed = false){ //returns value - location! chooses method based on weapon and fire order!
		$foundLocation = 0;
		if($weapon->ballistic){
			$movement = $shooter->getLastTurnMovement($fireOrder->turn); //turn - 1?...
			$posLaunch = mathlib::hexCoToPixel($movement->x, $movement->y);
			$foundLocation = $this->getHitSectionPos($posLaunch, $fireOrder->turn);
			//$toBeLogged = $this->name + ' MJS BALLISTIC wpn: ' + $weapon->displayName + '; location: ' $location + '; coord: ' + $fire->x + ' ' + $fire->y;
			//debug::log("$toBeLogged"); 
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
			$posLaunch = mathlib::hexCoToPixel($movement->x, $movement->y);
			$foundProfile = $this->getHitSectionProfilePos($posLaunch, $fireOrder->turn);
		}else{
			$foundProfile = $this->getHitSectionProfile($shooter, $fireOrder->turn);
		}
		return $foundProfile;
        }
        public function getHitSectionProfile($shooter){ //returns value - profile! DO NOT USE FOR BALLISTICS!
		$foundProfile = 0;
		if(isset($this->activeHitLocations[$shooter->id]) ){
			$foundProfile = $this->activeHitLocations[$shooter->id]["profile"];	
        	}else{
			$loc = $this->doGetHitSection($shooter, $preGoal); //finds array with relevant data!
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

	    
	    
	    public function getHitSystemPos($pos, $shooter, $fireOrder, $weapon, $location = null){
		    /*find target section (based on indicated position) before finding location*/
		    if($location==null){
			    $location = $this->getHitSectionPos($pos, $fireOrder->turn);
		    }
		    $foundSystem = $this->getHitSystem($shooter, $fireOrder, $weapon, $location);
		    return $foundSystem;
	    }
	    
	    
        public function getHitSystem($shooter, $fireOrder, $weapon, $location = null){
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
		$location_different = false; //target system may be on different location?
		$location_different_array = array(); //array(location,system) if so indicated
		$systems = array();

		if ($fire->calledid != -1){
			$system = $this->getSystemById($fire->calledid);
		}

		if ($system != null && !$system->isDestroyed()) return $system;

		if ($location == null) { 
			$location = $this->getHitSectionChoice($shooter, $fire, $weapon);
		}
          
		$hitChart = $this->hitChart[$location];
		$rngTotal = 20; //standard hit chart has 20 possible locations
		if ($weapon->flashDamage){ //Flash - change hit chart! 
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
			
		//now choose system from chart...
		$roll = Dice::d($rngTotal);
		$name = '';
		$isSystemKiller = $weapon->systemKiller;
		while ($name == ''){
			if (isset($hitChart[$roll])){
				$name = $hitChart[$roll];
				if($name == 'Structure' && $isSystemKiller) { //for systemKiller weapon, reroll Structure
					$isSystemKiller = false; //don't do that again
					$name = ''; //reset
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
			return $this->getHitSystemByTable($shooter, $fire, $weapon, 0);
		}
		$systems = $this->getSystemsByNameLoc($name, $location, false); //do NOT accept destroyed systems!
		if(sizeof($systems)==0){ //if empty, overkill to Structure
			$struct = $this->getStructureSystem($location);
			if($struct->isDestroyed()) $struct = $this->getStructureSystem(0); //if Structure destroyed, overkill to PRIMARY Structure
			return $struct;
		}
		
		//now choose one of equal eligible systems (they're already known to be undestroyed)
                $roll = Dice::d(sizeof($systems));
                $system = $systems[$roll-1];
		return $system;
		
        } //end of function getHitSystemByTable


        public function getHitSystemByDice( $shooter, $fire, $weapon, $location){
		/*same as by table, but prepare table out of available systems...*/
		$system = null;
		$name = false;
		$location_different = false; //target system may be on different location?
		$location_different_array = array(); //array(location,system) if so indicated
		$systems = array();

		if ($fire->calledid != -1){
			$system = $this->getSystemById($fire->calledid);
		}

		if ($system != null && !$system->isDestroyed()) return $system;

		if ($location == null) { 
			$location = $this->getHitSectionChoice($shooter, $fire, $weapon);
		}

          
		$hitChart = array(); //$hitChart will contain system names, as usual!
		//use only non-destroyed systems on section hit
		$rngTotal = 0; //range of current system
		$rngCurr = 0; //total range of live systems
		
		foreach ($this->systems as $system){ //ok, do use actual systems...
			if (($system->location == $location) && (!($system instanceof Structure))){ 
				//Flash - undestroyed only
				if((!$weapon->flashDamage) || (!$system->isDestroyed() )) {
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
		if((!$weapon->flashDamage) || (!$system->isDestroyed() )) {
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
		//is there anything to be hit? if not, just overkill to PRIMARY Structure...
		if($rngTotal==0){
			$struct = $this->getStructureSystem(0); //if Structure destroyed, overkill to PRIMARY Structure
			return $struct;
		}
			
		//for non-Flash, add PRIMARY to hit table...
		if(!$weapon->flashDamage){
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
		if(sizeof($systems)==0){ //if empty, overkill to Structure
			$struct = $this->getStructureSystem($location);
			if($struct->isDestroyed()) $struct = $this->getStructureSystem(0); //if Structure destroyed, overkill to PRIMARY Structure
			return $struct;
		}
		
		//now choose one of equal eligible systems (they're already known to be undestroyed)
                $roll = Dice::d(sizeof($systems));
                $system = $systems[$roll-1];
		return $system;
		
	} //end of function GetHitSystemByDice
		
		

        /* no longer needed, keeping code just in case
        public function getPiercingDamagePerLoc($damage){
            return ceil($damage/3);
        }
	*/
        
	    /* no longer needed, keeping code just in case
        public function getPiercingLocations($shooter, $pos, $turn, $weapon){
		$location = $this->getHitSection($shooter, $turn, true); //return location even if destroyed
            
            $locs = array();
            $finallocs = array();

            if ($location == 1 || $location == 2){
                $locs[] = 1;
                $locs[] = 0;
                $locs[] = 2;
            }else if ($location == 3 || $location == 4){
                $locs[] = 3;
                $locs[] = 0;
                $locs[] = 4;
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
        
        
        public static function hasBetterIniative($a, $b){
            if ($a->iniative > $b->iniative)
                return true;
            
            if ($a->iniative < $b->iniative)
                return false;
                
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
        
        public function getAllFireOrders()
        {
            $orders = array();
            
            foreach ($this->systems as $system){
                $orders = array_merge($orders, $system->getFireOrders());
            }
            
            return $orders;
        }
        
        protected function getUndamagedSameSystem($system, $location){
            foreach ($this->systems as $sys){
                // check if there is another system of the same class
                // on this location.
                
                if($sys->location == $location && get_class($system) == get_class($sys) && !$sys->isDestroyed()){
                    return $sys;
                }
            }

            return null;
        } 
        
    }
    
    class BaseShipNoAft extends BaseShip{

        public $draziCap = true;

        function __construct($id, $userid, $name, $slot){
            parent::__construct($id, $userid, $name,$slot);
        }

        public function getLocations(){
        //debug::log("getLocations");         
            $locs = array();

            $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 3, "min" => 180, "max" => 330, "profile" => $this->sideDefense);
            $locs[] = array("loc" => 4, "min" => 30, "max" => 180, "profile" => $this->sideDefense);

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
    
        public $draziHCV = true;
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
