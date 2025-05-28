<?php

class MovementGamePhase implements Phase
{
    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        //Have to load new gamedata, because the old object does not have moves for ships that were just submitted
        foreach ($dbManager->getTacGamedata($gameData->forPlayer, $gameData->id)->ships as $ship) {
            if ($ship->isDestroyed() || $ship->base || $ship->smallBase || $ship->isTerrain() || ($ship->getTurnDeployed($gameData) > $gameData->turn))  {
                continue;
            }

            $lastmove = $ship->getLastMovement();
            $newMove = new MovementOrder(null, 'end', $lastmove->position, 0, 0, $lastmove->speed, $lastmove->heading, $lastmove->facing, false, $gameData->turn, 0, 0);
            $dbManager->submitMovement($gameData->id, $ship->id, $gameData->turn, [$newMove]);
        }

        $gameData->setPhase(3);
        $gameData->setActiveship(-1);
        $dbManager->updateGamedata($gameData);
        $dbManager->setPlayersWaitingStatusInGame($gameData->id, false);        
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