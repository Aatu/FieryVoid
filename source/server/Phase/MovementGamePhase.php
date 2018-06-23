<?php

class MovementGamePhase implements Phase
{
    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        //this is done below after last ship has moved
        //TODO: handle advance better: always when last order is submitted
    }

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
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
            
            //TODO: Validate movement
            $dbManager->submitMovement($gameData->id, $ship->id, $gameData->turn, $ship->movement);

        }

        if ($gameData->rules->hasRule("processMovement")) {
            return $gameData->rules->callRule("processMovement", [$gameData, $dbManager, $ships]);
        } else {
            return $this->setNextActiveShip($gameData, $gameData);
        }
    }

    private function setNextActiveShip(TacGamedata $gameData, DBManager $dbManager) {
        $next = false;
        $nextshipid = -1;
        $firstship = null;
        foreach ($gameData->ships as $ship){

            if ($firstship == null)
                $firstship = $ship;

            if ($next && !$ship->isDestroyed() && !$ship->unavailable){
                $nextshipid = $ship->id;
                break;
            }

            if ($ship->id == $gameData->activeship)
                $next = true;
        }

        if ($nextshipid > -1){
            $gameData->setActiveship($nextshipid);
            $dbManager->updateGamedata($gameData);
        }else{
            $gameData->setPhase(3);
            $gameData->setActiveship(-1);
            $dbManager->updateGamedata($gameData);
        }

        return true;
    }
}