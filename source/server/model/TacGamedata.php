<?php

class TacGamedata{

    public static $currentTurn;
    public static $currentPhase;
    public static $currentGameID;
    public static $currentActiveship;
    
    public $id, $turn, $phase, $activeship, $name, $status, $points, $background, $creator;
    public $ships = array();
    public $players = array();
    public $waiting = false;
    public $changed = false;
    public $getDistanceHex = false;
    public $forPlayer;
    public $ballistics = array();
    
    
    
    function __construct($id, $turn, $phase, $activeship, $forPlayer, $name, $status, $points, $background, $creator){
        $this->id = (int)$id;
        $this->turn = (int)$turn;
        static::$currentPhase = (int)$phase;
        static::$currentTurn = (int)$turn;
        static::$currentGameID = (int)$id;
        $this->phase = (int)$phase;
        $this->activeship = (int)$activeship;
        $this->setForPlayer($forPlayer);
        $this->name = $name;
        $this->status = $status;
        $this->points = (int)$points;
        $this->background = $background;
        $this->creator = $creator;
   }
   
    public function setPhase($phase)
    {
        self::$currentPhase = $phase;
        $this->phase = $phase;
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
   
    public function doSortShips()
    {
        usort ( $this->ships , "self::sortShips" );
    }
   
    public function onConstructed(){
        
        
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
                        array("x"=>$movement->x, "y"=>$movement->y),
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
            
            $ship->onConstructed($this->turn, $this->phase);
        
        }
    
    
    }
   
    public function isFinished(){

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
    
        foreach ($this->players as $player){
            if ($player->id == $userid)
                continue;
                
            if ($player->lastturn != $this->turn || $player->lastphase != $this->phase)
                return false;
        
        }
        
        return true;
    
    }
    
    public function hasAlreadySubmitted($userid){
        $player = $this->getPlayerById($userid);
        
        if ($player == null)
            return true;
            
        if ($player->lastturn < $this->turn || $player->lastphase < $this->phase)
            return false;
            
        
        return true;
    
    }
    
    public function getPlayerById($id){
        foreach ($this->players as $player){
            if ($player->id == $id){
                return $player;
            }
        }
        
        return null;
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
            $shipPos = $ship->getCoPos();
            $curDis = mathlib::getDistance($pos, $shipPos);
        
            if ($curDis <= $dis){
                $ships[] = $ship;
            }
        }
        
        return $ships;
             
             
    }
    
    public function prepareForPlayer($turn, $phase, $activeship){
        $this->setWaiting();
        $this->checkChanged($turn, $phase, $activeship);
        $this->unanimateMovements($activeship, $turn); 
        $this->calculateTurndelays();
        $this->deleteHiddenData();
        $this->setPreTurnTasks();
        
        if ($this->status == "LOBBY"){
            $this->ships = array();
        }
        
        
    }
    
    private function setPreTurnTasks(){
        
        foreach ($this->ships as $ship){
            foreach ($ship->systems as $system){
                $system->beforeTurn($ship, $this->turn, $this->phase);
            }
        
        }
    
    }
    
    private function deleteHiddenData(){
    
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
    
    private function unanimateAllMovements()
    {
        if ($this->phase == 4 || ($this->phase == 3 && $this->waiting == true)){ //|| $this->waiting == true){
            return;
        }
        
        foreach ($this->ships as $ship)
        {
            if ($ship->id == $this->activeship){
                break;
            }

            $ship->unanimateMovements($this->turn);

        }
    }
    
    
    private function unanimateMovements($activeship, $turn){
        $found = false;
        
        if ($this->phase == 4 || ($this->phase == 3 && $this->waiting == true)){ //|| $this->waiting == true){
            return;
        }
        
        //if ($turn == -1)
        //    return;
        
        $turn = $this->turn;
            
        if ($activeship == -1)
        {
            $this->unanimateAllMovements();
            return;
        }
        
        $turnchanged = ($turn != $this->turn);
        
        for ( $i = $turn; $i <= $this->turn; $i++) {    
            foreach ($this->ships as $ship){
                if ($ship->id == $activeship && $i == $turn){
                    $found = true;
                }
                
                if ($ship->id == $this->activeship && $i == $this->turn){
                    break;
                }
                    
                    
                    
                if ($found){
                    if ($ship->userid != $this->forPlayer){
                        $ship->unanimateMovements($i);
                    }
                }
            }
        }
    }
     
     
    
    private function setWaiting(){
    
        $player = $this->getPlayerById($this->forPlayer);
        if ($player == null){
            $this->waiting = false;
            return;
        }
    
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
    
    private function checkChanged($turn, $phase, $activeship){
    
        if ($this->phase != $phase || $this->turn != $turn || $this->activeship != $activeship){
            $this->changed = true;
        }else{
            $this->changed = false;
        }
    
    }
    

}