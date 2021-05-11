<?php
class DrakhHeavyRaiderEscort extends MediumShip{
	/*Drakh Heavy Raider LCV*/
	/*approximated as MCV, no EW restrictions*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 370;
        $this->faction = "Drakh";
	$this->factionAge = 2; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	$this->phpclass = "DrakhHeavyRaiderEscort";
	$this->shipClass = "Heavy Raider Escort";
	$this->imagePath = "img/ships/DrakhHeavyRaider.png";
	    $this->canvasSize = 100;
	$this->unofficial = true;
        $this->gravitic = true;
	$this->advancedArmor = true;
        $this->occurence = "common";
	$this->variantOf = "Heavy Raider";
	$this->isd = 2255;
   
	$this->agile = true;
	$this->forwardDefense = 10;
	$this->sideDefense = 11;
	$this->turncost = 0.33;
	$this->turndelaycost = 0.25;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 2;
	$this->iniativebonus = 14 *5;
	
	
		$this->hangarRequired = "Raiders"; //Heavy Raiders can use regular Raider catapults
		$this->unitSize = 0.5; //they require twice as much space, though!
	    
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	    
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
	    
	$this->addPrimarySystem(new Reactor(4, 10, 0, 7));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    $sensors = new Scanner(4, 12, 4, 6);
		$sensors->markImproved();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(4, 12, 0, 7, 2));
	$this->addPrimarySystem(new customLtPhaseDisruptorShip(3, 0, 0, 240, 30));
	$this->addPrimarySystem(new customLtPhaseDisruptorShip(3, 0, 0, 330, 120));
	$this->addPrimarySystem(new AbsorbtionShield(2,6,4,1,0,360));
	$this->addPrimarySystem(new Structure( 4, 30));
	    
        $this->hitChart = array(
        		0=> array( //should never happen
        				10 => "Structure",
        				12 => "Light Phase Disruptor",
        				14 => "Absorption Shield",
        				16 => "Engine",
        				18 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				10 => "Structure",
        				12 => "0:Light Phase Disruptor",
        				14 => "0:Absorption Shield",
        				16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //PRIMARY hit table, effectively
        				10 => "Structure",
        				12 => "0:Light Phase Disruptor",
        				14 => "0:Absorption Shield",
        				16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart
    }
	
	
        public function getInitiativebonus($gamedata){
	    $iniBonus = parent::getInitiativebonus($gamedata);
            //may be boosted by  Raider Controller...
	    $iniBonus += DrakhRaiderController::getIniBonus($this);
            return $iniBonus;
        }
	
}
?>
