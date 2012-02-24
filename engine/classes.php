<?php
class Player{

	public $id;
	public $username;
	public $acceslevel;
	
	function __construct($id, $username, $acceslevel){
		$this->id = $id;
		$this->username = $username;
		$this->acceslevel = $acceslevel;
	}

}

class Game{
	
	public $id;
	public $turn;
	public $name;
	public $description;
	public $maps = array();

	function __construct($id, $turn, $name, $description, $maps = array()){
		$this->id = $id;
		$this->username = $username;
		$this->name = $name;
		$this->description = $description;
		$this->maps = $maps;
	}
	
}

class Map{

	public $id, $name, $hyperspacespeed, $normalspacespeed, $image;
	
	function __construct($id, $name, $hyperspacespeed, $normalspacespeed, $image){
		$this->id = $id;
		$this->name = $name;
		$this->hyperspacespeed = $hyperspacespeed;
		$this->normalspacespeed = $normalspacespeed;
		$this->image = $image;
	}
}

class Gamedata{

	public $game;
	public $map;
	public $taskforces = array();

	function __construct($game = null, $map,  $turn = -1, $map, $taskforces=array(), $json="" ) {
		$this->game = $game;
		$this->map = $map;
		$this->taskforces = $taskforces;

		if ($json != "")
			$this->fromJSON($json);
    }
	
	public function fromJSON($json){
		$json = str_replace("\\\"", "\"", $json);
		$json = json_decode($json, true);
	
		$this->turn = $json["turn"];
		$this->hyperspacespeed = $json["hyperspacespeed"];
		$this->normalspacespeed = $json["normalspacespeed"];
		
		$this->taskforces = array();
		
		if (is_array($json["taskforces"])){
			foreach ($json["taskforces"] as $id => $tf){
			
				$orders = array();
				$ships = array();
		
				if (is_array($tf["orders"])){
					foreach ($tf["orders"] as $ordernumber => $order){
						$orders[$ordernumber] = new Order($order["id"], $order["ordertype"], $order["number"], $order["done"], $order["x"], $order["y"], $order["time"], $order["turnsdone"], $order["cancellable"],
						$order["inhyperspace"], $order["alertlevel"], $order["fightersassigned"], $order["neworder"]);
					}
				}
				if (is_array($tf["ships"])){
					foreach ($tf["ships"] as $shipid => $ship){
						$ships[$shipid] = new Ship($shipid, $ship["type"], $ship["name"], $ship["shipclass"], $ship["scanner"], $ship["jumpengine"], $ship["scannerdamage"], $ship["scannerbonus"], 
						$ship["enginebonus"], $ship["enginedamage"], $ship["crewfatigue"], $ship["damagelevel"], $ship["fighters"], $ship["playerid"], $ship["lastjumpturn"], $ship["speed"]);
					}
				}	
			
				$this->taskforces[$id] =  new Taskforce($id, $tf["name"], $tf["x"], $tf["y"], $tf["hyperspace"], $tf["playerid"], $tf["mapid"], $ships, $orders);
			}
		}
	
	}
	
	public function toJSON(){
		return json_decode($this);
	
	}


}
class Taskforce{
	
	public $id; //int
	public $name; //Patrol I
	public $x; //int
	public $y; //int
	public $hyperspace; //boolean
	public $playerid; //int
	public $mapid; //int
		
	public $ships = array(); //object
	public $orders = array(); //object
	
	public $isjumpcapable = false;
	public $shipcount = 0;
	public $bestscanner = 0;
	public $totalfighters = 0;
	public $jumpspeed = 0;
	public $jumpenginesneeded = 0;
	public $speed = 0;
	public $effectivedetectionradius = 0;
	
	function __construct($id, $name, $x, $y, $hyperspace, $playerid, $mapid, $ships, $orders) {
		$this->id = $id;
        $this->name = $name;
        $this->x = $x;
		$this->y = $y;
		$this->hyperspace = $hyperspace;
		$this->playerid = $playerid;
		$this->mapid = $mapid;
		$this->setShips($ships);
		$this->orders = $orders;
		
		
    }
	
	public function setShips($ships){
		$this->ships = $ships;
		
		foreach ($this->ships as $ship){
			if ($ship->jumpengine > 0){
				$this->isjumpcapable = true;
				
			}
			if ($ship->scanner + $ship->scannerbonus - $ship->scannerdamage  > $this->bestscanner)
				$this->bestscanner = $ship->scanner + $ship->scannerbonus - $ship->scannerdamage;
				
			$this->totalfighters += $ship->fighters;
		}
		
		$this->shipcount = sizeof($this->ships);
		
		$this->calculateSpeed();
	}
	
	private function calculateSpeed(){
		$enormous = 0;
		$capital = 0;
		$heavy = 0;
		$medium = 0;
		$jumpengines = 0;
		$totalloadingtimes = 0;
		$averageloading = 0;
		$jumpenginesneeded = 0;
		$slowest = 100;
		
		foreach ($this->ships as $ship){
			
			if ($ship->speed < $slowest){
				$slowest = $ship->speed;
			}
			if ($ship->shipclass == 5)
				$enormous++;
				
			if ($ship->shipclass == 4)
				$capital++;
				
			if ($ship->shipclass == 3)
				$heavy++;
				
			if ($ship->shipclass == 2)
				$medium++;
				
			if ($ship->jumpengine > 0){
				$jumpengines++;
				$totalloadingtimes += $ship->jumpengine;
			}
				
		
		}
		$this->speed = $slowest;
		
		if ($jumpengines == 0){
			return;
		}
		$averageloading = $totalloadingtimes / $jumpengines;
		$jumpenginesneeded += $enormous + $capital*0.5 + $heavy *0.5 + $medium * 0.25;
		
		$this->$jumpenginesneeded = $jumpenginesneeded;
		$this->jumpspeed = ceil((ceil($jumpenginesneeded) / $jumpengines) * $averageloading);
		
	}

}

class Ship{ //Taskforce has 1 - n Ships

	public $id;  //int
	public $type; //Hyperion
	public $name; //EAS Budabest
	public $shipclass; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
	public $scanner; //int
	public $jumpengine; //int
	public $scannerdamage; //int
	public $scannerbonus; //int
	public $enginedamage; //int
	public $enginebonus; //int
	public $crewfatigue; //int
	public $damagelevel; //0: Undamaged, 1:Damaged, 2:Heavily damaged(1 section lost), 3:Crippled(2 sections lost), 4:Disabled(unmanoverable or reactor, C&C, engine destroyed) 5:Destroyed
	public $fighters; //int
	public $playerid; //int
	public $lastjumpturn;
	public $speed;
	
	
	function __construct($id, $type, $name, $shipclass, $scanner, $jumpengine, $sd, $sb, $ed, $eb, $fatigue, $damage, $fighters, $playerid, $lastjumpturn, $speed) {
		$this->id = $id;
		$this->type = $type;
		$this->name = $name;
        $this->shipclass = $shipclass;
        $this->scanner = $scanner;
		$this->jumpengine = $jumpengine;
		$this->scannerdamage = $sd;
		$this->scannerbonus = $sb;
		$this->enginedamage = $ed;
		$this->enginebonus = $eb;
		$this->crewfatigue = $fatigue;
		$this->damagelevel = $damage;
		$this->fighters = $fighters;
		$this->playerid = $playerid;
		$this->lastjumpturn = $lastjumpturn;
		$this->speed = $speed;
		
		
    }

}

class Order{ //Taskforce has 0 - n orders 0 orders will propably be considered doing something default

	public $id; //int
	public $ordertype; //JUMP, PATROL, jne...
	public $number; //place in the list
	public $done; //already executed
	public $x; //int
	public $y; //int
	public $time; //int (Patrols 10 turns) 0 for JUMP -> will be calculated 
	public $turnsdone; //int
	public $cancellable; //boolean
	public $inhyperspace;
	public $alertlevel;
	public $fightersassigned;
	public $neworder = false;
		
	function __construct($id, $type, $number, $done, $x, $y, $time, $turnsdone, $cancellable, $inhyperspace, $alertlevel, $fightersassigned, $neworder) {
		$this->id = $id;
		$this->ordertype = $type;
		$this->number = $number;
        $this->done = $done;
		$this->x = $x;
		$this->y = $y;
		$this->time = $time;
		$this->turnsdone = $turnsdone;
		$this->cancellable = $cancellable;
		$this->inhyperspace = $inhyperspace;
		$this->alertlevel = $alertlevel;
		$this->fightersassigned = $fightersassigned;
		$this->neworder = $neworder;
	}
	
	
} 

?>