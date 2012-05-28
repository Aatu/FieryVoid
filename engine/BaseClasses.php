<?php


class TacticalPlayer{
    public $id, $slot, $team, $lastturn, $lastphase, $name;
    
    function __construct($id, $slot, $team, $lastturn, $lastphase, $name){
        $this->id = $id;
        $this->team = $team;
        $this->lastturn = $lastturn;
        $this->lastphase = $lastphase;
        $this->name = $name;
        $this->slot = $slot;
    }
}

class MovementOrder{

    public $id, $type, $x, $y, $xOffset, $yOffset, $facing, $heading, $speed, $value;
    public $animating = false;
    public $animated = true;
    public $animationtics = 0;
    public $preturn;
    public $requiredThrust = array(0, 0, 0, 0, 0); //0:any, 1:front, 2:rear, 3:left, 4:right;
    public $assignedThrust = array();
    public $commit = true;
    public $turn;
    public $forced = false;
    
    
    function __construct($id, $type, $x, $y, $xOffset, $yOffset, $speed, $heading, $facing, $pre, $turn, $value){
        $this->id = (int)$id;
        $this->x = (int)$x;
        $this->y = (int)$y;
        $this->type = $type;
        $this->facing = (int)$facing;
        $this->heading = (int)$heading;
        $this->speed = (int)$speed;
        $this->preturn = $pre;
        $this->turn = (int)$turn;
        $this->xOffset = $xOffset;
        $this->yOffset = $yOffset;
        $this->value = $value;
        

    }
    
    public function getReqThrustJSON(){
        return json_encode($this->requiredThrust);
        
    }
    
    public function getAssThrustJSON(){
        return json_encode($this->assignedThrust);
        
    }
    
    public function setReqThrustJSON($json){
        $this->requiredThrust = json_decode($json, true);
    }
    
    public function setAssThrustJSON($json){
        $this->assignedThrust = json_decode($json, true);
    }
    
    public function getCoPos(){
        return mathlib::hexCoToPixel($this->x, $this->y);
    }
    
    public function getFacingAngle(){
    
        $d = $this->facing;
        if ($d == 0){
            return 0;
        }
        if ($d == 1){
            return 60;
        }
        if ($d == 2){
            return 120;
        }
        if ($d == 3){
            return 180;
        }
        if ($d == 4){
            return 240;
        }
        if ($d == 5){
            return 300;
        }
        
        return 0;
    }

}


class DamageEntry{

    public $id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed;
    public $pubnotes = "";
    public $updated = false;
    
    function __construct($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $pubnotes){
        $this->id = $id;
        $this->shipid = $shipid;
        $this->gameid = $gameid;
        $this->turn = $turn;
        $this->systemid = $systemid;
        $this->damage = $damage;
        $this->armour = $armour;
        $this->shields = $shields;
        $this->fireorderid = $fireorderid;
        $this->destroyed = $destroyed;
        $this->pubnotes = $pubnotes;
    }

}

class EWentry{
    
    public $id, $shipid, $turn, $type, $amount, $targetid;
    
    function __construct($id, $shipid, $turn, $type, $amount, $targetid){
         $this->id = $id;
         $this->shipid = $shipid;
         $this->turn = $turn;
         $this->type = $type;
         $this->amount = $amount;
         $this->targetid = $targetid;
    }
}

class FireOrder{
    
    public $id, $type, $shooterid, $targetid, $calledid, $weaponid, $turn, $firingmode, $needed, $rolled, $shots, $shotshit, $intercepted, $x, $y;
    public $notes = "";
    public $pubnotes = "";
    public $updated = false;
    public $addToDB = false;
    
    function __construct($id,  $type, $shooterid, $targetid, $weaponid, $calledid, $turn, $firingmode, $needed = 0, $rolled = 0, $shots = 1, $shotshit = 0, $intercepted = 0, $x, $y){
         $this->id = $id;
         $this->type = $type;
         $this->shooterid = $shooterid;
         $this->targetid = $targetid;
         $this->weaponid = $weaponid;
         $this->calledid = $calledid;
         $this->turn = $turn;
         $this->firingMode = $firingmode;
         $this->needed = $needed;
         $this->rolled = $rolled;
         $this->shots = $shots;
         $this->shotshit = $shotshit;
         $this->intercepted = $intercepted;
         $this->x = $x;
         $this->y = $y;
    }

}



class PowerManagementEntry{
    
    public $id, $shipid, $systemid, $type, $turn, $amount;
    public $updated = false;
    
    //types: 1:offline 2:boost, 3:overload
    
    function __construct($id, $shipid, $systemid, $type, $turn, $amount){
        $this->id = (int)$id;
        $this->shipid = (int)$shipid;
        $this->systemid = (int)$systemid;
        $this->type = (int)$type;
        $this->turn = (int)$turn;
        $this->amount = (int)$amount;

    }

}

class ShipSystem{

    public $location; //0:primary, 1:front, 2:rear, 3:left, 4:right;
    public $id, $armour, $maxhealth, $powerReq, $output, $name, $displayName;
    public $damage = array();
    public $outputMod = 0;
    public $boostable = false;
    public $power = array();
    public $data = array();
    public $critData = array();
    public $imagePath, $iconPath;
    
    public $possibleCriticals = array();
    
        
    public $criticals = array();
    
    function __construct($armour, $maxhealth, $powerReq, $output){
        $this->armour = $armour;
        $this->maxhealth = (int)$maxhealth;
        $this->powerReq = (int)$powerReq;
        $this->output = (int)$output;


    }
    
    public function onConstructed($ship, $turn, $phase){
            
     
    }
    
    public function beforeTurn($ship, $turn, $phase){
            
        $this->setSystemDataWindow($turn);
    }
    
    public function setDamage($damages){
        $this->damage = $damages;
    }
    
    public function setPower($power){
        $this->power = $power;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setCriticals($criticals, $turn){
        $crits = array();
        foreach( $criticals as $crit){
            if (!$crit->oneturn || ($crit->oneturn && $crit->turn >= $turn-1))
                $crits[] = $crit;
        }
        
        $this->criticals = $crits;
        $this->effectCriticals();
    }
    
    public function getArmour($gamedata, $shooter){
		return $this->armour;
	}
	
	public function getArmourPos($gamedata, $pos){
		return $this->armour;
	}
    
    public function setSystemDataWindow($turn){
        $critDesc = array();
        $counts = array();
        
        foreach ($this->criticals as $crit){
            if (isset($counts[$crit->phpclass])){
                $counts[$crit->phpclass]++;
            }else{
                $counts[$crit->phpclass] = 1;
            }
            
            
            $forturn = "";
            if ($crit->oneturn && $crit->turn == $turn)
                $forturn = "next turn.";
            
            if ($crit->oneturn && $crit->turn+1 == $turn)
                $forturn = "this turn.";
                
            $this->critData[$crit->phpclass] = $crit->description .$forturn;
        }
        /*
        foreach ($this->criticals as $crit){
            if (isset($critDesc[$crit->phpclass]))
                continue;

            if (!isset($this->criticalDescriptions[$crit->phpclass]))   
                continue;

            $c = $counts[$crit->phpclass];
            
            if ($c > 1){
                $desc = $c . " x " + $this->criticalDescriptions[$crit->phpclass];
            }else{
                $desc = $this->criticalDescriptions[$crit->phpclass];
            }
            
            $critDesc[] = $desc;
                
        }
        
        foreach ($critDesc as $desc){
            $this->critData[] = $desc;
        }
        */
        
        
    }
    
    public function testCritical($ship, $turn, $crits, $add = 0){
        
        $roll = Dice::d(20)+$this->getTotalDamage() + $add;
        $criticalTypes = -1;

        foreach ($this->possibleCriticals as $i=>$value){
        
            //print("i: $i value: $value");
            if ($roll >= $i){
                $criticalTypes = $value;
            }
        }
        
        if ($criticalTypes != -1){
            
            if (is_array($criticalTypes)){
                foreach ($criticalTypes as $phpclass){
                    $crit = new $phpclass(-1, $ship->id, $this->id, $phpclass, $turn);
                    $crit->updated = true;
                    $this->criticals[] =  $crit;
                    $crits[] = $crit;
                }
            }else{
                        
                $crit = new $criticalTypes(-1, $ship->id, $this->id, $criticalTypes, $turn);
                $crit->updated = true;
                $this->criticals[] =  $crit;
                $crits[] = $crit;
            }
            
            
            
        }
        
        return $crits;
         
    }
    
    
    public function hasCritical($type, $turn = false){
        $count = 0;
        foreach ($this->criticals as $critical){
            if ($critical->phpclass == $type && $critical->inEffect){
				
				if ($turn === false){
					$count++;
				}else if ((($critical->oneturn && $critical->turn+1 == $turn) || !$critical->oneturn) && $critical->turn<= $turn){
                    $count++;
				}
			}
                
        }
    
        return $count;
    }
    
    
    public function effectCriticals(){
           
        foreach ($this->criticals as $crit){
            $this->outputMod += $crit->outputMod;
        }
    
    
    }
    
    public function getTotalDamage($turn = false){
        $totalDamage = 0;
        
        foreach ($this->damage as $damage){
            $d = ($damage->damage - $damage->armour);
            if ( $d < 0 && ($damage->turn <=$turn || $turn === false))
                $d = 0;
                
            $totalDamage += $d;
        }
        
        return $totalDamage;
    
    }
    
    public function isDestroyed($turn = false){

        foreach ($this->damage as $damage){
            if (($turn === false || $damage->turn <= $turn) && $damage->destroyed)
                return true;
        }
  
        return false;
        
    }
    
    public function isDamagedOnTurn($turn){
        
        foreach ($this->damage as $damage){
            if ($damage->turn == $turn || $damage->turn == -1){
                if ($damage->damage > $damage->armour)
                    return true;
            }
        }
        
        return false;
        
    
    }
    
    public function getRemainingHealth(){
        $damage = $this->getTotalDamage();
        
        $rem = $this->maxhealth - $damage;
        if ($rem < 0 )
            $rem = 0;
            
        return $rem;
    }
      
    public function isOfflineOnTurn($turn){
    
        foreach ($this->power as $power){
            if ($power->type == 1 && $power->turn == $turn){
                return true;
            }
        }
        
        return false;
    
    }
    
    public function isOverloadingOnTurn($turn){
        
        foreach ($this->power as $power){
            if ($power->type == 3 && $power->turn == $turn){
                return true;
            }
        }
        
        return false;
    
    }

}

class Ballistic{
    public $fireOrderId, $position, $id, $facing, $targetpos, $targetid, $shooterid, $weaponid, $shots;
        
    function __construct($id, $fireid, $position, $facing, $targetpos, $targetid, $shooterid, $weaponid, $shots){
        $this->id = (int)$id;
        $this->fireOrderId = (int)$fireid;
        $this->facing = (int)$facing;
        $this->targetid = (int)$targetid;
        $this->shooterid = (int)$shooterid;
        $this->weaponid = (int)$weaponid;
        $this->position = $position;
        $this->targetposition = $targetpos;
        $this->shots = $shots;
        
   }

}

class TacGamedata{

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
        $this->phase = (int)$phase;
        $this->activeship = (int)$activeship;
        $this->setForPlayer($forPlayer);
        $this->name = $name;
        $this->status = $status;
        $this->points = (int)$points;
        $this->background = $background;
        $this->creator = $creator;
   }
   
    public function onConstructed(){
        $i = 0;
        foreach ($this->ships as $ship){
        
            foreach($ship->fireOrders as $fire){
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
            
            foreach ($ship->systems as $system){
                $system->onConstructed($ship, $this->turn, $this->phase);
            }
        
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
                
                if ($dis<70){
                    //print($ship->name . " is on distance $dis from " . $ship2->name);
                    return false;
                }
                    
            }
            
            
        }
        
        
        return true;
   
    }   
    
    public function setShips($ships){
    
        if (isset($ships)){
            usort ( $ships , "self::sortShips" );
            $this->ships = $ships;
        }
    }
    
    public static function sortShips($a, $b){
        
        if ($a->iniative == $b->iniative){
            if ($a->iniativebonus > $b->iniativebonus){
                return -1;
            }else{
                return 1;
            }
        }else if ($a->iniative < $b->iniative){
            return -1;
        }
        
        return 1;
    }
    
    
    public function getNewFireOrders(){
        $list = array();
        
        foreach ($this->ships as $ship){
            foreach($ship->fireOrders as $fire){
                if ($fire->addToDB == true)
                    $list[] = $fire;
            }
        }
        
        return $list;
    
    }
    
    public function getUpdatedFireOrders(){
        $list = array();
        
        foreach ($this->ships as $ship){
            foreach($ship->fireOrders as $fire){
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
    
    public function getShipById($id){
        foreach ($this->ships as $ship){
            if ($ship->id == $id){
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
        
            for ($i = sizeof($ship->fireOrders)-1; $i>=0; $i--){
                $fire = $ship->fireOrders[$i]; 
                $weapon = $ship->getSystemById($fire->weaponid);
                if ($fire->turn == $this->turn && !$weapon->ballistic && $this->phase == 3){
                    unset($ship->fireOrders[$i]);
                }
                if ($fire->turn == $this->turn && $weapon->ballistic && $this->phase == 1){
                    unset($ship->fireOrders[$i]);
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
    
    private function calculateTurndelays(){
    
        foreach ($this->ships as $ship){
            $ship->currentturndelay = Movement::getTurnDelay($ship);
        }
    }
    
    private function unanimateMovements($activeship, $turn){
        $found = false;
        
        if ($this->phase == 4){ //|| $this->waiting == true){
            return;
        }
        
        if ($turn == -1)
            return;
        
        $turn = $this->turn;
            
        if ($activeship == -1)
            $found = true;
        
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


?>
