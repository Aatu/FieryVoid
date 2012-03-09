<?php

class Reactor extends ShipSystem{

    public $name = "reactor";
    public $displayName = "Reactor";
    public $primary = true;
    
    public $possibleCriticals = array(11=>"OutputReduced2", 15=>"OutputReduced4", 19=>"OutputReduced6", 27=>"OutputReduced8", 100=>"ForcedOfflineOneTurn");
    
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
        
        
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
    
    function __construct($armour, $maxhealth, $powerReq, $output, $engineEfficiency, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
        
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
        
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
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
        
    function __construct($armour, $maxhealth, $powerReq, $output ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
    

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
    
    function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0 ){
        parent::__construct($armour, $maxhealth, $powerReq, $output );
         
        $this->thrustused = (int)$thrustused;
        $this->direction = (int)$direction;
    
    }
   
   
    
}

class Hangar extends ShipSystem{

    public $name = "hangar";
    public $displayName = "Hangar";
    public $squadrons = Array();
    public $primary = true;
    
    function __construct($armour, $maxhealth, $output = 6){
        parent::__construct($armour, $maxhealth, 0, $output );
 
    }

    
    
}

class JumpEngine extends ShipSystem{

    public $name = "jumpEngine";
    public $displayName = "Jump engine";
    public $delay = 0;
    public $primary = true;
    
    function __construct($armour, $maxhealth, $powerReq, $delay){
        parent::__construct($armour, $maxhealth, $powerReq, 0);
    
        $this->delay = $delay;
    }
    
   
    
    
}


class Structure extends ShipSystem{

    public $name = "structure";
    public $displayName = "Structure";

    
    function __construct($armour, $maxhealth){
        parent::__construct($armour, $maxhealth, 0, 0);
         
    
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
    public $animationExplosionType = "normal";
    public $explosionColor = array(250, 230, 80);
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
    public $intercept = 0;
    public $freeintercept = false;
    
    public $ballistic = false;
    public $hextarget = false;
    public $hidetarget = false;
    
    
    public $shots = 1;
    public $defaultShots = 1;
    public $canChangeShots = false;
    
    public $grouping = 0;
    public $guns = 1;
    public $projectilespeed = 17;
    
    public $rof = 2;
    
    public $firingMode = 1;
    
    public $flashDamage = false;
    public $damageType = "standard";
    public $minDamage, $maxDamage;
    
    
    
    public $possibleCriticals = array(14=>"ReducedRange", 19=>"ReducedDamage", 25=>array("ReducedRange","ReducedDamage"));
    
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc ){
        parent::__construct($armour, $maxhealth, $powerReq, 0 );
         
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
    
    public function setSystemDataWindow($turn){

        $this->data["Loading"] = $this->turnsloaded."/".$this->getNormalLoad();
        
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
        
        if ($this->intercept > 0){
            $this->data["Intercept"] = "-".$this->intercept*5;
        }

        
        $misc = array();
        
        if ($this->overloadturns > 0){
            $misc[] = " OVERLOADABLE";
        }
        if ($this->uninterceptable)
            $misc[] = " UNINTERCEPTABLE";
        
        //if (sizeof($misc)>0)
            //$this->data["Misc"] = $misc;
    
        parent::setSystemDataWindow($turn);
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
    
    public function onConstructed($ship, $turn, $phase){
            $this->setLoading($ship, $turn, $phase);
     
    }
    
    public function beforeTurn($ship, $turn, $phase){
        
        
        
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
    
    public function calculateRangePenalty($pos, $target){
        $targetPos = $target->getCoPos();
        $dis = mathlib::getDistanceHex($pos, $targetPos);
        
        $rangePenalty = ($this->rangePenalty*$dis);
        $notes = "shooter: ".$pos["x"].",".$pos["y"]." target: ".$targetPos["x"].",".$targetPos["y"]." dis: $dis, rangePenalty: $rangePenalty";
        return Array("rp"=>$rangePenalty, "notes"=>$notes);
    }
    
    public function calculateHit($gamedata, $fireOrder){
    
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        $pos = $shooter->getCoPos();
        if ($this->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
        }
                       
        $rp = $this->calculateRangePenalty($pos, $target);
        $rangePenalty = $rp["rp"];
        
        $dew = $target->getDEW($gamedata->turn);
        $oew = $shooter->getOEW($target, $gamedata->turn);
        $mod = 0;
        
        if (Movement::isRolling($shooter, null) && !$this->ballistic)
            $mod -=3;
        
        if (Movement::isPivoting($shooter, null) && !$this->ballistic)
            $mod -=3;
            
        if ($oew == 0)
            $rangePenalty = $rangePenalty*2;
            
        $defence = $target->getDefenceValuePos($pos);
        
        $CnC = $shooter->getSystemByName("CnC");
        $mod -= ($CnC->hasCritical("PenaltyToHit", $gamedata->turn-1));
       
        $firecontrol =  $this->fireControl[$target->getFireControlIndex()];
        
        $intercept = $this->getIntercept($gamedata, $fireOrder, $pos);
            
        $goal = ($defence - $dew - $rangePenalty - $intercept + $oew + $firecontrol + $mod);
        
        $change = round(($goal/20)*100);
        
        if ($change < 0)
            $change = 0;
        
        
        
        $notes = $rp["notes"] . ", DEW: $dew, OEW: $oew, defence: $defence, intercept: $intercept, F/C: $firecontrol, goal: $goal, chance: $change";
        
        $fireOrder->needed = $change;
        $fireOrder->notes = $notes;
        $fireOrder->updated = true;
    }
    
    public function getIntercept($gamedata, $fireOrder, $pos){
    
        $intercept = 0;
        if ($this->uninterceptable)
            return 0;
    
        foreach ($gamedata->ships as $ship){
            foreach ($ship->fireOrders as $fire){
                if ($fire->type == "intercept" && $fire->targetid == $fireOrder->id){
                    $intercept += $ship->getSystemById($fire->weaponid)->intercept;
                    
                }
            }
        }
        
        return $intercept;
        
    }
    public function fire($gamedata, $fireOrder){
    
        $shooter = $gamedata->getShipById($fireOrder->shooterid);
        $target = $gamedata->getShipById($fireOrder->targetid);
        
        $pos = $shooter->getCoPos();
        if ($this->ballistic){
            $movement = $shooter->getLastTurnMovement($fireOrder->turn);
            $pos = mathlib::hexCoToPixel($movement->x, $movement->y);
        }
                    
        $this->calculateHit($gamedata, $fireOrder);
        $intercept = $this->getIntercept($gamedata, $fireOrder, $pos);
        
        for ($i=0;$i<$fireOrder->shots;$i++){
            $needed = $fireOrder->needed - ($this->grouping*$i);
            $rolled = Dice::d(100);
            if ($rolled > $needed && $rolled <= $needed+($intercept*5)){
                //$fireOrder->pubnotes .= "Shot intercepted. ";
                $fireOrder->intercepted += 1;
            }
            
            $fireOrder->notes .= " FIRING SHOT ". ($i+1) .": rolled: $rolled, needed: $needed\n";
            if ($rolled <= $needed){
                $fireOrder->shotshit++;
                $this->damage($target, $shooter, $fireOrder, $pos, $gamedata);
            }
        }
        
        $fireOrder->rolled = 1;//Marks that fire order has been handled
                
    }

    
    protected function getOverkillSystem($target, $shooter, $system, $pos, $fireOrder, $gamedata){
    
        if ($this->flashDamage){
            return $target->getHitSystem($pos, $fireOrder->turn, $this);
        }else{
            
            $okSystem = $target->getStructureSystem($system->location);
            
            if ($okSystem == null || $okSystem->isDestroyed()){
                $okSystem = $target->getStructureSystem(0);
            }
            if ($okSystem == null || $okSystem->isDestroyed()){
                return null;
            }
        }
        
        return $okSystem;
    
    }
    
    
        
    
    public function damage($target, $shooter, $fireOrder, $pos, $gamedata){
        
        
        if ($target->isDestroyed())
            return;
            
        $system = $target->getHitSystem($pos, $fireOrder->turn, $this);
        
        if ($system == null)
            return;
            
        $this->doDamage($target, $shooter, $system, $this->getFinalDamage($shooter, $target, $pos, $gamedata), $fireOrder, $pos, $gamedata);
            
        
        
    }
    
    protected function getSystemArmour($system, $gamedata){
		$mod = $system->hasCritical("ArmorReduced", $gamedata->turn-1);
		$armor = $system->armour - $mod;
		if ($armor<0)
			$armor = 0;
			
        return $armor;
    }
    
    protected function getDamageMod($damage, $shooter, $target, $pos, $gamedata){
        if ($this->rangeDamagePenalty > 0){
            $targetPos = $target->getCoPos();
            $dis = round(mathlib::getDistanceHex($pos, $targetPos));
            
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
    
    protected function getFinalDamage($shooter, $target, $pos, $gamedata){
    
        $damage = $this->getDamage();
        return $this->getDamageMod($damage, $shooter, $target, $pos, $gamedata);
    }
    
    protected function doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata){

        $armour = $this->getSystemArmour($system, $gamedata);
        $systemHealth = $system->getRemainingHealth();
        $modifiedDamage = $damage;
        
        

        //print("damage: $damage armour: $armour\n");
            
        $destroyed = false;
        if ($damage-$armour >= $systemHealth){
            $destroyed = true;
            $modifiedDamage = $systemHealth + $armour;
            //print("destroying! rem: ".$system->getRemainingHealth()."\n");
        }
        
        
        $damageEntry = new DamageEntry(-1, $target->id, -1, $fireOrder->turn, $system->id, $modifiedDamage, $armour, 0, $fireOrder->id, $destroyed, "");
        $damageEntry->updated = true;
        $system->damage[] = $damageEntry;
        $this->onDamagedSystem($target, $system, $modifiedDamage, $armour, $gamedata);
        //print("damage: $damage armour: $armour destroyed: $destroyed \n");
        if ($damage-$armour > $systemHealth){
            //print("overkilling!\n\n");
             $damage = $damage-$modifiedDamage;
             $overkillSystem = $this->getOverkillSystem($target, $shooter, $system, $pos, $fireOrder, $gamedata);
             if ($overkillSystem != null)
                $this->doDamage($target, $shooter, $overkillSystem, $damage, $fireOrder, $pos, $gamedata);
        }
    
    
        
        
    }
    
    protected function onDamagedSystem($ship, $system, $damage, $armour, $gamedata){
		return;
	}
    
    


}


?>




