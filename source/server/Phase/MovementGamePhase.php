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
        $turn = $gameData->getActiveship()->getLastTurnMoved();
        if ($gameData->turn <= $turn)
            throw new Exception("The ship has already moved");

        //TODO: Validate movement
        $dbManager->submitMovement($gameData->id, $ships[$gameData->activeship]->id, $gameData->turn, $ships[$gameData->activeship]->movement);

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