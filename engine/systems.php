<?php

class Reactor extends ShipSystem{

    public $name = "reactor";
    public $displayName = "Reactor";
    public $primary = true;
    
    public $possibleCriticals = array(11=>"OutputReduced2", 15=>"OutputReduced4", 19=>"OutputReduced6", 27=>"OutputReduced8", 100=>"ForcedOfflineOneTurn");
    
    function __construct($armour, $maxhealth, $location, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $location, $powerReq, $output );
		
		
		
    }
    
   

    
    
}

class Engine extends ShipSystem{

    public $name = "engine";
    public $displayName = "Engine";
    public $engineEfficiency;
    public $thrustused;
    public $primary = true;
    public $boostable = true;
    
    public $possibleCriticals = array(15=>"OutputReduced2", 21=>"OutputReduced4", 27=>"ForcedOfflineOneTurn");
    
    function __construct($armour, $maxhealth, $location, $powerReq, $output, $engineEfficiency, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $location, $powerReq, $output );
        
        $this->thrustused = (int)$thrustused;
        $this->engineEfficiency = (int)$engineEfficiency;
    }
    
}

class Scanner extends ShipSystem{

    public $name = "scanner";
    public $displayName = "Scanner";
    public $primary = true;
    public $boostable = true;
    
    public $possibleCriticals = array(15=>"OutputReduced2", 19=>"OutputReduced4", 23=>"OutputReduced6", 27=>"OutputReduced8");
        
    function __construct($armour, $maxhealth, $location, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $location, $powerReq, $output );
    }

    
    public function getScannerOutput($turn){

        if ($this->isOfflineOnTurn($turn))
            return 0;
            
        $output = $this->output;
    
        foreach ($this->power as $power){
            if ($power->turn == $turn && $power->type == 2){
                $output += $power->amount;
            }
        
        }
        
        $output -= $this->outputMod;
        return $output;
        
    }
    
}

class CnC extends ShipSystem{

    public $name = "CnC";
    public $displayName = "C&C";
    public $primary = true;
    
    public $possibleCriticals = array(
	1=>"SensorsDisrupted", 
	9=>"CommunicationsDisrupted", 
	12=>"PenaltyToHit", 
	15=>"RestrictedEW",
	18=>array("ReducedIniativeOneTurn","ReducedIniative"), 
	21=>array("RestrictedEW","ReducedIniativeOneTurn","ReducedIniative"), 
	24=>array("RestrictedEW","ReducedIniative","ShipDisabledOneTurn"));
        
    function __construct($armour, $maxhealth, $location, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $location, $powerReq, $output );
	

    }
    
    
}

class Thruster extends ShipSystem{

    public $name = "thruster";
    public $displayName = "Thruster";
    public $direction;
    public $thrustused;
    public $thrustwasted = 0;
    
    public $possibleCriticals = array(15=>"FirstThrustIgnored", 20=>"HalfEfficiency", 25=>array("FirstThrustIgnored","HalfEfficiency"));
	
	
	public $criticalDescriptions = array(
		
	
	);
    
    function __construct($armour, $maxhealth, $location, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $location, $powerReq, $output );
         
        $this->thrustused = (int)$thrustused;
        $this->direction = (int)$direction;
	
    }
   
   
    
}

class Hangar extends ShipSystem{

    public $name = "hangar";
    public $displayName = "Hangar";
    public $squadrons = Array();
    public $primary = true;
    
    function __construct($armour, $maxhealth, $location, $output = 6){
        parent::__construct($armour, $maxhealth, $location, 0, $output );
 
    }

    
    
}

class JumpEngine extends ShipSystem{

    public $name = "jumpEngine";
    public $displayName = "Jump engine";
    public $delay = 0;
    public $primary = true;
    
    function __construct($armour, $maxhealth, $location, $powerReq, $delay){
        parent::__construct($armour, $maxhealth, $location, $powerReq, 0);
    
        $this->delay = $delay;
    }
    
   
    
    
}


class Structure extends ShipSystem{

    public $name = "structure";
    public $displayName = "Structure";

    
    function __construct($armour, $maxhealth, $location){
        parent::__construct($armour, $maxhealth, $location, 0, 0);
         
    
    }
    
 

}

class Weapon extends ShipSystem{

    public $startArc, $endArc;
    public $weapon = true;
    
    public $name = null;
    public $displayName ="";
    
    public $animation = "none";
    public $animationImg = null;
    public $animationImgSprite = 0;
    public $animationColor = null;
    public $animationWidth = 3;
	public $animationExplosionScale = 0.25;
	public $trailLength = 40;
	public $trailColor = array(248, 216, 65);
    public $rangePenalty = 0;
	public $rangeDamagePenalty = 0;
	public $dp = 0; //damage penalty per dice
    public $range = 0;
    public $fireControl =  array(0, 0, 0); // fighters, <mediums, <capitals 
    
    public $loadingtime = 1;
    public $turnsloaded;
    public $normalload = 0;
    public $overloadturns = 0;
	
	public $uninterceptable = false;
	
	public $ballistic = false;
    
    
    public $shots = 1;
	public $defaultShots = 1;
	public $guns = 1;
    public $projectilespeed = 17;
    
    public $firingMode = 1;
        
    public $damageType = "standard";
    public $minDamage, $maxDamage;
	
	
    
    public $possibleCriticals = array(14=>"ReducedRange", 19=>"ReducedDamage", 25=>array("ReducedRange","ReducedDamage"));
    
    function __construct($armour, $maxhealth, $location, $powerReq, $startArc, $endArc ){
        parent::__construct($armour, $maxhealth, $location, $powerReq, 0 );
         
        $this->startArc = (int)$startArc;
        $this->endArc = (int)$endArc;
       
		$this->setMinDamage();
        $this->setMaxDamage();
		
    }
	
	public function effectCriticals(){
        parent::effectCriticals();
        foreach ($this->criticals as $crit){
            if ($crit instanceof ReducedRange){
				
				if ($this->rangePenalty != 0){
					if ($this->rangePenalty >= 1){
						$this->rangePenalty += 1;
					}else{
						$this->rangePenalty = 1/(round(1/$this->rangePenalty)-1);
					}
					
				}
				
				if ($this->range != 0){
					$this->range = round($this->range *0.75);
				}
				
			}
			
			if ($crit instanceof ReducedDamage){
				$min = $this->minDamage * 0.25;
				$max = $this->maxDamage/$this->defaultShots * 0.25;
				$avg = round(($min+$max)/2);
				$this->dp = $avg;
			}
        }
    
		 
        $this->setMinDamage();
        $this->setMaxDamage();
    
    }
    
    public function getNormalLoad(){
        if ($this->normalload == 0){
            return $this->loadingtime;
        }
        return $this->normalload;
    }
    
    public function firedOnTurn($ship, $turn){
		
        foreach ($ship->fireOrders as $fire){
            if ($fire->weaponid == $this->id && $fire->turn == $turn){
                return true;
            }
        }
        return false;
    }
	
	public function setSystemDataWindow(){

		$this->data["Loading"] = $this->turnsloaded."/".$this->loadingtime;
		
		$dam = $this->minDamage."-".$this->maxDamage;
		if ($this->minDamage == $this->maxDamage)
			$dam = $this->maxDamage;
			
		$this->data["Damage"] = $dam;
		
		if ($this->rangePenalty > 0){
			$this->data["Range penalty"] =$this->rangePenalty;
		}else{
			$this->data["Range"] = $this->range;
		}
		
		if ($this->guns > 1){
			$this->data["Number of guns"] = $this->guns;
		}
		
		if ($this->shots > 1){
			$this->data["Number of shots"] = $this->shots;
		}
		
		$misc = array();
		
		if ($this->overloadturns > 0){
			$misc[] = " OVERLOADABLE";
		}
		if ($this->uninterceptable)
			$misc[] = " UNINTERCEPTABLE";
		
		//if (sizeof($misc)>0)
			//$this->data["Misc"] = $misc;
	
		parent::setSystemDataWindow();
	}
	
	public function setLoading($ship, $turn, $phase){
		$turnsloaded = 0;
    
    
        for ($i = 0;$i<=$turn;$i++){
            $step = 1;
            $off = $this->isOfflineOnTurn($i);
            $overload = $this->isOverloadingOnTurn($i-1);
            $nowoverloading = $this->isOverloadingOnTurn($i);
            if ($i == $turn && $phase == 1 && $overload){
                $nowoverloading = true;
            }           
            $fired = $this->firedOnTurn($ship, $i);
            
                    
            if ($i == 0){
                if (!$off){
                    $turnsloaded = $this->getNormalLoad();
                    if ($overload){
                        $turnsloaded = $this->overloadturns;
                    }
                }
                continue;
            }
            
            if ($off){
                $turnsloaded = 0;
                continue;
            }
            
            if ($overload){
                $step = 2;
            }
            
            $turnsloaded += $step;
            
            if (!$overload && $turnsloaded > $this->getNormalLoad()){
                $turnsloaded = $this->getNormalLoad();
            }
    
            if ($turnsloaded > $this->getNormalLoad() && !$nowoverloading){
                $turnsloaded = $this->getNormalLoad();
            }else if ($turnsloaded > $this->overloadturns && $nowoverloading){
                $turnsloaded = $this->overloadturns;
            }
                            
           
			
					
            if ($fired){
				$turnsloaded -= $this->getNormalLoad();
                if ($turnsloaded < 0)
                    $turnsloaded = 0;
                
            }
            
        }
        
        $this->turnsloaded = $turnsloaded;
	}
    
    public function beforeTurn($ship, $turn, $phase){
        
		$this->setLoading($ship, $turn, $phase);
        
		parent::beforeTurn($ship, $turn, $phase);
    }
    /*
    
    public function beforeTurn($ship, $turn){
        $lastfireturn = -1;
        
        foreach ($ship->fireOrders as $fire){
            if ($fire->weaponid == $this->id && $fire->rolled > 0 && $fire->turn > $lastfireturn){
                $lastfireturn = $fire->turn;
            }
        }
        if ($lastfireturn == -1){
            $this->turnsloaded = $this->loadingtime;
        }else{
            $this->turnsloaded = $turn - $lastfireturn;
        }
    
        if ($this->turnsloaded > $this->loadingtime)
            $this->turnsloaded = $this->loadingtime;
    }
    */
    
    public function getDamage(){
        return 0;
    }
    
    public function setMinDamage(){
    
    }
    
    public function setMaxDamage(){
    
    }
    
    public function calculateRangePenalty($shooter, $target){
        $shooterPos = $shooter->getCoPos();
        $targetPos = $target->getCoPos();
        $dis = mathlib::getDistance($shooterPos, $targetPos);
        $hexWidth = 50*0.8660254*2;
        
        $disInHex = $dis / $hexWidth;
        
        $rangePenalty = ($this->rangePenalty/$hexWidth*$dis);
        $notes = "shooter: ".$shooterPos["x"].",".$shooterPos["y"]." target: ".$targetPos["x"].",".$targetPos["y"]." distance: $dis, disInHex: $disInHex, rangePenalty: $rangePenalty";
        return Array("rp"=>$rangePenalty, "notes"=>$notes);
    }
    
    public function calculateHit($gamedata, $fireOrder){
    
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
               
        $rp = $this->calculateRangePenalty($shooter, $target);
        $rangePenalty = $rp["rp"];
        
        $dew = $target->getDEW($gamedata->turn);
        $oew = $shooter->getOEW($target, $gamedata->turn);
        $mod = 0;
        
        if (Movement::isRolling($shooter, null))
            $mod -=3;
        
        if (Movement::isPivoting($shooter, null))
            $mod -=3;
            
        if ($oew == 0)
            $rangePenalty = $rangePenalty*2;
            
        $defence = $target->getDefenceValue($shooter);
        
        $firecontrol =  $this->fireControl[$target->getFireControlIndex()];
            
        $goal = ($defence - $dew - $rangePenalty + $oew + $firecontrol + $mod);
        
        $change = round(($goal/20)*100);
        
        if ($change < 0)
            $change = 0;
        
        
        
        $notes = $rp["notes"] . ", DEW: $dew, OEW: $oew, defence: $defence, F/C: $firecontrol, goal: $goal, chance: $change";
        
        $fireOrder->needed = $change;
        $fireOrder->notes = $notes;
        $fireOrder->updated = true;
    }
    
    
    
    public function fire($gamedata, $fireOrder){
    
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        $damages = array();
        
        $this->calculateHit($gamedata, $fireOrder);
        $fireOrder->rolled = Dice::d(100);
		
        if ($fireOrder->rolled <= $fireOrder->needed){
			$fireOrder->shotshit = $fireOrder->shots;
            $this->damage($target, $shooter, $fireOrder);
        }
        
    
    }
    
    protected function getOverkillSystem($target, $shooter, $system){
    
            $okSystem = $target->getStructureSystem($system->location);
            
            if ($okSystem == null || $okSystem->isDestroyed()){
                return $target->getStructureSystem(0);
            }
            return $okSystem;
    
    }
    
    
        
    
    public function damage($target, $shooter, $fireOrder){
        
        
        if ($target->isDestroyed())
            return;
    
        $system = $target->getHitSystem($shooter, $fireOrder->turn);
        
        if ($system == null)
            return;
            
        $this->doDamage($target, $shooter, $system, $this->getFinalDamage($shooter, $target), $fireOrder);
            
        
        
    }
	
	protected function getSystemArmour($system){
		return $system->armour;
	}
	
	protected function getDamageMod($damage, $shooter, $target){
		if ($this->rangeDamagePenalty > 0){
			$shooterPos = $shooter->getCoPos();
			$targetPos = $target->getCoPos();
			$dis = round(mathlib::getDistanceHex($shooterPos, $targetPos));
			
			//print ("damage: $damage dis: $dis damagepen: " . $this->rangeDamagePenalty);
			$damage -= ($dis * $this->rangeDamagePenalty);
			//print ("damage: $damage \n\n");
			if ($damage < 0)
				return 0;
		}
		
		$damage -= $this->dp;
		if ($damage < 0)
			return 0;
		
		return $damage;
	}
	
	protected function getFinalDamage($shooter, $target){
	
		$damage = $this->getDamage();
		return $this->getDamageMod($damage, $shooter, $target);
	}
    
    protected function doDamage($target, $shooter, $system, $damage, $fireOrder){

        $armour = $this->getSystemArmour($system);
        $systemHealth = $system->getRemainingHealth();
		$modifiedDamage = $damage;
		
		

        //print("damage: $damage armour: $armour\n");
            
        $destroyed = false;
        if ($damage-$armour >= $systemHealth){
            $destroyed = true;
            $modifiedDamage = $systemHealth + $armour;
            //print("destroying! rem: ".$system->getRemainingHealth()."\n");
        }
        
        
        $damageEntry = new DamageEntry(-1, $target->id, -1, $fireOrder->turn, $system->id, $modifiedDamage, $armour, 0, $fireOrder->id, $destroyed);
        $damageEntry->updated = true;
        $system->damage[] = $damageEntry;
        //print("damage: $damage armour: $armour destroyed: $destroyed \n");
        if ($damage-$armour > $systemHealth){
            //print("overkilling!\n\n");
             $damage = $damage-$modifiedDamage;
             $overkillSystem = $this->getOverkillSystem($target, $shooter, $system);
             if ($overkillSystem != null)
                $this->doDamage($target, $shooter, $overkillSystem, $damage, $fireOrder);
        }
    
    
        
        
    }
    
    


}


?>




