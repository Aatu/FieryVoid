<?php
    class BaseShip{

        public $shipSizeClass = 3; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
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
        
        public $enabledSpecialAbilities = array();
        
        public $canvasSize = 200;

        public $activeHitLocation = array();
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
                $shooterCompassHeading = mathlib::getCompassHeadingOfPos($this, $pos);

                //if not on arc, continue!
                if (!mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection($system->startArc,$tf), Mathlib::addToDirection($system->endArc,$tf) )){
                    return false;
                }
            }
            
            return true;
        }

        
        public function getLastTurnMovement($turn){
        
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
            
            return null;
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
            
            if ($output >= 0)
                return false;
        
            return true;
        }


        
        public function getDefenceValuePos($pos, $preGoal){
            //debug::log("getDefenceValuePos");         
            $tf = $this->getFacingAngle();
            $shooterCompassHeading = mathlib::getCompassHeadingOfPos($this, $pos);

            $result = $this->doGetDefenceValue($tf,  $shooterCompassHeading, $preGoal);
            $this->activeHitLocation = $result;

            return $result;
        }
        
        public function getDefenceValue($shooter, $preGoal){
            //debug::log("getDefenceValue");         
            $tf = $this->getFacingAngle();
            $shooterCompassHeading = mathlib::getCompassHeadingOfShip($this, $shooter);
          
            $result = $this->doGetDefenceValue($tf,  $shooterCompassHeading, $preGoal);
            $result["validFor"] = $shooter->id;
            $this->activeHitLocation = $result;

            return $result;
        }


        public function doGetDefenceValue($tf, $shooterCompassHeading, $preGoal){
            //debug::log("doGetDefenceValue");         

            $locs = $this->getLocations();
            $valid = array();

            foreach ($locs as $loc){
                if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection($loc["min"], $tf), Mathlib::addToDirection($loc["max"], $tf))){
                    $valid[] = $loc;
                }
            }

            $valid = $this->fillLocations($valid);
            $pick = $this->pickLocationForHit($valid, $preGoal);

            return $pick;
        }

        public function getLocations(){
            //debug::log("getLocations");         
            $locs = array();

            $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
            $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 3, "min" => 210, "max" => 330, "profile" => $this->sideDefense);

            return $locs;
        }


        public function fillLocations($locs){
            //debug::log("fillLocations for".$this->phpclass);  

            foreach ($locs as $key => $loc){

                $structure = $this->getStructureSystem($locs[$key]["loc"]);


                if ($structure){
                    $locs[$key]["remHealth"] = $structure->getRemainingHealth();
                    $locs[$key]["armour"] = $structure->armour;
                }
                else {
                    debug::log("no structure!");
                    return null;
                }
            }

            return $locs;
        }


        public function pickLocationForHit($locs, $preGoal){           
            debug::log("pickLocationForHit");
                        
            $topValue = -1;
            $pick = -1;

            foreach ($locs as $loc){
                $value = $loc["remHealth"]; // remaining Health on Structure
                $value += floor($value/10) * ($loc["armour"] * 1.5); // add armour*1.5 per 10 remaining Health

                // $value is now approximatly a value of relative toughness of this section

                //since we have the hitchance PRE profile as parameter, apply the profile of this section
                //to get the END HIT CHANCE. High hit chance diminishes worth of toughness
                $goal = $preGoal + $loc["profile"];

                // divide toughness by expected hitchance effective defensive worth of a section

                $value = $value / $goal;

                // if the effective defensive worth is higher than the current one, replace it
                if ($value > $topValue){
                    $topValue = $value;
                    $pick = $loc;
                }
            }

            debug::log($this->phpclass." TOP def value on loc: ".$loc["loc"]." is: ".$topValue);
            if ($this instanceof OSAT){
                debug::log($pick["remHealth"]);
                debug::log($pick["profile"]);
                debug::log($pick["armour"]);
            }

            return $pick;
        }




        public function getHitSection($pos, $shooter, $turn, $weapon){

            $location = $this->activeHitLocation["loc"];

            if ($location != 0){
                if (!isset($this->hitChart[0])){
                    if ((($this instanceof MediumShip && Dice::d(20)>17 ) || Dice::d(10)>9) && !$weapon->flashDamage){
                        $location = 0;
                    }
                }

                $structure = $this->getStructureSystem($location);
                if ($structure != null && $structure->isDestroyed($turn-1))
                    return 0;
            }
        
            if (isset($this->activeHitLocation["loc"])){
                debug::log("RETURNING FOR DAMAGE: ".$this->activeHitLocation["loc"]);
            }
            
            return $location;
            
        }        



        public function getHitSystem($pos, $shooter, $fire, $weapon, $location = null){
            debug::log("______________________");
            debug::log("getHitSystem for: ".$this->phpclass." with id: ".$this->id);

            if (isset($this->hitChart[0])){
                debug::log("TABLE");
                $system = $this->getHitSystemByTable($pos, $shooter, $fire, $weapon, $location);
            }
            else {
                debug::log("DICE");
                $system = $this->getHitSystemByDice($pos, $shooter, $fire, $weapon, $location);
            }

            return $system;
        }



        public function getHitSystemByTable($pos, $shooter, $fire, $weapon, $location){
            $system = null;
            $name = false;
            $systems = array();
            
            if ($fire->calledid != -1){
                $system = $this->getSystemById($fire->calledid);
                debug::log("called shot vs ".$system->displayName.", destroyed: ".$system->destroyed);
            }
            
            if ($system != null && !$system->isDestroyed())
                return $system;
        
            if ($location === null)
                $location = $this->getHitSection($pos, $shooter, $fire->turn, $weapon);

            $destroyedThisTurn = false;

            if ($weapon->flashDamage){
            //    debug::log("is FLASH");
            //    debug::log("check if a system on target location was destroyed this turn");
                foreach ($this->systems as $system){
                    if ($system->isDestroyed() && $system->location == $location){
                        foreach ($system->damage as $damage){
                            if ($damage->turn == $fire->turn){
                                $destroyedThisTurn = true;
                                break;
                            }
                        }
                    }
                    if ($destroyedThisTurn){
                        break;
                    }
                }
            }



            if (!$weapon->flashDamage){
            //    debug::log("begin normal, non flash roll on hitchart routine");
                $hitChart = $this->hitChart[$location];
                $roll = mt_rand(1, 20);
            //    debug::log("intial roll: ".$roll);

                if (isset($this->hitChart[$location][$roll])){
                    $name = $this->hitChart[$location][$roll];
                }
                else {
                    while (!$name){
                        $roll++;
                        if (isset($hitChart[$roll])){
                            $name = $hitChart[$roll];
                        }
                    }
                }

            //    debug::log("roll: ".$roll." on loc: ".$location."__name: ".$name);

                if ($name == "Primary"){
                        $name = false;
                        $location = 0;
                        $roll = mt_rand(1, 20);
                        debug::log("redirecting to PRIMARY ___ NEW roll: ".$roll." on loc: ".$location);
                }

                if (isset($this->hitChart[$location][$roll])){
                        $name = $this->hitChart[$location][$roll];
                }
                else {
                    while (!$name){
                        $roll++;
                        if (isset($this->hitChart[$location][$roll])){
                            $name = $this->hitChart[$location][$roll];
                        }
                    }
                }

                debug::log("hitLoc: ".$location.", hitting: ".$name);

                foreach ($this->systems as $system){
                    if ($system->location == $location){
                        if ($system->displayName == $name){
                            $systems[] = $system;
                        }
                    }
                }
            }
            else {                
            //    debug::log("FLASH type, gathering all from location");
                foreach ($this->systems as $system){
                    if ($system->location == $location && !$system->isDestroyed() && !$system instanceof Structure){
                        $systems[] = $system;
                    }
                }
            }


            // if you have more than 0 systems if you elligable array
            if (sizeof($systems) > 0){
            //    debug::log("more than one valid sys");
                $roll = mt_rand(1, sizeof($systems));
                $system = $systems[$roll-1];
            //    debug::log("target systems: ".sizeof($systems).", rolled: ".$roll.", hitting: ".$system->displayName);
                if (!$system->isDestroyed()){
            //        debug::log("sys is not destroyed, return it!");
                    return $system;
                }
                else {
                //    debug::log("sys is destroyed, try getUndamagedSameSystem!");
                    $newSystem = $this->getUndamagedSameSystem($system, $location);

                    if ($newSystem){                        
                //    debug::log("got one, return it");
                        return $newSystem;
                    }
                    else {
                        if ($weapon->flashDamage){
                   //         debug::log("got no same name system and im flash, getHitSystem anew");
                            return $this->getHitSystem($pos, $shooter, $fire, $weapon, $location);
                        }
                        else {
                     //       debug::log("not flash, got no same name system, get structure ".$location);
                            $system = $this->getStructureSystem($location);
                            // this is no MCV, so check for outer structure being ded
                            if ($system->isDestroyed()){
                    //            debug::log("target outer struct sys is destroyed, getting overkill to structure 0");
                                return $this->getStructureSystem(0);
                            }
                            else {
                     //           debug::log("its alive, return it");
                                return $system;
                            }
                        }   
                    }
                }
            }

            // if you have no elligibe systems in your array
            else {
                debug::log("size of systems = 0 no valid target systems -- TYPO or section down ??");

                if ($weapon->flashDamage){
              //      debug::log("flash type");

               //     debug::log("checking for outer structure");
                    $system = $this->getStructureSystem($location);

                    if ($system->isDestroyed()){
                //    debug::log("but its destroyed, returning any primary system");
                        return $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                    }
                    else {
                //    debug::log("outer structure is alive, returning it");
                        return $system;
                    }
                }
                else if ($destroyedThisTurn){
                //    debug::log("non flash, destroyed something earlier on this target loc, overkill into struct 0");
                    $structure = $this->getStructureSystem(0);

                    if ($structure->isDestroyed()){
                 //       debug::log("structure destroyed - return null");
                        return null;
                    }
                    else {
                 //       debug::log("structure intact, return it");
                        return $structure;
                    }
                }
                else {
                    debug::log("LAST ELSE, cant resolve shit");
                }
            }
        }


        public function getHitSystemByDice($pos, $shooter, $fire, $weapon, $location){

            $system = null;
            
            if ($fire->calledid != -1)
                $system = $this->getSystemById($fire->calledid);
            
            if ($system != null && !$system->isDestroyed())
                return $system;
        
            if ($location === null)
                $location = $this->getHitSection($pos, $shooter, $fire->turn, $weapon);
            
            $systems = array();
            $totalStructure = 0;

            foreach ($this->systems as $system){
                if ($system->location == $location){
            
                    // For flash damage, only take into account the systems
                    // that are still alive and are not structure.
                    if ($weapon->flashDamage && ($system->isDestroyed() || $system->name == "structure" )){
                        continue;
                    }
                        
                    $systems[] = $system;
                        
                    if ($system->name == "structure"){
                       
                        $multiply = 0.5;
                        if ($location == 0)
                            $multiply = 2;
                            
                        $totalStructure += round($system->maxhealth * $multiply);
                    }else{
                        $totalStructure += $system->maxhealth;
                    }
                    
                }
            }   
            
            if(sizeof($systems) == 0){
                // all systems were destroyed. If there still is structure,
                // return that. If not, go to primary.
           //     debug::log("size of 0");
            //    debug::log("location: ".$location);

                $structure = $this->getStructureSystem($location);

                if ($structure){
           //         debug::log("strcuture: ".$structure->displayName);
                }
                else {
            //        debug::log("no structure");
                }

                if($structure->isDestroyed()){
            //        debug::log("structure true");
                    if ($location == 0)
                                return null;
                    // Go to primary
                    // Go to primary systems for flash damage
                    if ($weapon->flashDamage){
                        return $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                    }
                    else{
                        if($structure->isDestroyed($fire->turn -1)){
                            $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                        }
                        else{
                            $structure = $this->getStructureSystem(0);
                        
                            if($structure->isDestroyed()){
                                return null;
                            }
                            else{
                                return $structure;
                            }
                        }
                    }
                }
                else{
                    // there is still structure left.
                    return $structure;
                }
            }
            
            $roll = Dice::d($totalStructure);
            $goneTrough = 0;

            foreach ($systems as $system){
                $health = 0;
                    
                if ($system->name == "structure"){
                    $multiply = 0.5;
                    if ($location == 0)
                        $multiply = 2;
                        
                    $health = round($system->maxhealth * $multiply);
                }else{
                    $health = $system->maxhealth;
                }
                
                if ($roll > $goneTrough && $roll <= ($goneTrough + $health)){
                    //print("hitting: " . $system->displayName . " location: " . $system->location ."\n\n");
                    if ($system->isDestroyed()){
                        $newSystem = $this->getUndamagedSameSystem($system, $location);
                        
                        if($newSystem != null){
                            return $newSystem;
                        }
                        
                        if ($system instanceof Structure){
                            if ($system->location == 0){
                                return null;}
                                
                            // Go to primary systems for flash damage
                            // Go to primary structure for other weapons.
                            if ($weapon->flashDamage){
                                return $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                            }
                            else{
                                if($system->isDestroyed($fire->turn -1)){
                                    $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                                }
                                else{
                                    $structure = $this->getStructureSystem(0);

                                    if($structure->isDestroyed()){
                                        return null;
                                    }
                                    else{
                                        return $structure;
                                    }
                                }
                            }
                        }
                        
                        $structure = $this->getStructureSystem($location);
                        if ($structure == null || $structure->isDestroyed()){
                            if ($structure != null && $structure->location == 0){
                                return null;
                            }
                                
                            // Go to primary systems for flash damage
                            // Go to primary structure for other weapons.
                            if ($weapon->flashDamage){
                                return $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                            }
                            else{
                                if($structure != null && $structure->isDestroyed($fire->turn -1)){
                                    $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                                }
                                else{
                                    $structure = $this->getStructureSystem(0);

                                    if($structure != null && $structure->isDestroyed()){
                                        return null;
                                    }
                                    else{
                                        return $structure;
                                    }
                                }
                            }
                        }
                        else{
                            return $structure;
                        }
                            
                        
                    }
                    return $system;
                }
                
                $goneTrough += $health;
            }
            
            return null;
        }

        
        public function getPiercingDamagePerLoc($damage){
            return ceil($damage/3);
        }
        
        public function getPiercingLocations($shooter, $pos, $turn, $weapon){
            debug::log("getPiercingLocations");

            $location =  $this->activeHitLocation["loc"];
            
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
        debug::log("getLocations");         
            $locs = array();

            $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 4, "min" => 180, "max" => 330, "profile" => $this->sideDefense);
            $locs[] = array("loc" => 3, "min" => 30, "max" => 180, "profile" => $this->sideDefense);

            return $locs;
        }
    }

    
    class HeavyCombatVessel extends BaseShip{
    
        public $shipSizeClass = 2;        
        
        
        function __construct($id, $userid, $name, $slot){
            parent::__construct($id, $userid, $name,$slot);
        }



        public function getLocations(){
        debug::log("getLocations");         
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
        debug::log("getLocations");         
            $locs = array();
            $locs[] = array("loc" => 4, "min" => 0, "max" => 30, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 4, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
            $locs[] = array("loc" => 4, "min" => 150, "max" => 180, "profile" => $this->forwardDefense);

            $locs[] = array("loc" => 3, "min" => 150, "max" => 180, "profile" => $this->forwardDefense);
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
        debug::log("getLocations");         
            $locs = array();

//            $locs[] = array("loc" => 0, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);
  //          $locs[] = array("loc" => 0, "min" => 30, "max" => 150, "profile" => $this->sideDefense);
    //        $locs[] = array("loc" => 0, "min" => 150, "max" => 210, "profile" => $this->sideDefense);
      //      $locs[] = array("loc" => 0, "min" => 210, "max" => 330, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 1, "min" => 330, "max" => 30, "profile" => $this->forwardDefense);

            $locs[] = array("loc" => 1, "min" => 30, "max" => 90, "profile" => $this->sideDefense);
            $locs[] = array("loc" => 2, "min" => 90, "max" => 150, "profile" => $this->sideDefense);

            $locs[] = array("loc" => 2, "min" => 150, "max" => 210, "profile" => $this->forwardDefense);  

            $locs[] = array("loc" => 2, "min" => 210, "max" => 270, "profile" => $this->sideDefense);
            $locs[] = array("loc" => 1, "min" => 270, "max" => 330, "profile" => $this->sideDefense);


            return $locs;
        }


        public function fillLocations($locs){
        // debug::log("fillLocations for".$this->phpclass);

            foreach ($locs as $key => $loc){

                $structure = $this->getStructureSystem(0);

                if ($structure){
                    $locs[$key]["remHealth"] = $structure->getRemainingHealth();
                    $locs[$key]["armour"] = $structure->armour;
                }
                else {
                    debug::log("no structure!");
                    return null;
                }
            }

            return $locs;
        }



        public function getHitSystemByTable($pos, $shooter, $fire, $weapon, $location){
            $system = null;
            $name = false;
            $systems = array();
            
            if ($fire->calledid != -1)
                $system = $this->getSystemById($fire->calledid);
            
            if ($system != null && !$system->isDestroyed())
                return $system;
        
            if ($location === null)
                $location = $this->getHitSection($pos, $shooter, $fire->turn, $weapon);

            $destroyedThisTurn = false;

            if ($weapon->flashDamage){
           //     debug::log("is FLASH");
             //   debug::log("check if a system on target location was destroyed this turn");
                foreach ($this->systems as $system){
                    if ($system->isDestroyed() && $system->location == $location){
                        foreach ($system->damage as $damage){
                            if ($damage->turn == $fire->turn){
                                $destroyedThisTurn = true;
                                break;
                            }
                        }
                    }
                    if ($destroyedThisTurn){
                        break;
                    }
                }
            }


            if ($destroyedThisTurn){
          //      debug::log("one destroyed this turn!");
            }
            else {
          //      debug::log("nope");
            }


            if (!$weapon->flashDamage){
             //   debug::log("begin normal, non flash roll on hitchart routine");
                $hitChart = $this->hitChart[$location];
                $roll = mt_rand(1, 20);
             //   debug::log("intial roll: ".$roll);
                if (isset($this->hitChart[$location][$roll])){
                    $name = $this->hitChart[$location][$roll];
                }
                else {
                    while (!$name){
                        $roll++;
                        if (isset($hitChart[$roll])){
                            $name = $hitChart[$roll];
                        }
                    }
                }

                debug::log("hit result on loc: ".$location." has name: ".$name);

                if ($name == "Primary"){
                        $name = false;
                        $location = 0;
                        $roll = mt_rand(1, 20);
                   //     debug::log("redirecting to PRIMARY ___ NEW roll: ".$roll." on loc: ".$location);
                }
                else if ($name == "Structure"){
                  //  debug::log("MCV front/aft structure roll, checking for prim structure");
                    $system = $this->getStructureSystem(0);
                    if (!$system->isDestroyed()){
                  //      debug::log("return intact primary structure on MCV");
                        return $system;
                    }
                    else {
                        return null;
                    }
                }

                if (isset($this->hitChart[$location][$roll])){
                        $name = $this->hitChart[$location][$roll];
                }
                else {
                    while (!$name){
                        $roll++;
                        if (isset($this->hitChart[$location][$roll])){
                            $name = $this->hitChart[$location][$roll];
                        }
                    }
                }

                debug::log("hitLoc: ".$location.", hitting: ".$name);

                foreach ($this->systems as $system){
                    if ($system->location == $location){
                        if ($system->displayName == $name){
                            $systems[] = $system;
                        }
                    }
                }
            }
            else {                
            //    debug::log("FLASH type, gathering all from location");
                foreach ($this->systems as $system){
                    if ($system->location == $location && !$system->isDestroyed() && !$system instanceof Structure){
                        $systems[] = $system;
                    }
                }
            }


            // if you have more than 0 systems if you elligable array
            if (sizeof($systems) > 0){
              //  debug::log("more than one valid sys");
                $roll = mt_rand(1, sizeof($systems));
                $system = $systems[$roll-1];
             //   debug::log("target systems: ".sizeof($systems).", rolled: ".$roll.", hitting: ".$system->displayName);
                if (!$system->isDestroyed()){
              //      debug::log("sys is not destroyed, return it!");
                    return $system;
                }
                else {
             //       debug::log("sys is destroyed, try getUndamagedSameSystem!");
                    $newSystem = $this->getUndamagedSameSystem($system, $location);

                    if ($newSystem){                        
               //     debug::log("got one, return it");
                        return $newSystem;
                    }
                    else {
                        if ($weapon->flashDamage){
                  //          debug::log("got no same name system and im flash, getHitSystem anew");
                            return $this->getHitSystem($pos, $shooter, $fire, $weapon, $location);
                        }
                        else {
                   //         debug::log("not flash, got no same name system, get structure ".$location);
                            $system = $this->getStructureSystem(0);
                            // this is no MCV, so check for outer structure being ded
                            if ($system->isDestroyed()){
                      //          debug::log("target outer struct sys is destroyed, getting overkill to structure 0");
                                return $this->getStructureSystem(0);
                            }
                            else {
                     //           debug::log("its alive, return it");
                                return $system;
                            }
                        }   
                    }
                }
            }

            // if you have no elligibe systems in your array
            else {
                debug::log("size of systems = 0 = no valid target systems -- TYPO or SECTION down ??");

                if ($weapon->flashDamage){
            /*        debug::log("flash type");
                    debug::log("checking for outer structure");
                    $system = $this->getStructureSystem(0);

                    if ($system->isDestroyed()){
                    debug::log("but its destroyed, returning any primary system");
                        return $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                    }
                    else {
                    debug::log("outer structure is alive, returning it");
                        return $system;
                    }
            */

                debug::log("flash type vs mcv, no systems, return PRIMARY hitsystem !!!");
                    return $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                }
                else if ($destroyedThisTurn){
              //      debug::log("non flash, destroyed something earlier on this target loc, overkill into struct 0");
                    $structure = $this->getStructureSystem(0);

                    if ($structure->isDestroyed()){
             //           debug::log("structure destroyed - return null");
                        return null;
                    }
                    else {
                //        debug::log("structure intact, return it");
                        return $structure;
                    }
                }
                else {
                    debug::log("LAST ELSE, cant resolve shit");
                }
            }
        }



        public function getHitSystemByDice($pos, $shooter, $fire, $weapon, $location = null){

            // Turn counter needed to keep track of when a section was destroyed.
            $destroyedThisTurn = false;
            
            $system = null;
            if ($fire->calledid != -1){
                $system = $this->getSystemById($fire->calledid);
            }
            
            if ($system != null && !$system->isDestroyed())
                return $system;
        
            if ($location === null)
                $location = $this->getHitSection($pos, $shooter, $fire->turn, $weapon);
            
            $systems = array();
            $totalStructure = 0;

            foreach ($this->systems as $system){
                // For flash damage, only take into account the systems
                // that are still alive and are not structure.
                // The turn in which a system is destroyed is checked for each system
                // this is done to keep track of when the last system was destroyed on
                // this section, so we can decide whether to throw on the primary
                // hit section, or if we need to go to primary structure for overkill
                foreach ($system->damage as $damage){
                    if ($damage->turn === $fire->turn && $damage->destroyed){
                        // A system has had damage this turn. This means not all
                        // of them were destroyed at the start of this turn.
                        $destroyedThisTurn = true;
                    }
                }
                
                if ($weapon->flashDamage && ($system->isDestroyed() || $system instanceof Structure )){
                    continue;
                }
                    
                if ($system->location == $location || $system instanceof Structure){
                     $systems[] = $system;
                        
                    if ($system instanceof Structure){
                        $multiply = 0.5;
                            
                        $totalStructure += round($system->maxhealth * $multiply);
                    }else{
                        $totalStructure += $system->maxhealth;
                    }
                    
                }
            }   

            
            if(sizeof($systems) == 0){
                // All normal systems have already been destroyed on this section
                // If we already are doing primary:
                // If structure is gone, return null
                // else return structure
                if ($location == 0){
                    $structure = $this->getStructureSystem(0);
                    
                    if($structure->isDestroyed()){
                        return null;
                    }
                    else{
                        return $structure;
                    }
                }

                // all systems were destroyed. Go to primary.
                // Go to primary systems for flash damage
                // Go to primary structure for other weapons.
                if ($weapon->flashDamage){
                    return $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                }
                else{
                    if($destroyedThisTurn){
                        $structure = $this->getStructureSystem(0);

                        if($structure->isDestroyed()){
                            return null;
                        }
                        else{
                            return $structure;
                        }    
                    }
                    else{
                        return $this->getHitSystem($pos, $shooter, $fire, $weapon, 0);
                    }
                }
            }
            
            $roll = Dice::d($totalStructure);
            $goneTrough = 0;
            
            foreach ($systems as $system){
                $health = 0;
            
                if ($system->name == "structure"){
                    $multiply = 0.5;
                        
                    $health = round($system->maxhealth * $multiply);
                }else{
                    $health = $system->maxhealth;
                }
                
                if ($roll > $goneTrough && $roll <= ($goneTrough + $health)){
                    if ($system->isDestroyed())                        
                    {
                        $newSystem = $this->getUndamagedSameSystem($system, $location);
                        
                        if($newSystem != null){
                            return $newSystem;
                        }
 
                        if ($system instanceof Structure){
                            return null;}
                                
                        $structure = $this->getStructureSystem(0);
                        if ($structure == null || $structure->isDestroyed()){
                            return null;
                          
                        }else{
                            return $structure;
                        }
                    }

                    return $system;
                }
                
                $goneTrough += $health;
            }

            return null;
        }
    }
    
    class MediumShipLeftRight extends MediumShip{

        function __construct($id, $userid, $name, $slot){
            parent::__construct($id, $userid, $name, $slot);
        }


        public function getLocations(){
        debug::log("getLocations");         
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
    
    class LightShip extends BaseShip{
    
        public $shipSizeClass = 0;
        
        function __construct($id, $userid, $name, $slot){
            parent::__construct($id, $userid, $name, $slot);
        }
        
        public function getFireControlIndex(){
              return 1;
               
        }
        
        
        public function getHitSystem($pos, $shooter, $fire, $weapon, $location = 0){

            // Turn counter needed to keep track of when a section was destroyed.
            $destroyedThisTurn = false;
            
            $system = null;
            if ($fire->calledid != -1){
                $system = $this->getSystemById($fire->calledid);
            }
            
            if ($system != null && !$system->isDestroyed())
                return $system;
        
            $systems = array();
            $totalStructure = 0;

            foreach ($this->systems as $system){
                // For flash damage, only take into account the systems
                // that are still alive and are not structure.
                // The turn in which a system is destroyed is checked for each system
                // this is done to keep track of when the last system was destroyed on
                // this section, so we can decide whether to throw on the primary
                // hit section, or if we need to go to primary structure for overkill
                foreach ($system->damage as $damage){
                    if ($damage->turn === $fire->turn && $damage->destroyed){
                        // A system has had damage this turn. This means not all
                        // of them were destroyed at the start of this turn.
                        $destroyedThisTurn = true;
                    }
                }
                
                if ($weapon->flashDamage && ($system->isDestroyed() || $system instanceof Structure )){
                    continue;
                }
                    
                if ($system->location == $location || $system instanceof Structure){
                     $systems[] = $system;
                        
                    if ($system instanceof Structure){
                        $multiply = 0.5;
                            
                        $totalStructure += round($system->maxhealth * $multiply);
                    }else{
                        $totalStructure += $system->maxhealth;
                    }
                    
                }
            }   

            
            if(sizeof($systems) == 0){
                // All normal systems have already been destroyed
                // If structure is gone, return null
                // else return structure
                $structure = $this->getStructureSystem(0);

                if($structure->isDestroyed()){
                    return null;
                }
                else{
                    return $structure;
                }
            }
            
            $roll = Dice::d($totalStructure);
            $goneTrough = 0;
            
            foreach ($systems as $system){
                $health = 0;
            
                if ($system->name == "structure"){
                    $multiply = 0.5;
                        
                    $health = round($system->maxhealth * $multiply);
                }else{
                    $health = $system->maxhealth;
                }
                
                if ($roll > $goneTrough && $roll <= ($goneTrough + $health)){
                    if ($system->isDestroyed())                        
                    {
                        $newSystem = $this->getUndamagedSameSystem($system, $location);
                        
                        if($newSystem != null){
                            return $newSystem;
                        }
 
                        if ($system instanceof Structure){
                            return null;}
                                
                        $structure = $this->getStructureSystem(0);
                        if ($structure == null || $structure->isDestroyed()){
                            return null;
                          
                        }else{
                            return $structure;
                        }
                    }

                    return $system;
                }
                
                $goneTrough += $health;
            }

            return null;
        }
    }

    class OSAT extends MediumShip{
        public $osat = true;        
        public $canvasSize = 100;

        public function isDisabled(){
           return false;
        }


        public function getLocations(){
        debug::log("getLocations");         
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

        public function getPiercingLocations($shooter, $pos, $turn, $weapon){
            debug::log("getPiercingLocations");
            
            $location =  $this->activeHitLocation["loc"];
            
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


        public function getLocations(){
        debug::log("getLocations");         
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

        public function getLocations(){
        debug::log("getLocations");         
            $locs = array();

            $locs[] = array("loc" => 1, "min" => 270, "max" => 90, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 41, "min" => 330, "max" => 150, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 42, "min" => 30, "max" => 210, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 32, "min" => 90, "max" => 270, "profile" => $this->forwardDefense);
            $locs[] = array("loc" => 31, "min" => 150, "max" => 330, "profile" => $this->forwardDefense);

            return $locs;
        }
    }
?>
