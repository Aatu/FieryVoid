<?php

//Only one ally targeting Weapon right now, but potential to grow!!!

class AbbaiShieldProjector extends Weapon implements DefensiveSystem{        
	public $name = "abbaiShieldProjector";
	public $displayName = "Shield Projector";
	public $iconPath = "shieldProjector.png";

	public $damageType = "Raking"; //To prevent called shots
	public $weaponClass = "Support";

	public $uninterceptable = true; 
	public $doNotIntercept = true;
	public $priority = 1;
		
    public $useOEW = false;	
	public $noLockPenalty = false;	        
		
	public $loadingtime = 1;	
    public $fireControl = array(0, 0, 0); //No fire control per se, but gets automatic +3 points.		
		
	public $animation = "bolt";
	public $animationColor = array(150, 150, 220);
		
	public $output = 0;
	public $outputDisplay = ''; //if not empty - overrides default on-icon display text		
	public $animationExplosionScale = 0.4; //single hex explosion
	public $repairPriority = 5;		
	public $ballistic = true; //Fires during Initial Orders
		
    //defensive system
    public $startArc = 0;
    public $endArc = 0;
    public $defensiveSystem = true;
    public $tohitPenalty = 0;
    public $damagePenalty = 0;
    public $rangePenalty = 0;
    public $range = 10; //Let it target double it's effective range.
    public $canOffLine = true;    
	
	public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?
    		
	public $firingModes = array(
			1 => "Shield Projector"
		);

	protected $autoHit = true;//To show 100% hit chance in front end.
	protected $canTargetAllies = true; //To allow front end to target allies.
	private $outOfRange	= false; //To track when a target moves out of range by Firing phase.
		
	private static $shieldProjected = array();	//To track and prevent multiple Shield Projectors applying to same target.	
	 
	 
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $shieldFactor){
		if ( $maxhealth == 0 ) $maxhealth = 9;
        if ( $powerReq == 0 ) $powerReq = 0;                           
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $this->startArc = $startArc;       	
        $this->endArc = $endArc;
        $this->output = $shieldFactor;
 		$this->outputDisplay = $this->output;         
    }

    protected $possibleCriticals = array(
            16=>"OutputReduced1",
            20=>"DamageReductionRemoved",
            25=>array("OutputReduced1", "DamageReductionRemoved"));

		public function getOutput()
		{
			return $this->output;			
		}

    private function checkIsFighterUnderShield($target, $shooter, $weapon){
	if(!($shooter instanceof FighterFlight)) return false; //only fighters may fly under shield!
	if($weapon && $weapon->ballistic) return false; //fighter missiles may NOT fly under shield
        $dis = mathlib::getDistanceOfShipInHex($target, $shooter);
        if ( $dis == 0 ){ //If shooter are fighers and range is 0, they are under the shield
            return true;
        }
        return false;
    }
    
    public function getDefensiveType()
    {
        return "Shield";
    }
    
    public function getDefensiveHitChangeMod($target, $shooter, $pos, $turn, $weapon){
		//Cannot shield if destroyed, offline or has fired in Initial Orders phase.
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn() || $this->getTurnsloaded() < 1)
            return 0;
        
        if ($this->checkIsFighterUnderShield($target, $shooter, $weapon))
            return 0;

        $output = $this->output;
        $output += $this->outputMod; //outputMod itself is negative!
        return $output;
    }
    
    public function getDefensiveDamageMod($target, $shooter, $pos, $turn, $weapon){
		//Cannot shield if destroyed, offline or has fired in Initial Orders phase.
        if($this->isDestroyed($turn-1) || $this->isOfflineOnTurn() || $this->getTurnsloaded() < 1)
            return 0;
        
        if ($this->checkIsFighterUnderShield($target, $shooter, $weapon))
            return 0;
        
        if ($this->hasCritical('DamageReductionRemoved'))
            return 0;
        
        $output = $this->output;
        $output += $this->outputMod; //outputMod itself is negative!
        return $output;
    }
		    		
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
		$damageReduction = $this->output;
		$profileReduction = $this->output *5;
		$this->data["Special"] = "Can be fired at allied units in range to boost their Gravitic Shields by $damageReduction."; 
		$this->data["Special"] .= "<br>Allied unit MUST be within 5 hexes by Firing Phase, for Shield Projector to assist it.";
		$this->data["Special"] .= "<br>Allied units hit by multiple Shield Projectors will only gain the shield boost once.";		
		$this->data["Special"] .= "<br>When not fired, acts as Gravitic Shield, reducing damage by $damageReduction and hit chance by $profileReduction.";
		$this->data["Special"] .= "<br>When acting as Gravitic Shield, is ignored by fighter direct fire at range 0 (fighters flying under shields)."; 
	}	


	public function beforeFiringOrderResolution($gamedata)
	{
	    // Get all firing orders for the current turn
	    $weaponFiringOrders = $this->getFireOrders($gamedata->turn);

	    $weaponFireOrder = null;

	    // Loop through firing orders to find the first ballistic type
	    foreach ($weaponFiringOrders as $fireOrder) { 
	        if ($fireOrder->type == 'ballistic') { 
	            $weaponFireOrder = $fireOrder;
	            break; // No need to search further
	        }
	    }

	    // If no appropriate fire order, end the function
	    if ($weaponFireOrder == null) return;

	    // Get the target ship by ID
		$ship = $this->getUnit();
	    $target = $gamedata->getShipById($weaponFireOrder->targetid);
//		$targetBearing = $ship->getBearingOnUnit($target); //And still in Arc!
			    
//        if (Mathlib::getDistanceHex($target, $ship ) > 5 || !Mathlib::isInArc($targetBearing, $this->startArc, $this->endArc)){ //Target unit must be within 5 hexes by Firing phase.
		if (Mathlib::getDistanceHex($target, $ship ) > 5){ //Target unit must be within 5 hexes by Firing phase.
			$this->outOfRange = true;	        
        	return;	    
		}
		
	    // Check if the target is already engaged with sufficient shield projected
	    if (isset(AbbaiShieldProjector::$shieldProjected[$target->id]) && (AbbaiShieldProjector::$shieldProjected[$target->id] >= $this->output)) return;

	    // No effect on enormous targets, mark as engaged and return
	    if ($target->Enormous) {
	        AbbaiShieldProjector::$shieldProjected[$target->id] = true;
	        return;
	    }

	    // If the target is a FighterFlight, find all shields in the fighter flight
	    if ($target instanceof FighterFlight){
	    	if($target->faction = "Abbai Matriarchate") {//Only boost Abbai fighters, since FtrShield is generic.
		        foreach ($target->systems as $ftr) {
		            foreach ($ftr->systems as $sys) {
		                if ($sys instanceof FtrShield) {
		                    $sys->output += $this->output;	                    
		                }
		            }
		        }
			}    
	    } else { // For ships
	        foreach ($target->systems as $system) {
	            if ($system instanceof GraviticShield) {
	                $system->output += $this->output;
	            }
	        }
	    }

	    // Mark the target as engaged
	    AbbaiShieldProjector::$shieldProjected[$target->id] = $this->output;
	    
	} // end of beforeFiringOrderResolution 

		
	public function calculateHitBase($gamedata, $fireOrder)
	{

		if($this->outOfRange){
			$fireOrder->needed = 0; //always misses
			$fireOrder->updated = true;		
			$fireOrder->pubnotes .= " <br>Target was out of range when a Shield Projector fired!";
		}else{//Normal firing always hits.		
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;			
			$fireOrder->pubnotes .= " <br>A Shield Projector boosts any Gravitic Shields by " . $this->output ."!";
		}								
	}//endof calculateHitBase
			
			
	public function getDamage($fireOrder){       return 0;   } //no actual damage
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->autoHit = $this->autoHit; 
        $strippedSystem->canTargetAllies  = $this->canTargetAllies ;                                    
        return $strippedSystem;
	}
		
		    
} //endof class ShieldProjector


class ShieldReinforcement extends Weapon{
	public $name = "ShieldReinforcement";
	public $displayName = "Shield Reinforcement";
	public $iconPath = "ShieldReinforcement.png";

	public $damageType = "Raking"; //To prevent called shots
	public $weaponClass = "Support";

	public $uninterceptable = true; 
	public $doNotIntercept = true;
	public $priority = 1;
		
    public $useOEW = false;	
	public $noLockPenalty = false;	        
		
	public $loadingtime = 1;	
    public $fireControl = array(0, 0, 0); //No fire control per se, but gets automatic +3 points.		
		
	public $animation = "bolt";
	public $animationColor = array(204, 153, 255);
		
	public $output = 0;
	public $outputDisplay = ''; //if not empty - overrides default on-icon display text		
	public $animationExplosionScale = 0.4; //single hex explosion
	public $repairPriority = 5;		
	public $ballistic = true; //Fires during Initial Orders

    public $boostable = true; //can be boosted for additional effect
	public $boostEfficiency = 0; //cost to boost by 1 - FREE
    public $maxBoostLevel = 20; //maximum boost allowed - just technical limitation, rules dont set any maximum; 20 seems close enough to "unlimited" :)		
 
    public $rangePenalty = 0;
    public $range = 20;
    public $canOffLine = true;    
	
	public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?
    		
	public $firingModes = array(
			1 => "Shield Reinforcement"
		);

	protected $autoHit = true;//To show 100% hit chance in front end.
	protected $canTargetAllies = true; //To allow front end to target allies.
	private $outOfRange	= false; //To track when a target moves out of range by Firing phase.
	
	private $reinforceAmount = 0;//Updated by Notes so we know how much we're reinforcing ally.
		
	private static $shieldReinforced = array();	//To track and prevent multiple Shield Projectors applying to same target.	
	 
	 
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $shieldFactor){
		if ( $maxhealth == 0 ) $maxhealth = 9;
        if ( $powerReq == 0 ) $powerReq = 0;                           
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $this->startArc = $startArc;       	
        $this->endArc = $endArc;
        $this->output = $this->getCapacity();
 		$this->outputDisplay = $this->output;         
    }

    protected $possibleCriticals = array(
	);

	public function getOutput()
	{
		return $this->output;			
	}

	public function getCapacity(){
		return $this->getRemainingHealth();
	}
		    		
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
		$damageReduction = $this->output;
		$profileReduction = $this->output *5;
		$this->data["Special"] = "Can be fired at allied units during Initial Orders to reinforce their Thought Shields by adding EM Shield properties (e.g. hit chance and damage reductions)."; 
		$this->data["Special"] .= "<br>Select the amount you wish to reinforce an ally's shields by boosting this system, then target ally ship.";
		$this->data["Special"] .= "<br>Allied ship must remain within 20 hexes by end of Movement Phase to receive reinforcement.";		
		$this->data["Special"] .= "<br>Each level of shield reinforcement costs 1 point of this system's capacity per shield. All shields must be boosted equally on a ship.";
		$this->data["Special"] .= "<br>Any remaining capacity will be used to reinforce this ship's shields.";		 
	}	


	public function beforeFiringOrderResolution($gamedata)
	{
		if($this->isDestroyed($gamedata->turn)) return;
		if($this->isOfflineOnTurn($gamedata->turn)) return;			
		$ship = $this->getUnit();
		$deployTurn = $ship->getTurnDeployed($gamedata);
		if($deployTurn > $gamedata->turn) return;  //Ship not deployed yet, don't fire weapon!

	    // Get all firing orders for the current turn
		$weaponFiringOrders = $this->getFireOrders($gamedata->turn);
	    $weaponFireOrder = null;

	    // Loop through firing orders to find the first ballistic type
	    foreach ($weaponFiringOrders as $fireOrder) { 
	        if ($fireOrder->type == 'ballistic') { 
	            $weaponFireOrder = $fireOrder;
	            break; // No need to search further
	        }
	    }

	    // If no appropriate fire order, end the function
	    if ($weaponFireOrder != null) {

			    // Get the target ship by ID
			    $target = $gamedata->getShipById($weaponFireOrder->targetid);

				if (Mathlib::getDistanceHex($target, $ship ) > 20){ //Target unit must be within 20 hexes by Firing phase.
					$this->outOfRange = true;	        
		        	return;	    
				}
				
			    // Check if the target is already engaged with sufficient shield projected, only stronger reinforcements proceed.
			    if (isset(ShieldReinforcement::$shieldReinforced[$target->id]) && (ShieldReinforcement::$shieldReinforced[$target->id] >= $this->output)) return;

				$usedOutput = 0;//Initialise	
			    // If the target is a FighterFlight, find all shields in the fighter flight
			    if ($target instanceof FighterFlight){
				        foreach ($target->systems as $ftr) {
				            foreach ($ftr->systems as $sys) {
				                if ($sys instanceof ThoughtShield) {
				                    $sys->defenceMod = $this->reinforceAmount;	
				                    $usedOutput += $this->reinforceAmount;                    
				                }
				            }
				        }			    
			    } else { // For ships just find Thought Shields and update.
			        foreach ($target->systems as $system) {
			            if ($system instanceof ThoughtShield) {
			                $system->defenceMod = $this->reinforceAmount;
			                $usedOutput += $this->reinforceAmount; 	                 
			            }
			        }
			    }
			//Then see what what is left over and reinforce this ship's shields.
			$this->reinforceOwnShields($gamedata, $ship, $usedOutput);

			// Mark the target as engaged in Staic variable
			ShieldReinforcement::$shieldReinforced[$target->id] = $this->output;
			
		}else{//No fireOrder, just improve own shields.
			$this->reinforceOwnShields($gamedata, $ship, 0);						
		} 
		   
	} // end of beforeFiringOrderResolution 


	private function reinforceOwnShields($gamedata, $ship, $usedOutput){
		//Then see what what is left over and reinforce this ship's shields.				
		$remainingOutput = $this->getCapacity() - $usedOutput;//Deduct amount used on allies from total capacity (e.g. system health).  Front end checks shoudl stop this from being under 0.
		$remainingOutput = max(0, $remainingOutput);
		$ownShields = array();
		$reinforcePerShield = 0;

		if($remainingOutput > 0){//No point if remaining output is 0!		
				foreach ($ship->systems as $system) {
				    if ($system instanceof ThoughtShield) {//Find all Thought Shields on ship.
				        $ownShields[] = $system; //Add to array.
				    }
				}			

			$noOfOwnShields = count($ownShields); // 3 for Consortium, 6 for Mind's Eye, etc.
			         
			if ($noOfOwnShields > 0) { // Ensure we don't divide by zero
			    $reinforcePerShield = floor($remainingOutput / $noOfOwnShields); // Divide capacity by number of own shields, rounding down.

			    foreach ($ownShields as $shield) {
			        $shield->defenceMod = $reinforcePerShield;
			    }        
			}
		}
		
		//Now create fireOrder to show in Combat Log
		$newFireOrder = new FireOrder(
		    -1, "normal", $ship->id, $ship->id,
		    $this->id, -1, $gamedata->turn, 1, 
		    100, 100, 1, 1, 0, // needed, rolled, shots, shotshit, intercepted
		    0, 0, $this->weaponClass, -1 // X, Y, damageclass, resolutionorder
		);        

		$newFireOrder->addToDB = true;
		$newFireOrder->pubnotes .= " <br>Ship enhances its own Thought Shields with EM Shield properties (" . $reinforcePerShield . ")."; 
		$this->fireOrders[] = $newFireOrder;				

	}//endof reinforceOwnShields()
		
	public function calculateHitBase($gamedata, $fireOrder)
	{

		if($this->outOfRange){
			$fireOrder->needed = 0; //always misses
			$fireOrder->updated = true;		
			$fireOrder->pubnotes .= " <br>Target was out of range when Shield Reinforcement was attempted!";
		}else if($fireOrder->type == "normal"){
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;				
		}else{//Normal firing always hits.		
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;			
			$fireOrder->pubnotes .= " <br>Shield Reinforcement enhances Thought Shields with EM Shield properties (" . $this->reinforceAmount . ").";
		}								
	}//endof calculateHitBase
			
		
	// this method generates additional non-standard information in the form of individual system notes, in this case: - Initial phase: check setting changes made by user, convert to notes.	
	public function doIndividualNotesTransfer(){
	//data received in variable individualNotesTransfer. Denotes by how much player decided to reinfrocew ally shields.
	    if (is_array($this->individualNotesTransfer) && isset($this->individualNotesTransfer[0])) { // Check if it's an array and the key exists
	        $reinforceChange = $this->individualNotesTransfer[0];
	        $this->reinforceAmount = $reinforceChange;
	    }	    
	    // Clear the individualNotesTransfer array
	    $this->individualNotesTransfer = array();
	}
	
		
    public function generateIndividualNotes($gameData, $dbManager){ //dbManager is necessary for Initial phase only
		$ship = $this->getUnit();
		switch($gameData->phase){
					
				case 1: //Initial phase
					//data returned as a number to create a new damage entry.
					if($ship->userid == $gameData->forPlayer){ //only own ships, otherwise bad things may happen!
						//load existing data first - at this point ship is rudimentary, without data from database!
						$listNotes = $dbManager->getIndividualNotesForShip($gameData, $gameData->turn, $ship->id);	
						foreach ($listNotes as $currNote){
							if($currNote->systemid==$this->id){//note is intended for this system!
								$this->addIndividualNote($currNote);	 								
							}
						}
						$this->onIndividualNotesLoaded($gameData);		

						$reinforcement = $this->reinforceAmount;//Extract change value for shield this turn.																
												
						$notekey = 'reinforce';
						$noteHuman = 'Amount to reinforce ally shields';
						$notevalue = $reinforcement;
						$this->individualNotes[] = new IndividualNote(-1,TacGamedata::$currentGameID,$gameData->turn,$gameData->phase,$ship->id,$this->id,$notekey,$noteHuman,$notevalue);//$id,$gameid,$turn,$phase,$shipid,$systemid,$notekey,$notekey_human,$notevalue         
					}			
										
			break;				
		}
	} //endof function generateIndividualNotes
	

	public function onIndividualNotesLoaded($gamedata)
	{
							
	    foreach ($this->individualNotes as $currNote) {
	  		if($currNote->turn == $gamedata->turn) {  				    	
	        	$this->reinforceAmount = $currNote->notevalue;
			}
		}				
        //and immediately delete notes themselves, they're no longer needed (this will not touch the database, just memory!)
        $this->individualNotes = array();
	 		  
	}//endof onIndividualNotesLoaded	
	
			
	public function getDamage($fireOrder){       return 0;   } //no actual damage
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->autoHit = $this->autoHit; 
        $strippedSystem->canTargetAllies = $this->canTargetAllies;
        $strippedSystem->reinforceAmount = $this->reinforceAmount;                                            
        return $strippedSystem;
	}	
	
	
}//endof ShieldReinforcement



class ShadeModulator extends Weapon{
	public $name = "ShadeModulator";
	public $displayName = "Shade Modulator";
	public $iconPath = "ShadeModulator.png";

	public $damageType = "Raking"; //To prevent called shots
	public $weaponClass = "Support";

	public $uninterceptable = true; 
	public $doNotIntercept = true;
	public $priority = 1;
		
    public $useOEW = false;	
	public $noLockPenalty = false;	        
		
	public $loadingtime = 1;	
    public $fireControl = array(0, 0, 0); //No fire control per se, but gets automatic +3 points.		
		
	public $animation = "bolt";
	public $animationArray = array(1 => "ball", 2=> "bolt", 3=> "ball", 4=> "bolt");		
    public $animationColor = array(255, 255, 0);
	public $animationColorArray = array(1=>array(0, 128, 0), 2=>array(0, 128, 0), 3=>array(255, 255, 0), 4=>array(255, 255, 0));
	public $noProjectile = true; //Marker for front end to make projectile invisible for weapons that shouldn't have one.
	public $noProjectileArray = array(1 => true, 2=> false, 3=> true, 4=> false); 	 		
	public $output = 0;
	public $outputDisplay = ''; //if not empty - overrides default on-icon display text		
	public $animationExplosionScale = 0.4; //single hex explosion
	public $animationExplosionScaleArray = array(1 => 3, 2=> 0.4, 3=> 5, 4=> 0.4);	
	public $repairPriority = 5;		
    public $boostable = true; //can be boosted for additional effect
	public $boostEfficiency = 15; //cost to boost by 1
    public $maxBoostLevel = 3; //maximum boost allowed - just technical limitation, rules dont set any maximum; 20 seems close enough to "unlimited" :)		
 
	public $firingModes = array(
			1 => "1-Blanket Shield",
			2 => "2-Individual Shield",
			3 => "3-Blanket Shade",
			4 => "4-Individual Shade",						
		);

    public $rangePenalty = 0;
    public $range = 3;
	public $rangeArray = array(1 => 3, 2=> 5, 3=> 15, 4=> 20);	
    public $canOffLine = true;    
	protected $autoHit = true;//To show 100% hit chance in front end.
	public $autoFireOnly = true; //this weapon cannot be fired by player
	public $autoFireOnlyArray = array(1 => true, 2=> false, 3=> true, 4=> false);	
	protected $canTargetAllies = true; //To allow front end to target allies.
	protected $canTargetAlliesArray = array(1 => false, 2=> true, 3=> false, 4=> true); //To allow front end to target allies.

	public $hextarget = true;
	public $hextargetArray = array(1 => true, 2=> false, 3=> true, 4=> false);
	public $canSplitShots = true;	
	protected $multiModeSplit = true;

	//These private variables are used to track fire orders, and firings to prevent Combat Log spam
	private	$orderThisTurn = array(); //For Combat Logs entries
	private $firedAlready = array(); //Prevent multiple firings at same ship for same mode.	 
	 
    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $output){
		if ( $maxhealth == 0 ) $maxhealth = 9;
        if ( $powerReq == 0 ) $powerReq = 0;                           
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        $this->startArc = $startArc;       	
        $this->endArc = $endArc;
        $this->output = $output;
 		$this->outputDisplay = $output;         
    }

		protected $possibleCriticals = array(
			22=>array("OutputReduced1")
		);

		    		
    public function setSystemDataWindow($turn){
        parent::setSystemDataWindow($turn);
		$this->data["Special"] = "Has four different firing modes used to enhance Shading Fields of Tovalus allies during Firing Phase.";
		$this->data["Special"] .= "<br>Firing modes, effect and capacity cost are summarised below:";		 
		$this->data["Special"] .= "<br> 1. Blanket Shield: Increases EM shield effect by 1 to allies within 3 hexes (4).";
		$this->data["Special"] .= "<br> 2. Individual Shield: Increases EM shield effect by 1 to specific ally (2).";	
		$this->data["Special"] .= "<br> 3. Blanket Shade: Lowers defence profiles by 5% to Shaded allies within 5 hexes (2).";	
		$this->data["Special"] .= "<br> 4. Individual Shade: Lowers defence profiles by 5% to Shaded allies (1).";	
		$this->data["Special"] .= "<br>Different firing modes can be used on same turn, with the only limit being system's capacity.";											 
	}	


	public function beforeFiringOrderResolution($gamedata)
	{
		if ($this->isDestroyed($gamedata->turn)) return;
		if ($this->isOfflineOnTurn($gamedata->turn)) return;

		$ship = $this->getUnit();
		$deployTurn = $ship->getTurnDeployed($gamedata);
		if ($deployTurn > $gamedata->turn) return; // Ship not deployed yet

		$weaponFiringOrders = $this->getFireOrders($gamedata->turn);
		if (empty($weaponFiringOrders)) return; // No fire orders

		$alreadyBShield = [];
		$alreadyBShade = [];

		foreach ($weaponFiringOrders as $fireOrder) {
			if (!isset($this->orderThisTurn[$fireOrder->targetid])) {
				$this->orderThisTurn[$fireOrder->targetid] = [];
			}
			$this->orderThisTurn[$fireOrder->targetid][] = $fireOrder->firingMode;

			switch ($fireOrder->firingMode) {

				case 1:
					$inRange = $gamedata->getShipsInDistance($ship, 3);
					foreach ($inRange as $targetID => $target) {
						if ($target->faction != "Torvalus Speculators") continue;
						if ($target->team !== $ship->team) continue;						
						if ($target instanceof FighterFlight) continue;
						if ($target->isDestroyed()) continue;
						if ($target->isTerrain()) continue;
						if ($target->getTurnDeployed($gamedata) > $gamedata->turn) continue;
						if (in_array($target->id, $alreadyBShield, true)) continue;

						$alreadyBShield[] = $target->id;

						$shadingField = $target->getSystemByName("ShadingField");
						if (!$shadingField) continue;

						$shadingField->output += 1;
					}
					break;

				case 2:
					$target = $gamedata->getShipById($fireOrder->targetid);
					if (!$target) continue 2;
					if ($target->faction != "Torvalus Speculators") continue 2;
					if ($target instanceof FighterFlight) continue 2;
					if (Mathlib::getDistanceHex($target, $ship) > 5) continue 2;

					$shadingField = $target->getSystemByName("ShadingField");
					if (!$shadingField) continue 2;

					$shadingField->output += 1;
					break;

				case 3:
					$inRange = $gamedata->getShipsInDistance($ship, 5);
					foreach ($inRange as $targetID => $target) {
						if ($target->faction != "Torvalus Speculators") continue;
						if ($target->team !== $ship->team) continue;						
						if ($target instanceof FighterFlight) continue;
						if ($target->isDestroyed()) continue;
						if ($target->isTerrain()) continue;
						if ($target->getTurnDeployed($gamedata) > $gamedata->turn) continue;
						if (in_array($target->id, $alreadyBShade, true)) continue;

						$alreadyBShade[] = $target->id;

						$shadingField = $target->getSystemByName("ShadingField");
						if (!$shadingField || !$shadingField->shaded) continue;

						$target->forwardDefense -= 1;
						$target->sideDefense -= 1;
					}
					break;

				case 4:
					$target = $gamedata->getShipById($fireOrder->targetid);
					if (!$target) continue 2;
					if ($target->faction != "Torvalus Speculators") continue 2;
					if (Mathlib::getDistanceHex($target, $ship) > 20) continue 2;

					$shadingField = $target->getSystemByName("ShadingField");
					if (!$shadingField || !$shadingField->shaded) continue 2;

					$target->forwardDefense -= 1;
					$target->sideDefense -= 1;
					break;

				default:
					continue 2; // skip to next fire order safely
			}
		}
	} // end of beforeFiringOrderResolution 

		
	public function calculateHitBase($gamedata, $fireOrder)
	{
		$targetId = $fireOrder->targetid;
		if (!isset($this->orderThisTurn[$targetId])) return; //Safety

		$counts = array_count_values($this->orderThisTurn[$targetId]);
		$shieldAmount = $counts[$fireOrder->firingMode] ?? 0;
		$shadeAmount = $shieldAmount*5;

		if ($fireOrder->firingMode == 1 || $fireOrder->firingMode == 3) { 
			// Blanket orders
			$fireOrder->chosenLocation = 0; // recalc later
			$fireOrder->updated = true;

			if ($fireOrder->firingMode == 1) {
				$fireOrder->pubnotes = "<br>Shade Modulator enhances EM Shields for allied Torvalus ships within 3 hexes by {$shieldAmount}.";
			} else {
				$fireOrder->pubnotes = "<br>Shade Modulator reduces the Defence Profiles of allied Shaded units within 15 hexes by {$shadeAmount}.";
			}

		} else { 
			// Individual orders
			$targetId = $fireOrder->targetid;
			$fireOrder->needed = 100; //always true
			$fireOrder->updated = true;	

			if ($fireOrder->firingMode == 2) {
				$fireOrder->pubnotes = "<br>Shade Modulator enhances EM Shield for target ship by {$shieldAmount}.";
			} else if ($fireOrder->firingMode == 4) {
				$fireOrder->pubnotes = "<br>Shade Modulator reduces Defence Profiles of target Shaded unit by {$shadeAmount}.";
			}

		}
	}

	public function fire($gamedata, $fireOrder)
	{
		// Safety check: make sure array exists
		if (!isset($this->firedAlready)) {
			$this->firedAlready = [];
		}

		// If already fired at this target, skip
		if (!empty($this->firedAlready[$fireOrder->firingMode])) {
			return;
		}

		if ($fireOrder->firingMode == 1 || $fireOrder->firingMode == 3) {
			// Blanket firing modes (auto-hit, no roll)
			$fireOrder->rolled = 1;
			$fireOrder->shotshit = 1;
			TacGamedata::$lastFiringResolutionNo++;
			$fireOrder->resolutionOrder = TacGamedata::$lastFiringResolutionNo;
		} else {
			// Normal firing handled by parent
			parent::fire($gamedata, $fireOrder);
		}

		// Mark this target as fired upon
		$this->firedAlready[$fireOrder->firingMode] = true;
	}
			
	public function getDamage($fireOrder){       return 0;   } //no actual damage
	public function setMinDamage(){     $this->minDamage = 0 ;      }
	public function setMaxDamage(){     $this->maxDamage = 0 ;      }

    public function stripForJson() {
        $strippedSystem = parent::stripForJson();    
        $strippedSystem->autoHit = $this->autoHit; 
        $strippedSystem->canTargetAllies = $this->canTargetAllies;
        $strippedSystem->canTargetAlliesArray = $this->canTargetAlliesArray;
        $strippedSystem->multiModeSplit = $this->multiModeSplit;
        $strippedSystem->noProjectileArray = $this->noProjectileArray;							                                        
        return $strippedSystem;
	}	
	
	
}//endof ShadeModulator


class TransverseDrive extends Weapon{
    public $name = "TransverseDrive";
    public $displayName = "Transverse Drive";

	public $autoFireOnly = true;

     public function setSystemDataWindow($turn){
        $this->data["Special"] = "Transverse Drive not installed until Tuesday.";												
		parent::setSystemDataWindow($turn);     
    }

}	

?>
