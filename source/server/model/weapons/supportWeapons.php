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
    public $range = 15;
    public $canOffLine = true;    
	
	public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?
    		
	public $firingModes = array(
			1 => "Shield"
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
		$targetBearing = $ship->getBearingOnUnit($target); //And still in Arc!
			    
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

?>
