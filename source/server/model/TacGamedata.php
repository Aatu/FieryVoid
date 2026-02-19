<?php

class TacGamedata {

    public static $currentTurn;
    public static $currentPhase;
    public static $currentGameID;
    public static $safeGameID = 3730; //gameID that is safe for adding new features
    public static $lastFiringResolutionNo = 0; //firing resolution to be used

    public $id, $turn, $phase, $activeship, $name, $status, $points, $background, $creator, $gamespace, $description;
    public $ships = array();
    public $slots = array();
    public $waiting = false;
    public $changed = false;
    public $getDistanceHex = false;
    public $forPlayer;
    public $ballistics = array();
    public $waitingForThisPlayer = false;
    public $rules;
    public $blockedHexes;
    
    
    function __construct($id, $turn, $phase, $activeship, $forPlayer, $name, $status, $points, $background, $creator, $description='', $gamespace = null, $rules = null){
        $this->setId($id);
        $this->setTurn($turn);
        $this->setPhase($phase);
        $this->setActiveship($activeship);
        $this->setForPlayer($forPlayer);
        $this->setForPlayer($forPlayer);
        //$this->setBlockedHexes();
        $this->name = $name;
        $this->status = $status;
        $this->points = (int)$points;
        $this->background = $background;
		//description: replace \n with <br> to correctly display multiline!
        $description = preg_replace("/\r\n\r\n|\r\r|\n\n|\n/", "<br>", $description);
        $this->description = $description;
        $this->creator = $creator;
        $this->gamespace = $gamespace;
        $this->rules = new GameRules($rules);
    }
   
    public function setPhase($phase)
    {
        self::$currentPhase = (int) $phase;
        $this->phase = (int) $phase;
    }

    public function getPhase(): Phase {
        return PhaseFactory::get($this->phase);
    }
    
    public function setTurn($turn)
    {
        self::$currentTurn = $turn;
        $this->turn = $turn;
    }
    
    public function setActiveship($activeship)
    {
        $this->activeship = $activeship;
    }
    
    public function setId($id)
    {
        self::$currentGameID = $id;
        $this->id = $id;
    }
   
    public function doSortShips()
    {
        usort($this->ships, function ($a, $b) {
            return $this->sortShips($a, $b); // Call the instance method within the closure
        });
    }
   
    public function stripForJson() {        
        $strippedGamedata = new stdClass();
        $strippedGamedata->ships = array_map( function($ship) {return $ship->stripForJson();}, $this->ships);
        $strippedGamedata->id = $this->id;
        $strippedGamedata->turn = $this->turn;
        $strippedGamedata->phase = $this->phase;
        $strippedGamedata->activeship = $this->activeship;
        $strippedGamedata->name = $this->name;
        $strippedGamedata->status = $this->status;
        $strippedGamedata->points = $this->points;
        $strippedGamedata->background = $this->background;
		$strippedGamedata->description = $this->description;
        $strippedGamedata->creator = $this->creator;
        $strippedGamedata->gamespace = $this->gamespace;
        $strippedGamedata->slots = $this->slots;
        $strippedGamedata->waiting = $this->waiting;
        $strippedGamedata->changed = $this->changed;
        $strippedGamedata->rules = $this->rules;
        $strippedGamedata->forPlayer = $this->forPlayer;
        $strippedGamedata->blockedHexes = $this->blockedHexes;

        return $strippedGamedata;
    }

    public function onConstructed(){
        $this->setBlockedHexes();
        $this->waitingForThisPlayer = $this->getIsWaitingForThisPlayer();
        $this->doSortShips();

        $i = 0;
        foreach ($this->ships as $ship){
            $fireOrders = $ship->getAllFireOrders();
            foreach($fireOrders as $fire){
                $weapon = $ship->getSystemById($fire->weaponid);
                if (($this->phase >= 2) && $weapon->ballistic && $fire->turn == $this->turn){
                    $movement = $ship->getLastTurnMovement($fire->turn);
                    $target = $fire->targetid;
                    if ($fire->x != "null" && $fire->y != "null")
                        $targetpos = array("x"=>$fire->x, "y"=>$fire->y);
                    else
                        $targetpos = null;


                    $this->ballistics[$i] = new Ballistic(
                        $i,
                        $fire->id,
                        array("x"=>$movement->position->q, "y"=>$movement->position->r),
                        $movement->facing,
                        $targetpos,
                        $target,
                        $fire->shooterid,
                        $fire->weaponid,
                        $fire->shots
                        );

                        //$targetpos, $targetid, $shooterid, $weaponid
                    $i++;
                    //print(sizeof($this->ballistics));
                }

            }
            $this->markUnavailableShips();
            $ship->onConstructed($this->turn, $this->phase, $this);
        }
    }

    public function markUnavailableShips()
    {
        if ($this->phase < 0)
            return;
        
        foreach ($this->ships as $ship)
        {
            $turnDeploys = $ship->getTurnDeployed($this);
            
            if($turnDeploys > $this->turn){
                $ship->unavailable = true;
            } 
        }
    }
    
    public function isFinished(){
        foreach ($this->slots as $slot)
        {
            //still ships coming in
            if ($slot->depavailable > $this->turn)
                return false;
        }
        
        foreach ($this->ships as $ship){
            if ($ship->isDestroyed()){
                //print($ship->name . " is destroyed");
                continue;
            }
            
            if ($ship->isPowerless()){
                //print($ship->name . " is powerless");
                continue;
            }
            

            foreach ($this->ships as $ship2){
                if ($ship->team == $ship2->team)
                    continue;
                    
                if ($ship2->isDestroyed()){
                    continue;
                }
                
                if ($ship2->isPowerless()){
                    continue;
                }

                $dis = mathlib::getDistanceHex($ship, $ship2);
                
                if ($dis<100 || $this->turn < 5){
                    //print($ship->name . " is on distance $dis from " . $ship2->name);
                    return false;
                }
                    
            }
            
            
        }
        
        
        return true;
   
    }   
    
    public function setShips($ships){
    
        $this->ships = $ships;
    }
    
    public static function sortShips($a, $b){	    
        if ($a->iniative > $b->iniative) return 1;

        if ($a->iniative < $b->iniative) return -1;

            if ($a->iniativebonus > $b->iniativebonus) return 1;

            if ($b->iniativebonus > $a->iniativebonus) return -1;

	    if ($a->id > $b->id) {
			return 1;
	    } else{
			return -1;    
	    }
	    
	    
	    /* remade to make sure order is the same as in front end
        if ($a->iniative == $b->iniative){
            if ($a->iniativebonus == $b->iniativebonus){
                if ($a->id > $b->id)
                    return 1;
                else
                    return -1;
            }else if ($a->iniativebonus > $b->iniativebonus){
                return 1;
            }else{
                return -1;
            }
        }else if ($a->iniative < $b->iniative){
            return -1;
        }
	*/
        
        return 1; //should never reach here
    }
    
    
    public function getNewFireOrders(){
        $list = array();
        
        foreach ($this->ships as $ship){
            $fireOrders = $ship->getAllFireOrders();
            foreach($fireOrders as $fire){
                if ($fire->addToDB == true)
                    $list[] = $fire;
            }
        }
        
        return $list;
    
    }
    
    public function getUpdatedFireOrders(){
        $list = array();
        
        foreach ($this->ships as $ship){
            $fireOrders = $ship->getAllFireOrders();
            foreach($fireOrders as $fire){
                if ($fire->updated == true)
                    $list[] = $fire;
            }
        }
        
        return $list;
    
    }
    
    public function getUpdatedCriticals(){
        $list = array();
        
        foreach ($this->ships as $ship){
            foreach($ship->systems as $system){
				foreach($system->criticals as $crit){
					if ($crit->updated == true)
						$list[] = $crit;
				}
                
            }
        }
        
        return $list;
    
    }
    
    public function getNewDamages(){
        $list = array();
        
        foreach ($this->ships as $ship){
            foreach($ship->systems as $system){
                foreach($system->damage as $damage){
                    if ($damage->updated == true)
                        $list[] = $damage;
                }
                
            }
        }
        
        return $list;
    
    }

	


    public function addDamageEntry($damage){    
        $ship = $this->getShipById($damage->shipid);
        $ship->addDamageEntry($damage);    
    }
    
    public function getFirstShip(){    
        foreach ($this->ships as $ship){
            if ($ship->isDestroyed()) continue;
            if($ship->isTerrain()) continue; //Ignore terrain like asteroids.
            if($ship->getTurnDeployed($this) > $this->turn) continue;                          
            return $ship;
        }        
        return null;
    }
    
    public function othersDone($userid){
    
        foreach ($this->slots as $player){
            if ($player->id == $userid)
                continue;
                
            if ($player->lastturn != $this->turn || $player->lastphase != $this->phase)
                return false;
        
        }
        
        return true;
    
    }
    
    
    
    public function hasAlreadySubmitted($userid, $slotid = null){
        $slots = $this->getSlotsByPlayerId($userid, $slotid);
        
        foreach ($slots as $slot)
        {
            if ($slot->lastturn < $this->turn || $slot->lastphase < $this->phase || $slot->lastphase == 5 && $this->phase == 3){ 
                    return false;
            }
        }
        
        return true;
    
    }
    
    public function getSlotsByPlayerId($id, $slotid = null)
    {
        $slots = array();
        foreach ($this->slots as $slot){
            if ($slot->playerid == $id){
                if ($slotid == null || $slot->slot == $slotid)
                    $slots[] = $slot;
            }
        }
        
        return $slots;
        
    }

	public function getSlotById($id) {
        
		foreach ($this->slots as $slot) {
			if ($slot->slot == $id) return $slot;
		}

		return null;
	}    
    
    private function setForPlayer($player){
        $this->forPlayer = $player;
        
    }
    
    public function getActiveships() {
        if (is_array($this->activeship)) {
            $ships = [];
    
            foreach ($this->ships as $ship) {
                if (in_array($ship->id, $this->activeship) && !$ship->isTerrain() && ($ship->getTurnDeployed($this) <= $this->turn)) {
                    array_push($ships, $ship);
                }
            }
    
            return $ships; // No need to check count; empty array is returned naturally
        }
    
        foreach ($this->ships as $ship) {
            if ($ship->id == $this->activeship && !$ship->isTerrain() && ($ship->getTurnDeployed($this) <= $this->turn)) {
                return [$ship];
            }
        }
    
        return [];
    }
    
    //New check at end fo firing phase to see f we run Deployment Phase next turn as a Pre-Turn ORders phase for systems like Shading Field
    public function checkDeploymentPhaseForPlayer($playerId){
        foreach($this->ships as $ship){
            if(!$ship->canPreOrder) continue; //Can't pre-Order, filters out irreleveant ships and Terrain            
            if ($ship->userid != $playerId) continue; //Not players ship
            if($ship->isDestroyed()) continue;            
            
            //Torvalus block, other blocks could be added.
            if($ship->faction == "Torvalus Speculators"){
                $shadingField = $ship->getSystemByName("ShadingField");
                if(!$shadingField->isDestroyed() && !$shadingField->isOfflineOnTurn()){
                    return true; //At least one undestroyed, online Shading Field, do Pre-Orders
                }
            } 

            //Trek block.
            if($ship->hasSpecialAbility("Cloaking")){
                $cloakingDevice = $ship->getSystemByName("CloakingDevice");
                if(!$cloakingDevice->isDestroyed() && !$cloakingDevice->isOfflineOnTurn()){
                    return true; //At least one undestroyed, online Shading Field, do Pre-Orders
                }
            } 

        }
        return false;
    }

    private $shipsById = array();

    /**
     * @param $id
     * @return BaseShip|null
     */
    public function getShipById($id){
        
        if (isset($this->shipsById[$id]))
            return $this->shipsById[$id];
        
        foreach ($this->ships as $ship){
            if ($ship->id === $id){
                $this->shipsById[$id] = $ship;
                return $ship;
            }
        }
        
        return null;
    }
    
    public function getShipsInDistance($pos, $dis = 0){

        if ($pos instanceof BaseShip) {
            $pos = $pos->getHexPos();
        }

        if (! ($pos instanceof OffsetCoordinate)) {
            throw new Exception("only OffsetCoordinate supported");
        }

        $ships = array();
        foreach ($this->ships as $ship){
            if ($ship->unavailable)
                continue;

            if ( $ship->getHexPos()->distanceTo($pos) <= $dis){
                $ships[$ship->id] = $ship;
            }
        }

        return $ships;
    }

	public function getClosestShip($pos, $maxRange = 0){

	    if ($pos instanceof BaseShip) {
	        $pos = $pos->getHexPos();
	    }

	    if (!($pos instanceof OffsetCoordinate)) {
	        throw new Exception("only OffsetCoordinate supported");
	    }

	    $closestShips = array(); // Array to store equally closest ships
	    $closestDistance = 100; // Initialize with a large value

	    foreach ($this->ships as $ship){
	        if ($ship->unavailable) continue;
	        if ($ship->isTerrain()) continue; 
	        if ($ship->isDestroyed()) continue;                         

	        $distance = Mathlib::getDistanceHex($ship->getHexPos(), $pos);

	        if ($distance <= $maxRange && $distance < $closestDistance){
	            // New closest distance found, clear the array and add this ship
	            $closestShips = array($ship);
	            $closestDistance = $distance;
	        } elseif ($distance == $closestDistance) {
	            // Add ship to equally close ships
	            $closestShips[] = $ship;
	        }
	    }

	    // Randomly select among equally close ships
	    if (!empty($closestShips)) {
	        $randomIndex = array_rand($closestShips);
	        return $closestShips[$randomIndex];
	    } else {
	        return null; // No ships found within range
	    }
	}

	
	public function getClosestEnemyShip($shooter, $pos, $maxRange = 0){

	    if ($pos instanceof BaseShip) {
	        $pos = $pos->getHexPos();
	    }

	    if (!($pos instanceof OffsetCoordinate)) {
	        throw new Exception("only OffsetCoordinate supported");
	    }

	    $closestShips = array(); // Array to store equally closest ships
	    $closestDistance = 100; // Initialize with a large value

	    foreach ($this->ships as $ship){
	        if ($ship->unavailable) continue;
	        if ($ship->isTerrain()) continue;  

			if ($ship->team == $shooter->team)	        
				continue;
	        if ($ship->isDestroyed()) continue;              
		        
	        $distance = Mathlib::getDistanceHex($ship->getHexPos(), $pos);

	        if ($distance <= $maxRange && $distance < $closestDistance){
	            // New closest distance found, clear the array and add this ship
	            $closestShips = array($ship);
	            $closestDistance = $distance;
	        } elseif ($distance == $closestDistance) {
	            // Add ship to equally close ships
	            $closestShips[] = $ship;
	        }
	    }

	    // Randomly select among equally close ships
	    if (!empty($closestShips)) {
	        $randomIndex = array_rand($closestShips);
	        return $closestShips[$randomIndex];
	    } else {
	        return null; // No ships found within range
	    }
	}



    public function prepareForPlayer($all = false){
        $this->setWaiting();
        $this->calculateTurndelays();
        if (!$all) {
            $this->deleteHiddenData();
        }
        $this->setPreTurnTasks();
        
        if ($this->status == "LOBBY"){
            $this->ships = array();
        }
        
        return $this;
    }
    
    private function setPreTurnTasks(){
        foreach ($this->ships as $ship){
            foreach ($ship->systems as $system){
                $system->beforeTurn($ship, $this->turn, $this->phase);
            }
        
        }
    
    }
    
    private function deleteHiddenData(){
        
        if ($this->phase == -1){
            foreach ($this->ships as $ship){
                if ($ship->userid == $this->forPlayer)
                    continue;
                
                for ($i=(sizeof($ship->movement)-1);$i>=0;$i--)
                {
                    $move = $ship->movement[$i];
                    if ($move->type == "deploy" && $move->turn == $this->turn)
                        unset($ship->movement[$i]);
                }
            }
        }
    
        if ($this->phase == 1){
            foreach ($this->ships as $ship){
                if ($ship->userid != $this->forPlayer){
                    $ship->EW = Array();
                    
                    foreach($ship->systems as $system){
			//Marcin Sawicki: do send PREVIOUS TURNS Power for Jammer!
			if($system instanceof Jammer){
				$power2 = array();
				foreach($system->power as $powentry){
					if($powentry->turn < $this->turn){
						$power2[] = $powentry;
					}
				}
				$system->power = $power2;
			}else{
                        	$system->power = array();
			}
                    }
                }
            }
        }
        
        if ($this->phase == 2) {
            $this->hideActiveShipMovement();
        }
       
        foreach ($this->ships as $ship){
            if ($ship instanceof FighterFlight) {
                foreach ($ship->systems as $fighter){
                    $this->hideSystemFireOrders($fighter);
                } 
            } else {
                $this->hideSystemFireOrders($ship);
            }
        }
    }

    private function hideSystemFireOrders($ship){
        foreach ($ship->systems as $system){
            for ($i = sizeof($system->fireOrders)-1; $i>=0; $i--){
                $fire = $system->fireOrders[$i]; 
                $weapon = $ship->getSystemById($fire->weaponid);
                
                if ($fire->turn == $this->turn && !$weapon->ballistic && $this->phase == 3 && !$weapon->preFires){
                    if($fire->damageclass != 'TerrainCrash' && $fire->damageclass != 'TerrainCollision' && $fire->damageclass != 'AutoRam'){ //RammingAttack isn't PreFire, but we won't THESE fireorders to be passed to Front End for Replay.
                        unset($system->fireOrders[$i]);
                    }    
                }
                if ($fire->turn == $this->turn && $weapon->ballistic && $this->phase == 1){
                    unset($system->fireOrders[$i]);
                }

                if ($fire->turn == $this->turn && $weapon->preFires && $this->phase == 5){
                    unset($system->fireOrders[$i]);
                }                
               
				$weapon->changeFiringMode($fire->firingMode); //Select the current mode so the correct variables are considered, important for Stealth missile.                

                if ($fire->turn == $this->turn && $weapon->hidetarget && $this->phase < 6 && $ship->userid != $this->forPlayer){ //Change to <6 to prevent hidden orders appearing during pre-firing - DK Nov 2025
                    $fire->targetid = -1;
                    $fire->x = "null";
                    $fire->y = "null";

                    foreach ($this->ballistics as $ball){
                        if ($ball->fireOrderId == $fire->id){
                            $ball->targetid = -1;
                            $ball->targetposition  = null;

                        }
                    }
                }
            }
        }
    }

    private function hideActiveShipMovement() {
        $activeShips = $this->getActiveships();
        if (count($activeShips) === 0) {
            return;
        }
        
        $iniative = $activeShips[0]->iniative;

        foreach ($this->ships as $ship) {
            $toDelete = [];

            if ($ship->userid === $this->forPlayer || $ship->iniative !== $iniative) {
                continue;
            }

            foreach ($ship->movement as $i => $move) {
                if ($move->turn == $this->turn && $move->type !== "deploy" && $move->type !== "start") {
                    $toDelete[] = $i;
                }
            }

            foreach ($toDelete as $i) {
                unset($ship->movement[$i]);
            }
        }
    }
    
    private function calculateTurndelays(){
    
        foreach ($this->ships as $ship){
            $ship->currentturndelay = Movement::getTurnDelay($ship);
        }
    }
/*
    private function setWaiting(){
    
        $player = $this->getSlotsByPlayerId($this->forPlayer);
        if (!isset($player[0])){
            $this->waiting = false;
            return;
        }
        
        $player = $player[0];
    
        if ($this->phase === -1 || $this->phase === 1 || $this->phase === 3 || $this->phase === 4){
                            
            if ($player->lastturn == $this->turn && $player->lastphase == $this->phase){
                $this->waiting = true;
            }
        
        }else if ($this->phase == 2){  
            $this->waiting = true;
            if (count($this->getMyActiveShips()) > 0) {
                $this->waiting = false;
            }
        }else{
            $this->waiting = false;
        }
    }
*/

// This helped when a player controlled multiple slots.
private function setWaiting() {
    $slots = $this->getSlotsByPlayerId($this->forPlayer);

    if (empty($slots)) {
        $this->waiting = false;
        return;
    }

    // Default to true; we'll disqualify later if needed
    $this->waiting = true;

    foreach ($slots as $slot) {

        if ($this->phase === -1 || $this->phase === 1 || $this->phase === 3 || $this->phase === 4 || $this->phase === 5) {
            // If even one slot hasn't finished this turn+phase, player isn't done
            if (!($slot->lastturn == $this->turn && $slot->lastphase == $this->phase)) {
                $this->waiting = false;
                return;
            }

        } else if ($this->phase == 2) {
            // If any slot has active ships, player is not waiting
            $activeShips = $this->getMyActiveShips();
            if (count($activeShips) > 0) {
                $this->waiting = false;
                return;
            }

        } else {
            // For any other phases, default to not waiting
            $this->waiting = false;
            return;
        }
    }
}


    private function getIsWaitingForThisPlayer(){
        $slots = $this->getSlotsByPlayerId($this->forPlayer);

        if (count($this->getMyActiveShips()) > 0) {
            return true;
        }
        
        foreach ($slots as $slot){
            

            if ($slot->lastturn < $this->turn) 
                return true;
            
            if ($slot->lastphase < $this->phase && $this->phase != 2)
                return true;

            if ( ($slot->lastphase == 3 || $slot->lastphase == 4) &&
                $this->phase == 1){
                return true;
            }
        }

        return false;
    }

    public function getMyActiveShips() {
        $forPlayer = $this->forPlayer;
        return array_filter($this->getActiveships(), function($ship) use ($forPlayer) {
            return $ship->userid == $forPlayer;
        });
    }

    public function getOpponentActiveShips() {
        $forPlayer = $this->forPlayer;
        return array_filter($this->getActiveships(), function($ship) use ($forPlayer) {
            return $ship->userid != $forPlayer;
        });
    }

    /*
    private function isActiveShipMine() {
        $ships = $this->getActiveships();

        if (count($ships) === 0) {
            return false;
        }

        foreach ($ships as $ship) {
            if ($ship->userid == $this->forPlayer) {
                return true;
            }
        }
    }
    */

	/*check whether indicated ship belongs to this game - as it may happen that it does not!*/
	public function shipBelongs($shipToCheck){
		foreach($this->ships as $shp){
			if ($shp===$shipToCheck){ //yes!
				return true;
			}
		}
		return false; //this ship was not found
	}//endof function shipBelongs

    
    //Replaced by setBlockedHexes() below, but I've left in in case there's any calls I miss - DK 10.2.26
    public function getBlockedHexes() {
        $blockedHexes = [];

        foreach ($this->ships as $ship) {
            if($ship->isDestroyed()) continue;

            if ($ship->Enormous) { // Only enormous units block LoS
                $position = $ship->getHexPos();
                $blockedHexes[] = $position;

                // Check for custom hex offsets (non-circular terrain)
                if (property_exists($ship, 'hexOffsets') && !empty($ship->hexOffsets)) {

                    $move = $ship->getLastMovement();
                    $facing = $move->facing;
                    foreach ($ship->hexOffsets as $offset) {
                        // Use accurate pixel-based rotation
                        $newHex = Mathlib::getRotatedHex($position, $offset, $facing);
                        $blockedHexes[] = $newHex;
                    }
                } elseif ($ship->Huge > 0) { // Standard circular Huge terrain
                    $neighbourHexes = Mathlib::getNeighbouringHexes($position, $ship->Huge);

                    foreach ($neighbourHexes as $hex) {
                        $blockedHexes[] = new OffsetCoordinate($hex); // Ensure hexes are objects
                    }
                }
            }
        }
        return $blockedHexes;
    } //endof function getBlockedHexes


    public function setBlockedHexes() {
        $blockedHexes = [];

        try {
            foreach ($this->ships as $ship) {
                if($ship->isDestroyed()) continue;

                if ($ship->Enormous) { // Only enormous units block LoS
                    $position = $ship->getHexPos();
                    if (!$position) continue; // Skip if no position (e.g. in lobby/initialization)

                    $blockedHexes[] = $position;

                    // Check for custom hex offsets (non-circular terrain)
                    if (property_exists($ship, 'hexOffsets') && !empty($ship->hexOffsets)) {

                        $move = $ship->getLastMovement();
                        if (!$move) continue; // Skip if no movement data

                        $facing = $move->facing;
                        foreach ($ship->hexOffsets as $offset) {
                            // Use accurate pixel-based rotation
                            $newHex = Mathlib::getRotatedHex($position, $offset, $facing);
                            $blockedHexes[] = $newHex;
                        }
                    } elseif ($ship->Huge > 0) { // Standard circular Huge terrain
                        $neighbourHexes = Mathlib::getNeighbouringHexes($position, $ship->Huge);

                        foreach ($neighbourHexes as $hex) {
                            $blockedHexes[] = new OffsetCoordinate($hex); // Ensure hexes are objects
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore exceptions during blocked hex calculation (e.g. in Lobby)
        }

        $this->blockedHexes = $blockedHexes;
    } //endof function setBlockedHexes    

    
    public function getEnormousHexes() {
        $enormousHexes = [];
        
        foreach ($this->ships as $ship) {
            if($ship->isDestroyed()) continue;

            if ($ship->Enormous && $ship->Huge == 0) { // Only enormous units, nothing larger.
                if (property_exists($ship, 'hexOffsets') && !empty($ship->hexOffsets)) {  //Remove odd-shaped terrain as well.              
                    $position = $ship->getHexPos(); 
                    $enormousHexes[] = $position;
                }    
            }    
        }
      
        return $enormousHexes;
    } //endof function enormousHexes    
           

    public function getMinTurnDeployedSlot($slotid, $depavailable) {
        //Check for any bases/OSATs/Terrain, these will mean players still needs to Deploy even if slot->depavailable is set for a higher turn.        
        foreach ($this->ships as $ship) {
            if ($ship->slot != $slotid) {
                continue;
            }
            if($ship->userid == -5) continue; //Skip generated terrain.
            if ($this->phase == -1 && $ship->isTerrain()) return 1; //Player has bought terrain that needs manually placed.
            if ($ship->osat || $ship->base) return 1;
        }

        // Return slot value if no valid ships were found; otherwise return the lowest turn.
        return $depavailable;
    }


    //A check for Manager in case there are no ships deployed at all, in which case just proceed to next phase. 
    public function areDeployedShips() {
        foreach ($this->ships as $ship) {
            if (
                $ship->getTurnDeployed($this) <= $this->turn &&
                !$ship->userid !== -5
            ) {
                return true; //Found at least one player-owned ship, return true and proceed as normal with phase.
            }
        }
        return false; //There are no deployed, non-Terrain ship at this time.
    }


}



