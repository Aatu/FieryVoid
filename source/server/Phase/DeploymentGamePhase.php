<?php

class DeploymentGamePhase implements Phase
{
    public function advance(TacGamedata $gameData, DBManager $dbManager)
    {
        $gameData->setPhase(1);

        $dbManager->setPlayersWaitingStatusInGame($gameData->id, false);
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
        $dbManager->setPlayerWaitingStatus($gameData->forPlayer, $gameData->id, true);
    }

    private static function validateDeploymentArea($gameData, $ship, $move){

        $slot = $gameData->slots[$ship->slot];
        $hexpos = Mathlib::hexCoToPixel($move->position);

        $deppos = Mathlib::hexCoToPixel(new OffsetCoordinate($slot->depx, $slot->depy));

        $hexpos = [
            "x" => round($hexpos["x"]),
            "y" => round($hexpos["y"])
        ];

        $hexWidth = cos(30/180*pi()) * 2;
        $hexHeight = sin(30/180*pi()) + 1;

        if ($slot->deptype == "box"){
            $depw = $slot->depwidth*$hexWidth;
            $deph = $slot->depheight*$hexHeight;


            $leftBottom = [
                "x" => floor($deppos["x"]-($depw/2)),
                "y" => floor($deppos["y"]-($deph/2))
            ];

            $rightTop = [
                "x" => ceil($deppos["x"]+($depw/2)),
                "y" => ceil($deppos["y"]+($deph/2))
            ];

            if ($hexpos["x"] <= $rightTop["x"] && $hexpos["x"] >= $leftBottom["x"]){
                if ($hexpos["y"] <= $rightTop["y"] && $hexpos["y"] >= $leftBottom["y"]){
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

    private static function validateDeployment(TacGamedata $gameData, $ships)
    {
        $shipIdMoves = array();
        foreach ($ships as $ship)
        {
            if ($ship->userid !== $gameData->forPlayer)
                continue;

            $moves = array();
            $found = false;
            foreach ($ship->movement as $move)
            {
                if ($found)
                    throw new Exception("Deployment validation failed: Found more than one deployment entry for ship $ship->name.");

                if ($move->type == "deploy")
                {
                    
                    if ($move->turn != $gameData->turn) {
                        throw new Exception("Found deployment movement entry, but not for correct turn. Expected: " . $gameData->turn . " got: " . $move->turn);
                    };

                    $found = true;
                    $servership = $gameData->getShipById($ship->id);
                    if (self::validateDeploymentArea($gameData, $servership, $move))
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
