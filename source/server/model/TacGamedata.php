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
    
    
    
    function __construct($id, $turn, $phase, $activeship, $forPlayer, $name, $status, $points, $background, $creator, $description='', $gamespace = null, $rules = null){
        $this->setId($id);
        $this->setTurn($turn);
        $this->setPhase($phase);
        $this->setActiveship($activeship);
        $this->setForPlayer($forPlayer);
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
        usort ( $this->ships , "self::sortShips" );
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

        return $strippedGamedata;
    }

    public function onConstructed(){
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
            if ($this->slots[$ship->slot]->depavailable > $this->turn)
                $ship->unavailable = true;
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
                
                if ($dis<70 || $this->turn < 5){
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
        
        return 1;
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
            if ($ship->isDestroyed())
                continue;
                
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
            if ($slot->lastturn < $this->turn || $slot->lastphase < $this->phase)
            return false;
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
    
    private function setForPlayer($player){
        $this->forPlayer = $player;
        
    }
    
    public function getActiveships(){

        if (is_array($this->activeship)){
            $ships = [];


            foreach ($this->ships as $ship){
                if (in_array($ship->id, $this->activeship)){
                    array_push($ships, $ship);
                }
            }

            if (count($ships) === 0) {
                return [];
            }

            return $ships;
        }

        foreach ($this->ships as $ship){
            if ($ship->id == $this->activeship){
                return [$ship];
            }
        }
        
        return [];
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
                    if ($move->type == "deploy")
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
                if ($fire->turn == $this->turn && !$weapon->ballistic && $this->phase == 3){
                    unset($system->fireOrders[$i]);
                }
                if ($fire->turn == $this->turn && $weapon->ballistic && $this->phase == 1){
                    unset($system->fireOrders[$i]);
                }

                if ($fire->turn == $this->turn && $weapon->hidetarget && $this->phase < 4 && $ship->userid != $this->forPlayer){
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


}



