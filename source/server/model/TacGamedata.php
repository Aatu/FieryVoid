<?php

class TacGamedata{

    public static $currentTurn;
    public static $currentPhase;
    public static $currentGameID;
    public static $currentActiveship;
    
    public $id, $turn, $phase, $activeship, $name, $status, $points, $background, $creator, $gamespace;
    public $ships = array();
    public $slots = array();
    public $waiting = false;
    public $changed = false;
    public $getDistanceHex = false;
    public $forPlayer;
    public $ballistics = array();
    public $waitingForThisPlayer = false;
    
    
    
    function __construct($id, $turn, $phase, $activeship, $forPlayer, $name, $status, $points, $background, $creator, $gamespace = null){
        $this->setId($id);
        $this->setTurn($turn);
        $this->setPhase($phase);
        $this->setActiveship($activeship);
        $this->setForPlayer($forPlayer);
        $this->activeship = (int)$activeship;
        $this->name = $name;
        $this->status = $status;
        $this->points = (int)$points;
        $this->background = $background;
        $this->creator = $creator;
        $this->gamespace = $gamespace;
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
        self::$currentActiveship = $activeship;
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
   
    public function onConstructed(){
        $this->waitingForThisPlayer = $this->getIsWaitingForThisPlayer();
        $this->doSortShips();

        foreach ($this->ships as $ship){
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
            
                 
            $pos = $ship->getCoPos();
            foreach ($this->ships as $ship2){
                if ($ship->team == $ship2->team)
                    continue;
                    
                if ($ship2->isDestroyed()){
                    continue;
                }
                
                if ($ship2->isPowerless()){
                    continue;
                }
                    
                $pos2 = $ship2->getCoPos();
                $dis = mathlib::getDistanceHex($pos, $pos2);
                
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

    public function getNewDamagesForAA(){
        $list = array();
        
        foreach ($this->ships as $ship){
            if (isset($ship->adaptiveArmour)){
                $entry = array();

                foreach($ship->systems as $system){
                    foreach($system->damage as $damage){
                        if ($damage->updated == true){
                            $entry[] = $damage;
                        }
                    }
                }  

                $list[] = $entry;                     
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
    
    public function getActiveship(){
        foreach ($this->ships as $ship){
            if ($ship->id == $this->activeship){
                return $ship;
            }
        }
        
        return null;
    }
    
    private $shipsById = array();
    
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
        $ships = array();
        foreach ($this->ships as $ship){
            if ($ship->unavailable)
                continue;
            
            $shipPos = $ship->getCoPos();
            $curDis = mathlib::getDistance($pos, $shipPos);
        
            if ($curDis <= $dis){
                $ships[$ship->id] = $ship;
            }
        }
        
        return $ships;
             
             
    }
    
    public function prepareForPlayer(){
        $this->setWaiting();
        $this->calculateTurndelays();
        $this->deleteHiddenData();
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
                        $system->power = array();
                    }
                }
            }
        }
        
       
        foreach ($this->ships as $ship){
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
    
        if ($this->phase == 1 || $this->phase == 3 || $this->phase == 4){
                            
            if ($player->lastturn == $this->turn && $player->lastphase == $this->phase){
                $this->waiting = true;
            }
        
        }else if ($this->phase == 2){
            
            $ship = $this->getActiveship();
                            
            if ($ship != null && $ship->userid == $this->forPlayer){
                $this->waiting = false;
            }else{
                $this->waiting = true;
            }
        }else{
            $this->waiting = false;
        }
        
        
    }
    
    private function getIsWaitingForThisPlayer(){
        $slots = $this->getSlotsByPlayerId($this->forPlayer);

        
        $activeship = $this->getShipById($this->activeship);
        
        if ( $activeship != null)
        {
            if ($activeship->userid == $this->forPlayer)
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
}
