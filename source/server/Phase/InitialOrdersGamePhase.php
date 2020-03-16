<?php

class InitialOrdersGamePhase implements Phase
{

    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        
        $dbManager->setPlayersWaitingStatusInGame($gameData->id, true);
        $gameData->setPhase(2);
        if ($gameData->rules->hasRule("getNewActiveShip")) {
            $activeShipIds = $gameData->rules->callRule("getNewActiveShip", [$gameData, null]);
            $gameData->setActiveship($activeShipIds);
            $gameData->rules->callRule("setActiveShipPlayersNotWaiting", [$gameData, $dbManager]);
        } else {
            $ship = $gameData->getFirstShip();
            $dbManager->setPlayerWaitingStatus($ship->userid, $gameData->id, false);
            $gameData->setActiveship($ship->id);
        }
        $dbManager->updateGamedata($gameData);

    }

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {

        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer)
                continue;

            $powers = array();

            foreach ($ship->systems as $system){
                $powers = array_merge($powers, $system->power);
            }
		
            $dbManager->submitPower($gameData->id, $gameData->turn, $powers);
        }
		


		foreach ($ships as $currShip){ //generate system-specific information if necessary
			$currShip->generateIndividualNotes($gameData, $dbManager);
		}		
		foreach ($ships as $currShip){ //save system-specific information if necessary (separate loop - generate for all, THEN save for all!
			$currShip->saveIndividualNotes($dbManager);
		}


        $gd = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);


        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer)
                continue;

            if (EW::validateEW($ship, $gd)){
                $dbManager->submitEW($gameData->id, $ship->id, $ship->EW, $gameData->turn);
            }else{
                throw new Exception("Failed to validate EW");
            }	
        }


        $gd = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id); 
		

        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer) continue;

            if (Firing::validateFireOrders($ship->getAllFireOrders(), $gd)){
                $dbManager->submitFireorders($gameData->id, $ship->getAllFireOrders(), $gameData->turn, $gameData->phase);
            }else{
                throw new Exception("Failed to validate Ballistic firing orders");
            }
        }
		

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn);
        $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);
    }
}

?>