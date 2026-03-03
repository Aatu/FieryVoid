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

        	    
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
		//maxhealth and power reqirement are fixed; left option to override with hand-written values
            if ( $maxhealth == 0 ) $maxhealth = 5;
            if ( $powerReq == 0 ) $powerReq = 4;
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
    public $animation = "bolt";
    public $animationColor = array(141, 240, 255);
    public $animationExplosionScale = 0.25;        
    public $isTargetable = false; //cannot be targeted ever!        
    public $loadingtime = 1;
    public $ballistic = true;
    public $hidetarget = true;
    public $canOffline = true;
    public $fireControlMod = array(0, 0, 0); //MODIFIER for weapon fire control!        
    public $damageType = 'Standard';//mode of dealing damage
    public $weaponClass = 'Ballistic';//weapon class
    public $priority = 6;
    public $priorityAF = 5;
    public $firingModes = array(1 => "Captor");
    public $range = 0;
    private $diceType = 1; //What type of dice are used.
    private $dice = 1; //How many damage dice are there
    private $damageBonus = 0; //What is flat damage bonus
    private $locationHit = 0;   

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

    public function beforePreFiringOrderResolution($gamedata){
        
        if($this->isOfflineOnTurn()) return; //Mine weapon deactivated.

        $mine = $this->getUnit();
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

		//Make a list of relelvant ships e.g. this ship and enemy fighters in the game.
		foreach($allShips as $ship){
            if ($ship instanceof Terrain) continue;
            if ($ship->mine) continue;            
			if ($ship->isDestroyed()) continue;		
			if ($ship->id == $mine->id) continue; // Mine should never target itself!
			if ($ship->team == $mine->team && $IFFSystem) continue;	//Ignore friendly units if IFF purchased.	
			if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;  //Ignore fighters that are not deployed yet!			
			$relevantUnits[] = $ship;			
		}
	
        $mineTarget = null;
        $chosenLocation = 0;
		//Now check if any enemy units entered range and attack first one
		$targetData = $this->checkForValidTargets($relevantUnits, $mine, $minePosition, $gamedata);	

		if ($targetData !== null) { // Check if we found a valid target
            $mineTarget = $targetData['unit'];
            $chosenLocation = $targetData['location'];

			$newFireOrder = new FireOrder(
				-1, "prefiring", $mine->id, $mineTarget->id,
				$this->id, -1, $gamedata->turn, 1, 
				0, 0, 1, 0, 0, //needed, rolled, shots, shotshit, intercepted
				0,0,$this->weaponClass,-1 //X, Y, damageclass, resolutionorder
			);		
            $newFireOrder->chosenLocation = $chosenLocation;
            $this->locationHit = $chosenLocation;
//Debug::log("newFireOrder->chosenLocation " . $newFireOrder->chosenLocation);
//Debug::log("this->locationHit " . $this->locationHit);

			$newFireOrder->addToDB = true;
			$this->fireOrders[] = $newFireOrder;
		    $newFireOrder->pubnotes .= " Mine attacks a target. ";								
		
            //Now create a damage order to destroy mine.
            $structure = $mine->getStructureSystem(0);            
			$damageEntry = new DamageEntry(-1, $mine->id, -1, $gamedata->turn, $structure->id, 1, 0, 0, -1, true, false, "", "SelfDestruct");
			$damageEntry->updated = true;
			$this->damage[] = $damageEntry;							
		}        

	} //endof beforePreFiringOrderResolution

	public function fire($gamedata, $fireOrder){
//Debug::log("fireOrder->chosenLocation1 " . $fireOrder->chosenLocation);        
		$fireOrder->chosenLocation = $this->locationHit;
//Debug::log("fireOrder->chosenLocation2 " . $fireOrder->chosenLocation);          
		
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
			if($this->checkTargetConditions( $minePosition, $unitStartLoc->position,$gamedata)){
                   $relativeBearing = $this->getMineBearing($minePosition, $unitStartLoc->position, $unit, $previousFacing);
                   $location = $this->getHitSection($relativeBearing, $unit);
                   return ['unit' => $unit, 'location' => $location];
			}	

			//Now check other movements in turn	
    		foreach($unit->movement as $unitMove){
				if($unitMove->turn == $gamedata->turn){
	                // Only interested in moves where unit enters a NEW hex!
	                if ($unitMove->type == "move" || $unitMove->type == "slipleft" || $unitMove->type == "slipright") {

                        if($this->checkTargetConditions($minePosition, $unitMove->position, $gamedata)) {
                           //get bearing / location and return that too		    			
                            $relativeBearing = $this->getMineBearing($minePosition, $previousPosition, $unit, $previousFacing);
                            $location = $this->getHitSection($relativeBearing, $unit);
                            return ['unit' => $unit, 'location' => $location];
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



	private function checkTargetConditions($minePosition, $targetPostion, $gamedata){
		
		$distance =	mathlib::getDistanceHex($minePosition, $targetPostion);//Compare starting positions.						
		
	    if ($distance > $this->range) return false; //Not within range, skip LoS check and return false.

        //Captor Mines 'launch' like other ballistics so should obey LoS?
		$loSBlocked = $this->isLoSBlocked($minePosition, $targetPostion, $gamedata); //Returns true is LoS blocked
		if($loSBlocked) return false; //LoS Blocked

		return true;
	}	


    public function setSystemDataWindow($turn)
    {
        parent::setSystemDataWindow($turn);
        $this->data["Special"] = ".";
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
        $this->maxDamage = ($this->diceType * $this->dice) * $this->damageBonus ;
    }

} //endof class CaptorMine