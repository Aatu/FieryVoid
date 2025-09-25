<?php

class SimultaneousMovementRule implements JsonSerializable {

    private $categories = [];
    private $numberOfCategories = 0;
    
    //Initiative range being split into segments - anything below minimum will go to the first segment, above maximum to the last one
    private $iniRangeMin = -20; //-20 - covers Enormous units, as well as those with some Ini penalty, still within range (so Capitals - Ini 0 - will have lowered chance of going into first segment, unless they slow down)
    private $iniRangeMax = 220; //up to 220 - easily covers highest Ini in game (ultralight, 110) plus some bonuses
    public static $defaultNoOfCategories = 8; //8 categories means 30 points per category - which is equal to typical step of unit size difference
    

    function __construct($numberOfCategories) {
        $this->numberOfCategories = $numberOfCategories;
        $this->categories = $this->generateGategories($numberOfCategories);
    }

    public function getRuleName() {
        return 'initiativeCategories';
    }

    public function jsonSerialize(): mixed {
        return $this->numberOfCategories;
    }

    public function generateIniative($gameData) {

        foreach ($gameData->ships as $key=>$ship){

            $mod = 0;
            //Marcin Sawicki: cannot have criticals, but can have Ini modifiers all right!
            //if ($gameData->turn > 1) { //TODO: can't have criticals before turn 1
                $mod = $ship->getCommonIniModifiers( $gameData );
            //}

            $iniBonus =  $ship->getInitiativebonus($gameData);

            $iniative = Dice::d(100) + $iniBonus + $mod;

            //$newInitiative = 0;
            $newInitiative = $this->iniRangeMin;

            forEach ($this->categories as $category) {
               // if (($iniative+10) > $category) { //Ini+10 instead of just Ini - to move more units to brackets 5/6 and make Ini penalties for Cap ships more important
                if (($iniative) > $category) { //modified Ini ranges - retur to base Ini!
                    $newInitiative = $category;
                    break;
                } 
            }

            $ship->unmodifiedIniative = $iniative;
            $ship->iniative = $newInitiative;
        }
    }

    public function getNewActiveShip(TacGamedata $gameData, $lastShips = null) {

        $iniative = $this->categories[count($this->categories) - 1];
        $lastIndex = $this->getLastIniIndex($gameData, $lastShips);

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
                $ships = array_filter($gameData->ships, function($ship) use ($iniative, $category) {
                    return $ship->iniative == $category && !$ship->isDestroyed();
                });

                return $this->mapShipsToIds($ships);
            }
        }

        if ($lastIndex === null) {
            //throw new Exception("Unable to find ships for movement?");
            return []; //With delayed deployment it is now possible to find no ships, so return empty array and this will be handled in InitialOrderStrategy->advance()
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
    return count(array_filter($gameData->ships, function($ship) use ($iniative, $gameData) {
        return $ship->iniative == $iniative 
            && !$ship->isDestroyed() 
            && !$ship->isTerrain() 
            && $ship->getTurnDeployed($gameData) <= $gameData->turn;
    })) > 0;
}

    public function processMovement(TacGamedata $gameData, DBManager $dbManager, Array $ships) {
        $myActiveShips = $this->mapShipsToIds($gameData->getMyActiveShips());
        $allActiveShips = $this->mapShipsToIds($gameData->getActiveShips());
        $newActiveShipIds = $this->objectToArray(array_diff($allActiveShips, $myActiveShips));

        if (count($newActiveShipIds) === 0) {
            $newActiveShipIds = $this->getNewActiveShip($gameData, $gameData->getActiveShips());
        }

        if (count($newActiveShipIds) > 0){
            $gameData->setActiveship($newActiveShipIds);
            $dbManager->updateGamedata($gameData);
            $dbManager->setPlayersWaitingStatusInGame($gameData->id, true);
            $this->setActiveShipPlayersNotWaiting($gameData, $dbManager);
        }else{
            $movementPhase = new MovementGamePhase();
            $movementPhase->advance($gameData, $dbManager);
        }

        return true;
    }

    public function setActiveShipPlayersNotWaiting($gameData, $dbManager) {
        $activeUsers = [];
        foreach ( $gameData->getActiveShips() as $ship){
            if (!in_array($ship->userid, $activeUsers)) {
                $activeUsers[] = $ship->userid;
            }
        }

        foreach ( $activeUsers as $playerid){ 
            $dbManager->setPlayerWaitingStatus($playerid, $gameData->id, false);
        }
    }

    private function objectToArray($object) {
        $list = [];

        foreach($object as $entry) {
            array_push($list, $entry);
        }

        return $list;
    }


    private function mapShipsToIds($ships) {
        $list = [];

        foreach($ships as $ship) {
            array_push($list, $ship->id);
        }

        return $list;
    }

    private function generateGategories() {
        $categories = [];
        //changing range to that indicatd by class variables:
        //$step = (int)floor(200 / $this->numberOfCategories);
        $step = (int)floor( ($this->iniRangeMax - $this->iniRangeMin) / $this->numberOfCategories);
        
        $number = $this->numberOfCategories;

        while ($number--) {
            //array_push($categories, $step * $number);
            array_push($categories,  $this->iniRangeMin+($step * $number));
        }
        
        return $categories;
    }
}

?>
