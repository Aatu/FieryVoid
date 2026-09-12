<?php
	class EW{
	
	
		public static function validateEW($ship, $gamedata){

		    $EW = $ship->EW;
			$turn = $gamedata->turn;
			$used = 0;
      /* //Removed to allow Contrained ELINT vessels to work e.g. Mindriders - DK - 20.7.24  			
			foreach ($EW as $entry){
			
				if ($turn != $entry->turn)
					continue;

				$used += $entry->amount;
				
        
                if ($entry->type === "DIST")
                {
                   if ($entry->amount % 3 !== 0)
                       throw new Exception("Validation of EW failed: DIST ew not divisable by 3");
                }
			}
	*/		
			/* Marcin Sawicki, 27.09.2019: apparently at this point ship enhancements are NOT taken into account (and scanner output can be affected by them)
			hence I disable this check! judging it not to be necessary.
			*/
			/*
			$available = self::getScannerOutput($ship, $gamedata->turn);

			if ($available >= $used){
				return true;
			}

            throw new Exception("EW validation failed: used more than available (shipId: $ship->id). Used $used available $available");
			*/
			return true;
			
		}
		
		public static function getScannerOutput($ship, $turn){
		
            if ($ship->isDisabled())
                return 0;
            
			$output = 0;
			foreach ($ship->systems as $system){
			
				if ($system->outputType == "EW"){
					$output += $system->getOutput($turn);
				}
			}
            		$CnC = $ship->getSystemByName("CnC");
			if ($CnC && $CnC->hasCritical("RestrictedEW"))
				$output -= 2*$CnC->hasCritical("RestrictedEW");	
//GTS 13Jan22
            		$BSGHybrid = $ship->getSystemByName("BSGHybrid");
			if ($BSGHybrid && $BSGHybrid->hasCritical("SensorLoss"))
				$output -= 3*$BSGHybrid->hasCritical("SensorLoss");	

			/* WALKERS OF SIGMA-957 - the Energy Draining Field's total-EW drain used to be
			   subtracted here off an EdfEwDrain critical parked on the CnC. It now rides the
			   SCANNER and is applied inside Scanner::getOutput(), so the loop above already
			   has it - and, unlike this version, so does the client (ew.js sums the same
			   getOutput over the EW systems; it never looked at the CnC). See the comment on
			   Scanner::getOutput. */

			
			    if ($output < 0)
				$output = 0;
            
			return $output;
				
		}
        
		/* ==========================================================================================
		   WALKERS OF SIGMA-957 - MAPMAKER ELECTRONIC WARFARE (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12)
		   Client mirror: ew.getFlightEwCapacity / ew.getFlightEwLeft in public/client/ew.js.
		   ⚠️ MIRROR SET - a change to any one of these is a change to its twin.

		   ⭐⭐ WHY THIS IS NOT A BRANCH INSIDE getScannerOutput(), which is where the plan first put it.
		   getScannerOutput() has three other callers and each of them would have inherited a meaning it
		   was never asked for: getUnspentEw() would let the EW Detector's saved point be drawn out of a
		   flight's OEW pool AND would count its "Detect Mines" entries (bought with Offensive Bonus,
		   not with EW) as spending it; the Chameleon plausibility ceiling is about hulls; and
		   JumpEngine::rollExitDeviation reads it as a SENSOR RATING, so a Mapmaker's jump-point exit
		   scatter would silently change the day Stage 13 gives it a Jump Engine. A separate function
		   with one caller has none of that reach, and the gate stays exactly one property read.
		   ========================================================================================== */

		/* THE POOL, or 0 for every flight in the game but MapmakerProbes - and 0 for every SHIP, which
		   is deliberate: a ship's pool is its scanner output and is asked for elsewhere. */
		public static function getFlightEwCapacity($ship){
			if (!($ship instanceof FighterFlight)) return 0;

			return max(0, (int)$ship->ewCapacity);
		}

		/* WHAT IS LEFT OF IT. Counts OEW rows only, mirroring the ship-side rule that DEW is the
		   REMAINDER rather than an allocation (which is what makes convertUnusedToDEW idempotent).
		   ⚠️ "Detect Mines" rows are NOT counted. They come out of the flight's Offensive Bonus, which
		   is the other pool - see FighterFlight::$ewCapacity. */
		public static function getFlightEwLeft($ship, $turn){
			$capacity = self::getFlightEwCapacity($ship);
			if ($capacity <= 0) return 0;

			$used = 0;
			foreach ($ship->EW as $entry){
				if ($entry->turn != $turn) continue;
				if ($entry->type !== "OEW") continue;
				$used += (int)$entry->amount;
			}

			return $capacity - $used;
		}

		/* ⭐ THE ONLY THING STANDING BETWEEN A TAMPERED CLIENT AND UNLIMITED FIGHTER OEW (3.11 item 6).
		   EW::validateEW() returns true unconditionally - the ship-side budget check was disabled years
		   ago for Constrained ELINT hulls - so a flight-only clamp here is the whole of the server's
		   enforcement. Called from InitialOrdersGamePhase::process immediately before submitEW(), on the
		   POST-side ship, so what is clamped is what gets written.

		   ⚠️ IT RUNS ON EVERY FLIGHT, NOT ONLY ON THE ONES WITH A POOL, and that is the point: an
		   ORDINARY flight's capacity is 0, so every OEW/DEW row it carries is dropped. Gating on
		   ewCapacity > 0 would have left the tamper hole open for exactly the 99% of flights that
		   should never have had a row in the first place. A flight's EW array holds at most a couple of
		   entries, so the loop is cheaper than the property read the plan budgeted for.

		   ⚠️ POSTED ORDER IS SPEND ORDER, and it favours OEW by construction: the client pushes each
		   OEW row as the player creates it and appends the DEW remainder last, at commit. So an
		   over-budget array loses its DEW first, which is the same answer the client's own arithmetic
		   would have given.

		   ⚠️ EVERY OTHER TYPE IS LEFT ALONE. "Detect Mines" is the flight's Offensive Bonus being spent
		   and has nothing to do with this pool; touching it here would break every ordinary flight's
		   mine detection. */
		/* ⭐ WALKERS_OF_SIGMA_PLAN.md 3.14f (Stage 19, user ruling 2026-09-12) - A SHIP RIDING A
		   DOCKING BAY IS OUT OF THE ACTIVE EW GAME, BOTH WAYS.

		   "Waymarkers should also not use EW on transition Docking/Launching turns, nor should
		   ships have the opportunity to use any targeted EW on it."

		   ⚠️ IT KEEPS ITS DEW (user ruling, same day). Only ACTIVE allocations stop; unspent points
		   still fall into DEW the way they do for every ship, so a riding Waymarker is no easier to
		   hit than usual. What it loses is the ability to spend, and what its enemies lose is the
		   lock - after which they take the ordinary no-lock range penalty, which is the engine's
		   standing rule and is deliberately untouched.

		   ⚠️⚠️ $ship IS THE POST-SIDE COPY AND HAS NO NOTES, so $ship->attached is EMPTY on it
		   (arch_post_side_ship_reconstruction). Every predicate here is asked of $gamedata, the
		   RELOADED authoritative gamedata - which is also why this takes one and clampFlightEw,
		   its neighbour, does not.

		   Runs beside clampFlightEw for the same reason that one exists: EW::validateEW() returns
		   true unconditionally, so a submission is otherwise trusted wholesale. The client refuses
		   the allocation and hides the buttons; this is what makes it a rule. */
		public static function stripDockingRiderEw($ship, $gamedata, $turn){
			if (!$gamedata) return;

			$riders = self::collectDockingRiderIds($gamedata);
			if (empty($riders)) return;                       //empty in every game with no Docking Bay ride

			$sourceSuspended = isset($riders[(int)$ship->id]);
			$kept = array();

			foreach ($ship->EW as $entry){
				if ($entry->turn != $turn)        { $kept[] = $entry; continue; }
				if ($entry->type === 'DEW')       { $kept[] = $entry; continue; }   //the rider keeps its DEW
				if ($sourceSuspended) continue;                                     //spends nothing else
				if (isset($riders[(int)$entry->targetid])) continue;                //and nothing is spent AT one
				$kept[] = $entry;
			}

			$ship->EW = $kept;
		}

		/* Every unit currently riding a Docking Bay, as a SET of ids. Collected once per submitting
		 * ship rather than asked per EW row, and empty in every game that has no Docking Bay in it -
		 * the isDockingBay test rejects the whole fleet before anything else happens. */
		private static function collectDockingRiderIds($gamedata){
			$ids = array();
			foreach ($gamedata->ships as $carrier){
				if (!is_array($carrier->systems)) continue;
				foreach ($carrier->systems as $bay){
					if (empty($bay->isDockingBay) || empty($bay->shipsAttaching)) continue;
					foreach ($bay->shipsAttaching as $entry){
						$id = (int)($entry['shipId'] ?? 0);
						if ($id > 0) $ids[$id] = true;
					}
				}
			}
			return $ids;
		}

		public static function clampFlightEw($ship, $turn){
			if (!($ship instanceof FighterFlight)) return;

			$remaining = self::getFlightEwCapacity($ship);
			$kept = array();

			foreach ($ship->EW as $entry){
				if ($entry->turn != $turn){ $kept[] = $entry; continue; }
				if ($entry->type !== "OEW" && $entry->type !== "DEW"){ $kept[] = $entry; continue; }

				$allowed = min(max(0, (int)$entry->amount), $remaining);
				if ($allowed <= 0) continue;                 //dropped entirely - no zero rows in tac_ew

				$entry->amount = $allowed;
				$remaining -= $allowed;
				$kept[] = $entry;
			}

			$ship->EW = $kept;
		}

    public static function getBlanketDEW($gamedata, $target)
        {
            $FDEW = 0;
            foreach ($gamedata->ships as $ship)
            {
                if ( ($ship->team == $target->team) 
					&& $ship->isElint()
                    && Mathlib::getDistanceHex($target, $ship) <= 20
				){
                    $blanket = $ship->getBlanketDEW($gamedata->turn);
                    if ( $blanket > $FDEW ) $FDEW = $blanket;
                }
            }

		if($target->hasSpecialAbility("ConstrainedEW")){//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
            $FDEW = $FDEW * 0.2;			
		}else{
            $FDEW = $FDEW * 0.25;
		}    
			//if(($target->jammerMissile) && $FDEW < 2) $FDEW = 2; //Jammer Missiles provide 2 BDEW to all ships in range, but not in combination with normal BDEW!
			if(isset(AmmoMissileJ::$alreadyJammed[$target->id]) && $FDEW < 2) $FDEW = 2; //Jammer Missiles provide 2 BDEW to all ships in range, but not in combination with normal BDEW!            
				            
            return $FDEW;
        }
        
        public static function getSupportedOEW($gamedata, $ship, $target)
        {
			$jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter" => $ship, "target" => $target));
			if ($jammerValue>0) {
				return 0; //no lock-on on supported ship negates SOEW, if any
			}
			/*replaced by code above
			if ( ($ship->faction != $target->faction) && (!$ship->hasSpecialAbility("AdvancedSensors")) ){
					$jammer = $target->getSystemByName("jammer");
				if($jammer != null && $jammer->getOutput()> 0 ){ // Jammer protected ships cannot be targetten for SOEW
				return 0;
				}
			}
			*/
			
            $amount = 0;
            foreach ($gamedata->ships as $elint)
            {
                if ($elint->id === $ship->id) 
                    continue;
                
                if (!$elint->isElint())
                    continue;
                
                if (Mathlib::getDistanceHex( $target, $elint ) > 30) 
                    continue;
                
                if (Mathlib::getDistanceHex( $ship, $elint ) > 30) 
                    continue;

                if (!$elint->getEWbyType("SOEW", $gamedata->turn, $ship)) 
                    continue;

                $jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter" => $elint, "target" => $target));
				if ($jammerValue>0) {
					//return 40; //No sure why we returned 40, seems like an error - DK Nov 2025
					continue; //no lock-on negates SOEW, if any
				}                

                //Check line of sight between ELINT and target/shooter
                $elintPos = $elint->getHexPos();
                $shooterPos = $ship->getHexPos();
                $targetPos = $target->getHexPos();                

                $losBlockedTarget  = $elint->isLoSBlocked($elintPos, $targetPos, $gamedata); //Defaults false e.g. line of sight NOT blocked.
                if($losBlockedTarget) continue; //Line of sight blocked to one of the relevant units, skip.         

                $losBlockedShooter  = $elint->isLoSBlocked($elintPos, $shooterPos, $gamedata); //Defaults false e.g. line of sight NOT blocked.
                if($losBlockedShooter) continue; //Line of sight blocked to one of the relevant units, skip.                                       

				if($elint->hasSpecialAbility("ConstrainedEW")){//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
				    $foew = $elint->getEWByType("OEW", $gamedata->turn, $target) * 0.33;
				    $foew = round($foew * 3) / 3;		
				}else{
          	      $foew = $elint->getEWByType("OEW", $gamedata->turn, $target) * 0.5;
				}
								
				$dist = EW::getDistruptionEW($gamedata, $elint); //account for ElInt being disrupted
				$foew = $foew-$dist;		
				
                if ($foew > $amount) $amount = $foew;
            }

            if($ship instanceof FighterFlight){
                // Fighter flights only receive half the benefit of OEW
                // Kitchensink rules page 191
                return ($amount * 0.5);
            }else{
                return $amount;
            }
        }

        public static function getSupportedDEW($gamedata, $ship)
        {
            $amount = 0;
            foreach ($gamedata->ships as $elint)
            {
                if ($elint->id === $ship->id)
                    continue;
                
				/* faction should not affect it!
                if($elint->faction != $ship->faction)
                    continue;
                */
		    
                if (!$elint->isElint())
                    continue;
                
                if (Mathlib::getDistanceHex( $ship, $elint ) > 50)
                    continue;

                if($elint->hasSpecialAbility("ConstrainedEW")){//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
               		$fdew = $elint->getEWByType("SDEW", $gamedata->turn, $ship)*0.33;
				    $fdew = round($fdew * 3) / 3;	               				
				}else{
              	  $fdew = $elint->getEWByType("SDEW", $gamedata->turn, $ship)*0.5;
				}

                if ($fdew > $amount)
                $amount = $fdew;
            }

            return $amount;
        }

      
        public static function getDistruptionEW($gamedata, $ship)
        {
            $num = $ship->getOEWTargetNum($gamedata->turn);
            $amount = 0;

            foreach ($gamedata->ships as $elint)
            {
                if ($elint->id === $ship->id) continue;
                
                if (!$elint->isElint()) continue;
                
                if (Mathlib::getDistanceHex( $ship, $elint ) > 30) continue;               

                $elintPos = $elint->getHexPos();
                $shipPos = $ship->getHexPos();

                $losBlocked  = $elint->isLoSBlocked($elintPos, $shipPos, $gamedata); //Defaults false e.g. line of sight NOT blocked.
                if($losBlocked) continue; //Line of sight blocked to one of the relevant units, skip.                  

                if($elint->hasSpecialAbility("ConstrainedEW")){//Mindrider ships have less efficient ELINT abilities - DK 19.07.24.
        	        $fdew = $elint->getEWByType("DIST", $gamedata->turn, $ship) / 4 ;	
				}else{	
        	        $fdew = $elint->getEWByType("DIST", $gamedata->turn, $ship) / 3 ;//NOT *0.25;

                //if (fdew > amount)
                $amount += $fdew;
            }
            if ($num > 0) return $amount/$num;
            return 0; //NOT $amount;
    		 }
		}
		/* ============================================================================================
		   WALKERS OF SIGMA-957 - EW DETECTOR (WALKERS_OF_SIGMA_PLAN.md 3.8, Stage 10A)
		   Client mirror: ew.collectEwDetectors / ew.savedEwAllowanceFromDetectors /
		   ew.countEwDetectorsCovering / ew.getSavedEwAllowance in public/client/ew.js.
		   ⚠️ MIRROR SET - a change to any one of the four below is a change to its twin.

		   ⭐ WHY THE CLIENT MIRRORS THIS AT ALL, when every other EW sweep in this file is
		   server-only. The rule is "in range BOTH BEFORE AND AFTER movement", and the after-movement
		   position is a PLOTTED, UNCOMMITTED one - the server has not been told about it and by
		   definition cannot be, because the whole point of the allowance is that the player spends it
		   at the end of the Movement segment on the strength of where they ended up. So the client
		   needs the answer live, against positions only it knows. The server keeps the authoritative
		   copy for validation (Stage 10B) and for anything that has to reason about the allowance
		   after the fact.
		   ============================================================================================ */

		/* Every EW DETECTOR on the board that is currently doing its job, as flat tuples
		   (team, position, range). Collected ONCE and handed to the per-ship question below, so a
		   fleet-wide loop costs one sweep rather than one per ship.

		   ⚠️ THE SYSTEM SWEEP COMES FIRST AND EVERY OTHER QUESTION IS DEFERRED BEHIND IT, exactly as
		   TacGamedata::setEdfHexes() records: in all but a handful of games NOTHING is an EWDetector,
		   and `instanceof` is the cheapest question available - getHexPos(), isDestroyed() and
		   isReinforcement() are not, and none is worth asking of a ship that carries no detector. */
		public static function collectEwDetectors($gamedata, $turn = null){
			if ($turn === null) $turn = $gamedata->turn;

			$detectors = array();

			foreach ($gamedata->ships as $ship){
				$systems = array();
				foreach ($ship->systems as $system){
					if ($system instanceof EWDetector) $systems[] = $system;
				}
				if (empty($systems)) continue;

				$team = isset($ship->team) ? (int)$ship->team : null;
				if ($team === null) continue;

				/* ⭐ "DOCKED SHIPS WITH EW DETECTORS STILL CONTRIBUTE THEIR SAVED EW TO SHIPS WITHIN
				   20 HEXES" (user ruling 2026-09-12, WALKERS_OF_SIGMA_PLAN.md 3.14d). The shared
				   reader with setEdfHexes - destroyed, in hyperspace, never placed and not-arrived-
				   yet all still exclude a unit, and a STOWED one now detects from its CARRIER'S hex.
				   Sharing the reader is the point: the two sweeps had four identical exclusions
				   written out twice, and this rule had to reach both of them.
				   ⚠️ Range is still measured from the CARRIER, so a Waymarker that docks pulls its
				   20-hex bubble along with the Traveler - which is the rule, and also why the stowed
				   unit's own stale movement row must never be used. */
				$position = HangarOps::projectionOriginFor($ship, $gamedata);
				if (!$position) continue;                 //not on the board, or no position yet (lobby)

				foreach ($systems as $system){
					$range = (int)$system->getDetectorRange($turn);   //0 for destroyed or unpowered
					if ($range <= 0) continue;

					$detectors[] = array('team' => $team, 'pos' => $position, 'range' => $range);
				}
			}

			return $detectors;
		}

		/* The rules' degrading ladder, and the ONLY place it is written on the server.
		   "The first four EW Detectors allow the fleet to save 1 point of EW each. EW Detectors
		   number 5-8 allow the fleet to save 1/2 of a point each. All additional EW detectors allow
		   only 1/4 of a point each. Round down fractions of 1/4 and 1/2 and round up fractions of
		   3/4."

		   ⭐ COUNTED IN QUARTERS, AS INTEGERS, START TO FINISH. Every term of the ladder is a
		   multiple of 1/4, so the whole rule is exact in integer arithmetic - and integer arithmetic
		   is the only kind that can be PROMISED identical in PHP and JavaScript. A float version
		   would work today and drift the first time somebody added a third bracket.

		   And the rounding rule is one expression: down at 1/4 and 1/2, up at 3/4, is
		   floor(x + 1/4) - i.e. (quarters + 1) intdiv 4. Worked through:
		     1 -> 4q -> 1      4 -> 16q -> 4      5 -> 18q -> 4      8 -> 24q -> 6
		     9 -> 25q -> 6    10 -> 26q -> 6     11 -> 27q -> 7     12 -> 28q -> 7   */
		public static function savedEwAllowanceFromDetectors($count){
			$count = (int)$count;
			if ($count <= 0) return 0;

			$full     = min($count, 4);                    //detectors 1-4: a whole point each
			$halves   = min(max($count - 4, 0), 4);        //detectors 5-8: half a point each
			$quarters = max($count - 8, 0);                //detectors 9+ : a quarter each

			$totalQuarters = (4 * $full) + (2 * $halves) + $quarters;

			return (int)floor(($totalQuarters + 1) / 4);
		}

		/* How many of $detectors cover $position for $team. Split out from the ship question below
		   because the rule asks it TWICE of the same unit - once at Initial Orders and once at the
		   end of Movement - and the second position is not one a BaseShip can be asked for. */
		public static function countEwDetectorsCovering($detectors, $team, $position){
			if (empty($detectors) || $position === null) return 0;

			$team = (int)$team;
			$count = 0;

			foreach ($detectors as $detector){
				if ((int)$detector['team'] !== $team) continue;
				if (Mathlib::getDistanceHex($detector['pos'], $position) > $detector['range']) continue;
				$count++;
			}

			return $count;
		}


		/* =================== STAGE 10B - THE POOL, THE WINDOW AND THE WRITE ======================
		   Everything below is Stage 10B (WALKERS_OF_SIGMA_PLAN.md 3.8). The four functions above
		   answer "how many points do the DETECTORS offer this unit"; these answer "how many may it
		   actually save, when may it spend them, and how is that spend written down".
		   ========================================================================================= */

		/* THE UNSPENT POOL - the only pool a saved point may be drawn from (user ruling 2026-09-09):
		   "If a ship spends all their EW on non-DEW EW types, then they are unable to save a point of
		   EW ... DEW is the only pool of unspent EW that saved EW can be drawn from in
		   Pre-Firing/Firing."

		   ⚠️ TWO SOURCES, AND THE ORDER MATTERS. A committed DEW ROW is authoritative: it is what
		   getDEW() hands the defensive maths, and the client wrote it with getEWLeft(), which also
		   subtracts EW-boosted system boosts that nothing on the server re-derives. So when a row
		   exists it wins. There is no row during Initial Orders - convertUnusedToDEW writes it at
		   the moment of commit - hence the derived fallback, which is the same subtraction the
		   client's getEWLeft() performs.
		   ⚠️ The fallback is for READING only. submitLateEw() refuses to spend without a real row,
		   because a spend has to be DEBITED somewhere and there is nothing to debit. */
		public static function getUnspentEw($ship, $turn){
			foreach ($ship->EW as $entry){
				if ($entry->turn != $turn) continue;
				if ($entry->type !== "DEW") continue;
				return max(0, (int)$entry->amount);      //committed row - authoritative
			}

			/* Stage 12: a FLIGHT's unspent EW is its flight pool, never getScannerOutput() - which
			   returns 0 for a flight here and, if it ever stopped doing so, would be answering about
			   the mine-detection allowance instead. ⚠️ MIRROR PAIR with ew.getUnspentEwLive (JS). */
			if ($ship instanceof FighterFlight) return max(0, self::getFlightEwLeft($ship, $turn));

			$output = (int)self::getScannerOutput($ship, $turn);
			return max(0, $output - (int)$ship->getAllEWExceptDEW($turn));
		}

		/* Does this ship hold a committed DEW row for the turn? Distinguishes "no row yet" from
		   "a row that reads 0", which getDEW() cannot - and the two mean opposite things to
		   submitLateEw(). */
		public static function hasCommittedDewRow($ship, $turn){
			foreach ($ship->EW as $entry){
				if ($entry->turn == $turn && $entry->type === "DEW") return true;
			}
			return false;
		}

		/* THE LADDER AT THIS SHIP'S POSITION, before the pool clamp. Split out from
		   getSavedEwAllowance() below so the two questions stay separable: this one is what the
		   DETECTORS offer, that one is what the ship can actually take up.

		   ⚠️ THIS IS THE "BEFORE MOVEMENT" HALF when asked during Initial Orders, and the "after
		   movement" half when asked during Pre-Firing or Firing - the same function, because the
		   ship's position IS the answer and it has moved in between. That is the whole of
		   "if a vessel declares that it is saving an EW point but ends its movement step out of
		   range of the EW Detector, the point is lost": nothing declares, and the recomputation at
		   the spending phase is the loss. */
		public static function getDetectorAllowance($gamedata, $ship, $detectors = null, $turn = null){
			if ($turn === null) $turn = $gamedata->turn;
			if ($detectors === null) $detectors = self::collectEwDetectors($gamedata, $turn);
			if (empty($detectors)) return 0;

			/* ⭐ FLIGHTS ARE IN (user ruling 2026-09-10: "the effect applies to all Walker units").
			   No test for one is needed and none is wanted: the ladder is a question about POSITION, and
			   getSavedEwAllowance() then clamps it by what the unit actually has left to hold back. An
			   ordinary flight has no EW pool at all, so that clamp answers 0 for it without this
			   function ever having to know what a flight is - which is why the Mapmaker is the only
			   flight in the game this reaches. */
			if ($ship->isDestroyed()) return 0;
			if ($ship->isReinforcement()) return 0;
			if (!isset($ship->team)) return 0;

			$position = $ship->getHexPos();
			if (!$position) return 0;

			$count = self::countEwDetectorsCovering($detectors, (int)$ship->team, $position);

			return self::savedEwAllowanceFromDetectors($count);
		}

		/* HOW MANY EW POINTS THIS SHIP MAY ACTUALLY HOLD BACK - the ladder, clamped by the unspent
		   pool. This is the number the ship window shows and the number submitLateEw() budgets to.
		   ⚠️ MIRROR PAIR with ew.getSavedEwAllowance (JS). */
		public static function getSavedEwAllowance($gamedata, $ship, $detectors = null, $turn = null){
			if ($turn === null) $turn = $gamedata->turn;

			$allowance = self::getDetectorAllowance($gamedata, $ship, $detectors, $turn);
			if ($allowance <= 0) return 0;

			return min($allowance, self::getUnspentEw($ship, $turn));
		}

		/* WHEN A SAVED POINT MAY BE SPENT (user ruling 2026-09-09): "'End of movement' in FV is
		   essentially the start of Pre-Firing phase (if there is one) or start of Firing phase. If we
		   restrict the late EW allocation to these phases and don't worry too much about the Movement
		   phase for now that's fine."

		   5 is Pre-Firing and 3 is Firing (PhaseFactory). BOTH, sharing ONE budget - a fleet whose
		   Initial Orders skipped straight to Firing (nothing to activate) never sees phase 5 at all,
		   so gating on 5 alone would silently deny the allowance in exactly the games where a player
		   has fewest units left. ⚠️ MIRROR PAIR with ew.isLateEwPhase (JS). */
		public static function isLateEwPhase($phase){
			return ((int)$phase === 5 || (int)$phase === 3);
		}

		/* THE LATE WRITE. Called from PreFiringGamePhase::process and FireGamePhase::process with the
		   ships rebuilt from the player's POST.

		   ⚠️⚠️ ADDITIVE ONLY, AND THAT IS ENFORCED BY THE SHAPE OF THE DIFF, not by a check. Only
		   POSITIVE deltas against the stored rows are ever written, so a POST that has REMOVED or
		   REDUCED an Initial Orders allocation changes nothing at all - the stored row stands. The
		   one row this touches downwards is the ship's own DEW, which is not an allocation but the
		   unspent remainder the saved point is defined to come out of.

		   ⚠️ AN EXISTING ROW IS UPDATED, NEVER DUPLICATED. Adding a point to an OEW the ship already
		   holds on that target has to raise that row: getEWbyType() and getDEW() return the FIRST
		   matching row and would ignore a second, while getOEW() SUMS - so a duplicate row would be
		   counted by the shooting maths and not by the interface, or the reverse.

		   ⚠️ IDEMPOTENT BY CONSTRUCTION. A second submission in the same phase diffs the posted array
		   against rows that now already contain it, finds nothing, and writes nothing.

		   ⚠️ THE BUDGET IS RE-DERIVED SERVER-SIDE, from a fresh gamedata load - never from the POST.
		   A POST-side ship carries whatever movement the client sent, so its getHexPos() is
		   client-controlled, and its EW array is the thing being validated. */
		public static function submitLateEw($gameData, $dbManager, Array $ships){
			if (!self::isLateEwPhase($gameData->phase)) return;

			/* ⭐ $gameData IS ALREADY THE AUTHORITATIVE SERVER-SIDE LOAD, so this reloads nothing.
			   Manager::submitGamedata builds it with DBManager::getTacGamedata and hands the SAME
			   object to process(); only $ships is POST-side. Nothing earlier in either phase's
			   process() touches EW or a unit's position (a Fire-phase combat pivot changes facing,
			   not the hex), so it is current for both questions asked of it here - and the player
			   cannot have submitted this phase already, because Manager::submitGamedata throws on
			   hasAlreadySubmitted() before ever reaching us.
			   ⚠️ InitialOrdersGamePhase::process DOES re-load before EW::validateEW, and that is not
			   an inconsistency: it has written power and notes earlier in the same method and needs
			   to see them. We have written nothing.

			   The two guards below are the cheap exits for the 99% of games with no EW Detector in
			   them - one pass over the posted ships, then one over the loaded ones. */
			$turn = $gameData->turn;

			$anyOwn = false;
			foreach ($ships as $ship){
				if ($ship->userid == $gameData->forPlayer){ $anyOwn = true; break; }
			}
			if (!$anyOwn) return;

			$detectors = self::collectEwDetectors($gameData, $turn);
			if (empty($detectors)) return;

			foreach ($ships as $postShip){
				if ($postShip->userid != $gameData->forPlayer) continue;

				$serverShip = $gameData->getShipById($postShip->id);
				if (!$serverShip) continue;
				if ($serverShip->isDestroyed()) continue;

				/* No committed DEW row means there is nothing to debit the spend from - see
				   getUnspentEw(). A flight never gets one (convertUnusedToDEW returns early on a
				   flight), which is also the right answer: the rule is about ships. */
				if (!self::hasCommittedDewRow($serverShip, $turn)) continue;

				$budget = self::getSavedEwAllowance($gameData, $serverShip, $detectors, $turn);
				if ($budget <= 0) continue;

				$spent = 0;
				foreach (self::diffLateEw($serverShip, $postShip, $turn) as $delta){
					/* ALL OR NOTHING PER ENTRY, deliberately. A Disruption allocation is 3 points
					   (4 for ConstrainedEW) and means nothing at 1 or 2, so a budget that cannot
					   take the whole entry takes none of it rather than writing a fragment. */
					if ($delta['amount'] > ($budget - $spent)) continue;

					if ($delta['exists']){
						$dbManager->adjustEwAmount($gameData->id, $serverShip->id, $turn,
						                           $delta['type'], $delta['targetid'], $delta['amount']);
					}else{
						$dbManager->insertEwEntry($gameData->id, $serverShip->id, $turn,
						                          $delta['type'], $delta['amount'], $delta['targetid']);
					}

					$spent += $delta['amount'];
				}

				if ($spent > 0){
					$dbManager->adjustEwAmount($gameData->id, $serverShip->id, $turn, "DEW", -1, -$spent);
				}
			}
		}

		/* What the POST is asking to ADD, as (type, targetid, amount, exists) tuples in the posted
		   array's own order - which is the order the player created them in, and therefore the order
		   a clamped budget should honour.

		   DEW is skipped on both sides: it is the pool, not an allocation, and submitLateEw() debits
		   it itself rather than believing whatever the client sent. */
		public static function diffLateEw($serverShip, $postShip, $turn){
			$stored = array();
			foreach ($serverShip->EW as $entry){
				if ($entry->turn != $turn) continue;
				if ($entry->type === "DEW") continue;
				$key = $entry->type . '|' . (int)$entry->targetid;
				if (!isset($stored[$key])) $stored[$key] = (int)$entry->amount;   //FIRST row wins, as getEWbyType does
			}

			$deltas = array();
			$seen = array();
			foreach ($postShip->EW as $entry){
				if ($entry->turn != $turn) continue;
				if ($entry->type === "DEW") continue;
				if ($entry->type === "") continue;

				$key = $entry->type . '|' . (int)$entry->targetid;
				if (isset($seen[$key])) continue;    //a duplicated posted entry cannot buy a second delta
				$seen[$key] = true;

				$was = isset($stored[$key]) ? $stored[$key] : 0;
				$now = (int)$entry->amount;
				if ($now <= $was) continue;          //removed, reduced or unchanged - never written

				$deltas[] = array(
					'type'     => $entry->type,
					'targetid' => (int)$entry->targetid,
					'amount'   => $now - $was,
					'exists'   => isset($stored[$key]),
				);
			}

			return $deltas;
		}

	}
