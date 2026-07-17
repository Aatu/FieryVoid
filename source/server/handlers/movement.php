<?php
    class Movement{

        /*
         * Rollout safety switch for server-side thrust validation (validateThrustPayment).
         *
         * false (LOG-ONLY, default): the validator DETECTS illegal maneuvers and logs
         *   them, but returns the submitted movement UNCHANGED - it cannot alter or break
         *   any live game. Run in this mode first and watch the logs; a legal game should
         *   produce zero "would reject" lines.
         *
         * true  (ENFORCE): illegal maneuvers are actually dropped and the tail rebuilt as
         *   straight-line movement. Flip this to true only once log-only mode has proven
         *   silent on real, in-progress games.
         */
        public static $enforceThrustValidation = false;

        private static function checkIsNewMove($gamedata, $ship, $move){
            
            
            
            $newship = $gamedata->getShipById($ship->id);
   
            $newmove = $newship->getMovementById($move->id);

            if (!$newmove)
                return true;
          
            return false;
            
        }

        public static function validateMovement($gamedata, $ship){
            
            if ($gamedata->phase == 3){

                if (!($ship instanceof FighterFlight))
                    $ship->movement = array();

                for ($i=count($ship->movement)-1;$i>=0;$i--){
                    $move = $ship->movement[$i];

                    if (($move->type != "pivotright" && $move->type != "pivotleft" )
						||( $move->turn != $gamedata->turn )
						||(!self::checkIsNewMove($gamedata, $ship, $move))){
                        unset($ship->movement[$i]);
                       
                    }

                }
                
            }else{
                
            //These parts are never used since validateMovement is only called during FireGamePhase, and it has a mistake in it anyway... DK - Nov 2025    
                //$ship = $gamedata->ships[$gamedata->activeship];
                //$moves = $ships[$gamedata->activeship]->movement;     
                
            }
            
            return true;

        }

        /*
         * Server-side thrust validation for a submitted movement array.
         *
         * The client is the only place that normally enforces "a maneuver must be
         * fully paid before it can be committed" (doneAssignThrust). The server has
         * historically trusted whatever movement the client POSTs, so a tampered or
         * buggy client could submit an illegal maneuver - e.g. a turn paid with less
         * than the required directional thrust, or paid through thrusters that are
         * destroyed (game 6790: a Tinashi turned using only 3 of the 7 required thrust
         * because its rear thrusters were gone). This is the authoritative backstop.
         *
         * For each COMMITTED maneuver move (turn/pivot/roll/slip) that carries a real
         * required-thrust array, we recompute calculateThrustStillReq in STRICT mode
         * (the same math the client uses, but counting only surviving thrusters - thrust
         * routed through a destroyed thruster does not count). If any requirement is
         * still unpaid, the maneuver is illegal.
         *
         * An illegal maneuver invalidates every later move this turn (their positions
         * were plotted assuming the maneuver happened), so we drop the maneuver and
         * everything after it. A ship MUST still travel its full speed in hexes, so we
         * do not simply stop it short: instead we rebuild the tail as straight-line
         * "move" steps along the ship's last legal heading until it has covered its full
         * speed, then a final "end". The ship keeps its pre-maneuver heading/facing and
         * coasts straight for the rest of the turn.
         *
         * Returns the (possibly rebuilt) movement array. Flights pay through the "any"
         * slot and validate the same way. Server-generated types (start/end/sync/
         * attached) and non-thrust moves (plain move, jink, speedchange, halfPhase,
         * contract, detach) carry no directional requirement and pass through.
         */
        public static function validateThrustPayment($ship, $turn){
            if (!is_array($ship->movement))
                return $ship->movement;

            $maneuverTypes = array(
                "turnleft" => true, "turnright" => true,
                "pivotleft" => true, "pivotright" => true,
                "roll" => true, "slipleft" => true, "slipright" => true,
            );

            $hexTypes = array("move" => true, "slipleft" => true, "slipright" => true);

            $sanitised = array();
            $lastLegal = null;      // last move we accepted
            $hexesUsed = 0;         // hexes of speed already spent in the legal prefix
            $truncated = false;
            $illegalType = null;

            foreach ($ship->movement as $move){
                // Preturn/forced moves are generated server-side (rollovers, forced
                // pivots) and are trusted; other-turn moves are history. Keep as-is.
                if ($move->turn != $turn || $move->preturn || !empty($move->forced)){
                    $sanitised[] = $move;
                    $lastLegal = $move;
                    if (isset($hexTypes[$move->type])) $hexesUsed++;
                    continue;
                }

                // Note: $move->commit is not carried through the POST (it defaults to
                // true server-side), so every submitted maneuver is validated - which is
                // exactly the strict behaviour we want.
                $isManeuver = isset($maneuverTypes[$move->type]);
                $needsCheck = $isManeuver
                    && is_array($move->requiredThrust)
                    && self::requiresThrust($move->requiredThrust);

                if ($needsCheck){
                    // excludeDestroyed=true: thrust routed through destroyed thrusters
                    // does not count, so a dead-thruster maneuver is correctly rejected.
                    $stillReq = self::calculateThrustStillReq($ship, $move, true);
                    $unpaid = false;
                    foreach ($stillReq as $req){
                        if ($req > 0){ $unpaid = true; break; }
                    }

                    if ($unpaid){
                        // Illegal maneuver: drop it and everything after it this turn.
                        $truncated = true;
                        $illegalType = $move->type;
                        break;
                    }
                }

                $sanitised[] = $move;
                $lastLegal = $move;
                if (isset($hexTypes[$move->type])) $hexesUsed++;
            }

            if (!$truncated)
                return $ship->movement;

            // LOG-ONLY rollout mode: an illegal maneuver was detected, but we do NOT
            // modify the movement - just record what we WOULD have done. This makes the
            // check incapable of altering a live game while we confirm (via the logs)
            // that it never fires on legitimate play. Flip $enforceThrustValidation to
            // true only once these lines have proven absent for legal games.
            if (!self::$enforceThrustValidation){
                error_log("validateThrustPayment[LOG-ONLY]: WOULD reject illegal '"
                    . $illegalType . "' maneuver for ship " . $ship->id . " turn " . $turn
                    . " (unpaid required thrust). Movement left UNCHANGED.");
                return $ship->movement;
            }

            // Rebuild the tail: the ship must still cover its full speed in hexes, so
            // coast straight along its last legal heading for the remaining hexes, then
            // end. Anchor on the last accepted move (its pre-maneuver position/heading/
            // facing/speed); fall back to the ship's last committed move if nothing this
            // turn was accepted (illegal maneuver was the very first move).
            $anchor = $lastLegal ?: $ship->getLastMovement();
            if ($anchor){
                $speed   = (int)$anchor->speed;
                $heading = (int)$anchor->heading;
                $facing  = (int)$anchor->facing;
                // Ensure a real OffsetCoordinate so moveToDirection() is available even if
                // the anchor arrived from POST as an array/stdClass position.
                $pos     = new OffsetCoordinate($anchor->position);

                // One straight "move" per remaining hex of speed.
                for ($h = $hexesUsed; $h < $speed; $h++){
                    $pos = $pos->moveToDirection($heading);
                    $straight = new MovementOrder(
                        null, "move", $pos, 0, 0,
                        $speed, $heading, $facing,
                        false, $turn, 0, 0
                    );
                    $straight->requiredThrust = array(null, null, null, null, null);
                    $straight->assignedThrust = array();
                    $sanitised[] = $straight;
                }

                $sanitised[] = new MovementOrder(
                    null, "end", $pos, 0, 0,
                    $speed, $heading, $facing,
                    false, $turn, 0, 0
                );
            }

            debug::log("validateThrustPayment: rejected illegal '" . $illegalType
                . "' maneuver for ship " . $ship->id . " turn " . $turn
                . " (unpaid required thrust); rebuilt straight-line movement to full speed ("
                . ($anchor ? (int)$anchor->speed : 0) . " hexes).");

            return $sanitised;
        }

        /* True if a requiredThrust array asks for any positive amount of thrust. */
        private static function requiresThrust($requiredThrust){
            foreach ($requiredThrust as $req){
                if ($req > 0) return true;
            }
            return false;
        }


        public static function isPivoting($ship, $turn, $gamedata = null){
			if ($ship->agile || $ship instanceof FighterFlight)
				return 0;

            $pivoting = 0; // 0: false, 1: left, 2:right
            foreach ($ship->movement as $move){
                if ($move->turn != $turn || $turn == null)
                    continue;

                if ($move->type == "isPivotingLeft")
                    $pivoting = 1;

                if ($move->type == "isPivotingRight")
                    $pivoting = 2;


                if ($move->type == "pivotright" && !$move->preturn){
                    if ($pivoting == 1){
                        $pivoting = 0;
                    }else if ($pivoting == 0){
                        $pivoting = 2;
                    }
                }

                if ($move->type == "pivotleft" && !$move->preturn){
                    if ($pivoting == 2){
                        $pivoting = 0;
                    }else if ($pivoting == 0){
                        $pivoting = 1;
                    }
                }

                if ($move->value == "turnIntoPivot" && !$move->preturn){
  				   $pivoting = 0;
				}
            }

            // If attached via Grappling Claw and not pivoting itself, inherit host's pivot state
            if ($pivoting === 0 && $gamedata !== null && !empty($ship->attached)) {
                $hostId = key($ship->attached);
                $hostShip = $gamedata->getShipById($hostId);
                if ($hostShip && !($hostShip instanceof FighterFlight)) {
                    $pivoting = self::isPivoting($hostShip, $turn);
                }
            }

            return $pivoting;
        }
        
        public static function hasPivoted($ship, $turn, $gamedata = null){
            foreach ($ship->movement as $move){
                if ($move->turn != $turn)
                    continue;

                if ($move->type == "pivotleft" || $move->type == "pivotright" )
                    return true;

                if ($move->type == "isPivotingRight" || $move->type == "isPivotingLeft" )
                    return true;
            }

            // If attached via Grappling Claw, inherit pivot penalty from host ship
            if ($gamedata !== null && !empty($ship->attached)) {
                $hostId = key($ship->attached);
                $hostShip = $gamedata->getShipById($hostId);
                if ($hostShip && !($hostShip instanceof FighterFlight)) {
                    if (self::hasPivoted($hostShip, $turn)) return true;
                }
            }

            return false;
        }

        public static function hasTurned($ship, $turn){
            foreach ($ship->movement as $move){
                if ($move->turn != $turn)
                    continue;
                if ($move->type == "turneleft" || $move->type == "turnright" )
                    return true;
           }
            return false;
        }
        
        public static function hasRolled($ship, $turn, $gamedata = null){
            foreach ($ship->movement as $move){
                if ($move->turn != $turn)
                    continue;

                if ($move->type == "roll" || $move->type == "isRolling" )
                    return true;
            }

            // If attached via Grappling Claw, inherit roll penalty from host ship
            if ($gamedata !== null && !empty($ship->attached)) {
                $hostId = key($ship->attached);
                $hostShip = $gamedata->getShipById($hostId);
                if ($hostShip && !($hostShip instanceof FighterFlight)) {
                    if (self::hasRolled($hostShip, $turn)) return true;
                }
            }

            return false;
        }
        
        public static function isRolling($ship, $turn, $gamedata = null){
			if ($ship->agile || $ship instanceof FighterFlight)
				return false;

            $rolling = false;
            foreach ($ship->movement as $move){
                if ($move->turn != $turn || $turn == null)
                    continue;

                if ($move->type == "isRolling")
                    $rolling = true;

                if ($move->type == "roll"){
                    $rolling = !$rolling;
                }

            }

            // If attached via Grappling Claw, inherit rolling penalty from host ship
            if (!$rolling && $gamedata !== null && !empty($ship->attached)) {
                $hostId = key($ship->attached);
                $hostShip = $gamedata->getShipById($hostId);
                if ($hostShip && !($hostShip instanceof FighterFlight)) {
                    $rolling = self::isRolling($hostShip, $turn);
                }
            }

            return $rolling;
        }
        		
        public static function isHalfPhased($ship, $turn){
            foreach ($ship->movement as $move){
                if ($move->turn != $turn) continue;
                if ($move->type == "halfPhase" ) return true;
            }
            return false;
        }
		
        public static function getJinking($ship, $turn){
			$jinking = 0;
			
			foreach ($ship->movement as $move){
           
                if ($move->type == "jink" && $move->turn == $turn){
                    $jinking += (int)$move->value;
                }
                                   
                           
            }
          
            return $jinking;
		}    
		
		public static function getCombatPivots($ship, $turn){
			$pivots = 0;
			
			foreach ($ship->movement as $move){
           
                if (($move->type == "pivotleft" || $move->type == "pivotright" ) && $move->value == "combatpivot" && $move->turn == $turn){
					$pivots++;
                }
                                   
                           
            }
           
            return $pivots;
		}        
       
        public static function getTurnDelay($ship){
            $turndelay = 0;
			if (!is_array($ship->movement))
				return 0;
			
            $last = null;
            
            foreach ($ship->movement as $move){
                
                if ($move->turn < TacGamedata::$currentTurn -1)
                    continue;
           
                if (($move->type == "move" || $move->type == "slipright" || $move->type == "slipleft" ) && $turndelay > 0){
                    $turndelay--;
                }
                                   
                if ($move->type == "turnleft" || $move->type == "turnright"){
                    if (!$ship->agile || !$last 
                        || ($last->type != "turnleft" && $last->type != "turnright"))
                    {
                        $turndelay += self::calculateTurndelay($ship, $move);
                    }
                }
                $last = $move;
            }
        
            if ($turndelay < 0)
                $turndelay = 0;
        
            return $turndelay;
        }
        
        public static function calculateTurndelay($ship, $move){
            $speed = $move->speed;
			if ($speed == 0)
				return 0;
				
            $turndelay = ceil($speed * $ship->turndelaycost);
            
            if ($ship instanceof FighterFlight)
				return $turndelay;
            
            $turndelay -= self::calculateExtraThrustSpent($ship, $move);
            if ($turndelay < 1)
                $turndelay = 1;
                
            return $turndelay;
        }
        
        public static function calculateExtraThrustSpent($ship, $move){
            $reg = self::calculateThrustStillReq($ship, $move);

            $extra = 0 - $reg[0];
            
            if ( $extra < 0)
                $extra = 0;
                
            return $extra;

        }
        
        public static function calculateThrustStillReq($ship, $move, $excludeDestroyed = false){
            $assignedarray = self::calculateAssignedThrust($ship, $move, $excludeDestroyed);
            $requiredThrust = $move->requiredThrust;
            $stillReq = $requiredThrust;
            $any = 0;
        
            foreach ($requiredThrust as $i=>$req){
                $ass = $assignedarray[$i];
                
                if ( $ass>$req){
                    $stillReq[$i] = 0;
                    $any += $ass-$req;
                }else{
                    $stillReq[$i] -= $ass;
                }   
            }
            
            $stillReq[0] -= $any;
            
            
            if ($move->type == "pivotright" || $move->type == "pivotleft" && !($ship instanceof FighterFlight)) {
            
                $reversed = self::hasSidesReversedForMovement($ship);
                $right = ($move->type == "pivotright");
                if ($reversed){
                    $right = !$right;
                }
                
                if ($right){
                    if ($assignedarray[1]>0 || $assignedarray[3]>0){
                        $stillReq[2] = -1;
                        $stillReq[4] = -1;
                    }
                    if ($assignedarray[2]>0 || $assignedarray[4]>0){
                        $stillReq[1] = -1;
                        $stillReq[3] = -1;
                    }
                
                }else{
                    if ($assignedarray[1]>0 || $assignedarray[4]>0){
                        $stillReq[2] = -1;
                        $stillReq[3] = -1;
                    }
                    if ($assignedarray[2]>0 || $assignedarray[3]>0){
                        $stillReq[1] = -1;
                        $stillReq[4] = -1;
                    }
                }
                
                
            }
            
            
            return $stillReq;
            
        
        
        }
        
        public static function calculateAssignedThrust($ship, $move, $excludeDestroyed = false){
            $assignedarray = array(0,0,0,0,0);



            foreach ($move->assignedThrust as $i=>$value){
				if (empty($value))
					continue;

				 if ($ship instanceof FighterFlight){

					$assignedarray[0] += $value;

				}else{
					// Guard against stale thrust keyed by a system id that no longer
					// resolves to a Thruster. System ids are POSITIONAL (assigned in
					// addSystem() construction order), so editing a ship's blueprint
					// mid-game — e.g. inserting FighterRails — shifts every later id.
					// Persisted assignedThrust then points at a different system class
					// (a StdParticleBeam has no ->direction), which used to fatal here
					// with "Undefined property: ...::$direction" and 500 the whole game
					// at load. Skip such orphaned allocations instead of crashing.
					$system = $ship->systems[$i] ?? null;
					if ($system === null || !($system instanceof Thruster)) {
						continue;
					}
					// Strict (server thrust validation) only: a destroyed thruster cannot
					// provide thrust, so a tampered client that routes required thrust
					// through a dead thruster must not pass. The default path leaves
					// existing (turn-delay) callers untouched.
					// CRITICAL: check destruction as of the PREVIOUS turn, not the current one.
						// Movement is plotted in phase 2; damage lands in the fire phase (4). A
						// thruster destroyed during turn T's combat was still alive when turn T's
						// movement was paid, so that payment was LEGAL. Using the current turn here
						// wrongly rejects it (verified: game 4031 turn 2, Demos paid rear thrust
						// through a thruster only destroyed later that same turn). AS OF T-1 = gone
						// before this turn's movement.
						if ($excludeDestroyed && $system->isDestroyed(TacGamedata::$currentTurn - 1)) {
						continue;
					}
					$direction = $system->direction;
					$assignedarray[$direction] += $value;
				}
            }

            return $assignedarray;
        }
        
        public static function hasSidesReversedForMovement($ship){
            $back = self::isGoingBackwards($ship);
            
            $reversed = (($back || self::isRolled($ship)) && !($back && self::isRolled($ship)));
            
            return $reversed;
        }
        

        public static function setPreturnMovementStatusForShip($ship, $turn, $gamedata = null){
            $turn = $turn -1;                 
            
			// Handle attached ships synchronization
			if ($gamedata && !empty($ship->attached)) {             
				$parentId = key($ship->attached);
				$parent = $gamedata->getShipById($parentId);
				if ($parent) {
					$rolled = self::isRolled($parent, $turn);
					$rolling = self::isRolling($parent, $turn);
					$pivoting = self::isPivoting($parent, $turn); // 0: false, 1: left, 2:right
					$lastmove = $parent->getLastMovement();
					
					$location = $ship->attached[$parent->id];
					// Prefer the precise entry-side offset recorded at attach time; fall back to
					// the location-derived offset for in-progress games attached before this change.
					$locOffset = isset($ship->attachedFacing[$parent->id])
						? $ship->attachedFacing[$parent->id]
						: self::getAttachedFacingOffset($location);
					
					// Breaching pods as FighterFlight units cannot roll themselves, 
					// so we adjust their absolute facing by 180 degrees (+3) instead.
					$facingOffset = $locOffset;
					if ($ship instanceof FighterFlight && $rolled) {
						$facingOffset = ($facingOffset + 3) % 6;
					}
					
					$movements = array();            
					
					if ($pivoting == 1){
						$movements[] = new MovementOrder(null, "isPivotingLeft", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, ($lastmove->facing + $facingOffset)%6, true, ($turn+1), 0, $ship->iniative);
					}else if ($pivoting == 2){
						$movements[] = new MovementOrder(null, "isPivotingRight", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, ($lastmove->facing + $facingOffset)%6, true, ($turn+1), 0, $ship->iniative);
					}
					
					if ($rolling && !($ship instanceof FighterFlight)){
						$movements[] = new MovementOrder(null, "isRolling", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, ($lastmove->facing + $facingOffset)%6, true, ($turn+1), 0, $ship->iniative);
						$rolled = !$rolled;
					}
					
					if ($rolled && !($ship instanceof FighterFlight)){
						$movements[] = new MovementOrder(null, "isRolled", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, ($lastmove->facing + $facingOffset)%6, true, ($turn+1), 0, $ship->iniative);
					}
					
					// Ensure the pod always has at least one movement order for the new turn 
					// to synchronize its speed, heading, and position with the host ship.
					if (empty($movements)) {
						$movements[] = new MovementOrder(null, "sync", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, ($lastmove->facing + $facingOffset)%6, true, ($turn+1), 0, $ship->iniative);
					}
					
					return $movements;
				}         
			}

            $rolled = self::isRolled($ship, $turn);
            $rolling = self::isRolling($ship, $turn);
            $pivoting = self::isPivoting($ship, $turn); // 0: false, 1: left, 2:right
            $lastmove = $ship->getLastMovement();
            $movements = array();            
            
            if ($pivoting == 1){
                $movements[] = new MovementOrder(null, "isPivotingLeft", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1), 0, $ship->iniative);
                //$movements[] = new MovementOrder(null, "pivotleft", $lastmove->x, $lastmove->y, $lastmove->speed, $lastmove->heading, MathLib::addToHexFacing($lastmove->facing , -1), true, ($turn+1));
            }else if ($pivoting == 2){
                $movements[] = new MovementOrder(null, "isPivotingRight", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1), 0, $ship->iniative);
                //$movements[] = new MovementOrder(null, "pivotright", $lastmove->x, $lastmove->y, $lastmove->speed, $lastmove->heading, MathLib::addToHexFacing($lastmove->facing , 1), true, ($turn+1));
            }
            
            if ($rolling){
                $movements[] = new MovementOrder(null, "isRolling", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1), 0, $ship->iniative);
                $rolled = !$rolled;
            }
            if ($rolled){
                $movements[] = new MovementOrder(null, "isRolled", $lastmove->position, 0,0, $lastmove->speed, $lastmove->heading, $lastmove->facing, true, ($turn+1), 0, $ship->iniative);
            }
			
			//Find Engine on Ship
			$engine = $ship->getSystemByName("Engine");
			
			if($engine){
				//Check Engine Crits, if has the ControlsStuck crit on this turn ending, create new Movement Order.
				foreach ($engine->criticals as $critical) {
					if ($critical->phpclass === "ControlsStuck" && $critical->inEffect) {
						
						
						// Check if it matches the current turn
						if ($critical->turn == $turn) {		                
							// Found a matching "Engine Shorted" critical for this turn
							$movements[] = self::doStuckEngine($ship, $engine, $turn, $lastmove);
							break;
						}
					}
				}
			}
		
            return $movements;
            
        }//endof setPreturnMovementStatusForShip()

 
        
         public static function isRolled($ship, $turn = -1){
             $ret = false;
             $turnwas = 0;
             $lastmove = $ship->getLastMovement();
             $movements = array();
             
             if ($ship->agile){
                 foreach ($ship->movement as $move){
//                     if ($move->turn != $turn && $turn != -1)
//                         continue;
                     
//                     if ($move->turn != $turnwas){
//                         $ret = false;
//                     }

//                     if ($move->type == "isRolled"){
//                         $ret = true;
//                         $turnwas = $move->turn;
//                     }

                     if ($move->type == "roll")
                         $ret = !$ret;
                }
            }
            else
            {
                foreach ($ship->movement as $move){
                    if ($move->turn != $turn && $turn != -1)
                        continue;

                    if ($move->turn != $turnwas){
                        $ret = false;
                    }

                    if ($move->type == "isRolled"){
                        $ret = true;
                        $turnwas = $move->turn;
                    }
                }
            }
            
            return $ret;
        }
		
		private static function isGoingBackwards($ship){
			$lastmove = $ship->getLastMovement();
			$heading = $lastmove->heading;
			$facing = $lastmove->facing;
			
			return (Mathlib::addToHexFacing($heading, 3) == $facing);
				
		}
		
		public static function getAmountChanneled($system, $ship, $turn){
			$used = 0;
			foreach ($ship->movement as $movement){
				if ($movement->turn != $turn)
					continue;
				
				//print(var_dump($movement->assignedThrust));
				//print("Now handling ".$system->id ."\n\n");
				if (!isset($movement->assignedThrust[$system->id]))
					continue;
					
				$assigned = $movement->assignedThrust[$system->id];
				
				if ($assigned != null){
					$used += $assigned;
				}
				
			}
			
			if ($system->hasCritical("FirstThrustIgnored", $turn))
				$used--;
			
			if ($system->hasCritical("FirstThrustIgnoredOneTurn", $turn))
				$used--;			
				
			$crits = $system->hasCritical("HalfEfficiency", $turn);
			$used = round($used/($crits+1));
			
			return $used;
		
		}


	    //Called from canAccelerate() below	- basically going neither ahead nor backwards
        public static function isOutOfAlignment($ship) {
			$lastmove = $ship->getLastMovement();
			$heading = $lastmove->heading;
			$facing = $lastmove->facing;			
			       
	        if ($facing == $heading || Mathlib::addToHexFacing($facing, 3) == $heading || Mathlib::addToHexFacing($facing, -3) == $heading) return false; //in alignment either way
	        return true;
	    }//endof isOutOfAlignment()

	     
		//Called from Engine class function, doEngineShorted()
		public static function hasMainThruster($ship) {
		    $mainThrusters = array_filter($ship->systems, function($system) {
		        return $system instanceof Thruster && $system->direction == 2;
		    });

		    return !empty($mainThrusters);
		}//endof hasMainThrusters()


		//Called from Engine class function, doEngineShorted()
		public static function canAccelerate($ship, $gamedata) {
		    if ($ship->base || $ship->smallBase || $ship->osat || $ship->isDestroyed()) {
		        return false;
		    }

		    // Check if all engines are destroyed
		    $engines = $ship->getSystemsByName('Engine');
		    $allEnginesDestroyed = array_reduce($engines, function($carry, $engine) {
		        return $carry && $engine->isDestroyed();
		    }, true);

		    if ($allEnginesDestroyed) {
		        return false;
		    }

		    // Engine Shorted Crit: Thrust only via main thrusters
		    if (self::isOutOfAlignment($ship)) {
		        return false;
		    }

		    return true;
		}//endof canAccelerate()		

		//Called from doStuckEgine() below, which is called from setPreturnMovementStatus above :)
		public static function sortThrustersAllocations($mainThrusters, $maxThrust) {
		    $remainingThrust = $maxThrust; // Total thrust to allocate
		    $allocations = []; // Array to store final thrust allocation

		    // First, assign maximum thrust to each thruster (up to its output limit)
		    foreach ($mainThrusters as $thruster) {
		        if ($remainingThrust <= 0) {
		            break; // No more thrust to allocate
		        }

		        // Calculate the maximum thrust that can be assigned to this thruster
		        $thrustForThisThruster = min($thruster->output, $remainingThrust);
		        $allocations[$thruster->id] = $thrustForThisThruster;
		        $remainingThrust -= $thrustForThisThruster; // Deduct allocated thrust from remaining

		        // If this thruster is fully allocated, continue to the next one
		    }

		    // If there's any remaining thrust, distribute it evenly across all thrusters
		    if ($remainingThrust > 0) {
		        // Loop through each thruster and add as evenly as possible
		        $thrusterCount = count($mainThrusters);
		        $evenThrust = floor($remainingThrust / $thrusterCount);
		        $leftoverThrust = $remainingThrust % $thrusterCount;

		        foreach ($mainThrusters as $thruster) {
		            if (!isset($allocations[$thruster->id])) {
		                $allocations[$thruster->id] = 0; // Initialize if not allocated
		            }
		            $allocations[$thruster->id] += $evenThrust; // Add even thrust
		        }

		        // Distribute any leftover thrust
		        foreach ($mainThrusters as $thruster) {
		            if ($leftoverThrust > 0) {
		                $allocations[$thruster->id]++;
		                $leftoverThrust--;
		            }
		        }
		    }

		    return $allocations;
		}//endof sortThrustersAllocations()

		
		//Called from setPreturnMovementStatus above if conditions are met.
		public static function doStuckEngine($ship, $engine, $turn, $lastmove) {

		    $newMovement = null;
            
		    // Calculate acceleration from stuck engine
		    $maxThrust = $engine->getOutputWhenOffline();
	        foreach ($engine->criticals as $crit){
	            $maxThrust += $crit->outputMod;//outputMod is negative
	        }
//	        $maxThrust += $engine->getBoostLevel($turn); //Actually this si next turns thrust, where there would be no boost as engine offline.
	        	        		    
    
		    $maxAccel = floor($maxThrust / $ship->accelcost);
			$newHeading	= $lastmove->heading;	 
			    
		    if(self::isGoingBackwards($ship)){ //If main thrusters fire when backwards it SLOWS the ship!
		    	$newSpeed = $lastmove->speed - $maxAccel;
				if($newSpeed < 0 ){
					$newHeading = MathLib::addToHexFacing($lastmove->heading, 3);
            		$newSpeed = $newSpeed * -1;
				}
			}else{			
		    	$newSpeed = $lastmove->speed + $maxAccel;				
			}	
		    $value = $newSpeed - $lastmove->speed;	//Not sure this is needed, but just in case!

		    // Create new movement order with updated speed
		    $newMovement = new MovementOrder(null, "speedchange", $lastmove->position, 0, 0, $newSpeed, $newHeading, $lastmove->facing, true, ($turn+1), $value, $ship->iniative);
		    			
			$mainThrusters = array();
		    $thrusters = $ship->getSystemsByName('Thruster');
		    		    
		    foreach($thrusters as $thruster){
		    	if($thruster->location == 2) $mainThrusters[] = $thruster;
			}		    	   	

			// Allocate thrust to thrusters
			$allocatedThrust = self::sortThrustersAllocations($mainThrusters, $maxThrust);

			// Iterate through the allocated thrust and assign it
			foreach ($allocatedThrust as $systemId => $thrustAllocation) {
			    $newMovement->assignedThrust[$systemId] = $thrustAllocation;  
			}

			return $newMovement;

		}//endof doStuckEngine()

		public static function getAttachedFacingOffset($location) {
			$locOffset = 0;
			if ($location == 1) $locOffset = 3; // Forward section, pod faces Aft
			else if ($location == 2) $locOffset = 0; // Aft section, pod faces Forward
			else if (in_array($location, [3, 32])) $locOffset = 1; // Port, pod faces Starboard-Forward
			else if ($location == 31) $locOffset = 2; // Port-Forward, pod faces Starboard-Aft
			else if (in_array($location, [4, 42])) $locOffset = 5; // Starboard, pod faces Port-Forward
			else if ($location == 41) $locOffset = 4; // Starboard-Forward, pod faces Port-Aft

			return $locOffset;
		}

		// Returns the precise hex-side facing offset (0-5) for a ship attaching to a target,
		// derived from their current geometric positions. Roll-invariant: offset is measured
		// in the host's unrolled facing-frame so the (FighterFlight + rolled-host) +3 mirror
		// at runtime continues to compose correctly.
		public static function getAttachFacingOffsetFromBearing($target, $shooter) {
			if (!$target || !$shooter) return null;
			$tf = $target->getFacingAngle();
			$compassHeading = mathlib::getCompassHeadingOfShip($target, $shooter);
			$relativeBearing = mathlib::addToDirection($compassHeading, -$tf);
			$entrySide = ((int) round($relativeBearing / 60.0)) % 6;
			return ($entrySide + 3) % 6;
		}

    

    }
?>
