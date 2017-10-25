<?php

    class AoE extends Weapon{ //directly tailored for EMine, really - not a generic base class
	    public $damageType = "Flash"; 
	    public $weaponClass = "Ballistic";
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    
	public function calculateHitBase($gamedata, $fireOrder){
		$fireOrder->needed = round(100-(100*0.25*0.4)); //chance of not hitting target hex: 25%; chance of dissipating: 40$ of that
		$fireOrder->updated = true;
	}
	    
	    /*October 2017 - Marcin Sawicki - no longer needed
	public function calculateHit($gamedata, $fireOrder){
		$fireOrder->needed = round(100-(100*0.25*0.4)); //chance of not hitting target hex: 25%; chance of dissipating: 40$ of that
		$fireOrder->updated = true;
	}*/
	    
	public function fire($gamedata, $fireOrder){ //sadly here it really has to be completely redefined... or at least I see no option to avoid this
		$this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
           	$shooter = $gamedata->getShipById($fireOrder->shooterid);

		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
		$posLaunch = mathlib::hexCoToPixel($movement->x, $movement->y);//at moment of launch!!!	
		
		//sometimes player does manage to target ship after all..
		if($fireOrder->targetid != -1){ 
			$targetship = $gamedata->getShipById($fireOrder->targetid); 
			//insert correct target coordinates: last turns' target position
			$movement = $targetship->getLastTurnMovement($fireOrder->turn);
        		$fireOrder->x = $movement->x; 
			$fireOrder->y = $movement->y;
			$fireOrder->targetid = -1; //correct the error
		}
		
            	$target = array("x"=>$fireOrder->x, "y"=>$fireOrder->y);
            
            	$this->calculateHit($gamedata, $fireOrder);
		
		$rolled = Dice::d(100);
		$fireOrder->rolled = $rolled;
            	if ($rolled > $fireOrder->needed){ //miss!
			$fireOrder->pubnotes .= "Charge dissipates. ";  
		}else{//hit!
			$fireOrder->shotshit++;
			if($rolled>75){ //deviation!
				$maxdis = mathlib::getDistanceHex($posLaunch, mathlib::hexCoToPixel($target["x"], $target["y"]));
				$dis = Dice::d(6); //deviation distance
				$dis = min($dis,floor($maxdis));
				$direction = Dice::d(6); //deviation direction
				for ($i=0;$i<$dis;$i++){
					$target = mathlib::getHexToDirection($direction, $target["x"], $target["y"]);
				}
				$fireOrder->pubnotes .= " deviation from " . $fireOrder->x . ' ' . $fireOrder->y;
				$fireOrder->x = $target["x"];
				$fireOrder->y = $target["y"];
				$fireOrder->pubnotes .= " to " . $fireOrder->x . ' ' . $fireOrder->y;
				$fireOrder->pubnotes .= "Shot deviates $dis hexes. ";   
			}
			
			//do damage to ships in range...
			$explosionPos = mathlib::hexCoToPixel($fireOrder->x, $fireOrder->y);
                    	$ships1 = $gamedata->getShipsInDistance($explosionPos);
			$ships2 = $gamedata->getShipsInDistance($explosionPos, mathlib::$hexWidth+1);
			foreach($ships2 as $targetShip){
				if(isset($ships1[$targetShip->id])){ //ship on target hex!
					$sourceHex = $posLaunch;
					$damage = $this->maxDamage;
				}else{ //ship at range 1!
					$sourceHex = $explosionPos;
					$damage = $this->minDamage;
				}
				$this->AOEdamage( $targetShip, $shooter, $fireOrder, $sourceHex, $damage, $gamedata);
			}
		}
	    
		$fireOrder->rolled = max(1,$fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
	} //endof function fire
	    

	public function AOEdamage($target, $shooter, $fireOrder, $sourceHex, $damage, $gamedata){
            if ($target->isDestroyed()) return; //no point allocating
		$damage = $this->getDamageMod($damage, $shooter, $target, $sourceHex, $gamedata);
		$damage -= $target->getDamageMod($shooter, $sourceHex, $gamedata->turn, $this);
		if ($target instanceof FighterFlight){
		    foreach ($target->systems as $fighter){
			if ($fighter == null || $fighter->isDestroyed()){
			    continue;
			}
			$this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, $sourceHex, $gamedata, false);
		    }
		}else{
		    $tmpLocation = $target->getHitSectionPos($sourceHex, $fireOrder->turn);
		    $system = $target->getHitSystem($shooter, $fireOrder, $this, $tmpLocation);
		    $this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, false, $tmpLocation);
		}   
        }

	/*only half damage vs Enormous units...*/
	public function getDamageMod($damage, $shooter, $target, $sourceHex, $gamedata){
		$modifiedDmg = parent::getDamageMod($damage, $shooter, $target, $sourceHex, $gamedata);
		if($target->Enormous) $modifiedDmg = floor($modifiedDmg/2);
		return $modifiedDmg;
	}

    } //endof class AoE

    

    class EnergyMine extends AoE{
        public $name = "energyMine";
        public $displayName = "Energy Mine";
        public $range = 50;
        public $loadingtime = 2;
        public $ballistic = true;
        public $hextarget = true;
        public $hidetarget = true;
        
        public $flashDamage = true;
        public $priority = 1;
        
            
        public $trailColor = array(141, 240, 255);
        public $animation = "ball";
        public $animationColor = array(141, 240, 255);
        public $animationExplosionScale = 1;
        public $animationExplosionType = "AoE";
        public $explosionColor = array(141, 240, 255);
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
	    
	    
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Weapon type"] = "Ballistic";
        }
        
	    /*getDamage in itself depends on actually hit ship - this function is meaningless here, really!*/
        public function getDamage($fireOrder){        return 10;   } 
	    /*these are important, though!*/
        public function setMinDamage(){     $this->minDamage = 10;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }



?>
