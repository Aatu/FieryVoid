<?php

class SimultaneousMovementRule {

    private $categories = [];
    private $numberOfCategories = 0;

    function __construct($numberOfCategories) {
        $this->numberOfCategories = $numberOfCategories;
        $this->categories = $this->generateGategories($numberOfCategories);
    }

    public function toJSON() {
        return '"initiativeCategories": ' . $this->numberOfCategories;
    }

    public function generateIniative($gameData) {

        foreach ($gameData->ships as $key=>$ship){

            $mod = 0;
            if ($gameData->turn > 1) { //TODO: can't have criticals before turn 1
                $mod = $ship->getCommonIniModifiers( $gameData );
            }

            $iniBonus =  $ship->getInitiativebonus($gameData);

            $iniative = Dice::d(100) + $iniBonus + $mod;

            $newInitiative = 0;

            forEach ($this->categories as $category) {
                if ($iniative > $category) {
                    $newInitiative = $category;
                    break;
                } 
            }

            debug::log("changing inative from $iniative to $newInitiative as per category");

            $ship->iniative = $newInitiative;
        }
    }

    public function getNewActiveShip(TacGamedata $gameData, $lastShips = null) {

        debug::log("get new active ship, categories: " . json_encode($this->categories));
        $iniative = $this->categories[count($this->categories) - 1];
        $lastIndex = $this->getLastIniIndex($gameData, $lastShips);

        if ($lastIndex === null) {
            debug::log("This is first batch of ships for movement this turn");
        } else {
            debug::log("Last index of iniative was '$lastIndex' at iniative '".$this->categories[$lastIndex]."'");
        }

        $found = $lastIndex === null ? true : false;

        for ($index = count($this->categories) - 1; $index >= 0; $index--) {
            $category = $this->categories[$index]; 

            if ($index === $lastIndex) {
                $found = true;
                continue;
            }

            if (!$found) {
                continue;
            }

            if ($this->hasShipsAtIniative($gameData, $category)) {
                debug::log("Hey there are ships at iniative '$category'");
                $ships = array_filter($gameData->ships, function($ship) use ($iniative, $category) {
                    return $ship->iniative === $category;
                });

                $list = [];

                foreach($ships as $ship) {
                    array_push($list, $ship->id);
                }

                return $list;
            }
        }

        return [];
    }

    private function getLastIniIndex(TacGamedata $gameData, $lastShips = null) {
        if ($lastShips !== null) {
            $lastIni = $lastShips[0]->iniative;

            foreach ($this->categories as $key => $category) {
                if ($lastIni === $category) {
                    return $key;
                } 
            }

            throw new Exception("This shoud never get this far!");
        } else {
            return null;
        }
    }

    private function hasShipsAtIniative(TacGamedata $gameData, $iniative) {
        return count(array_filter($gameData->ships, function($ship) use ($iniative) {
            return $ship->iniative === $iniative;
        })) > 0;
    }

    public function processMovement(TacGamedata $gameData, DBManager $dbManager, Array $ships) {

        $updatedGameData = $dbManager->getTacGamedata($gameData->forPlayer, $gameData->id);

        foreach ($updatedGameData->getActiveShips() as $ship) {
            $turn = $ship->getLastTurnMoved();
            if ($turn < $gameData->turn) {
                return true; //Not all active ships have moved yet, do nothing.
            }
        }

        $newActiveShipIds = $this->getNewActiveShip($gameData, $gameData->getActiveShips());

        debug::log("new active ships for movement " . json_encode($newActiveShipIds));
        if (count($newActiveShipIds) > 0){
            $gameData->setActiveship($newActiveShipIds);
            $dbManager->updateGamedata($gameData);
        }else{
            $gameData->setPhase(3);
            $gameData->setActiveship(-1);
            $dbManager->updateGamedata($gameData);
        }

        return true;
    }

    private function generateGategories() {
        $categories = [];
        $step = 200 / $this->numberOfCategories;
        $number = $this->numberOfCategories;

        while ($number--) {
            array_push($categories, $step * $number);
        }
        
        return $categories;
    }
}

