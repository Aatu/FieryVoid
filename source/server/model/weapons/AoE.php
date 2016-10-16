<?php

    class AoE extends Weapon{

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function calculateHit($gamedata, $fireOrder){
            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $shooterPos = $shooter->getCoPos(); 
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
		$posLaunch = mathlib::hexCoToPixel($movement->x, $movement->y);//at moment of launch!!!
            $target = array("x"=>$fireOrder->x, "y"=>$fireOrder->y);
            $hit = false;
            
            $fireOrder->needed = 0;
            $rolled = Dice::d(4);
            if ($rolled == 1){ //no exact hit...
                $rolled = Dice::d(10);
                if ($rolled<7){
                    $hit = true;
                    $dis = Dice::d(6);
                    
                    
                    $maxdis = mathlib::getDistanceHex($posLaunch, mathlib::hexCoToPixel($target["x"], $target["y"]));

                    if ($dis>$maxdis){
                        $dis = floor($maxdis);
                    }

                    for ($i=0;$i<$dis;$i++){
                        $target = mathlib::getHexToDirection($rolled, $target["x"], $target["y"]);
                    }

                    $fireOrder->x = $target["x"];
                    $fireOrder->y = $target["y"];
                    $fireOrder->pubnotes .= "Shot deviates $dis hexes. ";   
                    
                }else{
                    $fireOrder->pubnotes .= "Charge dissipates. ";  
                }
                
            }else{
                $hit = true;
            }
            
            if ($hit){
                $fireOrder->shotshit++;
            }
            
             
            
            $fireOrder->rolled = 1;//Marks that fire order has been handled
            $fireOrder->updated = true; 
        }
        
	    
	    
        public function fire($gamedata, $fireOrder){
            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $shooterpos = $shooter->getCoPos();
		$movement = $shooter->getLastTurnMovement($fireOrder->turn);
		$posLaunch = mathlib::hexCoToPixel($movement->x, $movement->y);//at moment of launch!!!		
            $target = array("x"=>$fireOrder->x, "y"=>$fireOrder->y);
            
            $this->calculateHit($gamedata, $fireOrder);
            if ($fireOrder->shotshit>0){
                    $pos = mathlib::hexCoToPixel($fireOrder->x, $fireOrder->y);
                    $ships1 = $gamedata->getShipsInDistance($pos);
                    
                    
                foreach($ships1 as $ship){
                    $this->AOEdamage($ship, $shooter, $fireOrder, $posLaunch, 30, $gamedata);
                    $fireOrder->notes .= $ship->name ." in same hex. "; 
                }
                
                
                $ships2 = $gamedata->getShipsInDistance($pos, mathlib::$hexWidth+1);
                 
                foreach($ships2 as $ship){
                    if (isset($ships1[$ship->id]))
                        continue;
                    
                    $fireOrder->notes .= $ship->name ." in adjacent hex. "; 
                    $this->AOEdamage($ship, $shooter, $fireOrder, $pos, 10, $gamedata);
                 }
            }
                  
        }
        
        public function AOEdamage($target, $shooter, $fireOrder, $pos, $amount, $gamedata){
            if ($target->isDestroyed()) return;                    
            $amount -= $target->getDamageMod($shooter, $pos, $gamedata->turn);
            
            if ($target instanceof FighterFlight){
			$this->fighterDamage($target, $shooter, $fireOrder, $pos, $amount, $gamedata);
	    } else {
                if ($target instanceof StarBase || $target instanceof OSAT){
                    $amount = floor($amount/2);
                }

                $hitLoc = $target->getHitSectionPos($pos, $fireOrder->turn);
		$system = $target->getHitSystem($shooter, $fireOrder, $this, $hitLoc);
			
		if ($system == null) return;

		$this->doDamage($target, $shooter, $system, $amount, $fireOrder, $pos, $gamedata, $hitLoc);
	    }
        }
        
        public function fighterDamage($target, $shooter, $fireOrder, $pos, $amount, $gamedata){
		foreach ($target->systems as $fighter){
			if ($fighter == null || $fighter->isDestroyed()){
				continue;
			}

			$this->doDamage($target, $shooter, $fighter, $amount, $fireOrder, $pos, $gamedata);
		}
	}
    }

    
    class EnergyMine extends AoE{
    
        public $name = "energyMine";
        public $displayName = "Energy Mine";
        public $range = 50;
        public $loadingtime = 2;
        public $ballistic = true;
        public $hextarget = true;
        public $hidetarget = true;
        
        public $flashDamage = true;
        public $priority = 2;
        
            
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
        
        public function getDamage($fireOrder){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10;      }
        public function setMaxDamage(){     $this->maxDamage = 30;      }
    
    }



?>
