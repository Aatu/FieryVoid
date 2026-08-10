<?php

class TacGamedata {

    public static $currentTurn;
    public static $currentPhase;
    public static $currentGameID;
    public static $safeGameID = 3730; //gameID that is safe for adding new features
    public static $lastFiringResolutionNo = 0; //firing resolution to be used
    //viewer context for per-recipient JSON pruning (gamedata is built and cached PER PLAYER):
    //the player this load is being prepared for, and their team (set once slots are known).
    //ONLY for stripForJson-level masking of hidden orders (Kirishiac Orbital dock/deploy,
    //Shading Field) - never use for game logic; null = no viewer (server processing) = reveal.
    public static $currentForPlayer = null;
    public static $currentForPlayerTeam = null;
    //Chameleon Sensor Suite gate. About one ship in 2000 carries the suite, and it only matters once a
    //player has actively picked a simulacrum, so the ENTIRE feature hangs off this single per-load
    //boolean - a game without a disguised ship pays for one false check and nothing else.
    //Set in onConstructed(), after every ship has been constructed (enhancements included).
    public static $chameleonPresent = false;
    /*Second Chameleon gate, for the ONE effect that is a property of the suite rather than of the
      deception: D11 arming masking, which applies to a CSS ship showing as ITSELF - left on "None",
      or already revealed - and therefore cannot hang off $chameleonPresent. Tests the live special
      ability, so a suite that is destroyed or offline stops masking on its own.*/
    public static $chameleonSuitePresent = false;
    /*Every team id in this game. Needed because ChameleonSensors::isDisguisedFrom() has to answer
      "has EVERY team seen through this?" for an observer, and a system has no route to $gamedata.*/
    public static $chameleonAllTeams = array();
    /*D15, second half: a FINISHED game drops every deception so the post-mortem shows what actually
      happened. Set from $this->status, read by applyChameleonDisguise() and maskChameleonArming().
      Deliberately NOT implemented by forcing the two gates above to false: maskChameleonFireOrders()
      still has to run, because stripping the CHAM: storage tag out of fire-order notes is
      unconditional and must stay that way. With nothing marked disguised it is the only thing left
      for that pass to do.*/
    public static $chameleonDisclosed = false;

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
    public $isStealthPresent = false;
    public $areMinesPresent = false; //Marks that ENEMY mines are present.
    
    
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
    
    public function getPlayerTeam() {
        foreach ($this->slots as $slot) {
            if ($slot->playerid == $this->forPlayer) return $slot->team;
        }
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
        $strippedGamedata->isStealthPresent = $this->isStealthPresent;
        $strippedGamedata->areMinesPresent = $this->areMinesPresent;        

        return $strippedGamedata;
    }

    public function onConstructed(){
        self::$currentForPlayerTeam = $this->getPlayerTeam(); //viewer context (slots are loaded by now) - teammates see each other's hidden orders
        $this->setChameleonTeamList();
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
            $ship->onConstructed($this->turn, $this->phase, $this);
        }

        //One sweep over the ships for all the per-game markers. This used to run INSIDE the loop
        //above (so once per ship, each time walking every ship); moving it out is both cheaper and
        //more accurate, because every ship is now fully constructed - which the Chameleon gate
        //requires, since onConstructed() is what applies enhancements and fills special abilities.
        $this->markUnavailableSetMarkers();
    }

    /*Every team in this game, on a static because a ShipSystem has no route back to $gamedata -
      ChameleonSensors::isRevealedToEveryTeam() is the consumer, and through it the whole "is this
      suite still projecting to anybody?" question.

      Filled from onConstructed() ABOVE the per-ship sweep, not only from markUnavailableSetMarkers()
      below it: Enhancements::setEnhancements runs inside that sweep and asks the same question when
      it decides whether to write the "Disguised as" line. isRevealedToEveryTeam() fails CLOSED on an
      empty list, so filling it afterwards would not have been a visible bug - it would silently have
      answered "still projecting" on the first load of every request. Idempotent, and cheap enough
      (one pass over the slots) that markUnavailableSetMarkers keeps calling it rather than relying
      on the earlier call, since it is the one place that documents the whole marker set.*/
    private function setChameleonTeamList()
    {
        self::$chameleonAllTeams = array();
        foreach ($this->slots as $slot){
            $teamId = (int)$slot->team;
            if (!in_array($teamId, self::$chameleonAllTeams, true)) self::$chameleonAllTeams[] = $teamId;
        }
    }

    public function markUnavailableSetMarkers()
    {
        self::$chameleonPresent = false; //before the phase guard: the static outlives a single load
        self::$chameleonSuitePresent = false;
        self::$chameleonDisclosed = ($this->status === "FINISHED"); //D15: the post-mortem sees everything
        $this->setChameleonTeamList();
        if ($this->phase < -1)
            return;

        foreach ($this->ships as $ship)
        {
            $turnDeploys = $ship->getTurnDeployed($this);

            if($turnDeploys > $this->turn){
                $ship->unavailable = true;
            }

            //Just a convenient place to set Stealth/Mine variable since we're already going through ships in the game.
            if($ship->userid !== $this->forPlayer){
                if($ship->trueStealth && !$ship instanceof Mine && !$ship->isDestroyed() && $ship->factionAge <= 2) $this->isStealthPresent = true; //Hyach and Trek cloaks atm.
                if($ship instanceof Mine && !$ship->isDestroyed()) $this->areMinesPresent = true; //Marks that ENEMY mines are present.
            }

            //Chameleon Sensor Suite gate - see $chameleonPresent. Tests the DISGUISE CHOICE, not
            //isChameleonDisguised(): a destroyed or offlined array loses the ChameleonSensors special
            //ability, and that is precisely the case the shutdown reveal has to record. A suite left
            //on the default "None" still keeps the whole game on the common path.
            if(!self::$chameleonPresent && !empty($ship->chameleonDisguiseClass)) self::$chameleonPresent = true;

            //D11 (Stage 8) gate. Unlike the one above this DOES test the ability, because arming
            //masking survives the reveal but not the loss of the array. hasSpecialAbility is an
            //isset() on a map onConstructed() already filled, so this costs no systems walk.
            if(!self::$chameleonSuitePresent && $ship->hasChameleonSensors()) self::$chameleonSuitePresent = true;
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
                //Some fighter systems can have criticals as well e.g. MissileLost from Magazine.    
                if($system instanceof Fighter){
                    foreach($system->systems as $subsystem){
                        foreach($subsystem->criticals as $crit){
                            if ($crit->updated == true)
                                $list[] = $crit;
                        }
                    }
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
                //Fighter subsystems can have damage entries as well - a flight keeps its defensive
                //systems on the individual craft, and a capacity-pool absorber records what it
                //soaked as a damage entry on ITSELF (ThoughtShield/ThirdspaceShield::absorbDamage).
                //Without this loop none of that ever reached the database: the craft's shield pool
                //never moved (so it absorbed forever and its rating display never changed) and the
                //combat log had no absorption row to report. Mirrors getUpdatedCriticals() above.
                if($system instanceof Fighter){
                    foreach($system->systems as $subsystem){
                        foreach($subsystem->damage as $damage){
                            if ($damage->updated == true)
                                $list[] = $damage;
                        }
                    }
                }

            }
        }

        /*Chameleon phantom sheets (D2/D3). They are deliberately NOT in $this->ships, so the sweep
          above cannot see them and their mirrored damage would be allocated in memory, rendered
          once, and then silently lost on the next load. The plan assumed the existing machinery
          carried them because assignDamageReturnOverkill stamps $target->id - it does, and the
          negative id persists correctly, but only if the entry reaches this list in the first
          place. Gated, so an ordinary game does not walk its ships twice.*/
        if (self::$chameleonPresent){
            foreach ($this->ships as $ship){
                if ($ship->chameleonPhantom === null) continue;
                foreach ($ship->chameleonPhantom->systems as $system){
                    foreach ($system->damage as $damage){
                        if ($damage->updated == true) $list[] = $damage;
                    }
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
        //isDestroyed() also covers Hangar Ops $removed flights — see BaseShip::isDestroyed
        foreach ($this->ships as $ship){
            if ($ship->isDestroyed()) continue;
            if($ship->isTerrain()) continue; //Ignore terrain like asteroids.
            if($ship->mine) continue; //Ignore terrain like asteroids.
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
        self::$currentForPlayer = $player;
    }
    
    public function getActiveships() {
        if (is_array($this->activeship)) {
            $ships = [];
    
            foreach ($this->ships as $ship) {
                if (in_array($ship->id, $this->activeship) && !$ship->isTerrain() && !$ship->mine && ($ship->getTurnDeployed($this) <= $this->turn)) {
                    array_push($ships, $ship);
                }
            }
    
            return $ships; // No need to check count; empty array is returned naturally
        }
    
        foreach ($this->ships as $ship) {
            if ($ship->id == $this->activeship && !$ship->isTerrain() && !$ship->mine && ($ship->getTurnDeployed($this) <= $this->turn)) {
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

            
//Debug::log("name " . $ship->name); 
//Debug::log("name " . $ship->spawned);    

            if($ship instanceof Mine && $ship->spawned == $this->turn){
                return true; //trigger pre-turn phase so Mine settings can be applied at the start of next turn.
            }
            
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
	        if ($ship->mine) continue;           
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
	        if ($ship->mine) continue;     
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

	public function getClosestEnemyMine($shooter, $pos, $maxRange = 0){

	    if ($pos instanceof BaseShip) {
	        $pos = $pos->getHexPos();
	    }

	    if (!($pos instanceof OffsetCoordinate)) {
	        throw new Exception("only OffsetCoordinate supported");
	    }

	    $closestShips = array(); // Array to store equally closest ships
	    $closestDistance = 100; // Initialize with a large value

	    foreach ($this->ships as $ship){
            if(!$ship instanceof Mine) continue;
			if ($ship->team == $shooter->team) continue;
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
        $this->applyChameleonDisguise(); //after setPreTurnTasks: it reads live system state
        $this->maskChameleonFireOrders(); //after applyChameleonDisguise: it reads the flag it sets
        $this->setChameleonFleetValueAdjust(); //likewise - reads chameleonDisguisedForViewer

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

        //Chameleon phantom sheets are deliberately NOT in $this->ships (D1), so the sweep above
        //misses them and their system tooltips would go out empty. Same turn and phase as the real
        //ships get - the phantom is meant to be indistinguishable from an ordinary hull.
        if (!self::$chameleonPresent) return;
        foreach ($this->ships as $ship){
            if ($ship->chameleonPhantom === null) continue;
            foreach ($ship->chameleonPhantom->systems as $system){
                $system->beforeTurn($ship->chameleonPhantom, $this->turn, $this->phase);
            }
        }
    }
    
    /*Chameleon Sensor Suite - decide, once per load, which ships THIS viewer sees as somebody else.
      Marks the ships; BaseShip::stripForJson() does the swapping.

      Called from prepareForPlayer() rather than from deleteHiddenData() deliberately.
      deleteHiddenData is skipped when $all is true, which is how a PAST turn is served
      (Manager::getReplayGameData passes $actualTurn > $turn). Every other kind of hidden data is
      public once its turn has resolved - a disguise is not, and scrubbing back a turn must not
      undress the ship.

      Behind the per-load $chameleonPresent gate, so a game without a disguised ship pays one
      boolean. isChameleonDisguisedFrom() carries every way a deception can end, including the
      own-team check, so there is no policy in here at all.*/
    private function applyChameleonDisguise(){
        if (!self::$chameleonPresent) return;

        //D15: once the game is over there is nothing left to protect and a post-mortem that still
        //lied about which hull was which would be worse than useless. Leaves every ship's
        //chameleonDisguisedForViewer at its false default, which switches off stripForJsonDisguised(),
        //the fire-order remap and the per-viewer threshold mask in one move.
        if (self::$chameleonDisclosed) return;

        foreach ($this->ships as $ship){
            $ship->chameleonDisguisedForViewer = false;
            if (empty($ship->chameleonDisguiseClass)) continue;
            if ($ship->userid == $this->forPlayer) continue;
            if (!$ship->isChameleonDisguisedFrom(self::$currentForPlayerTeam)) continue;
            //a stored class that no longer resolves leaves an ordinary, honest ship (D10)
            if ($ship->getChameleonBlueprint() === null) continue;
            $ship->chameleonDisguisedForViewer = true;
        }
    }

    /*Chameleon Sensor Suite - stop a disguised fleet's visible point total EXCEEDING its budget.

      The fleet list values every row off the blueprint the viewer was served (fleetList.js:370), so
      a disguised ship contributes its SIMULACRUM's cost, and the header is a client-side sum of the
      rows. When the simulacrum is dearer than the real hull that sum can climb above what the slot
      was allowed to spend - a Dargan (750) wearing an Octurion (1350) puts its fleet 600 over - and
      a fleet costing more than its budget is not merely suspicious, it is impossible. That is a
      certain reveal available for no effort at all, which is the one worth removing.

      So: cap the disguised ship's contribution at what it ACTUALLY cost (hull + enhancements) and
      hand the client the overstatement to subtract from the header. Never negative - a CHEAPER
      simulacrum is left exactly as it is, because a fleet that reads light is ordinary (players
      leave points on the table) and inflating it back up would be a lie in the other direction.

      ⚠️ This is a bar, not a wall, and is meant to be. The rows still show the simulacrum's own cost,
      so a viewer who sums them and compares against this header recovers the difference - as does
      anyone who reads gamedata.slots in devtools. Guiding constraint §0 stands: everything the
      enemy's browser receives is public. What it buys is that the PASSIVE view - the number sitting
      on screen - is no longer self-evidently impossible.

      Why the adjustment is per SLOT and not a field on the disguised ship: any per-ship field marks
      that ship. Being the one hull in the fleet carrying an unusual key is a far better clue than
      the arithmetic this exists to bury, and it survives every reveal rule we have. PlayerSlot
      declares $fleetValueAdjust with a 0 default so it ships on every slot of every game.

      Not gated on $chameleonPresent for the reset - the zeroing has to be unconditional or a slot
      could carry an adjustment from whatever the object held before. Only the sweep is gated.*/
    private function setChameleonFleetValueAdjust(){
        foreach ($this->slots as $slot) $slot->fleetValueAdjust = 0;

        if (!self::$chameleonPresent) return;
        if (self::$chameleonDisclosed) return; //D15: the post-mortem shows the real numbers

        foreach ($this->ships as $ship){
            if (!$ship->chameleonDisguisedForViewer) continue;
            if (!isset($this->slots[$ship->slot])) continue;

            $blueprint = $ship->getChameleonBlueprint();
            if ($blueprint === null) continue; //cannot happen here - applyChameleonDisguise checked

            //What the viewer's fleet list will add up for this row. Enhancements are masked to 0 on
            //a disguised payload (ShipClasses::stripForJsonDisguised), so the row is the hull alone.
            $shown = (float)$blueprint->pointCost;

            //What it really cost. pointCostEnh2 is folded into pointCostEnh once a game is running
            //(Manager.php:1026) but is summed here anyway so this cannot rot if that changes.
            $paid = (float)$ship->pointCost + (float)$ship->pointCostEnh + (float)$ship->pointCostEnh2;

            if ($shown <= $paid) continue; //cheaper simulacrum - nothing to cap
            $this->slots[$ship->slot]->fleetValueAdjust += (int)round($shown - $paid);
        }
    }

    /*Chameleon Sensor Suite (D3b, Stage 7) - the per-viewer fire-order mask.

      This is a masking site of a shape no other one in FV has, which is why it needed its own pass.
      Every existing mask hides a ship's OWN private state from people who are not on its team. This
      one hides state that lives on the SHOOTER's weapon, from the shooter themselves, because the
      shooter is the deceived party: they must be shown the shot they think they took at the ship
      they think they were shooting at. Only the disguised ship's own side sees the truth.

      Three things are rewritten for a viewer who still believes the deception:

        needed   - the simulacrum's threshold, so their combat log agrees with the hit chance their
                   client previewed off the fake blueprint (finding #10);
        shotshit - how many hits the PHANTOM took, which is what their damage entries show;
        notes    - REBUILT rather than edited. Since Stage 7 the stored breakdown names the real
                   hull outright ("defence: 16" on a ship the viewer believes has 14), and the
                   client parses only two things out of this string, so the safe construction is to
                   emit those two and nothing else. Same principle as stripForJsonDisguised(): the
                   default for any field is "fake", never "real", so a field nobody thought about
                   cannot leak. CSS is not protection - #log .notes is display:none, but devtools
                   is not, and the whole feature assumes the enemy reads their own payload.

      The CHAM: tag is stripped for EVERYONE, tag or no tag, disguised or not - one unconditional
      pass, so there is no path on which it can reach a browser. Likewise notes are scrubbed for any
      order at a disguised target even when no tag was written (a weapon with its own roll loop that
      never reached Weapon::fire()'s tagging line): the numbers then stay real, but the breakdown
      still goes.

      Runs from prepareForPlayer(), not deleteHiddenData(), for the same reason applyChameleonDisguise
      does: deleteHiddenData is skipped when $all is true, which is how a PAST turn is served, and
      replaying back over a disguised turn must not undress the ship.*/
    private function maskChameleonFireOrders(){
        if (!self::$chameleonPresent) return;

        $disguisedIds = array();
        foreach ($this->ships as $ship){
            if ($ship->chameleonDisguisedForViewer) $disguisedIds[$ship->id] = true;
        }

        foreach ($this->ships as $ship){
            if ($ship instanceof FighterFlight) {
                foreach ($ship->systems as $fighter){
                    $this->maskChameleonFireOrdersOn($fighter, $disguisedIds);
                }
            } else {
                $this->maskChameleonFireOrdersOn($ship, $disguisedIds);
            }
        }
    }

    private function maskChameleonFireOrdersOn($unit, $disguisedIds){
        foreach ($unit->systems as $system){
            foreach ($system->fireOrders as $fire){
                $fire->chameleonFake = null; //transient resolution state, never serialised
                $fire->notes = (string)$fire->notes;

                $fake = null;
                if (strpos($fire->notes, 'CHAM:') !== false){
                    if (preg_match_all('/\s*CHAM:(-?\d+):(\d+)/', $fire->notes, $m)){
                        $last = sizeof($m[1]) - 1;
                        $fake = array((int)$m[1][$last], (int)$m[2][$last]);
                    }
                    $fire->notes = preg_replace('/\s*CHAM:-?\d+:\d+/', '', $fire->notes);
                }

                if (!isset($disguisedIds[$fire->targetid])) continue;

                //notes rebuilt BEFORE needed is overwritten - the per-shot shift is measured off it
                $fire->notes = self::buildChameleonFireOrderNotes(
                    $fire->notes, ($fake === null) ? $fire->needed : $fake[0], $fire->needed);

                /*Order-level pubnotes describe what pass 1 did to the REAL hull, and under the dual
                  threshold (Stage 7) that can flatly contradict what the viewer is shown: a shot
                  that missed the real ship but hit the phantom carries " MISSED! " while the
                  phantom's damage entries render underneath it. Most of the rest is weapon-effect
                  narrative that names the mechanism outright. Dropped rather than filtered, on the
                  same default-absent principle as the notes rebuild. The per-system DamageEntry
                  pubnotes are untouched, so the viewer still gets the damage story.
                  Deferred refinement: serve the PHANTOM pass's narrative instead of nothing, which
                  needs pass 2's pubnotes captured and persisted the way the CHAM: tag is.*/
                $fire->pubnotes = '';

                if ($fake === null) continue;

                $fire->needed   = $fake[0];
                $fire->shotshit = $fake[1];
            }
        }
    }

    /*The two fragments combatLog.js reads back out of a fire order's notes, and nothing else:
      "Interception: n sources:m" (combatLog.js:117) and one "rolled: x, needed: y" per shot
      (combatLog.js:137, which greens the roll when it beat the threshold). Every per-shot threshold
      differs from the order's by the same grouping modifier, so shifting them all by one delta keeps
      the dice tooltip consistent with the shots-hit count the viewer is shown.

      Shared with the Stage 6 mask on orders fired FROM a disguised ship, which passes the same value
      for both thresholds: there the numbers are already true and it is the BREAKDOWN that leaks,
      since it carries the real weapon's fire control and range penalty. One definition of "which
      fragments survive" so the two masks cannot drift apart.*/
    public static function buildChameleonFireOrderNotes($rawNotes, $fakeNeeded, $realNeeded){
        $rawNotes = (string)$rawNotes;
        $notes = '';
        if (preg_match('/Interception: (\d+) sources:(\d+)/', $rawNotes, $m)){
            $notes .= 'Interception: ' . $m[1] . ' sources:' . $m[2] . ', final to hit: ' . $fakeNeeded;
        }

        $delta = $fakeNeeded - $realNeeded;
        if (preg_match_all('/rolled: (-?\d+), needed: (-?\d+)/', $rawNotes, $shots, PREG_SET_ORDER)){
            $n = 0;
            foreach ($shots as $shot){
                $n++;
                $notes .= ' FIRING SHOT ' . $n . ': rolled: ' . $shot[1] . ', needed: ' . ((int)$shot[2] + $delta) . "\n";
            }
        }
        return $notes;
    }

    /* Hangar Ops - deployment-phase dock masking (phase -1 only).
       A flight (or LCV) a player chooses to START the game inside a carrier is docked the
       moment THAT player commits their deployment - DeploymentGamePhase::process resolves it
       immediately, long before the phase advances. The dock marks the flight $removed (which
       isDestroyed() reports as true) and appends a hangarUsage entry on the carrier, so an
       opponent who had not committed yet watched the flight vanish off the board, its fleet-list
       row turn "Docked", and the carrier's hangar fill up: the whole hangar loadout leaked
       before they had written their own orders. Undo both, for this turn's docks only, for any
       viewer who does not own the ships - matching the deploy-move rule directly above (which
       likewise hides a slot's placements from everyone but its owner).

       Keying on the DOCK TURN is what keeps older docks public. Phase -1 is the first phase of
       a turn, so the only dock that can carry dockedTurn == the current turn at this point is a
       deploy-start dock submitted in this very phase; a fighter that landed during last turn's
       Fire Phase carries dockedTurn == turn-1 and is left exactly as it is. The carrier pass runs
       first and collects the ship ids from the entries it drops, so a unit is only ever un-removed
       when a this-turn dock record is actually what took it off the board.

       An un-removed flight falls back on its "start" movement row - every ship is given one at its
       slot's deployment-box centre when the game is created - so it simply sits where every other
       not-yet-placed enemy ship sits during Deployment and reveals nothing by reappearing. Runs
       from deleteHiddenData, so the history/replay path ($all) keeps the real docked state. */
    private function hideDeploymentDocks(){
        $dockedIds = array();

        foreach ($this->ships as $ship){
            if ($ship->userid == $this->forPlayer) continue;

            foreach ($ship->systems as $system){
                if (!($system instanceof Hangar)) continue;

                if (is_array($system->hangarUsage) && !empty($system->hangarUsage)){
                    $kept = array();
                    foreach ($system->hangarUsage as $entry){
                        if (isset($entry['dockedTurn']) && (int)$entry['dockedTurn'] === (int)$this->turn){
                            //Auto-filled default shuttles carry no dockedTurn, so only real
                            //docks are ever dropped here.
                            if (!empty($entry['dockedFlightId'])) $dockedIds[] = (int)$entry['dockedFlightId'];
                            continue;
                        }
                        $kept[] = $entry;
                    }
                    $system->hangarUsage = $kept;
                }

                //LCV rails dock a WHOLE ship; the link snapshot carries its own dock turn.
                if (!empty($system->isLCVRail) && is_array($system->lcvDocked)
                    && (int)($system->lcvDocked['dockTurn'] ?? 0) === (int)$this->turn){
                    if (!empty($system->lcvDocked['shipId'])) $dockedIds[] = (int)$system->lcvDocked['shipId'];
                    $system->lcvDocked = null;
                }
            }
        }

        foreach ($dockedIds as $id){
            $unit = $this->getShipById($id);
            if (!$unit || !$unit->removed) continue;
            if ($unit->removedTurn !== null && (int)$unit->removedTurn !== (int)$this->turn) continue;
            $unit->removed = false;
            $unit->removedTurn = null;
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

                //Re-index: deploy rows sit at the FRONT, so unsetting them leaves the array
                //keyed from 1..n and json_encode would emit a JSON object, not an array.
                $ship->movement = array_values($ship->movement);
            }

            $this->hideDeploymentDocks();
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

        if ($this->phase == 3) {
            $this->hideEnemyCombatPivots();
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
        
        $this->hideStealthShipMovement(); //Send empty arrays if current player's team can't see the ship.
    }

    private function hideSystemFireOrders($ship){
        $playerTeam = $this->getPlayerTeam();
        // Fighter objects (individual fighters within a FighterFlight) have no userid/team; look up the parent flight
        if ($ship instanceof Fighter) {
            $flight = $this->getShipById($ship->flightid);
            $isAlly = $flight ? $flight->userid == $this->forPlayer || $flight->team == $playerTeam : false;
        } else {
            $isAlly = $ship->userid == $this->forPlayer || $ship->team == $playerTeam;
        }
        foreach ($ship->systems as $system){
            for ($i = sizeof($system->fireOrders)-1; $i>=0; $i--){
                $fire = $system->fireOrders[$i];
                $weapon = $ship->getSystemById($fire->weaponid);
                
                if ($fire->turn == $this->turn && !$weapon->ballistic && $this->phase == 3 && !$weapon->preFires){
                    if($fire->damageclass != 'TerrainCrash' && $fire->damageclass != 'TerrainCollision' && $fire->damageclass != 'AutoRam'){ //RammingAttack isn't PreFire, but we want THESE fireorders to be passed to Front End for Replay.                         
                        unset($system->fireOrders[$i]);
                    }    
                }
                /*Initial Orders is still open - this turn's launch declarations are not public yet.
                Match the ORDER's own type as well as the weapon's ballistic flag: some weapons whose
                class is not ballistic still declare type-'ballistic' orders in Initial Orders (e.g.
                Gravitic Augmenter Modes 1/2), and the client draws launch/target hexes straight from
                fire orders - so those leaked the moment the owner committed. Load-generated plasma
                cloud markers (damageclass 'PersistentEffectPlasma') represent LAST turn's already
                public cloud and must keep flowing.*/
                if ($fire->turn == $this->turn && $this->phase == 1
                    && ($weapon->ballistic || $fire->type == 'ballistic')
                    && $fire->damageclass != 'PersistentEffectPlasma'){
                    unset($system->fireOrders[$i]);
                }

                //Hide a pre-firing order during Pre-Firing (the client re-creates it live in this
                //phase). Must key off the ORDER's type, not just $weapon->preFires: a dual-nature
                //weapon (Gravitic Augmenter) has preFires=true but also carries BALLISTIC Mode 1/2
                //orders declared in Initial Orders — those are NOT pre-firing orders and must survive
                //phase 5 (they only resolve in Firing). A normal pre-firing weapon's orders are all
                //type 'prefiring', so this extra guard is a no-op for them.
                if ($fire->turn == $this->turn && $weapon->preFires && $this->phase == 5 && $fire->type == 'prefiring'){
                    unset($system->fireOrders[$i]);
                }

                /*Weapons whose use is entirely invisible to the enemy until it resolves (Thought
                Wave, Second Sight): strip their pending CURRENT-turn orders from enemy/spectator
                payloads in every live phase. Ballistic launches are normally public once Initial
                Orders close, but these weapons have no visible launch - the orange launch hex from
                Movement phase onward betrayed the activation. Once the turn resolves the orders are
                historical (turn < current) and flow normally, so the replay and combat log still work.*/
                if ($fire->turn == $this->turn && !$isAlly && $weapon->getHideFireOrdersFromEnemies()
                    && ($this->phase == 1 || $this->phase == 2 || $this->phase == 5 || $this->phase == 3)){
                    unset($system->fireOrders[$i]);
                }
               
				$weapon->changeFiringMode($fire->firingMode); //Select the current mode so the correct variables are considered, important for Stealth missile.                

                $hideTargetPhase = $weapon->revealAfterPreFire
                    ? ($this->phase == 1 || $this->phase == 2 || $this->phase == 5) //Reveal in phases 3/4 after PreFire resolution.
                    : ($this->phase < 6);
                if ($fire->turn == $this->turn && $weapon->hidetarget && $hideTargetPhase && !$isAlly){ //Change to <6 to prevent hidden orders appearing during pre-firing - DK Nov 2025
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

            $hideOwn = $ship->userid !== $this->forPlayer && $ship->iniative === $iniative;

            //A mirrored 'attached' move is a copy of the HOST's committed movement
            //(auto-duplicated at host commit — MovementGamePhase::process), so it must be
            //hidden whenever the host's own movement is hidden — INCLUDING on the viewer's
            //OWN pod, which $hideOwn never covers: showing the pod at the host's
            //destination hex leaks the enemy host's secret move while the pod's owner is
            //still plotting in the same initiative bracket. Hidden, the pod falls back to
            //its preturn 'sync' row = the host's start-of-turn hex — identical to the
            //state before the host committed, and to where a detach plotted BEFORE the
            //host's commit starts from (commit order no longer changes what either
            //player sees or plots).
            $hideAttachedMirror = false;
            if (!empty($ship->attached)) {
                $host = $this->getShipById((int)key($ship->attached));
                if ($host && $host->userid !== $this->forPlayer && $host->iniative === $iniative) {
                    $hideAttachedMirror = true;
                }
            }

            if (!$hideOwn && !$hideAttachedMirror) {
                continue;
            }

            foreach ($ship->movement as $i => $move) {
                if ($move->turn != $this->turn) {
                    continue;
                }

                if ($hideAttachedMirror && $move->type === "attached") {
                    $toDelete[] = $i;
                    continue;
                }

                if (!$hideOwn) {
                    continue;
                }

                //Never hide a forced move (Gravitic Augmenter's free jinks are marked forced=true):
                //they are a REVEALED committed effect (declared in Initial Orders alongside the visible
                //stat buffs), not the enemy's secret in-progress plot. Hiding it made the opponent lose
                //the +3 jink (and the -15% to-hit it grants against the Warrior) during the Movement phase.
                //Server-side the only forced movement is that transient jink; persisted moves never carry
                //forced (no DB column), so this can't leak a normal manoeuvre.
                //
                //Also never hide the turn-start STATE MARKERS (isRolled / isRolling /
                //isPivotingLeft / isPivotingRight, written at end-of-turn movement generation):
                //they carry state established at the START of the turn — a completed roll flip,
                //or a roll/pivot announced the previous turn and still in progress — which the
                //owner cannot change with this turn's move, and which the opponent already saw
                //in last turn's tooltip. Hiding them made an active-initiative enemy's tooltip /
                //ship window / icon lose its Rolled/Rolling/Pivoting status (and the window's
                //port-starboard mirroring) while awaiting their move. A roll or pivot ORDERED
                //this turn is a normal "roll"/"pivot" move and stays hidden until committed.
                if ($move->type !== "deploy" && $move->type !== "start"
                    && $move->type !== "isRolled" && $move->type !== "isRolling"
                    && $move->type !== "isPivotingLeft" && $move->type !== "isPivotingRight"
                    && empty($move->forced)) {
                    $toDelete[] = $i;
                }
            }

            foreach ($toDelete as $i) {
                unset($ship->movement[$i]);
            }

            //MUST re-index: the carve-outs above (start/deploy, the isRolled/isRolling/
            //isPivoting markers, and forced moves) are scattered THROUGH the deleted block -
            //the Gravitic Augmenter's forced free jink in particular is appended LAST - so
            //unset() leaves gaps in the keys. json_encode turns a gappy array into a JSON
            //OBJECT, and the client's consumeMovement then dies on movements.filter().
            $ship->movement = array_values($ship->movement);
        }
    }

    /*Fighter flights may change facing (combat pivot) while declaring their fire in the Firing
    phase; FireGamePhase::process persists those as current-turn value='combatpivot' movement rows
    at COMMIT time. A player still declaring their own fire must not see the enemy flight's icon
    already rotated (facing feeds arcs and hit modifiers), so strip enemy pivot rows declared this
    turn while the Firing phase is still open. Once firing resolves the turn advances and the rows
    are public history - the next-turn replay animates them normally.*/
    private function hideEnemyCombatPivots() {
        $playerTeam = $this->getPlayerTeam();

        foreach ($this->ships as $ship) {
            if ($ship->userid == $this->forPlayer || $ship->team == $playerTeam) {
                continue; //owner and teammates see their own pending pivots
            }

            for ($i = sizeof($ship->movement) - 1; $i >= 0; $i--) {
                $move = $ship->movement[$i];
                if ($move->turn == $this->turn && $move->value == 'combatpivot') {
                    unset($ship->movement[$i]);
                }
            }

            //Re-index so json_encode still emits an ARRAY - a transient move appended after
            //the pivot (e.g. the Augmenter's forced jink) would otherwise leave a key gap.
            $ship->movement = array_values($ship->movement);
        }
    }

    private function hideStealthShipMovement() {
        $playerTeam = $this->getPlayerTeam();

        foreach ($this->ships as $ship) {
            if ($ship->userid == $this->forPlayer || $ship->team == $playerTeam) {
                continue;
            }

            if (!$ship->trueStealth) {
                continue;
            }

            $isDetected = false;

            if($ship instanceof FighterFlight){
                //A flight's stealth systems live one level deeper, on each fighter. Detection state
                //is tracked on the FIRST fighter's system ONLY: checkStealth resolves it via
                //FighterFlight::getSystemByName (first match, destroyed fighters NOT skipped) and
                //checkStealthNextPhase writes its notes against that one system id. Every other
                //fighter's copy therefore keeps the class default detected=true / empty detectedNew,
                //so scanning them all would report "detected" for a shaded flight forever.
                $stealthSystem = null;
                foreach ($ship->systems as $fighter){
                    foreach ($fighter->systems as $sys){
                        if ($sys instanceof Stealth || $sys instanceof ShadingField || $sys instanceof CloakingDevice) {
                            $stealthSystem = $sys;
                            break 2;
                        }
                    }
                }

                if ($stealthSystem) {
                    if (isset($stealthSystem->detectedNew) && is_array($stealthSystem->detectedNew) && in_array($playerTeam, $stealthSystem->detectedNew)) {
                        $isDetected = true;
                    } elseif (isset($stealthSystem->detected) && $stealthSystem->detected === true && empty($stealthSystem->detectedNew)) {
                        $isDetected = true;
                    }
                }
            }else{
                foreach ($ship->systems as $system) {
                    if ($system instanceof Stealth || $system instanceof ShadingField || $system instanceof CloakingDevice) {
                        if (isset($system->detectedNew) && is_array($system->detectedNew) && in_array($playerTeam, $system->detectedNew)) {
                            $isDetected = true;
                            break;
                        }
                        if (isset($system->detected) && $system->detected === true && (!isset($system->detectedNew) || empty($system->detectedNew))) {
                            $isDetected = true;
                            break;
                        }
                    }
                    if ($system instanceof MineStealth) {
                        if (isset($system->detected) && is_array($system->detected) && in_array($playerTeam, $system->detected)) {
                            $isDetected = true;
                            break;
                        }
                    }
                }
            }    

            if (!$isDetected) {
                // Give it a dummy deploy movement completely off screen so the client doesn't crash reading position
                $ship->movement = array(
                    new MovementOrder(-1, "deploy", new OffsetCoordinate(-10000, -10000), 0, 0, 0, 0, 0, false, $this->turn, 0, 0)
                );
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



