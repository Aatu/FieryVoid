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
    /*The post-mortem discloses everything: once a game has ENDED its replay shows the standing
      logistics state that was own-team-only while it was being played (ordnance loads, hangar
      contents, queued launch/dock orders). Read by ShipSystem::isDisclosedToCurrentViewer(), which
      is the gate on those masks. Set from isGameOver(), so SURRENDERED counts as well as FINISHED -
      most dead games never leave SURRENDERED. A static because a ShipSystem has no route back to
      $gamedata; re-set on every load, since one request can build gamedata twice. Defaults false so
      a load that never reached onConstructed() masks rather than discloses.*/
    public static $currentGameFinished = false;

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

        /* ⭐⭐ REINFORCEMENTS STAGE 9 - THE ARRIVAL INITIATIVE PENALTY, ATTACHED HERE AND NOWHERE
           ELSE (user request 2026-08-29: a ship tooltip line and a ship-window banner for it).
           The number itself is BaseShip::getReinforcementArrivalIniModifier's, which is the same
           method both initiative generators use - so the UI can never quote a figure the roll did
           not actually apply.

           ⚠️ WHY IT IS BOLTED ON HERE RATHER THAN INSIDE BaseShip::stripForJson, where every other
           reinforcement field lives: the answer needs a GAMEDATA (the scatter is rebuilt from an
           IndividualNote on the OPENER's engine, so it takes a walk to another ship), and a ship's
           stripForJson is handed none. The alternative - a public $arrivalIniPenalty on BaseShip -
           would ride the STATIC BLUEPRINT of all 6000-odd hulls: ShipCompactor walks the RAW
           BaseShip object, not this method ([[arch_shipcompactor_key_stripping]]).

           ⚠️ THE RULE GATE IS RESOLVED ONCE, OUTSIDE THE LOOP, and the per-ship test after it is
           the plain $reinforcement property read - so an ordinary game pays one boolean per ship
           per payload and nothing else. Same ordering, and the same reason, as
           getReinforcementArrivalIniModifier's own two tests.

           ⚠️ EMITTED ONLY WHEN NON-ZERO, so every other unit's payload is byte-identical to before
           and the client can treat the key's presence as the whole question. Zero is also the right
           answer for a doorway that did not scatter (a gate's never does) and for every turn after
           the arrival one - neither should carry a banner.

           ⚠️ THE array_map THIS REPLACED PRESERVED KEYS (it was passed a single array), which is why
           hideHyperspaceReinforcements rebuilds $this->ships by append rather than unset()-ing from
           it - a gap would have made json_encode emit a JSON OBJECT where the client requires an
           array. The append below re-indexes unconditionally, so that hazard is now covered twice;
           do not read this as licence to remove the rebuild there, which also drops the id cache. */
        $reinforcementsRule = ($this->rules && $this->rules->hasRuleName('allowReinforcements'));

        $strippedGamedata->ships = array();
        foreach ($this->ships as $ship){
            $strippedShip = $ship->stripForJson();

            if ($reinforcementsRule && !empty($ship->reinforcement)){
                $penalty = (int)$ship->getReinforcementArrivalIniModifier($this);
                if ($penalty !== 0) $strippedShip->arrivalIniPenalty = $penalty;
            }

            $strippedGamedata->ships[] = $strippedShip;
        }

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
        self::$currentGameFinished = $this->isGameOver(); //post-mortem discloses private logistics (ammo loads, hangar contents)
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

                    /* REINFORCEMENTS_PLAN.md - A BALLISTIC WHOSE SHOOTER IS NOT ON THE MAP.
                       getLastTurnMovement() skips every 'start' row, so a unit that has never
                       actually moved answers null - and a reinforcement declaring its jump point
                       EXIT from hyperspace is exactly that unit. Reading ->position off the
                       null was a fatal ErrorException on EVERY gamedata load from phase 2 of the
                       turn the exit was declared (both players, every poll); phase-1 secrecy
                       - hideSystemFireOrders strips every current-turn ballistic order from every
                       phase-1 payload, including its author's - is what hid it until Initial
                       Orders were committed.

                       Nothing wants a Ballistic record for one either: $this->ballistics is absent
                       from stripForJson so it never reaches the client at all, the exit marker
                       is drawn from the fire order itself (BallisticIconContainer's exitOrders,
                       or the slot's formingExits for an enemy viewer), and the only
                       server-side consumer is the hidetarget mask in hideSystemFireOrders - which
                       an exit is not subject to, being masked wholesale by
                       hideHyperspaceReinforcements instead.

                       The null test is the general rule and is deliberately NOT written as
                       "damageclass === 'jumpexit'": any ballistic order from a shooter with no
                       movement row lands here, and inventing a position for one is never right. */
                    if ($movement === null) continue;

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
            //REINFORCEMENTS_PLAN.md §3.6 - but NOT for a unit still in hyperspace. Both of these are
            //one-bit broadcasts to every viewer ("somebody has a cloak", "enemy mines are out
            //there"), and a unit nobody can see must not set either: hideHyperspaceReinforcements
            //deletes the ship from the payload a moment later, and a flag it had already flipped
            //would survive it and say the ship was there.
            if($ship->userid !== $this->forPlayer && !$ship->isReinforcement()){
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
    
    /*Has this game ENDED? Both terminal statuses count, and in practice SURRENDERED is the usual
      one: a surrender that leaves one team standing writes SURRENDERED, and only a subsequent
      Manager::changeTurn promotes it to FINISHED - which most dead games never reach, because the
      turn stops rolling. (Local corpus at the time of writing: 118 SURRENDERED vs 1 FINISHED.)
      Same pairing DiscordNotifier already uses to decide a game is over.
      NOT to be confused with isFinished() below, which asks a different question entirely - whether
      two hostile ships can still fight - and is a game-STATE test, not a status one.*/
    public function isGameOver(){
        return ($this->status === "FINISHED" || $this->status === "SURRENDERED");
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
        $this->markJumpedDockedFlights(); //after deleteHiddenData: it reads the MASKED movement (see the method)
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

    /* Hangar Ops x JUMP_POINTS_PLAN.md Stage 4 - flag every flight that is sitting in a hangar of a
       carrier which has left through a jump vortex, so the fleet list can paint its row "Jumped"
       (orange) rather than "Docked" (blue).

       The client cannot answer this for itself except on its OWN fleet. It walks each carrier's
       hangarUsage for dockedFlightId links (fleetList.js getJumpedDockedFlightIds), and bay contents
       are own-team-only - Hangar::stripForJson masks $hangarUsage out of an opponent's payload, see
       the ruling there - so an opponent gets an empty list and their copy of the row stays blue for
       the rest of the game. Answered here instead, as one boolean on the FLIGHT. It says "this unit
       is in hyperspace" and nothing whatever about what else the bay holds, so the contents mask is
       untouched - and the flight already has its own row on that screen, so no unit is disclosed
       either.

       Runs AFTER deleteHiddenData, deliberately: a jump-out this viewer is not entitled to see yet -
       hideActiveShipMovement strips the order while its initiative bracket is still moving - is
       already gone from $carrier->movement by the time we look, so the flag inherits that masking
       for free rather than restating it. */
    private function markJumpedDockedFlights(){
        foreach ($this->ships as $carrier){
            if ($carrier instanceof FighterFlight) continue;   //flights carry nothing themselves

            //Collect the stored flights FIRST: it is a pair of instanceof tests on a hull with no
            //hangar, which is most of them, and it keeps the departure test off every unit in every
            //ordinary game that has no vortex in it.
            $docked = array();
            foreach ($carrier->systems as $system){
                if (!($system instanceof Hangar)) continue;
                if (!is_array($system->hangarUsage)) continue;

                foreach ($system->hangarUsage as $entry){
                    if (!empty($entry['dockedFlightId'])) $docked[] = (int)$entry['dockedFlightId'];
                }
            }

            if (empty($docked)) continue;                      //nothing aboard to take with it
            if (!$this->hasLeftThroughVortex($carrier)) continue;

            foreach ($docked as $flightId){
                $flight = $this->getShipById($flightId);
                if ($flight) $flight->jumpedWithCarrier = true;
            }
        }
    }

    /* Has this unit left the battle through a vortex, as far as THIS viewer can tell?

       Two states, because the departure spans a phase. A COMMITTED jump-out is on the board but not
       yet resolved - Movement::resolveJumpOuts removes the unit at the END of the Movement phase - and
       is read straight off the (already masked) movement. Afterwards the order is history and the
       removal is the record, which is what hasJumpedToHyperspace answers.

       hasJumpedToHyperspace MUST be paired with isDestroyed: JumpEngine::hasJumped only distinguishes
       "jumped" from "damage-killed" among units already out of play, so on a healthy hull with a jump
       engine it returns true on its own. The client twin of this pairing is in fleetList.js. */
    private function hasLeftThroughVortex($ship){
        /* ⚠️ ORDER MATTERS, AND THE REMOVAL IS THE AUTHORITY ONCE THERE IS ONE. The jump-out ORDER
           was the first test here until the Vortex Disruptor shipped (2026-08-29), and it has to
           give way to it: a unit killed by a collapsing jump point still carries the order it flew
           in on, so asking the order first said "it left" about a ship that is a wreck, and the
           docked flights inside it were painted Jumped for the rest of the game. A destroyed unit's
           damage entries are the record - hasJumpedToHyperspace subtracts the HyperspaceJump ones
           and asks whether the rest was fatal, which the disruptor's VortexCollapse entry makes it.
           The client twin of this fix is fleetListManager.getJumpedDockedFlightIds. */
        if ($ship->isDestroyed()) return $ship->hasJumpedToHyperspace();

        //Still on the board: a COMMITTED jump-out is a departure the phase has not resolved yet.
        return (Movement::getJumpOutOrder($ship->movement, $this->turn) !== null);
    }

    /* REINFORCEMENTS_PLAN.md §3.6 - WHAT AN ENEMY IS TOLD ABOUT A FLEET STILL IN HYPERSPACE:
     * a count and a point total, on the slot. Never classes, never names.
     *
     * Every OTHER list in the game already drops these units for free, because getTurnDeployed
     * answers 999 for them (§3.2). This sweep exists because that is not enough: a hyperspace
     * unit's full sheet - its class, its enhancements, its damage - is still in the payload for
     * anyone who opens the console. So the ship is removed from the list outright.
     *
     * ⭐ A UNIT STOPS BEING CONCEALED THE MOMENT IT IS ASSIGNED an arrival turn, which is the same
     * turn its jump point becomes a public blue marker. The disclosure is therefore: turn N -
     * "three units are coming, 1250 points, somewhere near here"; turn N+1 - the ships themselves.
     * That is deliberately a step MORE concealed than today's late-deploy slots, which show their
     * full composition from turn 1.
     *
     * RUNS FIRST inside deleteHiddenData, so every later mask (hideDeploymentDocks,
     * hideActiveShipMovement, hideEnemyCombatPivots, the fire-order sweep, hideStealthShipMovement)
     * walks the already-shortened list and cannot write to a ship that is about to vanish. Living
     * inside deleteHiddenData also inherits the $all skip for free - a PAST turn served to the
     * replay is not masked, which is the same rule applyChameleonDisguise follows.
     */
    private function hideHyperspaceReinforcements(){

        //Post-mortem discloses everything, exactly as ShipSystem's private-logistics masks do.
        //⚠️ NOT self::$chameleonDisclosed, which is FINISHED only and misses SURRENDERED - the
        //overwhelmingly common terminal status.
        if (self::$currentGameFinished) return;

        /* Zeroed unconditionally and up front, mirroring setChameleonFleetValueAdjust. A PlayerSlot
           is rebuilt per load so in practice both already hold their declared 0, but nothing else
           in the request zeroes them and a per-viewer field that can only ever be added to is one
           refactor away from leaking. */
        foreach ($this->slots as $slot){
            $slot->reinforcementCount = 0;
            $slot->reinforcementPoints = 0;
            $slot->formingExits = array();
        }

        /* ⭐ STAGE 9 EFFICIENCY GATE (user request 2026-08-29), and it goes AFTER the zeroing above
           so the defensive reset still happens in every game. Nothing below can be true without the
           rule - BuyingGamePhase::process writes `$ship->reinforcement = $allowReinforcements && …`,
           so the column is 0 for every unit of every game that does not have it - and this method
           is on the per-viewer load path, which is the single hottest sweep the feature touches.

           ⚠️ IT FAILS OPEN. The test is "rules exist AND say no", never "rules do not say yes": a
           load that somehow arrives without a GameRules object still does the masking. Getting that
           backwards would turn a missing object into a concealment failure, which is the one
           direction a mask must never fail ([[arch_info_bleed_masking]]). */
        if ($this->rules && !$this->rules->hasRuleName('allowReinforcements')) return;

        $playerTeam = $this->getPlayerTeam(); //null for an observer, who owns no slot
        $kept = array();
        $removedAny = false;

        foreach ($this->ships as $ship){

            /* ⚠️ BOTH TESTS, not isReinforcement() alone. getTurnDeployed returns 1 for an OSAT,
               a base or terrain BEFORE it ever looks at the reinforcement branch, so such a row
               carrying the flag is on the board on turn 1 while isReinforcement() still answers
               true - and deleting a visible unit here would also change what
               getMinTurnDeployedSlot tells Manager::updateLateDeployments, which WRITES to the
               database. 999 is the "not on the board" sentinel §3.2 sets. */
            if (!$ship->isReinforcement() || $ship->getTurnDeployed($this) <= $this->turn){
                $kept[] = $ship;
                continue;
            }

            //The owner and their team see the real rows and no aggregate. Same shape as
            //hideEnemyCombatPivots and hideStealthShipMovement, deliberately.
            if ($ship->userid == $this->forPlayer || $ship->team == $playerTeam){
                $kept[] = $ship;
                continue;
            }

            $removedAny = true;

            if (!isset($this->slots[$ship->slot])) continue; //no slot to report it on

            /* THE SAME ARITHMETIC fleetList.js prices every other row with (a flight is costed per
               craft off a six-craft blueprint price), so the enemy's header still sums.
               ⚠️ pointCostSysEnh is NOT added: getTacShips reads tac_ship.enhvalue - which
               submitShip wrote as all THREE buckets added together - straight into pointCostEnh,
               and getEnhancementsForShips then re-derives pointCostSysEnh from its own table.
               Adding it would double-count. pointCostEnh2 is 0 once a game is running and is
               summed only so this cannot rot if that ever changes. */
            $pts = (float)$ship->pointCost;
            if ($ship instanceof FighterFlight) $pts = $pts * ($ship->flightSize / 6);
            $pts += (float)$ship->pointCostEnh + (float)$ship->pointCostEnh2;

            $this->slots[$ship->slot]->reinforcementCount++;
            $this->slots[$ship->slot]->reinforcementPoints += (int)round($pts);

            $this->republishFormingExits($ship);
        }

        if (!$removedAny) return;

        /* ⚠️ RE-INDEX. stripForJson maps over $this->ships with array_map, which PRESERVES KEYS
           when passed a single array - so a gap would make json_encode emit a JSON OBJECT where
           the client requires a real array (gamedata.ships is .filter()ed, .some()d and .length'd
           all over the client). $kept is built by append, so it is already 0-indexed; the array is
           replaced wholesale rather than unset() from, for exactly that reason.
           ⚠️ AND DROP THE ID CACHE. $shipsById is populated for EVERY ship in the game long before
           this runs - getMovesForShips resolves every id during the DB load - so without this,
           markJumpedDockedFlights, hideDeploymentDocks and hideSystemFireOrders could all still
           resolve an id to a unit that is no longer in the payload. */
        $this->ships = $kept;
        $this->shipsById = array();
    }

    /* REINFORCEMENTS_PLAN.md §2.3 - REPUBLISH THIS UNIT'S JUMP POINT EXIT AS A BARE HEX.
     *
     * Called from the sweep above, for a ship that is about to be deleted from this viewer's
     * payload. The BLUE "Jump Point Forming" marker is public for the whole of the turn a jump
     * point forms in - it is the warning an opponent gets in exchange for reinforcements arriving
     * able to act - but the ORDER that carries it lives on a ship this viewer must not see. So the
     * hex and the facing are lifted onto the slot, and the client draws the identical marker from
     * either source (BallisticIconContainer::generateExitHexes).
     *
     * ⭐ THE HEX AND THE FACING, AND NOTHING ELSE. Not the opener, not the manifest, not how many
     * units ride it. Those are exactly the things §3.6 says are never disclosed, and the slot
     * already carries the only aggregate that is (a count and a point total).
     *
     * ⚠️ NEVER IN PHASE 1. A declaration is secret while Initial Orders are open - that is the rule
     * hideSystemFireOrders enforces on the order itself, stripping every current-turn ballistic
     * order from every phase-1 payload INCLUDING ITS AUTHOR'S - and republishing a summary of one
     * here would leak in phase 1 exactly what that sweep spends its time hiding. From phase 2 the
     * declaration is public and so is this.
     *
     * ⚠️ THE TRUE HEX, AS OF 2026-08-29, AND THAT IS THE RULE RATHER THAN A LEAK. The deviation is
     * now rolled at the END OF INITIAL ORDERS (§2.3, user ruling), and JumpEngine::openExitVortex
     * moves the declaration onto the hex the doorway actually formed at - so this sweep, which
     * copies the order's own x/y/firingMode, republishes where the exit really is rather than where
     * the player aimed. That is the point: both sides get to see it and react to it for the whole
     * of the formation turn. Nothing here had to change for it; the timing did.
     */
    private function republishFormingExits($ship)
    {
        if ((int)$this->phase === 1) return;   //secret while Initial Orders are open
        if (!isset($this->slots[$ship->slot])) return;
        if (!is_array($ship->systems)) return;

        foreach ($ship->systems as $system){
            if (!($system instanceof JumpEngine)) continue;

            foreach ($system->fireOrders as $fire){
                if ($fire->damageclass !== 'jumpexit') continue;
                if ((int)$fire->turn !== (int)$this->turn) continue;
                if (!empty($fire->rejected)) continue;
                if ($fire->x === null || $fire->y === null || $fire->x === "null" || $fire->y === "null") continue;

                /* firingMode is the storage for the facing - mode = facing + 1 - the same convention
                   an entrance uses, so the client's arrow maths is shared verbatim.

                   ⭐ STAGE 9 - 'phase' IS THE THIRD FIELD, and it is the only thing this viewer
                   cannot work out for themselves. The opening ship is gone from their payload
                   (hideHyperspaceReinforcements deleted it, engine and all), so they have nothing
                   to ask "is that a legacy drive?" of, and the marker would say "Jump Point
                   Forming" over a hex where no jump point will ever form. It discloses nothing the
                   next turn does not: either terrain appears there or it does not. */
                $this->slots[$ship->slot]->formingExits[] = array(
                    'x'      => (int)$fire->x,
                    'y'      => (int)$fire->y,
                    'facing' => ((((int)$fire->firingMode - 1) % 6) + 6) % 6,
                    'phase'  => $system->isLegacyJump() ? 1 : 0,
                );
            }
        }
    }

    private function deleteHiddenData(){

        //REINFORCEMENTS_PLAN.md §3.6 - FIRST, so every mask below walks the shortened list.
        $this->hideHyperspaceReinforcements();

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

        /* REINFORCEMENTS_PLAN.md STAGE 9 - ON A CONTESTED JUMP GATE, ONLY THE NEAREST TEAM'S SIGNAL
           IS DRAWN (user request 2026-08-29). See JumpEngine::maskLosingGateClaims for the rule.
           ⚠️ BEFORE the sweep below, and that ordering is load-bearing: the distance is measured to
           the unit named in the claim's targetid, and hideSystemFireOrders is what masks targetid to
           -1 for every viewer the claim does not belong to. Run it after and every enemy claim looks
           like a claim naming nothing, which the mask reads as "cannot win" - so a player would see
           only their own marker on every contested gate, always, whoever was nearer. */
        JumpEngine::maskLosingGateClaims($this);

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

                /* ⭐⭐ JUMP GATES (PHASE 2) - THE ONLY FIELD THAT NAMES A GATE'S SIGNALLER, AND THE
                   ONE REAL COST OF THE CONCEALMENT RULING (JUMP_GATES_PLAN.md sections 2.1 and 3.3,
                   trap 4).

                   Signalling a fixed jump gate NEVER reveals a hidden unit - a stealthed, shaded or
                   cloaked ship may signal and keeps its concealment, which is the opposite of the
                   rule for a ship opening its own vortex. On the server that ruling holds for free
                   (JumpEngine::hasVortexDeclaration walks a ship's OWN engines, and a gate claim
                   sits on the GATE's), and every combat-log line names the PLAYER rather than a
                   unit. This is the exception: a gate claim has no player column to live in, so
                   targetid carries the claiming player as their nearest qualifying unit - and fire
                   orders become public from phase 2 onward. Left alone, the enemy could read "the
                   ship at X signalled the gate" straight out of the payload and pick a cloaked hull
                   out of it.

                   ⚠️ EVERY TURN, not just the current one. The signaller is never named, ever -
                   including in a replay of the turn it happened, which is the one place a
                   turn-scoped mask would quietly leak it.

                   ⚠️ NOT $isAlly - that is computed from the GATE, which belongs to whoever bought
                   it and usually to nobody the claimant is allied with. The question here is who
                   owns the TARGETED unit, and the test is the same one the hidetarget branch below
                   uses on the viewer: their own ship, or their team's.

                   The HEX is deliberately left alone: it is the gate's own, it is public, and the
                   marker has to be drawn on it. */
                if ($weapon instanceof JumpEngine && $weapon->isGateJump() && (int)$fire->targetid > 0){
                    $signaller = $this->getShipById((int)$fire->targetid);
                    $ownSignaller = $signaller
                        && ($signaller->userid == $this->forPlayer || $signaller->team == $playerTeam);
                    if (!$ownSignaller) $fire->targetid = -1;
                }

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

//            if ($ship->Enormous) { // Only enormous units block LoS
			if ($ship->Enormous && !($ship instanceof spawnMeteoroid) && !($ship instanceof spawnDustField) && !($ship instanceof spawnHyperspaceWaveform)) { // Only enormous units block LoS, but not these terrain GTS_Change
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
                /* REINFORCEMENTS_PLAN.md §3.6 - a unit still in HYPERSPACE blocks nothing. Its
                   only movement row is the 'start' one every ship is given at its slot's
                   deployment-box centre, so an Enormous reinforcement would otherwise plant a
                   phantom line-of-sight blocker there for EVERY player, itself included - and,
                   since blockedHexes is computed once in onConstructed and is not per-viewer,
                   it would survive hideHyperspaceReinforcements and tell an enemy that something
                   big is coming and roughly where from.
                   ⚠️ isReinforcement() specifically, NOT a general getTurnDeployed test: a
                   late-SLOT Enormous unit has blocked its deployment-box hex from turn 1 since
                   long before this feature, and changing that is a line-of-sight rule change for
                   existing games rather than a concealment fix. */
                if($ship->isReinforcement()) continue;

//                if ($ship->Enormous) { // Only enormous units block LoS
if ($ship->Enormous && !($ship instanceof spawnMeteoroid) && !($ship instanceof spawnDustField) && !($ship instanceof spawnHyperspaceWaveform)) {
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


    /*The turn this SLOT gets its Deployment phase - the placement-turn twin of
      getMinTurnDeployedSlot above. A late slot picks its entry hexes on depavailable-1 so the
      resulting Jump Point markers give opponents a turn of warning; see BaseShip::getTurnPlaced.
      Bases/OSATs/Terrain in the slot still force a turn-1 deployment, exactly as above - they
      have to be placed manually before anything else happens.*/
    public function getMinTurnPlacedSlot($slotid, $depavailable) {
        $minTurnDeploy = $this->getMinTurnDeployedSlot($slotid, $depavailable);
        return ($minTurnDeploy > 1) ? ($minTurnDeploy - 1) : $minTurnDeploy;
    }


    /*Has ANY ship in this slot already committed a deploy entry? (Destroyed ones count - a wreck
      still proves the slot was placed.) Used only by the legacy
      safety valve in FireGamePhase: a game that rolled past a late slot's placement turn under
      the OLD (arrival-turn) rule would otherwise never be granted a Deployment phase at all and
      its ships would be stranded off-board forever.
      Deliberately "any", not "all": a slot can legitimately hold units with no deploy move of
      their own (a flight queued for a hangar deploy-start dock writes no movement), so requiring
      every ship to be placed would keep re-granting the phase for ever.*/
    public function slotHasPlacedShips($slotid) {
        foreach ($this->ships as $ship) {
            if ($ship->slot != $slotid) continue;
            if ($ship->userid == -5) continue; //generated terrain is never player-placed
            foreach ($ship->movement as $move) {
                if ($move->type == "deploy") return true;
            }
        }
        return false;
    }

    /* REINFORCEMENTS_PLAN.md STAGE 7 - does this player have anything coming out of hyperspace on
       $turn? The clause that GRANTS the Deployment phase in FireGamePhase::advance's slot loop
       (plan §4 Stage 7), and the only thing that does: a reinforcement's arrival turn is decided in
       play by the exit it rides, so no slot value - depavailable, getMinTurnPlacedSlot or
       otherwise - can predict it.

       ⚠️ ASK IT OF THE GAMEDATA THE SWEEP STAMPED. JumpEngine::stampExitManifests writes
       arrivalTurn to the DB and to its own in-memory ships; the outer $gameData FireGamePhase was
       handed was loaded BEFORE any of that happened, so asking it would answer "nobody is arriving"
       every time and the wave would never get a phase to walk through the door in.

       PER PLAYER and not per slot, matching checkDeploymentPhaseForPlayer beside it: the loop grants
       or skips a slot at a time, but a player with two slots is only marked "skipped" when EVERY one
       of their slots was, so a spare Deployment phase on a slot with nothing to place costs nothing -
       validateDeployment asks for a move only from units whose placement turn is this turn.

       isDestroyed() is asked because pre-battle damage can in principle kill a unit before it ever
       leaves hyperspace, and a phase granted for a wreck is a phase the player cannot finish. */
    public function hasReinforcementsArriving($playerid, $turn) {
        foreach ($this->ships as $ship) {
            if ($ship->userid != $playerid) continue;
            if (!$ship->reinforcement) continue;
            if ($ship->arrivalTurn === null) continue;
            if ((int)$ship->arrivalTurn !== (int)$turn) continue;
            if ($ship->isDestroyed()) continue;

            return true;
        }

        return false;
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



