<?php

class InitialOrdersGamePhase implements Phase
{

    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        $gameData->setPhase(2);
        $gameData->setActiveship($gameData->getFirstShip()->id);
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

        $gd = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);

        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer)
                continue;

            if (EW::validateEW($ship, $gd)){
                $dbManager->submitEW($gameData->id, $ship->id, $ship->EW, $gameData->turn);
            }
        }


        foreach ($ships as $ship){
            if ($ship instanceof WhiteStar){
                $dbManager->updateAdaptiveArmour($gameData->id, $ship->id, $ship->armourSettings);
            }
        }

        $gd = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);

        foreach ($ships as $ship){
            if ($ship->userid != $gameData->forPlayer)
                continue;

            if (Firing::validateFireOrders($ship->getAllFireOrders(), $gd)){
                $dbManager->submitFireorders($gameData->id, $ship->getAllFireOrders(), $gameData->turn, $gameData->phase);
            }else{
                throw new Exception("Failed to validate Ballistic firing orders");
            }
        }

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn);

        return true;
    }
}