<?php

class MovementGamePhase implements Phase
{

    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        // Store slots that have at least one ship ready to prefire
        $preFiringSlots = [];

        // Load fresh gamedata (to include newly submitted moves)
        $latestgameData = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);

        foreach ($latestgameData->ships as $ship) {
            // Track slots that have pre-firing ships ready to shoot
            // Checked BEFORE skipping bases/mines to ensure mines with command control get a Pre-Firing Phase
            if (
                !$ship->isDestroyed() &&
                $ship->getTurnDeployed($gameData) <= $gameData->turn &&
                !$ship->isTerrain() &&
                $ship->hasSpecialAbility("PreFiring") && 
                $ship->hasPreFireWeaponsReady($latestgameData)
            ) {
                $preFiringSlots[$ship->slot] = true; // use associative key to ensure uniqueness
            }

            // Skip destroyed, terrain, or undeployed ships
            if (
                $ship->isDestroyed() ||
                $ship->base || $ship->smallBase || $ship->mine ||
                $ship->isTerrain() ||
                ($ship->getTurnDeployed($gameData) > $gameData->turn)
            ) {
                continue;
            }

            // Submit a dummy "end" move so the ship has a completed movement order for this turn
            $lastMove = $ship->getLastMovement();
            $newMove = new MovementOrder(
                null,
                'end',
                $lastMove->position,
                0,
                0,
                $lastMove->speed,
                $lastMove->heading,
                $lastMove->facing,
                false,
                $gameData->turn,
                0,
                0
            );
            $dbManager->submitMovement($gameData->id, $ship->id, $gameData->turn, [$newMove]);

            // Perform post-move stealth check for ships with that ability (e.g. Hyach, Torvalus)
            if ($ship->trueStealth) {
                $ship->checkStealth($latestgameData);
            }

        }

        // Update game phase to Pre-Firing
        $gameData->setPhase(5);
        $gameData->setActiveship(-1);
        $dbManager->updateGamedata($gameData);
        $dbManager->setPlayersWaitingStatusInGame($gameData->id, false);

        // Identify slots that have no pre-firing ships
        $preFiringSlotIds = array_keys($preFiringSlots);
        $allSlotIds = array_map(fn($s) => $s->slot, $gameData->slots);
        $slotsToSkip = array_diff($allSlotIds, $preFiringSlotIds);

        // Mark slots as completed for phase 5 if:
        // - They have no deployed units
        // - They have surrendered
        // - They have no pre-firing ships
        foreach ($gameData->slots as $slot) {
            $minTurnDeploy = $gameData->getMinTurnDeployedSlot($slot->slot, $slot->depavailable);

            // Skip slot if undeployed or surrendered
            if (
                $minTurnDeploy > $gameData->turn ||
                ($slot->surrendered !== null && $slot->surrendered <= $gameData->turn)
            ) {
                $dbManager->updatePlayerSlotPhase($gameData->id, $slot->playerid, $slot->slot, 5, $gameData->turn);
                continue;
            }

            // Skip slot if it has no pre-firing ships
            if (in_array($slot->slot, $slotsToSkip, true)) {
                $dbManager->updatePlayerSlotPhase($gameData->id, $slot->playerid, $slot->slot, 5, $gameData->turn);
                $dbManager->setPlayerWaitingStatus($slot->playerid, $gameData->id, true); //Set waiting to prevent game highlighting in lobby               
            }
        }
    }

	/*//old version - before changes - in case of need of rollback
    public function process_bak(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {
        foreach ($gameData->getMyActiveShips() as $ship) {
            $turn = $ship->getLastTurnMoved();
            if ($turn >= $gameData->turn) {
                throw new Exception("The ship has already moved");
            }
        }

        $activeShips = $gameData->getMyActiveShips();
        foreach ($ships as $ship) {
            $found = false;
            foreach ($activeShips as $activeShip) {
                if ($ship->id === $activeShip->id) {
                    $found = true;
                }
            }

            if (!$found) {
                continue;
            }
            
            //TODO: Validate movement: Make sure that all ships of current player have moved and the moves are legal
			$dbManager->submitMovement($gameData->id, $ship->id, $gameData->turn, $ship->movement);			
		}		

        if ($gameData->rules->hasRule("processMovement")) {
            return $gameData->rules->callRule("processMovement", [$gameData, $dbManager, $ships]);
        } else {
            return $this->setNextActiveShip($gameData, $dbManager);
        }
    }
    */

	public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
	{
		$submittedShipIds = array();
		foreach ($ships as $s) $submittedShipIds[$s->id] = true;

		$activeShips = $gameData->getMyActiveShips();
		foreach ($ships as $ship) {
			foreach ($activeShips as $activeShip) { 
				if ($activeShip->id === $ship->id) {
					// Check for detachment move in the submitted orders
					$detachedMove = false;
					foreach ($ship->movement as $move) {
						if ($move->type === 'detach') {
							$detachedMove = true;
							break;
						}
					}

					// If we are detaching, ALWAYS allow submission (overwriting any mirrored parent movement)
					$isSubmitted = $dbManager->isMovementAlreadySubmitted($gameData->id, $ship->id, $gameData->turn);
					if ($detachedMove || !$isSubmitted) {
						if ($detachedMove && $isSubmitted) {
							// Clear existing mirrored movement to prevent duplicates/conflicts
							$dbManager->deleteMovement($gameData->id, $ship->id, $gameData->turn);
						}

						$dbManager->submitMovement($gameData->id, $ship->id, $gameData->turn, $ship->movement);
					
						// Create detached note if the ship submitted a 'detach' move
						$isAttached = !empty($ship->attached) || !empty($activeShip->attached);
						if($isAttached && $detachedMove) {                        
							$hostId = key($ship->attached) ?: key($activeShip->attached);
							$targetShip = $gameData->getShipById($hostId);
							if ($targetShip) {
								$cnc = $targetShip->getSystemByName("CnC");
								if ($cnc) {
									$cnc->addIndividualNote(new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$targetShip->id,$cnc->id,"Detached","Detached",$ship->id . "=>Detach"));
									$cnc->saveIndividualNotes($dbManager);

                                    if(!$ship instanceof FighterFlight){ //Grapple ships, need to unset their $hostShipId
                                        foreach($ship->systems as $claw){
                                            if($claw->name == "GrapplingClaw"){
										        $claw->hostShipId = -1;                                                
                                                $clawNote = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$claw->id,"ClawDetached","ClawDetached",-2);											
										        Manager::insertIndividualNote($clawNote);	                                            
                                            }
                                        }
                                    }		

									// Clear attachment in memory to prevent mirroring during this process call
									// Clear on both the submitted ship and the gameData representation to ensure JSON response is correct
									unset($ship->attached[$hostId]);
									unset($activeShip->attached[$hostId]);
									unset($targetShip->hasAttached[$ship->id]);
								}
							}
						}
					}
					
					// Update in-memory movement data so that subsequent checks (like mine detection) use the actual new position	
					$activeShip->movement = $ship->movement;
				}
			}
		}

		// Duplicating parent ship movements for attached ships
		foreach($ships as $ship) {
			$activeShip = $gameData->getShipById($ship->id);
			if ($activeShip && !empty($activeShip->hasAttached)) {
				foreach ($activeShip->hasAttached as $attachedShooterId => $location) {
					$attachedShip = $gameData->getShipById($attachedShooterId);
					if ($attachedShip && !$attachedShip->isDestroyed() && isset($attachedShip->attached[$activeShip->id])) {
						// Skip if the attached ship is also submitting its own movement (e.g. detaching)
						if (!isset($submittedShipIds[$attachedShip->id]) && !$dbManager->isMovementAlreadySubmitted($gameData->id, $attachedShip->id, $gameData->turn)) {
							$attachedMoves = array();
							$locOffset = Movement::getAttachedFacingOffset($location);

							// When the parent ship is rolled, breaching pods as FighterFlight 
							// units cannot roll themselves, so we adjust their absolute 
							// facing by 180 degrees (+3) instead.
							$facingOffset = $locOffset;
							if ($attachedShip instanceof FighterFlight && Movement::isRolled($activeShip, $gameData->turn)) {
								$facingOffset = ($facingOffset + 3) % 6;
							}

							foreach ($ship->movement as $move) {
								$moveObj = new MovementOrder(
									-1, // New entry
									'attached',
									new OffsetCoordinate($move->position),
									$move->xOffset,
									$move->yOffset,
									$move->speed,
									$move->heading,
									($move->facing + $facingOffset) % 6,
									$move->preturn,
									$move->turn,
									$activeShip->id,
									$move->at_initiative
								);
								$moveObj->requiredThrust = $move->requiredThrust;
								$moveObj->assignedThrust = $move->assignedThrust;
								$attachedMoves[] = $moveObj;
							}
							$dbManager->submitMovement($gameData->id, $attachedShip->id, $gameData->turn, $attachedMoves);
							$attachedShip->movement = $attachedMoves;
						}
					}
				}
			}
		}

        if($gameData->areMinesPresent){ //There are mines in the game, check if any have been detected.        
            // --- HYDRATE MOVEMENT RECORDS ---
            // During the interval between phase changes, empty movement instructions or single
            // newly-created instructions might be stored as `stdClass` objects or associative arrays.
            // We MUST hydrate them into full MovementOrder and OffsetCoordinate objects before
            // Mine detection or other backend systems loop through getHexPos().
            $fullGamedata = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);
            //$gameData->ships = $fullGamedata->ships;

            foreach ($fullGamedata->ships as $gdShip) {                 
                if($gdShip instanceof Terrain) continue;
                if($gdShip instanceof Mine) continue;
                if($gdShip->isDestroyed()) continue;

                $hydratedMovements = [];
                if (is_array($gdShip->movement) && !empty($gdShip->movement)) {
                    foreach ($gdShip->movement as $move) {
                        if($move->turn == $gameData->turn){ //Only hydrate this turn.
                        
                            if (is_object($move) && !($move instanceof MovementOrder)) {
                                // Decoded as stdClass
                                $posX = isset($move->position->x) ? clone $move->position->x : (isset($move->position['x']) ? $move->position['x'] : 0);
                                $posY = isset($move->position->y) ? clone $move->position->y : (isset($move->position['y']) ? $move->position['y'] : 0);
                                
                                $hydratedMovements[] = new MovementOrder(
                                    $move->id ?? -1,
                                    $move->type ?? '',
                                    new OffsetCoordinate($posX, $posY),
                                    $move->xOffset ?? 0,
                                    $move->yOffset ?? 0,
                                    $move->speed ?? 0,
                                    $move->heading ?? 0,
                                    $move->facing ?? 0,
                                    $move->preturn ?? false,
                                    $move->turn ?? $gameData->turn,
                                    $move->value ?? 0,
                                    $move->at_initiative ?? 0
                                );
                            } elseif (is_array($move)) {
                                // Decoded as associative array
                                $posX = isset($move['position']['x']) ? clone $move['position']['x'] : (isset($move['position']->x) ? $move['position']->x : 0);
                                $posY = isset($move['position']['y']) ? clone $move['position']['y'] : (isset($move['position']->y) ? $move['position']->y : 0);

                                $hydratedMovements[] = new MovementOrder(
                                    $move['id'] ?? -1,
                                    $move['type'] ?? '',
                                    new OffsetCoordinate($posX, $posY),
                                    $move['xOffset'] ?? 0,
                                    $move['yOffset'] ?? 0,
                                    $move['speed'] ?? 0,
                                    $move['heading'] ?? 0,
                                    $move['facing'] ?? 0,
                                    $move['preturn'] ?? false,
                                    $move['turn'] ?? $gameData->turn,
                                    $move['value'] ?? 0,
                                    $move['at_initiative'] ?? 0
                                );
                            } else {
                                // Already a MovementOrder object
                                $hydratedMovements[] = $move;
                            }
                        }
                    }    
                } else {
                    $lastMove = $gdShip->getLastMovement();
                    if ($lastMove) {                       
                        $hydratedMovements[] = new MovementOrder(-1, 'start', $lastMove->position, 0, 0, $lastMove->speed, $lastMove->heading, $lastMove->facing, false, $gameData->turn, 0, 0);
                    }
                }
                
                if(!empty($hydratedMovements)){
                    $gdShip->movement = $hydratedMovements;
                }else{ //No moves, hydrate with last known movement from previous turn
                    $lastMove = $gdShip->getLastMovement();
                    if ($lastMove) {                       
                        $hydratedMovements[] = new MovementOrder(-1, 'start', $lastMove->position, 0, 0, $lastMove->speed, $lastMove->heading, $lastMove->facing, false, $gameData->turn, 0, 0);
                    }
                    $gdShip->movement = $hydratedMovements; // Fix: Actually apply the generated dummy array back to the ship!  DK 26.3.26
                }    
                
                // Re-sync the hydrated array back to the matching $activeShips proxy so
                // memory references stay aligned for the remainder of the phase.
                foreach ($activeShips as $activeShip) {
                    if ($gdShip->id === $activeShip->id) {
                        $activeShip->movement = $hydratedMovements;
                    }
                }
            }

            foreach ($gameData->ships as $ship) {
                if ($ship->mine) {
                    $ship->generateIndividualNotes($gameData, $dbManager);
                    $ship->saveIndividualNotes($dbManager);
                }
            }
        }    

		//Added August 2024 for Mindrider Contraction.       
		foreach ($ships as $ship){ //generate system-specific information if necessary    
			$ship->generateIndividualNotes($gameData, $dbManager);
		}		
		foreach ($ships as $ship){ //save system-specific information if necessary (separate loop - generate for all, THEN save for all!         
			$ship->saveIndividualNotes($dbManager);
		} 
		
        if ($gameData->rules->hasRule("processMovement")) {
            return $gameData->rules->callRule("processMovement", [$gameData, $dbManager, $ships]);
        } else {
            return $this->setNextActiveShip($gameData, $dbManager);
        }
    }

    private function setNextActiveShip(TacGamedata $gameData, DBManager $dbManager) {
        $next = false;
        $nextship = null;
        $firstship = null;
        foreach ($gameData->ships as $ship){
            if($ship->isTerrain()) continue; //Ignore terrain like asteroids.
            if($ship->mine) continue; //Ignore mines
            if($ship->getTurnDeployed($gameData) > $gameData->turn) continue;
            if ($firstship == null)
                $firstship = $ship;

            if ($next && !$ship->isDestroyed() && !$ship->unavailable){
                $nextship = $ship;
                break;
            }

            if ($ship->id == $gameData->activeship)
                $next = true;
        }

        if ($nextship){
            $gameData->setActiveship($nextship->id);
            $dbManager->updateGamedata($gameData);
            $dbManager->setPlayersWaitingStatusInGame($gameData->id, true);
            $dbManager->setPlayerWaitingStatus($nextship->userid, $gameData->id, false);
        }else{
          $this->advance($gameData, $dbManager);
        }

        return true;
    }
}