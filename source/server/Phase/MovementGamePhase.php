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
            // Skip destroyed, terrain, or undeployed ships
            if (
                $ship->isDestroyed() ||
                $ship->base || $ship->smallBase ||
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

            // Perform post-move stealth check for ships with that ability (e.g. Torvalus)
            if ($ship->trueStealth) {
                $ship->checkStealth($gameData);
            }

            // Track slots that have pre-firing ships ready to shoot
            if ($ship->hasSpecialAbility("PreFiring") && $ship->hasPreFireWeaponsReady($gameData)) {
                $preFiringSlots[$ship->slot] = true; // use associative key to ensure uniqueness
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

	/*old version - before changes - in case of need of rollback*/
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

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {
        $activeShips = $gameData->getMyActiveShips();
		foreach ($ships as $ship) {
			foreach ($activeShips as $activeShip) { 
				if ($ship->id === $activeShip->id) {
					if(!$dbManager->isMovementAlreadySubmitted($gameData->id, $ship->id, $gameData->turn)){ //in case of wrong activeship indicated - do not re-send orders! (...but proceed with other actions)
						$dbManager->submitMovement($gameData->id, $ship->id, $gameData->turn, $ship->movement);
					}
				}
			}
		}
		//Added August 2024 for Mindriders.       
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