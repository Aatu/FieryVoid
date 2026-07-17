<?php

    class Torpedo extends Weapon{
    
        public $ballistic = true;
        public $damageType = "Standard"; 
        public $weaponClass = "Ballistic"; 
		
		
        public $animation = "torpedo";
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
       
        /*moving to Weapon class - this is generally useful!
        public function isInDistanceRange($shooter, $target, $fireOrder)
        {
			if(!$this->ballistic) return true; //non-ballistic weapons don't risk target moving out of range
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $distanceRange = max($this->range, $this->distanceRange); //just in case distanceRange is not filled! Then it's assumed to be the same as launch range
            if($distanceRange <=0 ) return true; //0 means unlimited range

            if(mathlib::getDistanceHex($movement->position,  $target) > $distanceRange )
            {
                $fireOrder->pubnotes .= " FIRING SHOT: Target moved out of distance range.";
                return false;
            }

            return true;
        }
		*/
		
       
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
        }

    } //endof class Torpedo


    
    class IonTorpedo extends Torpedo{
    
        public $name = "ionTorpedo";
        public $displayName = "Ion Torpedo";
        public $range = 50;
        public $loadingtime = 2;
        
        public $fireControl = array(-4, 1, 3); // fighters, <mediums, <capitals 
        
        public $animation = "torpedo";
        public $animationColor = array(30, 170, 255);
		/*
        public $trailColor = array(141, 240, 255);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
		*/
		
		
        public $priority = 6;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $loadingTime = 2){
            $this->loadingtime = $loadingTime; //The mine version has loading time of 3.            
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 15;   }
        public function setMinDamage(){     $this->minDamage = 15; /*- $this->dp;*/      }
        public function setMaxDamage(){     $this->maxDamage = 15 ;/*- $this->dp;*/      }
    
    }//endof class IonTorpedo


    
    class BallisticTorpedo extends Torpedo{
    
        public $name = "ballisticTorpedo";
        public $displayName = "Ballistic Torpedo";
        public $range = 25;
        public $loadingtime = 1;
        public $shots = 6;
        public $defaultShots = 1;
        public $normalload = 6;
        
        public $fireControl = array(0, 3, 4); // fighters, <mediums, <capitals 
        
        public $trailColor = array(141, 240, 255);
        public $animation = "trail";

        public $priority = 5;

		//Saturation Mode (ships only): torpedoes fired at the same ship are merged into ONE fire order
		//by beforeFiringOrderResolution, then resolved as a single Pulse-type volley - one to-hit roll,
		//one interception, and every $grouping points the shot beats its needed number lands another
		//torpedo (base 1 + floor(margin/3)), capped by the number fired at that target. Fighter targets
		//are NOT saturated: they keep one torpedo per fighter (see fire() below).
        public $damageType = 'Pulse';
        public $grouping = 20; //+1 per 20%
        public $maxpulses = 6; //upper bound (matches max held torpedoes); real per-order cap set in fire()
        public $maxpulsesArray = array();

		public $canSplitShots = true;
    	public $calledShotMod = 0; //Cannot make called shots against ships anyway, but shouldn't get modifier when we specificy specific fighters
//        public $canChangeShots = true; //No longer needed.


        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);

        }


		/*Saturation Mode resolution. Ships: a merged fire order carries $fireOrder->shots torpedoes at
		one target; cap the Pulse volley at that count and let Weapon::fire() do one roll + one
		interception for the whole volley. Fighters: torpedoes are aimed one-per-fighter (own calledid),
		so resolve those in plain Standard mode - each torpedo is its own hit on its own fighter, no
		saturation bonus.*/
		public function fire($gamedata, $fireOrder){
			$target = $gamedata->getShipById($fireOrder->targetid);
			if ($target instanceof FighterFlight){ //individual targeting - one torpedo, one fighter
				$savedType = $this->damageType;
				$this->damageType = 'Standard'; //bypass the Pulse volley path in Weapon::fire()
				parent::fire($gamedata, $fireOrder);
				$this->damageType = $savedType;
				return;
			}
			$this->maxpulses = max(1, (int)$fireOrder->shots); //this order's torpedo count is the cap
			parent::fire($gamedata, $fireOrder);
		}

		//base number of torpedoes that land on a hit: the shot hit at all, so at least one
		protected function getPulses($turn){
			return 1;
		}

		//every $grouping (=3) points the shot beats its needed number lands an extra torpedo
		protected function getExtraPulses($needed, $rolled){
			return floor(($needed - $rolled) / $this->grouping);
		}

		public function rollPulses($turn, $needed, $rolled){
			$pulses = $this->getPulses($turn);
			$pulses += $this->getExtraPulses($needed, $rolled);
			$pulses = min($pulses, $this->maxpulses); //no more than torpedoes fired at this target
			$pulses = max($pulses, 1); //we only get here on a hit, so at least one lands
			return $pulses;
		}

      
        public function firedOnTurn($turn){  
        	$totalShots = 0; //Counter for shots this turns          
            foreach ($this->fireOrders as $fire){
                if ($fire->weaponid == $this->id && $fire->turn == $turn){
                    $totalShots += $fire->shots;
                }
            }
            return $totalShots;
        }
        
        public function calculateLoading(TacGamedata $gamedata)
        {
            $loading = new WeaponLoading($this->turnsloaded, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
            $shotsfired = $this->firedOnTurn(TacGamedata::$currentTurn);
            if (TacGamedata::$currentPhase == 2)
            {
                if  
                ( $this->isOfflineOnTurn(TacGamedata::$currentTurn) )
                {
                    $loading = new WeaponLoading(0, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
                }
                else if ($shotsfired)
                {
                    $newloading = $this->turnsloaded-$shotsfired;
                    if ($newloading < 0)
                        $newloading = 0;

                $loading = new WeaponLoading($newloading, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
                }
            }
            else if (TacGamedata::$currentPhase == 1)
            {
                $newloading = $this->turnsloaded+1;
                if ($newloading > $this->getNormalLoad())
                    $newloading = $this->getNormalLoad();

                $loading = new WeaponLoading($newloading, 0, 0, 0, $this->getLoadingTime(), $this->firingMode);
            }

            return $loading;
        }//endof calculateLoading()


		public function beforeFiringOrderResolution($gamedata) {
		    $turn = $gamedata->turn; // Assume $turn is accessible from $gamedata
		    $shipOrders = [];
		    $fighterOrders = [];

		    // Group fire orders by targetid and type of target
		    foreach ($this->fireOrders as $fire) {
		        if ($fire->weaponid == $this->id && $fire->turn == $turn) {
		            $targetId = $fire->targetid;
		            $targetShip = $gamedata->getShipById($targetId);
		            
		            if ($targetShip instanceof FighterFlight) {                    
		                if (!isset($fighterOrders[$targetId])) {
		                    $fighterOrders[$targetId] = [$fire]; // Initialize as an array for multiple orders
		                } else {
		                    $fighterOrders[$targetId][] = $fire; // Add to the list of orders for this target
		                }
		            } else {            
		                if (!isset($shipOrders[$targetId])) {
		                    $shipOrders[$targetId] = $fire;
		                } else {
		                    $shipOrders[$targetId]->shots += $fire->shots;
		                }
		            }
		        }
		    }

		    // Process fighterOrders to ensure unique calledid for each fire order
		    foreach ($fighterOrders as $targetId => $fires) {
		        $fighterFlight = $gamedata->getShipById($targetId);

		        // Get a list of undestroyed fighters in the flight
		        $fighterList = [];
		        foreach ($fighterFlight->systems as $fighter) {
		            if (!$fighter->isDestroyed()) {
		                $fighterList[] = $fighter; // Add to the list of valid fighters
		            }
		            array_unshift($fighterList, $fighter); // array_unshift adds element at the beginning of array rather than at the end		    
		        }

		        // Assign unique calledid to fire orders
		        $fighterIndex = 0; // Keep track of the current fighter being targeted
		        foreach ($fires as $fire) {
		            if (!isset($fighterList[$fighterIndex])) {
		                break; // If there are no more fighters to assign, stop
		            }
		            $fire->calledid = $fighterList[$fighterIndex]->id; // Assign a unique fighter
		            $fighterIndex++; // Move to the next fighter
		        }
		    }

		    // Combine shipOrders and fighterOrders into this->fireOrders
		    $combinedOrders = [];

		    // Merge shipOrders
		    if (!empty($shipOrders)) {
		        $combinedOrders = array_merge($combinedOrders, array_values($shipOrders));
		    }

		    // Merge fighterOrders
		    if (!empty($fighterOrders)) {
		        foreach ($fighterOrders as $orders) {
		            $combinedOrders = array_merge($combinedOrders, $orders); // Flatten nested arrays
		        }
		    }

		    $this->fireOrders = $combinedOrders;
		    
		}//endof beforeFiringOrderResolution()
		

		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}

			$this->data["Special"] .= "Can fire at multiple targets, providing it has enough shots available.";
			$this->data["Special"] .= "<br>Multiple torpedoes from the same launcher against the same ship are combined into a single volley with ONE to-hit roll (Saturation Mode): the shot lands one torpedo, plus one more for every 3 points it beats the needed number by (up to the number fired). Interception is rolled once for the whole volley.";
			$this->data["Special"] .= "<br>Against enemy fighters flights, torpedoes always attack a different fighter. If there are more torpedoes than fighters, excess munitions are lost.";
		}
	
	        
        public function onConstructed($ship, $turn, $phase){
            parent::onConstructed($ship, $turn, $phase);
            $this->shots = $this->turnsloaded;
        }        
        
        public function getDamage($fireOrder){        return Dice::d(10,2);   }
        public function setMinDamage(){     $this->minDamage = 2;   }
        public function setMaxDamage(){     $this->maxDamage = 20;     }
    
		public function stripForJson()
		{
			$strippedSystem = parent::stripForJson();
			$strippedSystem->maxVariableShots = $this->turnsloaded;
			return $strippedSystem;
		}     
    
    
    } //endof class BallisticTorpedo


//Kirishiac Torpedo that can split shots like Ballistic Torp
 class PhasedGraviticTorpedo extends BallisticTorpedo{
    
        public $name = "phasedGraviticTorpedo";
        public $displayName = "Phased Gravitic Torpedo";
        public $range = 50;
        public $loadingtime = 1;
        public $shots = 9;
        public $defaultShots = 1;
        public $normalload = 9;
        public $grouping = 15; //+1 per 15%        
        public $fireControl = array(3, 4, 5); // fighters, <mediums, <capitals

        public $trailColor = array(141, 240, 255);
        public $animation = "trail";

        public $priority = 5;

		//Pulse machinery (damageType='Pulse', getPulses/getExtraPulses/rollPulses) is inherited from
		//BallisticTorpedo. Unlike BallisticTorpedo, this launcher ALWAYS saturates - fighter flights get
		//the same single-roll pulse volley as ships (see beforeFiringOrderResolution + fire() below).
		//It also holds more torpedoes (9 vs 6).
        public $maxpulses = 9; //upper bound (matches max held torpedoes); real per-order cap set in fire()

		public $canSplitShots = true;
    	public $calledShotMod = 0; //Cannot make called shots against ships anyway, but shouldn't get modifier when we specificy specific fighters

		private $pairing = null;	//Which Heavy Orbital is it paired with? (null on standalone mounts)
		public $linkedOrbital = null; //set by KirishiacOrbital::addOrbitalWeapon - null on standalone mounts
		private $rolledMod = 0; //Captures reduction of damage reduction per torpedo 
		public $weaponClass = "Gravitic";

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $pairing = null){
            $this->pairing = $pairing;
            if ($pairing !== null) $this->displayName = 'Phased Gravitic Torpedo ' . $pairing;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }


		/*This weapon saturates fighter flights too (unlike BallisticTorpedo, which splits torpedoes
		one-per-fighter). So merge ALL torpedoes aimed at the same target - ship OR flight - into one
		fire order, summing shots, and leave calledid = -1 so a flight is hit by the standard Pulse
		distribution (getHitSystem picks fighters as a normal pulse would).*/
		public function beforeFiringOrderResolution($gamedata) {
			$turn = $gamedata->turn;
			$mergedOrders = [];
			foreach ($this->fireOrders as $fire) {
				if ($fire->weaponid != $this->id || $fire->turn != $turn) continue;
				$targetId = $fire->targetid;
				if (!isset($mergedOrders[$targetId])) {
					$mergedOrders[$targetId] = $fire; //first order at this target becomes the carrier
				} else {
					$mergedOrders[$targetId]->shots += $fire->shots; //fold the rest of the torpedoes in
				}
			}
			$this->fireOrders = array_values($mergedOrders);
		}//endof beforeFiringOrderResolution()


		/*Always a saturation volley - no fighter->Standard branch. The merged order carries every
		torpedo aimed at this target; cap the pulse count at that and let Weapon::fire() do the single
		to-hit roll and single interception.*/
		public function fire($gamedata, $fireOrder){
			$this->rolledMod = 0; //reset each shot.			
			$this->maxpulses = max(1, (int)$fireOrder->shots); //this order's torpedo count is the cap
			Weapon::fire($gamedata, $fireOrder);
		}


		/*paired-launcher overkill: while the Heavy Orbital is deployed, excess damage passes into
		the Orbital's structure; if the Orbital is already gone the excess is lost. While DOCKED
		the launcher is still hittable (docked sub-chart) but the orbital is part of the hull, so
		overkill follows the normal ship flow (section = combined Structure). Standalone mounts
		behave normally. Mirrors HypergravitonBeam::getOverkillDestination.*/
		public function getOverkillDestination($target){
			if ($this->linkedOrbital === null) return null; //standalone mount - normal flow
			if ($this->linkedOrbital->activeEffective) return null; //docked - overkill returns to the ship (combined Structure)
			if ($this->linkedOrbital->isDestroyed() || ($this->linkedOrbital->getRemainingHealth() == 0)) return false; //orbital gone - overkill lost
			return $this->linkedOrbital;
		}

		/*a Heavy Orbital's launcher normally stays OPERATIONAL while docked (with its stowed arcs);
		this guard only fires if a paired launcher was never given stowed arcs in the blueprint*/
		public function calculateHitBase($gamedata, $fireOrder){
			if ($this->stowed && $this->stowedArcStart === null){
				$fireOrder->needed = 0;
				$fireOrder->shotshit = 0;
				$fireOrder->pubnotes .= " Launcher is stowed (Orbital docked) - cannot fire.";
				$fireOrder->updated = true;
				return;
			}
			parent::calculateHitBase($gamedata, $fireOrder);
		}

		public function setSystemDataWindow($turn){
			//parent (BallisticTorpedo) already appends the Saturation-Mode + fighter-targeting lines;
			//only add the paired-Orbital specifics here.
			parent::setSystemDataWindow($turn);
			if ($this->linkedOrbital !== null){
				$this->data["Special"] .= "<br>Mounted on " . $this->linkedOrbital->displayName . ": cannot be targeted by called shots; overkill passes to the Orbital.";
				if ($this->stowedArcStart !== null){
					$this->data["Special"] .= "<br>Remains operational while the Orbital is docked, with a reduced arc (" . $this->stowedArcStart . ".." . $this->stowedArcEnd . " - the Arc line above always shows the arc currently in effect). May be powered down only while docked.";
				}
			}
		}
	  
        
		/*Phasing (crit-banking) is NOT done here any more - shieldInteractionDamage is called for EVERY
		in-arc shield during damage-mod calculation, so banking here double-hit overlapping-arc shields
		(game 4223 turn 3: a 330-degree hit banked crits on BOTH the F and L shields, when only L
		absorbed). Banking now happens once per torpedo in getFinalDamage(), against the single shield
		that actually absorbs/reduces. Here we only REPORT the shield's current reduction - which already
		reflects the crit just banked (getDefensiveDamageMod sums the params), so return it unchanged.*/
		public function shieldInteractionDamage($target, $shooter, $pos, $turn, $shield, $mod) {
			// younger races: absorption ignored entirely (house rule: EMShields still work)
			if ($target->factionAge <= 2 && !$shield instanceof EMShield) return 0;
			return $mod;
		}

		/*Bank the per-torpedo phasing crit ONCE, on the single shield that actually protects this hit,
		then let the normal damage flow run (it re-reads the crit live, so this shot's reduction already
		reflects it). Runs once per pulse.*/
		protected function getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder)
		{
			$this->applyPhasing($target, $shooter, $pos, $gamedata);
			return parent::getFinalDamage($shooter, $target, $pos, $gamedata, $fireOrder);
		}

		//Pick the one shield this hit will actually use and bank a single rolled crit on it.
		private function applyPhasing($target, $shooter, $pos, $gamedata){
			$turn = $gamedata->turn;

			$shield = $this->getPhasingShield($target, $shooter, $pos, $turn);
			if ($shield === null) return; //no shield protects this hit

			// cap: base absorption pool for projection shields (not reflected in a damage mod), else the
			// shield's live damage reduction (what's left to remove).
			if ($shield instanceof ThoughtShield || $shield instanceof ThirdspaceShield){
				$cap = $shield->getEffectiveBaseRating($turn);
			} else {
				$cap = max(0, $shield->getDefensiveDamageMod($target, $shooter, $pos, $turn, $this));
			}
			$amount = min(Dice::d(10, 1), (int)$cap);
			$this->bankReduction($shield, $target, $amount);
		}

		//The single shield that protects this hit: the capacity-pool absorber if any (Thought/Thirdspace),
		//otherwise the in-arc EM Shield with the greatest current damage reduction. Null if none applies.
		private function getPhasingShield($target, $shooter, $pos, $turn){
			//Younger races: the torpedo IGNORES non-EM shield absorption entirely (rules) - so it never
			//phases a capacity-pool projection shield on a younger-race ship; only EM Shields (house rule)
			//are still affected. (In practice projection shields only appear on Primordial/Ancient hulls.)
			$youngerRace = ($target->factionAge <= 2);

			//1) capacity-pool absorber (getSystemProtectingFromDamage picks exactly one)
			if (!$youngerRace){
				$protector = $target->getSystemProtectingFromDamage($shooter, $pos, $turn, $this, null, 0, false);
				if ($protector instanceof ThoughtShield || $protector instanceof ThirdspaceShield){
					return $protector;
				}
			}

			//2) EM Shields: lock onto ONE in-arc EM Shield for the whole volley and only ever phase that
			//one (never spill to another overlapping EM Shield, even after it reaches 0). Deterministic
			//first-in-arc pick (by construction order) so every torpedo in the volley agrees. Overlapping
			//EM Shields reduce by the same amount and getDamageMod takes the max, so which of the equal
			//ones we pick doesn't change the damage - only where the permanent crit lands.
			$bearing = ($pos !== null) ? $target->getBearingOnPos($pos) : $target->getBearingOnUnit($shooter);
			foreach ($target->systems as $sys){
				if (!($sys instanceof EMShield)) continue;
				if ($sys->isDestroyed($turn-1) || $sys->isOfflineOnTurn($turn)) continue;
				if (is_int($sys->startArc) && is_int($sys->endArc)){
					if (!mathlib::isInArc($bearing, $sys->startArc, $sys->endArc)) continue;
				}
				return $sys; //first in-arc EM Shield - stable across the volley
			}
			return null;
		}

		//Banks ONE permanent DamageReductionReduced critical carrying $amount in its param (not $amount
		//separate crits). No crit is added if $amount <= 0 (the shield is already phased to zero).
		private function bankReduction($shield, $target, $amount){
			$amount = (int)$amount;
			if ($amount <= 0) return; //already reduced to 0 - do not apply any further crits
			$crit = new DamageReductionReduced(-1, $target->id, $shield->id, 'DamageReductionReduced', TacGamedata::$currentTurn, 0, false, $amount);
			$crit->updated = true;
			$shield->criticals[] = $crit;
		}

//        public function getDamage($fireOrder){        return Dice::d(10,2);   }
        public function getDamage($fireOrder){        return 20;   } //flat 20 per torpedo for this test run
        public function setMinDamage(){     $this->minDamage = 20;   } //match flat getDamage (was 2, for Dice::d(10,2))
        public function setMaxDamage(){     $this->maxDamage = 20;     }
    
		public function stripForJson()
		{
			$strippedSystem = parent::stripForJson();
			$strippedSystem->maxVariableShots = $this->turnsloaded;
			if ($this->linkedOrbital !== null){ //paired with a Heavy Orbital - dynamic state the client needs on every load
				$strippedSystem->stowed = (bool)$this->stowed;
				$strippedSystem->powerLocked = !$this->stowed; //deployed launcher cannot be powered down (client Off-button gate)
				$strippedSystem->isTargetable = $this->isTargetable;
				$strippedSystem->repairPriority = $this->repairPriority;
				$strippedSystem->privateRepairOnly = $this->privateRepairOnly; //deployed Heavy Orbital weapon: excluded from the ship-wide SelfRepair list (only the orbital's on-board SR)
				$strippedSystem->startArc = $this->startArc; //live arcs - reduced set while docked (applyStowedArcs)
				$strippedSystem->endArc = $this->endArc;
				$strippedSystem->stowedArcStart = $this->stowedArcStart; //non-null = operational while stowed (client fire/arc gates)
				$strippedSystem->stowedArcEnd = $this->stowedArcEnd;
				if ($this->structureHomeLocation !== null) $strippedSystem->structureHomeLocation = $this->structureHomeLocation;
			}
			return $strippedSystem;
		}


    } //endof class PhasedGraviticTorpedo



    class PlasmaWaveTorpedo extends Torpedo{
        public $name = "PlasmaWaveTorpedo";
        public $displayName = "Plasma Wave";
        public $iconPath = "plasmaWaveTorpedo.png";
        public $range = 30;
        public $loadingtime = 3;
        
        public $weaponClass = "Plasma"; //deals Plasma, not Ballistic, damage. Should be Ballistic(Plasma), but I had to choose ;)
        public $damageType = "Flash"; 
        
        
        public $fireControl = array(null, 0, 2); // fighters, <mediums, <capitals 
        
        public $animation = "torpedo";
        public $animationColor = array(75, 230, 90);
		/*
        public $trailColor = array(75, 230, 90);
        public $animationExplosionScale = 0.3;
        public $projectilespeed = 11;
        public $animationWidth = 10;
        public $trailLength = 10;
		*/
        public $priority = 1; //Flash! should strike first 
         
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 7;
            }
            if ( $powerReq == 0 ){
                $powerReq = 4;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    //ignores half armor (as a Plasma weapon should!) - now handled by standard routines
    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "Ignores half of armor.";
			$this->data["Special"] .= "<br>Ballistic weapon that can use offensive EW.";
		}
        
        
        public function getDamage($fireOrder){        return Dice::d(10, 3);   }
        public function setMinDamage(){     $this->minDamage = 3;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      } 
    
    }//endof class PlasmaWaveTorpedo



    class PacketTorpedo extends Torpedo{
        public $name = "PacketTorpedo";
        public $displayName = "Packet Torpedo";
        public $iconPath = "packetTorpedo.png";
        public $range = 50;  
        public $loadingtime = 2;
		public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation - such a method should be present in .js!
        
        public $weaponClass = "Ballistic"; 
        public $damageType = "Standard"; 
        
		public $hidetarget = true;
        
        public $fireControl = array(-6, 3, 3); // fighters, <mediums, <capitals 
		public $rangePenalty = 0.5; //-1/2 hexes - BUT ONLY AFTER 10 HEXES
        
        public $animation = "torpedo";
        public $animationColor = array(130, 170, 255);
		/*
        public $trailColor = array(191, 200, 215);
        public $animationExplosionScale = 0.25;
        public $projectilespeed = 14;
        public $animationWidth = 11;
        public $trailLength = 16;
		*/
        public $priority = 6; //heavy Standard
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 6;
            }
            if ( $powerReq == 0 ){
                $powerReq = 5;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
            	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "This weapon suffers range penalty (like direct fire weapons do), but only after first 10 hexes of distance.";
		$this->data["Special"] .= "<br>Also, targeting information is hidden for opponent - weapon will be marked as fired, but target will not be highlighted, and weapon will not be shown as incoming.";
	}
        
	    //override standard to skip first 10 hexes when calculating range penalty
		/*
	    public function calculateRangePenalty(OffsetCoordinate $pos, BaseShip $target)
	    {
			$targetPos = $target->getHexPos();
			$dis = mathlib::getDistanceHex($pos, $targetPos);
			$dis = max(0,$dis-10);//first 10 hexes are "free"

			$rangePenalty = ($this->rangePenalty * $dis);
			$notes = "shooter: " . $pos->q . "," . $pos->r . " target: " . $targetPos->q . "," . $targetPos->r . " dis: $dis, rangePenalty: $rangePenalty";
			return Array("rp" => $rangePenalty, "notes" => $notes);
	    }*/
		public function calculateRangePenalty($distance){
			$rangePenalty = 0;//base penalty
			$rangePenalty += $this->rangePenalty * max(0,$distance-10); //everything above 10 hexes receives range penalty
			return $rangePenalty;
		}
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+10;    }
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }//endof class PacketTorpedo



class PsionicTorpedo extends Torpedo{ //Powerful Thirdspace weapon that detonates and reduces armour of targets.  
        public $name = "PsionicTorpedo";
        public $displayName = "Psionic Torpedo";
        public $iconPath = "PsionicTorpedo.png";                
  	  	public $animation = "torpedo";
        public $animationColor = array(128, 0, 0);        

        public $range = 50;
        public $loadingtime = 2;
        
        public $weaponClass = "Psychic";
        public $damageType = "Standard";     
                
        public $fireControl = array(null, 4, 5); // fighters, <mediums, <capitals 
        public $priority = 1; //Flash! should strike first 
        
		private static $alreadyAffected = array();         
        
        public $boostable = true;
        public $boostEfficiency = 0;
        public $maxBoostLevel = 2; 
        
    protected $ewBoosted = true;         
        
		public $repairPriority = 5;//priority at which system is repaired (by self repair system); higher = sooner, default 4; 0 indicates that system cannot be repaired                 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 9;
            }
            if ( $powerReq == 0 ){
                $powerReq = 3;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }	
		
		//Ignores armor, nasty for flash damage on fighters.
		public function getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos=null){
		if (($system->advancedArmor) && (!$system->hardAdvancedArmor)){ //Negates Advanced armor's +2 bonus against ballistics and reduces by a further 2. No impact on hardened advanced armor
            $armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);
            $armour = $armour-4;
            return $armour;
		}elseif ($system->hardAdvancedArmor){
            $armour = parent::getSystemArmourBase($target, $system, $gamedata, $fireOrder, $pos);            
			return (($armour/2)+2);
		}else{					
			return 0;
        }
	}    	

	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);			
		//Weapon has hit, so apply+1 to crit roll, +1 to dropout roll to the damge elements
		$mod = 2;
//		if ($ship instanceof FighterFlight) $mod++;
		$system->critRollMod += $mod; 
		
		if (isset(PsionicTorpedo::$alreadyAffected[$ship->id])) return; //But not if affected already.											
		$boostlevel = $this->getBoostLevel($fireOrder->turn);			
		$effectEW = Dice::d(2,1)+$boostlevel;//strength of effect: -1 to -4 EW.				
		$effectIni = Dice::d(3,1)+$boostlevel;//strength of effect: -5 to -25 initiative.		
		$effectPower = Dice::d(4,1)+$boostlevel;//strength of effect: -1 to -6 to power.
//		$effectThrust = Dice::d(3,1); //Potentially could add additional crit effect.		
			
		$fireOrder->pubnotes .= "<br> Target has Initiative reduced, and suffers from power and scanner fluctations.";				
			
		if ($system->advancedArmor){		
			$effectEW = ceil($effectEW/2);//Other Ancients are somewhat resistant to pyschic attack from Thirdspace Aliens, 50% effect.		
			$effectIni = ceil($effectIni/2);  	
			$effectPower = 0; //Reducing power in Vorlons can make it impossible for them to commit Initial Orders.		
		}

		if ($ship instanceof FighterFlight){  //No additional effect on fighters beyond flash damage.			
			PsionicTorpedo::$alreadyAffected[$ship->id] = true;//mark affected already.	
			return;
		}else{				
			$CnC = $ship->getSystemByName("CnC");
			$reactor = $ship->getSystemByName("Reactor");
			$scanner = $ship->getSystemByName("Scanner");			
			PsionicTorpedo::$alreadyAffected[$ship->id] = true;//mark affected already.					
			for($i=1; $i<=$effectIni;$i++){				
				$crit = new tmpinidown(-1, $ship->id, $CnC->id, 'tmpinidown', $gamedata->turn); 
				$crit->updated = true;
		        $CnC->criticals[] =  $crit;
			}							
            if ($effectPower > 0) {
                for($i=1; $i<=$effectPower;$i++){
                    if ($reactor){
                        $crit = new OutputReduced1(-1, $ship->id, $reactor->id, "OutputReduced1", $gamedata->turn, $gamedata->turn+1);
                        $crit->updated = true;
                        $reactor->criticals[] =  $crit;
                    }     
                }
            }
			for($i=1; $i<=$effectEW;$i++){
				if ($scanner){//Some ships might not have a traditional scanner.						
					$crit = new OutputReduced1(-1, $ship->id, $scanner->id, "OutputReduced1", $gamedata->turn, $gamedata->turn+1);
					$crit->updated = true;
					$scanner->criticals[] =  $crit;
				}     
			}								
		}
			
	} //endof function onDamagedSystem	
    	
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);
			if (!isset($this->data["Special"])) {
				$this->data["Special"] = '';
			}else{
				$this->data["Special"] .= '<br>';
			}
			$this->data["Special"] .= "<br>Ignores armor (Advanced Armor is treated as 2 points less, Hardened Avanced Armor is treated as half +2 for ballistics).";	
			$this->data["Special"] .= "<br>Uses psychic energy to disrupt target next turn in the following ways:";
			$this->data["Special"] .= "<br> - 1d2 penalty to EW,";
			$this->data["Special"] .= "<br> - 1d3 penatly to Initiative,";
			$this->data["Special"] .= "<br> - 1d4 penalty to available power.";													
			$this->data["Special"] .= "<br>Ballistic weapon that can use offensive EW.";
			$this->data["Special"] .= "<br>Multiple Psionic Torpedoes do not stack effects (but do stack with Psychic Field).";			
			$this->data["Special"] .= "<br>Can be boosted twice using EW, for +4 damage per boost, and +1 to each disruptive effect above.";			
		    $this->data["Special"] .= "<br>Has +2 modifier to critical hits.";			
		}

        private function getExtraDamagebyBoostlevel($turn){
            $add = 0;
            switch($this->getBoostLevel($turn)){
                case 1:
                    $add = 4;
                    break;
                case 2:
                    $add = 8;
                    break;
                                        
                      
                default:
                    break;
            }
            return $add;
        }

         private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                if ($i->turn != $turn){
                   continue;
                }
                if ($i->type == 2){
                    $boostLevel += $i->amount;
                }
            }
            return $boostLevel;
        }
        
        public function getDamage($fireOrder){
            $add = $this->getExtraDamagebyBoostlevel($fireOrder->turn);
            $dmg = Dice::d(10,1) + 12 + $add ;
            return $dmg;
        }

        public function getAvgDamage(){
            $this->setMinDamage();
            $this->setMaxDamage();

            $min = $this->minDamage;
            $max = $this->maxDamage;
            $avg = round(($min+$max)/2);
            return $avg;
        }

        public function setMinDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->minDamage = 13 + ($boost * 4);
        }   

        public function setMaxDamage(){
            $turn = TacGamedata::$currentTurn;
            $boost = $this->getBoostLevel($turn);
            $this->maxDamage = 22 + ($boost * 4); 
		}

	public function stripForJson(){
		$strippedSystem = parent::stripForJson();
		$strippedSystem->ewBoosted = $this->ewBoosted;													
		return $strippedSystem;
	} 
		    
    }//endof class PsionicTorpedo


class LimpetBoreTorpedo extends Torpedo{
        public $name = "LimpetBoreTorpedo";
        public $displayName = "Limpet Bore Torpedo";
		    public $iconPath = "LimpetBoreTorpedo.png";
        public $animation = "trail";
        public $trailColor = array(141, 240, 255);
        public $animationColor = array(50, 50, 50);
        public $animationExplosionScale = 0.2;
        public $projectilespeed = 10;
        public $animationWidth = 4;
        public $trailLength = 100;    

        public $useOEW = true; 
        public $ballistic = true;
        public $range = 30;
        public $distanceRange = 30;
        public $ammunition = 5; //limited number of shots

		public $noPrimaryHits = true; //cannot hit PRIMARY from outer table

        public $calledShotMod = 0; //instead of usual -8
        
        public $loadingtime = 2; // 1/2 turns
        public $rangePenalty = 0;
        public $fireControl = array(null, 2, 4);
	    
		public $noOverkill = true; //Matter weapon
		public $priority = 9; //Matter weapon
		    

		public $damageType = "Standard"; //MANDATORY (first letter upcase) actual mode of dealing damage (Standard, Flash, Raking, Pulse...) - overrides $this->data["Damage type"] if set!
	    public $weaponClass = "Matter"; //should be Ballistic and Matter, but FV does not allow that. Instead decrease advanced armor encountered by 2 points (if any) (usually system does that, but it will account for Ballistic and not Matter)

		protected $overrideCallingRestrictions = true;//Front End: To allow this as a ballistic weapon to make called shots.
		protected $canTargetAllExtSections = true; //Front End: Allow this weapon to target any system on external sections of target ship.  Keep separate from above in case useful at a later point.
		protected $canOnlyCalledShot = true;//Front End: To block it from targeting ships, only systems.		

			 
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		        //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }     
	    
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Special"] = "Ballistic weapon used ONLY for Called Shots.";
            $this->data["Special"] .= "<br>Can target any system on target, even those not facing the firing vessel.";            
            $this->data["Special"] .= "<br>Once it hits a ship, it will attach to target system and try to damage it the following turn by adding a critical effect.";
            $this->data["Special"] .= "<br>If the target system is not on the section that the torpedo hits, it will take an additional turn(s) to travel to it.";            
            $this->data["Special"] .= "<br>Once attached, this critical effect will remain until target system is destroyed, or after five failed attempts.";            
            $this->data["Special"] .= "<br>Has no effect on OSATs or targets equipped with Advanced Armor.";
            $this->data["Special"] .= "<br>No Called Shot penalty.";
            $this->data["Special"] .= "<br>Ignores Armor. No Overkill.";                     
            $this->data["Ammunition"] = $this->ammunition;
        }
        

        public function setAmmo($firingMode, $amount){
            $this->ammunition = $amount;
        }
        
        
       public function fire($gamedata, $fireOrder){ //note ammo usage
            parent::fire($gamedata, $fireOrder);
            $this->ammunition--;
            Manager::updateAmmoInfo($fireOrder->shooterid, $this->id, $gamedata->id, $this->firingMode, $this->ammunition, $gamedata->turn);
        }


	protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder){ //really no matter what exactly was hit!
		
		if ($system->advancedArmor) {//no effect on Advanced Armor	
			$fireOrder->pubnotes .= "<br> Limpet Bore cannot attach to advanced armor.";				
			return; 	
		}

		if ($system->id != $fireOrder->calledid) {//In case it ends up in general hit chart somehow.
			$fireOrder->pubnotes .= "<br> Limpet Bore was not targeted at a system.";				
			return; 	
		}
		
		if ($ship instanceof OSAT) {//In case it ends up in general hit chart somehow.
			$fireOrder->pubnotes .= "<br> Limpet Bore cannot attach to OSAT units.";				
			return; 	
		}			
			
		if($system){
		
			$locationPairsTwoTurn = [//Create list of non-adjacent sections, that Limpet Bore will take TWO turns to reach.
			    [1, 2],
			    [2, 1],
			    [3, 4],
			    [4, 3],
			    [1, 42],
			    [42, 1],
			    [1, 32],
			    [32, 1],			    
			    [2, 31],
			    [31, 2],
			    [2, 41],
			    [41, 2]				    			    			    			    
			];						
			$locationPairsThreeTurn = [//Create list of OPPOSITE sections in six sided units, that Limpet Bore will take THREE turns to reach.
			    [32, 41],
			    [41, 32],
			    [31, 42],
			    [42, 31]			    			    			    			    
			];		

			$turnActivates = $gamedata->turn+1;//Usually Limpet will attack the turn AFTER it hits.
						    			
				if($system->location == $fireOrder->chosenLocation || $ship instanceof LCV){//Hits same side as target system or an LCV.  Attaches (current turn +1)
					
					$fireOrder->pubnotes .= "<br> Limpet Bore attaches to ship, it will attack target system next turn.";				
					$crit = new LimpetBore(-1, $ship->id, $system->id, 'LimpetBore', $gamedata->turn+1); //The crit takes effect the FOLLOWING turn.
					$crit->updated = true;
					$system->criticals[] =  $crit;
								
				}elseif(in_array([$system->location, $fireOrder->chosenLocation], $locationPairsTwoTurn)){//Non-Adjacent locations, takes longer to travel there.
					$turnActivates += 2;//Add two turn of travel time before attaching (current turn +3).
					
					if($ship instanceof MediumShip) $turnActivates = $gamedata->turn+2;//MCVs and HCVs can only have a maximum of 1 turn travel time.
					if($ship instanceof VreeCapital) $turnActivates -= 1; //Easier to traverse Vree ships due to wider structure arcs.						
					if((($system->location == 1 && $fireOrder->chosenLocation == 2) || ($system->location == 2 && $fireOrder->chosenLocation == 1)) && $ship instanceof StarBaseSixSections) $turnActivates += 1; //Starbases 1 and 2 locs are actually OPPOSITE!
	
																					
						//Add crit to show Limpet Bore is on its way.
						$fireOrder->pubnotes .= "<br>Limpet Bore attaches to ship and will traverse the hull to attack target system.";				
						$crit = new LimpetBoreTravelling(-1, $ship->id, $system->id, 'LimpetBoreTravelling', $gamedata->turn+1, $turnActivates-1); //The crit takes effect the turn after next.
						$crit->updated = true;
						$system->criticals[] =  $crit;
					
						//Now add crit to actually show Limpet attached and will attack.			
						$crit2 = new LimpetBore(-1, $ship->id, $system->id, 'LimpetBore', $turnActivates); //The crit takes effect the FOLLOWING turn.
						$crit2->updated = true;
						$system->criticals[] =  $crit2;	
																	
				}elseif(in_array([$system->location, $fireOrder->chosenLocation], $locationPairsThreeTurn)){//Opposite locations, takes longer to travel there.
					$turnActivates += 3;//Add three turns of travel time before attaching (current turn +4).
					
					if($ship instanceof MediumShip) $turnActivates = $gamedata->turn+2;//MCVs can only have a maximum of 1 turn travel time.
					if($ship instanceof VreeCapital) $turnActivates -= 1; //Easier to traverse Vree ships due to wider structure arcs.
																	
						//Add crit to show Limpet Bore is on its way.
						$fireOrder->pubnotes .= "<br>Limpet Bore attaches to ship and will traverse the hull to attack target system.";				
						$crit = new LimpetBoreTravelling(-1, $ship->id, $system->id, 'LimpetBoreTravelling', $gamedata->turn+1, $turnActivates-1); //The crit takes effect the turn after next.
						$crit->updated = true;
						$system->criticals[] =  $crit;
						
						//Now add crit to actually show Limpet attached and will attack.			
						$crit2 = new LimpetBore(-1, $ship->id, $system->id, 'LimpetBore', $turnActivates); //The crit takes effect the FOLLOWING turn.
						$crit2->updated = true;
						$system->criticals[] =  $crit2;	
																	
				}else{//Adjacent sections, Primary sections or anything else.
					$turnActivates += 1;//Add extra turn of travel time, attaches current turn +2.
					
						//Add crit to show Limpet Bore is on its way.
						$fireOrder->pubnotes .= "<br>Limpet Bore attaches to ship and will traverse the hull to attack target system.";					
						$crit = new LimpetBoreTravelling(-1, $ship->id, $system->id, 'LimpetBoreTravelling', $gamedata->turn+1, $gamedata->turn+1); //The crit takes effect the turn after next.
						$crit->updated = true;
						$system->criticals[] =  $crit;
						
						//Now add crit to actually show Limpet attached and will attack.			
						$crit2 = new LimpetBore(-1, $ship->id, $system->id, 'LimpetBore', $turnActivates); //The crit takes effect the FOLLOWING turn.
						$crit2->updated = true;
						$system->criticals[] =  $crit2;										
				}		
				
			}
			parent::onDamagedSystem($ship, $system, $damage, $armour, $gamedata, $fireOrder);			
	}//endof onDamagedSystem() 	
		
		
    public function getDamage($fireOrder){ //Damage is handled in criticalPhaseEffects() once Limpet Bore attaches.
            return 0;
    }
    
    
    public function setMinDamage(){     $this->minDamage = 12;      } //However, keep these values for intercept calculations.
    public function setMaxDamage(){     $this->maxDamage = 30;      }

    public function stripForJson() {
            $strippedSystem = parent::stripForJson();    
            $strippedSystem->ammunition = $this->ammunition;
            $strippedSystem->overrideCallingRestrictions = $this->overrideCallingRestrictions;
            $strippedSystem->canOnlyCalledShot = $this->canOnlyCalledShot;   
            $strippedSystem->canTargetAllExtSections = $this->canTargetAllExtSections;                            
            return $strippedSystem;
    }
		
}//endof LimpetBoreTorp



class LimpetBoreTorpedoBase extends LimpetBoreTorpedo{
        public $name = "LimpetBoreTorpedoBase";
        public $displayName = "Limpet Bore Torpedo";
 
        public $range = 60;
        public $distanceRange = 60;
        public $ammunition = 10; //limited number of shots.  Should be 15 with two reload phases every 5 shots, so we'll settle for just 10
        
        public $loadingtime = 1; 
	    		
}//endof LimpetBoreBase


	//Custom Updagrade Version of Packet Torpedo for Abraxas' campaign.
   	class FlexPacketTorpedo extends Torpedo{
        public $name = "FlexPacketTorpedo";
        public $displayName = "Flexible Packet Torpedo";
        public $iconPath = "flexPacketTorpedo.png";
        public $range = 50;  
		public $specialRangeCalculation = true; //to inform front end that it should use weapon-specific range penalty calculation
        
        public $weaponClass = "Ballistic"; 
        public $damageType = "Standard"; 

		public $loadingtime = 1;
		public $normalload = 2;        
		public $hidetarget = true;
		
		public $firingMode = 1;	
		public $firingModes = array(
			1 => "Torpedo"
		);		
        
        public $fireControl = array(-6, 3, 3); // fighters, <mediums, <capitals 
		public $rangePenalty = 0.5; //-1/2 hexes - BUT ONLY AFTER 10 HEXES
        
        public $animation = "torpedo";
        public $animationColor = array(130, 170, 255);

		private $firedInRapidMode = false; //was this weapon fired in rapid mode (this turn)?
        public $priority = 6; //heavy Standard
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            //maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ){
                $maxhealth = 9;
            }
            if ( $powerReq == 0 ){
                $powerReq = 9;
            }
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
            	
	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn);
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}
		$this->data["Special"] .= "Weapon suffers range penalty (like direct fire weapons do), but only after first 20 hexes of distance.";		
		$this->data["Special"] .= "<br>Can fire after only recharging for one turn, but range penalty is applied after first 5 hexes of distance.";					
		$this->data["Special"] .= "<br>Targeting information is hidden for opponent - weapon will be marked as fired, but target will not be highlighted, and weapon will not be shown as incoming.";
	}
        
		
	private function nullFireControl() {//Extra function needed to null Fire Control values across ALL ammo types in recalculateFireControl.
			$this->fireControl = array(null,null,null);//I need this method if launched has NO ammo modes.
			$this->fireControlArray = array();		
	}		

	
	// This method generates additional non-standard information in the form of individual system notes
	 public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
			$ship = $this->getUnit();
			switch($gameData->phase){								
				case 1: //Initial phase 
					//if weapon is marked as firing in Rapid mode, make a note of it!
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						if($this->firedInRapidMode){
							$notekey = 'RapidFire';
							$noteHuman = 'fired in Rapid mode';
							$noteValue = 'R';
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$noteValue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue
						}		
					}							
                break;
			}					

	} //endof function generateIndividualNotes
	
	//act on notes just loaded - to be redefined by systems as necessary
	public function onIndividualNotesLoaded($gamedata){
		foreach ($this->individualNotes as $currNote) 
			if($currNote->turn == $gamedata->turn) if ($currNote->notevalue == 'R'){ //only current round matters!
			$this->firedInRapidMode = true;			
		}	
		//and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)		
		$this->individualNotes = array();
	} //endof function onIndividualNotesLoaded
	
	
	public function doIndividualNotesTransfer(){
		//data received in variable individualNotesTransfer, further functions will look for variable firedInRapidMode
		if(is_array($this->individualNotesTransfer)) foreach($this->individualNotesTransfer as $entry) {
			if ($entry == 'R') $this->firedInRapidMode = true;
			if ($entry == 'L') $this->firedInLongRangeMode = true;				
		}
		
		$this->individualNotesTransfer = array(); //empty, just in case
	}			
		
		public function calculateRangePenalty($distance){

			if($this->firedInRapidMode){
				$rangePenalty = 0;//base penalty
				$rangePenalty += $this->rangePenalty * max(0,$distance - 5); //Normal range penalty in rapid mode
				return $rangePenalty;				
			}else{
				$rangePenalty = 0;//base penalty
				$rangePenalty += $this->rangePenalty * max(0,$distance-20); //everything above 10 hexes receives range penalty
				return $rangePenalty;
			}
		}
			
	public function stripForJson(){
		$strippedSystem = parent::stripForJson();	
		$strippedSystem->firedInRapidMode = $this->firedInRapidMode;			
		return $strippedSystem;
	}
        
        public function getDamage($fireOrder){        return Dice::d(10, 2)+10;    }
        public function setMinDamage(){     $this->minDamage = 12;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }//endof class FlexPacketTorpedo
    
	

	
	
    
    
?>
