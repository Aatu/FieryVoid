<?php

class ShipSystem{

    public $jsClass = false;
    public $destroyed = false;
    public $startArc, $endArc;
    public $location; //0:primary, 1:front, 2:rear, 3:left, 4:right;
    public $id, $armour, $maxhealth, $powerReq, $output, $name, $displayName;
    public $outputType = null;
    public $specialAbilities = array();
    
    public $damage = array();
    public $outputMod = 0;
    public $boostable = false;
    public $boostEfficiency = null;
    public $maxBoostLevel = null;
    public $power = array();
    public $fireOrders = array();
    public $canOffLine = false;

    public $data = array();
    public $critData = array();
    public $imagePath, $iconPath;
    
    public $possibleCriticals = array();
    
        
    public $criticals = array();
    
    protected $structureSystem;
    
    protected $parentSystem = null;
    
    function __construct($armour, $maxhealth, $powerReq, $output){
        $this->armour = $armour;
        $this->maxhealth = (int)$maxhealth;
        $this->powerReq = (int)$powerReq;
        $this->output = (int)$output;


    }
    
    public function onConstructed($ship, $turn, $phase){
        $this->structureSystem = $ship->getStructureSystem($this->location);
        $this->effectCriticals();
        $this->destroyed = $this->isDestroyed();
    }
    
    public function getSpecialAbilityList($list)
    {
        if ($this instanceof SpecialAbility)
        {
            if ($this->isDestroyed() || $this->isOfflineOnTurn())
                return;

            foreach ($this->specialAbilities as $effect)
            {
                if (!isset($list[$effect]))
                {
                    $list[$effect] = $this->id;
                }
            }
        }
        
        return $list;
    }
    
    public function beforeTurn($ship, $turn, $phase){
        $this->setSystemDataWindow($turn);
    }
    
    public function setDamage($damage){
        $this->damage[] = $damage;
    }
    
    public function setDamages($damages){
        $this->damage = $damages;
    }
    
//    public function setPowers($power){
//        $this->power = $power;
//    }
//    
    public function setPower($power){
        $this->power[] = $power;
    }
    
    public function getFireOrders(){
        return $this->fireOrders;
    }
    
    public function setFireOrder($fireOrder){
        $this->fireOrders[] = $fireOrder;
    }
    
    public function setFireOrders($fireOrders){
        $this->fireOrders = $fireOrders;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setCritical($critical, $turn){
        
        if (!$critical->oneturn || ($critical->oneturn && $critical->turn >= $turn-1))
            $this->criticals[] = $critical; 
    }
    
    public function setCriticals($criticals, $turn){
        foreach( $criticals as $crit){
            $this->setCritical($crit, $turn);
        }
    }
    
    public function getArmour($gamedata, $shooter){
        return $this->armour;
    }
	
    public function getArmourPos($gamedata, $pos){
        return $this->armour;
    }
    
    public function setSystemDataWindow($turn){
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
        
    }
    
    public function testCritical($ship, $gamedata, $crits, $add = 0){
        
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
                    $crit = $this->addCritical($ship->id, $phpclass, $gamedata);
                    if ($crit)
                        $crits[] = $crit;
                }
            }else{
                $crit = $this->addCritical($ship->id, $criticalTypes, $gamedata);
                if ($crit)
                    $crits[] = $crit;
            }
            
            
            
        }
        
        return $crits;
         
    }
    
    public function addCritical($shipid, $phpclass, $gamedata)
    {
        $crit = new $phpclass(-1, $shipid, $this->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $this->criticals[] =  $crit;
        return $crit;
    }
    
    public function hasCritical($type, $turn = false){
        $count = 0;
        foreach ($this->criticals as $critical){
            if (strcmp($critical->phpclass, $type) == 0 && $critical->inEffect){
				
                if ($turn === false){
                        $count++;
                }else if ((($critical->oneturn && $critical->turn+1 == $turn) || !$critical->oneturn) && $critical->turn<= $turn){
                $count++;
                }
            }
                
        }
    
        return $count;
    }
    
    public function getOutput(){
        
        if ($this->isOfflineOnTurn())
            return 0;
        
        if ($this->isDestroyed())
            return 0;
        
        $output = $this->output;
        $output -= $this->outputMod;
        return $output;
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
        
        if ( ! ($this instanceof Structure) && $this->structureSystem && $this->structureSystem->isDestroyed($turn))
            return true;
  
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
      
    public function isOfflineOnTurn($turn = null){
        if ($turn === null)
            $turn = TacGamedata::$currentTurn;
        
        if ($this->parentSystem && $this->parentSystem instanceof DualWeapon)
            return $this->parentSystem->isOfflineOnTurn($turn);
    
        foreach ($this->power as $power){
            if ($power->type == 1 && $power->turn == $turn){
                return true;
            }
        }
        
        if ($this->hasCritical("ForcedOfflineOneTurn", $turn-1)){
            return true;
        }
        
        return false;
    
    }
    
    public function isOverloadingOnTurn($turn = null){
        if ($turn === null)
            $turn = TacGamedata::$currentTurn;
        
//        if ($this->parentSystem && $this->parentSystem instanceof DualWeapon)
//            return $this->parentSystem->isOverloadingOnTurn($turn);
        
        foreach ($this->power as $power){
            if ($power->type == 3 && $power->turn == $turn){
                return true;
            }
        }
        
        return false;
    
    }
    
    public function onAdvancingGamedata($ship)
    {
    }
    
    public function setSystemData($data, $subsystem)
    {
    }
    
    public function setInitialSystemData($ship)
    {
    }

}