<?php

class ShipSystem {
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
	public $fighter = false; //important for actual fighters

    public $data = array();
    public $critData = array();
    public $destructionAnimated = false;
    public $imagePath, $iconPath;
    public $critRollMod = 0; //penalty tu critical damage roll: positive means crit is more likely, negative less likely (for this system)
    
    public $possibleCriticals = array();
	
    public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?	
    
    public $forceCriticalRoll = false; //true forces critical roll even if no damage was done
	
    public $criticals = array();
	public $advancedArmor = false; //indicates that system has advanced armor
    
    protected $structureSystem;
	
    protected $parentSystem = null;
    protected $unit = null; //unit on which system is mounted
	
	protected $individualNotes = array();
	public $individualNotesTransfer = "";//variable for transferring individual notes from interface to servr layer
	
    
    function __construct($armour, $maxhealth, $powerReq, $output){
        $this->armour = $armour;
        $this->maxhealth = (int)$maxhealth;
        $this->powerReq = (int)$powerReq;
        $this->output = (int)$output;
    }

    public function stripForJson(){
        $strippedSystem = new stdClass();
        $strippedSystem->id = $this->id;
        $strippedSystem->name = $this->name;
        $strippedSystem->damage = $this->damage;
        $strippedSystem->criticals = $this->criticals;
        $strippedSystem->critData = $this->critData;
        $strippedSystem->power = $this->power;
        $strippedSystem->specialAbilities = $this->specialAbilities;
        $strippedSystem->output = $this->output;
        $strippedSystem->outputMod = $this->outputMod;
        $strippedSystem->destroyed = $this->destroyed;
		$strippedSystem->individualNotesTransfer = $this->individualNotesTransfer; //necessary 

		//ship enhancements - check if this system is affected...
		$strippedSystem = Enhancements::addSystemEnhancementsForJSON($this->unit, $this, $strippedSystem);//modifies $strippedSystem object

        return $strippedSystem;
    }
	
	public function doIndividualNotesTransfer(){//optionally to be redefined if system can receive any private data from front endthat need immediate attention		
	}

    public function onConstructed($ship, $turn, $phase){
        if($ship->getAdvancedArmor()==true){
            $this->advancedArmor = true;
        }
        $this->structureSystem = $ship->getStructureSystem($this->location);
        $this->effectCriticals();
        $this->destroyed = $this->isDestroyed();
    }
	
	/*saves individual notes (if any new ones exist) to database*/
	public function saveIndividualNotes(DBManager $dbManager){ //loading exisiting notes is done in dbmanager->getSystemDataForShips()
		foreach ($this->individualNotes as $currNote){
			$dbManager->insertIndividualNote($currNote);//function itself will decide whether this note really needs saving
		}
		//...and delete them, after saving they serve no more function
		$this->individualNotes = array();
	}
	
	/*generates individual notes (if necessary)
	base version is empty, to be redefined by systems as necessary
	*/
	public function generateIndividualNotes($gamedata, $dbManager){	}	
	
	public function addIndividualNote($noteObject){
		$this->individualNotes[] = $noteObject;
	}
	
	/*act on notes just loaded - to be redefined by systems as necessary*/
	public function onIndividualNotesLoaded($gamedata){
		$this->individualNotes = array();//delete notes, after reaction on their load they serve no further purpose
	}
		
	
    public function setUnit($unit){
		$this->unit = $unit;    
    }
	
    public function getUnit(){
		return $this->unit;    
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
    
	/*function called before firing orders are resolved; weapons with special actions (like auto-fire, combination fire, etc)
		will have their special before firing logic here (like creating additional fire orders!)
		In future, other systems may have similar needs
	*/
    public function beforeFiringOrderResolution($gamedata)
    {
    }
	
    public function beforeTurn($ship, $turn, $phase){
        $this->setSystemDataWindow($turn);
    }
    
    public function setDamage($damage){ //$damage object 
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
    
    public function getFireOrders($turn = -1){
	if($turn<1){
        	return $this->fireOrders;
	}else{ //indicated a particular turn
		$fireOrders = array();
		foreach($this->fireOrders as $fireOrder){
			if($fireOrder->turn == $turn){
				$fireOrders[] = $fireOrder;
			}
		}
		return $fireOrders;
	}
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
        if ($critical->param){            
            $currentTurn = TacGamedata::$currentTurn;
            if ($currentTurn > $critical->turn + $critical->param){
                return;
            }
        }        
        if (!$critical->oneturn || ($critical->oneturn && $critical->turn >= $turn-1))
            $this->criticals[] = $critical; 
    }
    
    public function setCriticals($criticals, $turn){
        foreach( $criticals as $crit){
            $this->setCritical($crit, $turn);
        }
    }
    
	/*used by estiamtions only, doesn't take all things into account - do not use in actual damage dealing routines!*/
    public  function getArmourComplete($target, $shooter, $dmgClass, $pos=null){ //gets armour - from indicated direction if necessary direction 
		//$pos is to be included if launch position is different than firing unit position
		$armour = $this->getArmourBase($target, $shooter, $dmgClass, $pos);    
		$armour += $this->getArmourAdaptive($target, $shooter, $dmgClass, $pos);    
		return $armour;
    }
		
    public  function getArmourBase($target, $shooter, $dmgClass, $pos=null){ //gets armour - from indicated direction if necessary direction 
		//$pos is to be included if launch position is different than firing unit position
		$armour = $this->armour;    
		return $armour;
    }
	
    public function getArmourAdaptive($target, $shooter, $dmgClass, $pos=null){ //gets adaptive part of armour
		$armour = 0;
		$AAController = $this->unit->getAdaptiveArmorController(); 
		if($AAController){
			$armour = $AAController->getArmourValue($dmgClass);			
		}
		return $armour;
	}
	    
	
    
    public function setSystemDataWindow($turn){
	if($this->startArc !== null){
		$this->data["Arc"] = $this->startArc . ".." . $this->endArc;
	}
	    
	if($this->powerReq > 0){
		$this->data["Power used"] = $this->powerReq;
	} else {
		$this->data["Power used"] = 'none';
	}
	    
	    /* no longer needed, info available in Notes
	if($this->advancedArmor == true){
		$this->data["Others"] = "Advanced Armor";
	}
	*/
	    
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
	//use additional value to critical!
	$bonusCrit = $this->critRollMod + $ship->critRollMod;	
	$crits = array_values($crits); //in case some criticals were deleted!		
	    
        $damageMulti = 1;

        if ($ship instanceof OSAT){ //leaving instanceof OSAT here - MicroSATs will have crits appropriate for their size (eg. dropout tests)
            if ($this->displayName == "Thruster" && sizeof($this->criticals) == 0){
                if ($this->getTotalDamage()+$bonusCrit > ($this->maxhealth/2)){
                    $crit = $this->addCritical($ship->id, "OSatThrusterCrit", $gamedata);
                    $crits[] = $crit;
                }
            }
        }        

	    /*moved to potentially exploding systems themselves
        if ($this instanceof MissileLauncher || $this instanceof ReloadRack){
            $crit = $this->testAmmoExplosion($ship, $gamedata);
            $crits[] = $crit;
        }
        else */
	if ($this instanceof SubReactor){
            //debug::log("subreactor, multiple damage by 0.5");
            $damageMulti = 0.5;
        }

        $roll = Dice::d(20) + floor(($this->getTotalDamage())*$damageMulti) + $add +$bonusCrit;
        $criticalTypes = -1;

        foreach ($this->possibleCriticals as $i=>$value){
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
	
    
    public function addCritical($shipid, $phpclass, $gamedata){
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
	$output = max(0,$output); //don't let output be negative!
        return $output;
    }
    
    
    public function effectCriticals(){ 
        $percentageMod = 0;
        foreach ($this->criticals as $crit){
            $this->outputMod += $crit->outputMod;
        $percentageMod += $crit->outputModPercentage;
        }
        //convert percentage mod to absolute value...
        if($percentageMod != 0){
            $this->outputMod += round($percentageMod * $this->output /100 );
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
	    /*system is destroyed if it was destroyed directly, up to indicated turn*/
	    /*or if it's Structure system is destroyed AT LEAST ONE TURN EARLIER*/
	    $currTurn = TacGamedata::$currentTurn;
	    if($turn !== false) $currTurn = $turn;
	    $prevTurn = $currTurn-1;

        foreach ($this->damage as $damage){ //was actually destroyed up to indicated turn
            if (($damage->turn <= $currTurn) && $damage->destroyed) return true;
        }
        
        if ( ! ($this instanceof Structure) && $this->structureSystem && $this->structureSystem->isDestroyed($prevTurn))
            return true;
  
        return false;
        
    }


    public function wasDestroyedThisTurn($turn){
        foreach ($this->damage as $damage){
            if ($damage->turn == $turn && $damage->destroyed){
                return true;
            }
        }  
        return false;
    }

    
    public function isDamagedOnTurn($turn){
	if($this->forceCriticalRoll) return true; //allow forced crit roll
        
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

        if ($this->hasCritical("ForcedOfflineForTurn")){
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
    
    public function onAdvancingGamedata($ship, $gamedata)
    {
    }
    
    public function setSystemData($data, $subsystem)
    {
    }
    
    public function setInitialSystemData($ship)
    {
    }
	
	/*assigns damage, returns remaining (overkilling) damage and how much armor was actually pierced
	all extra data needed for defensive modifying damage as it's being dealt - like Shadow Energy Diffusers and Gaim Bulkheads
	returns array: dmgDealt, dmgRemaining, armorPierced
	*/
	public function assignDamageReturnOverkill($target, $shooter, $weapon, $gamedata, $fireOrder, $damage, $armour, $pos=null){
		$returnArray = array("dmgDealt"=>0, "dmgRemaining"=>0, "armorPierced"=>0);
		$effectiveDamage = $damage;
		$remainingDamage = 0;
		$effectiveArmor = $armour;
		$systemHealth = $this->getRemainingHealth();
		$systemDestroyed = false;
		
		//CALL DEFENSIVE SYSTEMS HERE! - once I get around to actually doing them
		
		//if damage is more than system has structure left - mark rest as overkilling
		if ($effectiveDamage-$effectiveArmor >= $systemHealth){
			$systemDestroyed = true;
			$remainingDamage = $effectiveDamage-$effectiveArmor - $systemHealth;
			$effectiveDamage = $effectiveDamage-$remainingDamage;
		}
				
		//if damage remaining is less than affecting armor - decrease armor appropriately
		if ($effectiveDamage < $effectiveArmor) $effectiveArmor = $effectiveDamage;
		
		//mark damage done
		$damageEntry = new DamageEntry(-1, $target->id, -1, $fireOrder->turn, $this->id, $effectiveDamage, $effectiveArmor, 0, $fireOrder->id, $systemDestroyed, "", $weapon->weaponClass, $shooter->id, $weapon->id);
		$damageEntry->updated = true;
		$this->damage[] = $damageEntry;
		
		$returnArray["dmgDealt"] = $effectiveDamage;
		$returnArray["dmgRemaining"] = $remainingDamage;
		$returnArray["armorPierced"] = $effectiveArmor;
		return $returnArray;
	}

}
