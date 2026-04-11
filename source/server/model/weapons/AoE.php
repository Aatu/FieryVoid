<?php

class AoE extends Weapon
{ //directly tailored for EMine, really - not a generic base class
    public $damageType = "Flash";
    public $weaponClass = "Ballistic";
	public $hextarget = true;

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }


	public function setSystemDataWindow($turn){
		parent::setSystemDataWindow($turn); 
		if (!isset($this->data["Special"])) {
			$this->data["Special"] = '';
		}else{
			$this->data["Special"] .= '<br>';
		}	    
		$dmgDirect = $this->maxDamage;
		$dmgNear = $this->minDamage;
		$this->data["Special"] .= "Ballistic weapon targeted on hex, not unit.";  
		$this->data["Special"] .= "<br>All units on hex hit suffer $dmgDirect damage, all units on nearby hexes $dmgNear. Enormous units suffer half of indicated damage, while in case of flight level units every craft is damaged separately.";  
		$this->data["Special"] .= "<br>Hit chance (of target hex): 75%. Missing mine may scatter d6 hexes (but no further than actual distance traveled) or dissipate completely (40%).";  
        }
	
	
    public function calculateHitBase($gamedata, $fireOrder)
    {
        $fireOrder->needed = round(100 - (100 * 0.25 * 0.4)); //chance of not hitting target hex: 25%; chance of dissipating: 40$ of that
        $fireOrder->updated = true;
    }

    /*October 2017 - Marcin Sawicki - no longer needed
public function calculateHit($gamedata, $fireOrder){
    $fireOrder->needed = round(100-(100*0.25*0.4)); //chance of not hitting target hex: 25%; chance of dissipating: 40$ of that
    $fireOrder->updated = true;
}*/

    /**
     * @param TacGamedata $gamedata
     * @param FireOrder $fireOrder
     */
    public function fire($gamedata, $fireOrder)
    { //sadly here it really has to be completely redefined... or at least I see no option to avoid this
        $this->changeFiringMode($fireOrder->firingMode);//changing firing mode may cause other changes, too!
        $shooter = $gamedata->getShipById($fireOrder->shooterid);

        /** @var MovementOrder $movement */
        $movement = $shooter->getLastTurnMovement($fireOrder->turn);

        $posLaunch = $movement->position;//at moment of launch!!!

        //sometimes player does manage to target ship after all..
        if ($fireOrder->targetid != -1) {
            $targetship = $gamedata->getShipById($fireOrder->targetid);
            //insert correct target coordinates: last turns' target position
            $movement = $targetship->getLastTurnMovement($fireOrder->turn);
            $fireOrder->x = $movement->position->q;
            $fireOrder->y = $movement->position->r;
            $fireOrder->targetid = -1; //correct the error
        }

        $target = new OffsetCoordinate($fireOrder->x, $fireOrder->y);

        //$this->calculateHit($gamedata, $fireOrder); //already calculated!

        $rolled = Dice::d(100);
        $fireOrder->rolled = $rolled;
        if ($rolled > $fireOrder->needed) { //miss!
            $fireOrder->pubnotes .= "Charge dissipates. ";
        } else {//hit!
            $fireOrder->shotshit++;
            if ($rolled > 75) { //deviation!
                $maxdis = $posLaunch->distanceTo($target);
                $dis = Dice::d(6); //deviation distance
                $dis = min($dis, floor($maxdis));
                $direction = Dice::d(6)-1; //deviation direction

                $target = $target->moveToDirection($direction, $dis);

                $fireOrder->pubnotes .= " Deviation from " . $fireOrder->x . ' ' . $fireOrder->y;
                $fireOrder->x = $target->q;
                $fireOrder->y = $target->r;
                $fireOrder->pubnotes .= " to " . $fireOrder->x . ' ' . $fireOrder->y . '. ';
                $fireOrder->pubnotes .= "Shot deviates $dis hexes. ";
            }

            //do damage to ships in range...
            $ships1 = $gamedata->getShipsInDistance($target);
            $ships2 = $gamedata->getShipsInDistance($target, 1);
            foreach ($ships2 as $targetShip) {
                if (isset($ships1[$targetShip->id])) { //ship on target hex!
                    $sourceHex = $posLaunch;
                    $damage = $this->maxDamage;
                } else { //ship at range 1!
                    $sourceHex = $target;
                    $damage = $this->minDamage;
                }
                $this->AOEdamage($targetShip, $shooter, $fireOrder, $sourceHex, $damage, $gamedata);
            }
        }

        $fireOrder->rolled = max(1, $fireOrder->rolled);//Marks that fire order has been handled, just in case it wasn't marked yet!
    } //endof function fire


    public function AOEdamage($target, $shooter, $fireOrder, $sourceHex, $damage, $gamedata)
    {
        if ($target->isDestroyed()) return; //no point allocating
        if ($target->mine) return; //Mines are not damaged by Proximity weapons.
        $damage = $this->getDamageMod($damage, $shooter, $target, $sourceHex, $gamedata);
        $damage -= $target->getDamageMod($shooter, $sourceHex, $gamedata->turn, $this);
        if ($target instanceof FighterFlight) {
            foreach ($target->systems as $fighter) {
                if ($fighter == null || $fighter->isDestroyed()) {
                    continue;
                }
                $this->doDamage($target, $shooter, $fighter, $damage, $fireOrder, $sourceHex, $gamedata, false);
            }
        } else {
            $tmpLocation = $target->getHitSectionPos(Mathlib::hexCoToPixel($sourceHex), $fireOrder->turn);
            $system = $target->getHitSystem($shooter, $fireOrder, $this, $gamedata, $tmpLocation);
            $this->doDamage($target, $shooter, $system, $damage, $fireOrder, null, $gamedata, false, $tmpLocation);
        }
    }

    //only half damage vs Enormous units...
    public function getDamageMod($damage, $shooter, $target, $sourceHex, $gamedata)
    {
        $modifiedDmg = parent::getDamageMod($damage, $shooter, $target, $sourceHex, $gamedata);
        if ($target->Enormous || $target->osat) $modifiedDmg = floor($modifiedDmg / 2);
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
        
            
        public $animation = "ball";
        public $animationColor = array(141, 240, 255);
        public $animationExplosionScale = 1;
		/*
        public $trailColor = array(141, 240, 255);
        public $animationExplosionType = "AoE";
        public $explosionColor = array(141, 240, 255);
        public $projectilespeed = 12;
        public $animationWidth = 10;
        public $trailLength = 10;
	    */
	public $firingModes = array(
		1 => "Energy Mine"
	);

        	    
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $loadingTime = 2, $range = 50){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 4;
            $this->loadingtime = $loadingTime;
            $this->range = $range;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);
        $this->data["Weapon type"] = "Ballistic";
    }

    //getDamage in itself depends on actually hit ship - this function is meaningless here, really!
    public function getDamage($fireOrder)
    {
        return 10;
    }

    //these are important, though!*/
    public function setMinDamage()
    {
        $this->minDamage = 10;
    }

    public function setMaxDamage()
    {
        $this->maxDamage = 30;
    }

} //endof class EnergyMine



/*non-canon weapon for Escalation Wars setting*/
class LightEnergyMine extends AoE{
        public $name = "LightEnergyMine";
        public $displayName = "Light Energy Mine";
		public $iconPath = "EWLightEnergyMine.png";
        public $range = 25;
        public $loadingtime = 2;
        public $ballistic = true;
        public $hextarget = true;
        public $hidetarget = true;
        
        public $flashDamage = true;
        public $priority = 1;
        
        public $animation = "ball";
        public $animationColor = array(141, 240, 255);
        public $animationExplosionScale = 1;

	public $firingModes = array(
		1 => "AoE"
	);

        

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 3;
            if ( $powerReq == 0 ) $powerReq = 3;
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);
        $this->data["Weapon type"] = "Ballistic";
    }

    //getDamage in itself depends on actually hit ship - this function is meaningless here, really!
    public function getDamage($fireOrder)
    {
        return 5;
    }

    //these are important, though!*/
    public function setMinDamage()
    {
        $this->minDamage = 5;
    }

    public function setMaxDamage()
    {
        $this->maxDamage = 10;
    }

} //endof class LightEnergyMine


class CaptorMine extends Weapon{
    public $name = "CaptorMine";
    public $displayName = "Captor Mine";
	public $iconPath = "EWLightEnergyMine.png";
    public $animation = "torpedo";
    public $animationColor = array(141, 240, 255);
    public $animationExplosionScale = 0.25;        
    public $isTargetable = false; //cannot be targeted ever!        
    public $loadingtime = 1;
    public $ballistic = false; //Will be marked true later, but this lets Command Control mines target.
    public $hidetarget = true;
    public $canOffLine = false;
    public $fireControl = array(0, 0, 0); //MODIFIER for weapon fire control!        
    public $damageType = 'Standard';//mode of dealing damage
    public $doNotIntercept = false; //for attacks that are not subject to interception at all - like fields and ramming
    public $uninterceptable = false;
    public $priority = 6;
    public $priorityAF = 5;
    public $firingModes = array(1 => "Captor");
    public $weaponClass = "Ballistic";
    public $range = 0;
    public $distanceRange = 60; //So that shots don't cancel if target moves far away after triggering a mine.    
    private $diceType = 1; //What type of dice are used.
    private $dice = 1; //How many damage dice are there
    protected $damageBonus = 0; //What is flat damage bonus
    public $autoFireOnly = true; // for weapons that should never be fired manually 
    public $canTargetAll = true;
	public $currClass = '';//for front end.       
    public $allocatedRanges = array('Capitals-HCVs' => null, 'LCVs-MCVs' => null, 'Fighters' => null); //Ranges allocated for given ship type 
    public $setRanges = array(); //Ranges allocated for given ship type     
    public $mineSet = false; //For front end, to confirm mine ranges have been manually set.

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $range, $accuracy, $diceType, $dice, $damageBonus){
	//maxhealth and power reqirement are fixed; left option to override with hand-written values
        if ( $maxhealth == 0 ) $maxhealth = 1;
        if ( $powerReq == 0 ) $powerReq = 0;
        //Set min and max damage variables
        $this->diceType = $diceType;
        $this->dice = $dice;
        $this->damageBonus = $damageBonus;   
        $this->range = $range;
        $this->fireControl[0] = $accuracy; 
        $this->fireControl[1] = $accuracy;  
        $this->fireControl[2] = $accuracy;

        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

    public function addToDamageBonus($add){
        $this->damageBonus += $add;             
    }

    public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Range"] = $this->range;       
            foreach($this->allocatedRanges as $shipType=>$range){
                $this->data[' - '.$shipType.' range'] =  $range;
            }         
            $this->data["Special"] = "Attacks as a ballistic weapon during the firing phase against first viable target.";
			$this->data["Special"] .= "<br>Set ranges for different types of enemy on turn that Mine deploys, these cannot then be changed.";	            
			$this->data["Special"] .= "<br>All range are halved against Jammer-equipped units.";											
	}	


	public function doIndividualNotesTransfer(){
	    // Data received in variable individualNotesTransfer, further functions will look for it in currchangedSpec
	    if (is_array($this->individualNotesTransfer)) {
         
	        foreach ($this->individualNotesTransfer as $shipType => $rangeValue) {      
	            $this->setRanges[$shipType] = $rangeValue; //Temporarily fill values to generate notes.
             
	        }
	    }                                	   
	    $this->individualNotesTransfer = array(); // Empty, just in case
	}	    

    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
           
		switch($gameData->phase){
						
				case -1: //Deployment/Pre-Turn phase
					//data returned as allocatedBFCP table, with one value passed per BFCP point in each FCType e.g. 'Fighter' mean +1 in allocatedBFCP['Fighter']
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
 																							
						foreach ($this->setRanges as $shipType => $rangeValue) { //Will always be three keys.  
 												
							$notekey = $shipType;
							$noteHuman = 'Mine Range set';
							$notevalue = $rangeValue;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$notevalue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue         
						}			
		}								
			break;				
		}
	} //endof function generateIndividualNotes
	

    public function onIndividualNotesLoaded($gamedata)
    {
        //Otherwise, what were the points set this turn at end of Initial Orders.
        foreach ($this->individualNotes as $currNote) {	    	
            $shipType = $currNote->notekey;
            $rangeValue = $currNote->notevalue;

            //Increment the value associated with the appropriate key e.g. Fighter, MCV, Capital.
            $this->allocatedRanges[$shipType] = $rangeValue;

        }
                      
        //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
        $this->individualNotes = array();
        
            
    }//endof onIndividualNotesLoaded


    public function beforeFiringOrderResolution($gamedata){
        
        if($this->isOfflineOnTurn()) return; //Mine weapon deactivated.

        $mine = $this->getUnit();
        if($mine->getCommandControl()){            
            $firingOrders = $this->getFireOrders($gamedata->turn);
            
            $hasFireOrder = null;
                    foreach ($firingOrders as $fireOrder) { 
                        if ($fireOrder->type == 'normal' || $fireOrder->type == 'ballistic') { 
                        $hasFireOrder = $fireOrder;
                        break; //no need to search further
                        }
                    }    			
                    
            if($hasFireOrder !== null){
                $this->ballistic = true; //Mark as ballistic so fireOrder is processed.	                
                $this->destroyMine($mine, $gamedata);                
                return; //Has a manual fire order, end of work
            }    
        }
        if($mine->isDestroyed()) return; //Mine is destroyed.
		$deployTurn = $mine->getTurnDeployed($gamedata);
		if($deployTurn > $gamedata->turn) return;  //Ship not deployed yet, don't fire weapon!

		// Mines are stationary — their position is always their deploy-move position.
		// We deliberately avoid getHexPos() because mines have no subsequent movement
		// records and getHexPos() would crash on a null movement.
		$minePosition = null;
		foreach ($mine->movement as $move) {
			if ($move->type === 'deploy') {
				$minePosition = $move->position;
				break;
			}
		}
		if ($minePosition === null) return; // Mine has no deploy move yet, can't be detected	

        $IFFSystem = $mine->getIFFSystem();
		
    	if($this->isDestroyed($gamedata->turn)) return;//Pulsar Mine is destroyed
		if($this->isOfflineOnTurn($gamedata->turn)) return; //Pulsar Mine is offline

    	$allShips = $gamedata->ships;  
    	$relevantUnits = array();

		//Make a list of relevant ships e.g. this ship and enemy fighters in the game.
		foreach($allShips as $ship){
            if ($ship instanceof Terrain) continue;
            if ($ship->mine) continue;  
            if ($ship->base || $ship instanceof OSAT) continue; //They are movement activated.          
			if ($ship->isDestroyed()) continue;		
			if ($ship->id == $mine->id) continue; // Mine should never target itself!
			if ($ship->team == $mine->team && $IFFSystem) continue;	//Ignore friendly units if IFF purchased.	
			if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore units that are not deployed yet!			
			$relevantUnits[] = $ship;			
		}
	
        $mineTarget = null;

		//Now check if any enemy units entered range and attack first one
		$mineTarget = $this->checkForValidTargets($relevantUnits, $mine, $minePosition, $gamedata);	        	

		if ($mineTarget !== null) { // Check if we found a valid target

			$newFireOrder = new FireOrder(
				-1, "ballistic", $mine->id, $mineTarget->id,
				$this->id, -1, $gamedata->turn, 1, 
				0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
				0,0,$this->weaponClass,-1 //X, Y, damageclass, resolutionorder
			);		

			$newFireOrder->addToDB = true;
			$this->fireOrders[] = $newFireOrder;
            $this->ballistic = true; //Mark as ballistic so fireOrder is processed.	            							
		
            //Now create a damage order to destroy mine.
            $this->destroyMine($mine, $gamedata);  						
		}        

	} //endof beforeFiringOrderResolution

    private function destroyMine($mine, $gamedata){
        //Now create a damage order to destroy mine.
        $structure = $mine->getStructureSystem(0);            
		$damageEntry = new DamageEntry(-1, $mine->id, -1, $gamedata->turn, $structure->id, 1, 0, 0, -1, true, false, "", "SelfDestruct");
		$damageEntry->updated = true;
		$this->damage[] = $damageEntry;	
    }   


	public function fire($gamedata, $fireOrder){    
        $this->ballistic = true; //Mark as ballistic so fireOrder is processed.	 		
		parent::fire($gamedata, $fireOrder);
	}

	private function checkForValidTargets($relevantUnits, $mine, $minePosition, $gamedata){

        // Sort units by ascending initiative (lower value = moved first this turn).
        usort($relevantUnits, function($a, $b) {
            $iniA = ($a->iniative ?? 0) + ($a->iniativeadded ?? 0);
            $iniB = ($b->iniative ?? 0) + ($b->iniativeadded ?? 0);
            return $iniA <=> $iniB;
        });

		foreach($relevantUnits as $unit){//Now look through relevant ships and take appropriate action.				
										    
			$unitStartLoc = $unit->getLastTurnMovement($gamedata->turn);
            if($unitStartLoc == null) continue;
								
			//Check if unit can be attacked in its starting position	
			if($this->checkTargetConditions( $minePosition, $unitStartLoc->position,$gamedata, $mine, $unit)){
                return $unit;                   
			}	

			//Now check other movements in turn	
    		foreach($unit->movement as $unitMove){
				if($unitMove->turn == $gamedata->turn){
	                // Only interested in moves where unit enters a NEW hex!
	                if ($unitMove->type == "move" || $unitMove->type == "slipleft" || $unitMove->type == "slipright") {

                        if($this->checkTargetConditions($minePosition, $unitMove->position, $gamedata,  $mine, $unit)) {
                            return $unit;
                       }else{
                            continue;
                        }
                    } else {
                    }    		 		 		
				}
			}					
		}			

	    return null; 		
		
	}//end of checkForValidTargets    


	private function checkTargetConditions($minePosition, $targetPostion, $gamedata, $mine, $target){
		
		$distance =	mathlib::getDistanceHex($minePosition, $targetPostion);//Compare starting positions.						
        $effectiveRange = $this->range; //Start with max range.

        $shipType = 'Capitals-HCVs'; //Default as Captials.
        $FCIndex = $target->getFireControlIndex(); //Get FC array index of potential target.
        if($FCIndex == 0){ //Fighters
            $shipType = 'Fighters';          
        }else if($FCIndex == 1){ //LCV or MCV
            $shipType = 'LCVs-MCVs';                    
        }
   
        if($this->allocatedRanges[$shipType] !== null) $effectiveRange = $this->allocatedRanges[$shipType]; //Find and set appropriate range for this type of target.

        //take into account jammer effects.                    
		$jammerValue = $target->getSpecialAbilityValue("Jammer", array("shooter" => $mine, "target" => $target));
		if($jammerValue > 0) $effectiveRange = floor($effectiveRange / 2);	
	    if ($distance > $effectiveRange) return false; //Not within range, skip LoS check and return false.

        $loSBlocked = false;
        if (!empty($gamedata->blockedHexes)) { 
		    $loSBlocked = $this->isLoSBlocked($minePosition, $targetPostion, $gamedata); //Returns true is LoS blocked
        }
		if($loSBlocked) return false; //LoS Blocked

		return true;
	}	

    //getDamage in itself depends on actually hit ship - this function is meaningless here, really!
    public function getDamage($fireOrder)
    {
        return Dice::d($this->diceType, $this->dice) + $this->damageBonus;
    }

    //these are important, though!*/
    public function setMinDamage()
    {
        $this->minDamage = $this->dice + $this->damageBonus;
    }

    public function setMaxDamage()
    {
        $this->maxDamage = ($this->diceType * $this->dice) + $this->damageBonus ;
    }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->allocatedRanges = $this->allocatedRanges;      			                             
        return $strippedSystem;
    }
	    

} //endof class CaptorMine

class ProximityMine extends Weapon implements SpecialAbility{
    public $name = "ProximityMine";
    public $displayName = "Proximity Mine";
	public $iconPath = "EWLightEnergyMine.png";
    public $animation = "ball";
    public $animationColor = array(141, 240, 255);
    public $animationExplosionScale = 1;        
    public $isTargetable = false; //cannot be targeted ever!  
	public $specialAbilities = array("PreFiring"); 
	public $specialAbilityValue = true; //so it is actually recognized as special ability! 
    public $doNotIntercept = true;
    public $uninterceptable = true;             
    public $loadingtime = 1;
    public $preFires = false; //Will be marked true by Command Controller
    public $hextarget = false; //Actually yes.
    public $hidetarget = true;
    public $canTargetAll = true;
    public $canOffLine = false;
    public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!        
    public $damageType = 'Standard';//mode of dealing damage
    public $weaponClass = 'Ballistic';//weapon class
    public $priority = 6;
    public $priorityAF = 5;
    public $firingModes = array(1 => "Proximity");
    public $range = 0;
    public $distanceRange = 10; //So that shots don't cancel if target moves far away after triggering a mine.    
    private $diceType = 1; //What type of dice are used.
    private $dice = 1; //How many damage dice are there
    protected $damageBonus = 0; //What is flat damage bonus
    public $autoFireOnly = true; // for weapons that should never be fired manually 
	public $currClass = '';//for front end.       
    public $allocatedShipTypes = array('Capitals-HCVs' => true, 'LCVs-MCVs' => true, 'Fighters' => true); //Types allocated for ships to attack 
    public $setShipTypes = array(); //Ranges allocated for given ship type from front end.    
    public $mineSet = false; //For front end, to confirm mine ranges have been manually set.
    protected $autoHit = true;
    public $potentialTargets = array(); //Tracks possible targets for Command Controller enhancement.



    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $range, $diceType, $dice, $damageBonus){
	//maxhealth and power reqirement are fixed; left option to override with hand-written values
        if ( $maxhealth == 0 ) $maxhealth = 1;
        if ( $powerReq == 0 ) $powerReq = 0;
        //Set min and max damage variables
        $this->diceType = $diceType;
        $this->dice = $dice;
        $this->damageBonus = $damageBonus;   
        $this->range = $range;

        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

	public function getSpecialAbilityValue($args)
    {
		return $this->specialAbilityValue;
	}


    public function addToDamageBonus($add){
        $this->damageBonus += $add;             
    }

    public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
            $this->data["Range"] = $this->range;
            foreach($this->allocatedShipTypes as $shipType=>$range){
                $this->data[' - Attack '.$shipType] =  $range;
            }         
            $this->data["Special"] = "Automatically damages first viable target at the start of Firing Phase.";
			$this->data["Special"] .= "<br>Set types of enemies that will trigger mine explosion on the turn that Mine deploys, these cannot then be changed.";	            											
	}	


	public function doIndividualNotesTransfer(){
	    // Data received in variable individualNotesTransfer, further functions will look for it in currchangedSpec
	    if (is_array($this->individualNotesTransfer)) {          
	        foreach ($this->individualNotesTransfer as $shipType => $willAttack) {       
	            $this->setShipTypes[$shipType] = $willAttack; //Temporarily fill values to generate notes.             
	        }
	    }                                	   
	    $this->individualNotesTransfer = array(); // Empty, just in case
	}	    

    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
           
		switch($gameData->phase){
						
				case -1: //Deployment/Pre-Turn phase
					//data returned as allocatedBFCP table, with one value passed per BFCP point in each FCType e.g. 'Fighter' mean +1 in allocatedBFCP['Fighter']
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!  							
																
						foreach ($this->setShipTypes as $shipType => $willAttack) { //Will always be three keys. 												
							$notekey = $shipType;
							$noteHuman = 'Attack type set';
							$notevalue = $willAttack ? 1 : 0;
							$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$notevalue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue         
						}			
		}								
			break;				
		}
	} //endof function generateIndividualNotes
	

    public function onIndividualNotesLoaded($gamedata)
    {      
        foreach ($this->individualNotes as $currNote) {	    	
            if ($currNote->notekey === 'potentialTarget') {
                if ($currNote->turn == $gamedata->turn) {
                    $this->potentialTargets[(int)$currNote->notevalue] = (int)$currNote->notekey_human;
                }
                continue;
            }

            $shipType = $currNote->notekey;
            $willAttack = $currNote->notevalue;
            $this->allocatedShipTypes[$shipType] = ($willAttack == 1 || $willAttack === 'true' || $willAttack === true);
        }
        $this->individualNotes = array();
    }


    private function isValidShipType($ship){

        $FCIndex = $ship->getFireControlIndex(); //Get FC array index of potential target.
        if($FCIndex == 0 && $this->allocatedShipTypes['Fighters'] == true) return true;
        if($FCIndex == 1 && $this->allocatedShipTypes['LCVs-MCVs'] == true) return true;
        if($FCIndex == 2 && $this->allocatedShipTypes['Capitals-HCVs'] == true) return true;

        return false;
    }

    public function beforePreFiringOrderResolution($gamedata){
        
        if($this->isOfflineOnTurn()) return; //Mine weapon deactivated.

        $mine = $this->getUnit();
        if($mine->getCommandControl()){            
            $firingOrders = $this->getFireOrders($gamedata->turn);
            
            $hasFireOrder = null;
                    foreach ($firingOrders as $fireOrder) { 
                        if ($fireOrder->type == 'prefiring') { 
                        $hasFireOrder = $fireOrder;
                        break; //no need to search further
                        }
                    }    			
                    
            if($hasFireOrder !== null){
                $this->ballistic = true; //Mark as ballistic so fireOrder is processed.	                 
                $this->destroyMine($mine, $gamedata);                
                return; //Has a manual fire order, end of work
            }    
        }            

        if($mine->isDestroyed()) return; //Mine is destroyed.
		$deployTurn = $mine->getTurnDeployed($gamedata);
		if($deployTurn > $gamedata->turn) return;  //Ship not deployed yet, don't fire weapon!

		// Mines are stationary — their position is always their deploy-move position.
		// We deliberately avoid getHexPos() because mines have no subsequent movement
		// records and getHexPos() would crash on a null movement.
		$minePosition = null;
		foreach ($mine->movement as $move) {
			if ($move->type === 'deploy') {
				$minePosition = $move->position;
				break;
			}
		}
		if ($minePosition === null) return; // Mine has no deploy move yet, can't be detected	

        $IFFSystem = $mine->getIFFSystem();
		
    	if($this->isDestroyed($gamedata->turn)) return;//Pulsar Mine is destroyed
		if($this->isOfflineOnTurn($gamedata->turn)) return; //Pulsar Mine is offline

    	$allShips = $gamedata->ships;  
    	$relevantUnits = array();

		//Make a list of relevant ships e.g. this ship and enemy fighters in the game.
		foreach($allShips as $ship){
            if ($ship instanceof Terrain) continue;
            if ($ship->mine) continue;  
            if ($ship->base || $ship instanceof OSAT) continue; //They are movement activated.          
			if ($ship->isDestroyed()) continue;		
            if (!$this->isValidShipType($ship)) continue;
			if ($ship->id == $mine->id) continue; // Mine should never target itself!
			if ($ship->team == $mine->team && $IFFSystem) continue;	//Ignore friendly units if IFF purchased.	
			if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore units that are not deployed yet!			
			$relevantUnits[] = $ship;			
		}
	
        $targetUnit = null;
        $chosenLocation = 0;
		//Now check if any enemy units entered range and attack first one
		$targetUnit = $this->checkForValidTargets($relevantUnits, $mine, $minePosition, $gamedata);	        	

		if ($targetUnit !== null) { // Check if we found a valid target
            $mineTarget = $targetUnit['unit'];
            $chosenLocation = $targetUnit['location'];

			$newFireOrder = new FireOrder(
				-1, "prefiring", $mine->id, $mineTarget->id,
				$this->id, -1, $gamedata->turn, 1, 
				0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
				0,0,'AutoProximity',-1 //X, Y, damageclass, resolutionorder
			);		
            $newFireOrder->chosenLocation = $chosenLocation;

			$newFireOrder->addToDB = true;
			$this->fireOrders[] = $newFireOrder;
            $this->ballistic = true; //Mark as ballistic so fireOrder is processed.							
		
            //Now create a damage order to destroy mine.
            $this->destroyMine($mine, $gamedata);
            
		}        

	} //endof beforePreFiringOrderResolution


    private function destroyMine($mine, $gamedata){
        //Now create a damage order to destroy mine.
        $structure = $mine->getStructureSystem(0);            
		$damageEntry = new DamageEntry(-1, $mine->id, -1, $gamedata->turn, $structure->id, 1, 0, 0, -1, true, false, "", "SelfDestruct");
		$damageEntry->updated = true;
		$this->damage[] = $damageEntry;	
    }             

    public function calculateHitBase($gamedata, $fireOrder)
    {
        $fireOrder->needed = 100; //Always hits if valid target found
        $fireOrder->updated = true;
    }    


	public function fire($gamedata, $fireOrder){ 
           
        if($fireOrder->damageclass !== 'AutoProximity'){
            if (isset($this->potentialTargets[$fireOrder->targetid])) {             
                $fireOrder->chosenLocation = $this->potentialTargets[$fireOrder->targetid];
            }
        }       
             
		parent::fire($gamedata, $fireOrder);
	}

	private function checkForValidTargets($relevantUnits, $mine, $minePosition, $gamedata){

        // Sort units by ascending initiative (lower value = moved first this turn).
        usort($relevantUnits, function($a, $b) {
            $iniA = ($a->iniative ?? 0) + ($a->iniativeadded ?? 0);
            $iniB = ($b->iniative ?? 0) + ($b->iniativeadded ?? 0);
            return $iniA <=> $iniB;
        });

		foreach($relevantUnits as $unit){//Now look through relevant ships and take appropriate action.				
										    
			$unitStartLoc = $unit->getLastTurnMovement($gamedata->turn);
            if($unitStartLoc == null) continue;
            
            $previousPosition = $unitStartLoc->position;
            $previousFacing = $unitStartLoc->getFacingAngle();
								
			//Check if unit can be attacked in its starting position	
			if($this->checkTargetConditions( $minePosition, $unitStartLoc->position,$gamedata, $mine, $unit)){
                $relativeBearing = $this->getMineBearing($minePosition, $unitStartLoc->position, $unit, $previousFacing);
                $location = $this->getHitSection($relativeBearing, $unit);
                return ['unit' => $unit, 'location' => $location];
                //return $unit;                   
			}	

			//Now check other movements in turn	
    		foreach($unit->movement as $unitMove){
				if($unitMove->turn == $gamedata->turn){
	                // Only interested in moves where unit enters a NEW hex!
	                if ($unitMove->type == "move" || $unitMove->type == "slipleft" || $unitMove->type == "slipright") {

                        if($this->checkTargetConditions($minePosition, $unitMove->position, $gamedata,  $mine, $unit)) {
                           //get bearing / location and return that too		    			
                           $relativeBearing = $this->getMineBearing($minePosition, $previousPosition, $unit, $previousFacing);
                           $location = $this->getHitSection($relativeBearing, $unit);
                           return ['unit' => $unit, 'location' => $location];
                           //return $unit;
                       }else{
                            $previousPosition = $unitMove->position;
                            $previousFacing = $unitMove->getFacingAngle();
                            continue;
                        }
                    } else {
                        $previousPosition = $unitMove->position;
                        $previousFacing = $unitMove->getFacingAngle();
                    }    		 		 		
				}
			}					
		}			

	    return null; 		
		
	}//end of checkForValidTargets    

    
	private function getMineBearing($minePosition, $shipPosition, $ship, $facing){
		$relativeBearing = 0;	
		$oPos = mathlib::hexCoToPixel($shipPosition);//Convert to pixel format		
		$tPos = mathlib::hexCoToPixel($minePosition); //Convert to pixel format
				
		$compassHeading = mathlib::getCompassHeadingOfPoint($oPos, $tPos);//Get heading using pixel formats.
        $relativeBearing =  Mathlib::addToDirection($compassHeading, -$facing);//relative bearing, compass - current facing.
       
        if( Movement::isRolled($ship) ){ //if ship is rolled, mirror relative bearing.
            if( $relativeBearing !== 0 ) { //mirror of 0 is 0
                $relativeBearing = 360-$relativeBearing;
            }
        }        

		return round($relativeBearing);//Round and return!
	}

	private function getHitSection($relativeBearing, $target) {
        if (!method_exists($target, 'getLocations')) return 0; // Target has no locations (e.g. FighterFlight sometimes)

		foreach ($target->getLocations() as $location) {
			$min = $location["min"];
			$max = $location["max"];
			
			// Normal range check
			if ($min < $max && $relativeBearing >= $min && $relativeBearing < $max) {
				return $location["loc"];
			}
			
			// Wrap-around range check (e.g., 330-30)
			if ($min > $max && ($relativeBearing >= $min || $relativeBearing < $max)) {
				return $location["loc"];
			}
		}
		
		return 0; // Should not happen but return default if so.
	} 
    


	private function checkTargetConditions($minePosition, $targetPostion, $gamedata, $mine, $target){
		
		$distance =	mathlib::getDistanceHex($minePosition, $targetPostion);//Compare starting positions.						
        $effectiveRange = $this->range; 
		
	    if ($distance > $effectiveRange) return false; //Not within range, skip LoS check and return false.

        $loSBlocked = false;
        if (!empty($gamedata->blockedHexes)) { 
		    $loSBlocked = $this->isLoSBlocked($minePosition, $targetPostion, $gamedata); //Returns true is LoS blocked
        }
		if($loSBlocked) return false; //LoS Blocked

		return true;
	}	

    //Called during movementPhase->advance() when mine has command controller enhancement.  Checks for ships in range and triggers PreFiring phase to manually target.
	public function checkForPreFiringTargets($mine, $gamedata){
		
        if ($mine->getTurnDeployed($gamedata) > $gamedata->turn) return false; // Not deployed yet
        if($mine->isDestroyed()) return false;
        $newNotes = array();

        $minePos = null;
        foreach ($mine->movement as $move) {
            if ($move->type === 'deploy') {
                $minePos = $move->position;
                break;
            }
        }
        if ($minePos === null) return false;

        $IFFSystem = $mine->getIFFSystem();        

        if (! ($minePos instanceof OffsetCoordinate)) {
            throw new Exception("only OffsetCoordinate supported");
        }

        $this->potentialTargets = array();
        $relevantUnits = array();

        foreach ($gamedata->ships as $ship){
            if ($ship instanceof Terrain) continue;
            if ($ship->mine) continue;  
            if ($ship->base || $ship instanceof OSAT) continue; //They are movement activated.          
			if ($ship->isDestroyed()) continue;		
            if (!$this->isValidShipType($ship)) continue;
			if ($ship->id == $mine->id) continue; // Mine should never target itself!
			if ($ship->team == $mine->team && $IFFSystem) continue;	//Ignore friendly units if IFF purchased.	
			if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore units that are not deployed yet!	
            $relevantUnits[] = $ship;
        }            

		foreach($relevantUnits as $unit){//Now look through relevant ships and take appropriate action.				
										    
			$unitStartLoc = $unit->getLastTurnMovement($gamedata->turn);
            if($unitStartLoc == null) continue;
            
            $previousPosition = $unitStartLoc->position;
            $previousFacing = $unitStartLoc->getFacingAngle();
								
			//Check if unit can be attacked in its starting position	
			if($this->checkTargetConditions( $minePos, $unitStartLoc->position,$gamedata, $mine, $unit)){
                $relativeBearing = $this->getMineBearing($minePos, $unitStartLoc->position, $unit, $previousFacing);
                $location = (int)$this->getHitSection($relativeBearing, $unit);
                $this->potentialTargets[$unit->id] = $location;
                $newNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,5,$mine->id,$this->id,'potentialTarget',$location,$unit->id);
                
                continue; // Found first entry for this ship, move to next ship
			}	

			//Now check other movements in turn	
    		foreach($unit->movement as $unitMove){
				if($unitMove->turn == $gamedata->turn){
	                // Only interested in moves where unit enters a NEW hex!
	                if ($unitMove->type == "move" || $unitMove->type == "slipleft" || $unitMove->type == "slipright") {

                        if($this->checkTargetConditions($minePos, $unitMove->position, $gamedata,  $mine, $unit)) {
                           //get bearing / location and return that too		    			
                           $relativeBearing = $this->getMineBearing($minePos, $previousPosition, $unit, $previousFacing);
                           $location = (int)$this->getHitSection($relativeBearing, $unit);
                           $this->potentialTargets[$unit->id] = $location;
                           $newNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gamedata->turn,5,$mine->id,$this->id,'potentialTarget',$location,$unit->id);
                           break; // Found first entry for this ship, stop checking movements
                       }else{
                            $previousPosition = $unitMove->position;
                            $previousFacing = $unitMove->getFacingAngle();
                        }
                    } else {
                        $previousPosition = $unitMove->position;
                        $previousFacing = $unitMove->getFacingAngle();
                    }    		 		 		
				}
			}					
		}
        
        foreach($newNotes as $note){
            Manager::insertIndividualNote($note);
        }        

		return count($this->potentialTargets) > 0;
	}

  
    public function getDamage($fireOrder)
    {
        return Dice::d($this->diceType, $this->dice) + $this->damageBonus;
    }

    public function setMinDamage()
    {
        $this->minDamage = $this->dice + $this->damageBonus;
    }

    public function setMaxDamage()
    {
        $this->maxDamage = ($this->diceType * $this->dice) + $this->damageBonus ;
    }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->allocatedShipTypes = $this->allocatedShipTypes;      
        $strippedSystem->autoHit = $this->autoHit; 	 
        if (isset($this->potentialTargets) && !empty($this->potentialTargets)) {
            $strippedSystem->potentialTargets = $this->potentialTargets;
        } 	                         			                             
        $strippedSystem->potentialTargets = $this->potentialTargets;
        return $strippedSystem;
    }
	    
} //endof class ProximityMine
