<?php

class DeploymentGamePhase implements Phase
{
    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        $gameData->setPhase(1);

        $dbManager->updateGamedata($gameData);
    }

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships)
    {
        $moves = $this->validateDeployment($gameData, $ships);
        foreach ($moves as $shipid=>$move)
        {
            $dbManager->insertMovement($gameData->id, $shipid, $move);
        }

        $dbManager->updatePlayerStatus($gameData->id, $gameData->forPlayer, $gameData->phase, $gameData->turn);
    }

    private static function validateDeploymentArea($gamedata, $ship, $move){

        $slot = $gamedata->slots[$ship->slot];
        $hexpos = Mathlib::hexCoToPixel($move->position);

        $deppos = Mathlib::hexCoToPixel(new OffsetCoordinate($slot->depx, $slot->depy));

        //TODO: Test this properly. I have no faith in it.
        if ($slot->deptype == "box"){
            $depw = $slot->depwidth;
            $deph = $slot->depheight;

            if ($hexpos["x"] < ($deppos["x"]+($depw/2)) && $hexpos["x"] > ($deppos["x"]-($depw/2))){
                if ($hexpos["y"] < ($deppos["y"]+($deph/2)) && $hexpos["y"] > ($deppos["y"]-($deph/2))){
                    return true;
                }
            }
        }else if ($slot->deptype=="distance"){
            if (Mathlib::getDistance($deppos, $hexpos) <= $slot->depheight){
                if (Mathlib::getDistance($deppos, $hexpos) > $slot->depwidth){
                    return true;
                }
            }
        }else{
            if (Mathlib::getDistance($deppos, $hexpos) <= $slot->depwidth){
                return true;
            }
        }


        return false;

    }

    private static function validateDeployment(TacGamedata $gamedata, $ships)
    {
        $shipIdMoves = array();
        foreach ($ships as $ship)
        {
            if ($ship->userid !== $gamedata->forPlayer)
                continue;

            $moves = array();
            $found = false;
            foreach ($ship->movement as $move)
            {
                if ($found)
                    throw new Exception("Deployment validation failed: Found more than one deployment entry for ship $ship->name.");

                if ($move->type == "deploy")
                {
                    $found = true;
                    $servership = $gamedata->getShipById($ship->id);
                    if (self::validateDeploymentArea($gamedata, $servership, $move))
                    {
                        $moves[] = $move;
                        $servership->movement[] = $move;
                    }else{
                        throw new Exception("Deployment validation failed: Illegal placement. Ship: " . $ship->name . "(".$move->position->q .",".$move->position->r.")");
                    }
                }
            }

            if (!$found)
                throw new Exception("Deployment validation failed: Entry not found for ship $ship->name.");

            $shipIdMoves[$ship->id] = $moves;
        }

        return $shipIdMoves;
    }
}