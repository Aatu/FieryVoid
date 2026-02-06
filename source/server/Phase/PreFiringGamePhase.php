<?php

class PreFiringGamePhase implements Phase
{
    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        $dbManager->updateGamedata($gameData);

        $servergamedata = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);
        Firing::preparePreFiring($servergamedata); //Calculate base hit first / Ramming Attack systems checks for collisions and skindancing
        Firing::firePreFiringWeapons($servergamedata);


		foreach ($servergamedata->ships as $currShip){ //generate system-specific information if necessary
			$currShip->generateIndividualNotes($servergamedata, $dbManager);
		}		
		foreach ($servergamedata->ships as $currShip){ //save system-specific information if necessary (separate loop - generate for all, THEN save for all!
			$currShip->saveIndividualNotes($dbManager);
		}
		

        $dbManager->submitFireorders($servergamedata->id, $servergamedata->getNewFireOrders(), $servergamedata->turn, 5); //Picks up any new firing orders from beforePreFiringOrderResolution()
        $dbManager->updateFireOrders($servergamedata->getUpdatedFireOrders());

        $dbManager->submitDamages($servergamedata->id, $servergamedata->turn, $servergamedata->getNewDamages()); //Potentially damaging weapons could be added later.

        $gameData->setPhase(3);
        $gameData->setActiveship(-1);
        $dbManager->updateGamedata($gameData);
        $dbManager->setPlayersWaitingStatusInGame($gameData->id, false);

        foreach($gameData->slots as $slot){
            $minTurnDeploy = $gameData->getMinTurnDeployedSlot($slot->slot, $slot->depavailable);
            if($minTurnDeploy > $gameData->turn || ($slot->surrendered !== null && $slot->surrendered <= $gameData->turn)){ //Slot has no units deployed or has surrendered.
                $dbManager->updatePlayerSlotPhase($gameData->id, $slot->playerid, $slot->slot, 3, $gameData->turn);
            }     
        }        
         
    }

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {
        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer)
                continue;

            if ($ship->isDestroyed())
                continue;

            /* //We are not moving Combat Pivot to Pre-Firing yet.
            if (Movement::validateMovement($gameData, $ship)){
                if (count($ship->movement)>0)
                    $dbManager->submitMovement($gameData->id, $ship->id, $gameData->turn, $ship->movement);
            }
            */

            if (Firing::validateFireOrders($ship->getAllFireOrders(), $gameData)){
                $dbManager->submitFireorders($gameData->id, $ship->getAllFireOrders(), $gameData->turn, $gameData->phase);
            }
            //Not boosting in Pre-Firing...yet
            /*
            $powers = array();
            //Can now boost Fighter Systems, so look for this.
            if($ship instanceof FighterFlight){                
                foreach($ship->systems as $ftr) foreach($ftr->systems as $ftrsys){                   
                    if (is_array($ftrsys->boostOtherPhases) && in_array($gameData->phase, $ftrsys->boostOtherPhases)) {  //Prevent duplication                   
                        if (!empty($ftrsys->power)) {                            
                            // Peel off the last entry so we can save it later
                            $lastPower = array_pop($ftrsys->power);

                            // Remove any power entries saved during Initial Orders
                            $ftrsys->removePowerEntriesForTurn($gameData);

                            // Put the last entry back if you still want it in $system->power
                            $ftrsys->power[] = $lastPower;
                        }
                        $powers = array_merge($powers, $ftrsys->power);
                        $ftrsys->doIndividualNotesTransferGD($gameData);                           
                    }                 
                }
            }else{
                foreach ($ship->systems as $system){
                    if (is_array($system->boostOtherPhases) && in_array($gameData->phase, $system->boostOtherPhases)) {  //Prevent duplication
                        if (!empty($system->power)) {
                            // Peel off the last entry so we can save it later
                            $lastPower = array_pop($system->power);

                            // Remove any power entries saved during Initial Orders
                            $system->removePowerEntriesForTurn($gameData);

                            // Put the last entry back if you still want it in $system->power
                            $system->power[] = $lastPower;
                        }
                        $powers = array_merge($powers, $system->power);
                        $system->doIndividualNotesTransferGD($gameData);                      
                    }                   
                }
            }
                                    
            $dbManager->submitPower($gameData->id, $gameData->turn, $powers);
            */
        }		

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn);
        $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);
        
        return true;
    }
}
