<?php
class DrakhMobileBeamPlatform extends MediumShip{
	/*Drakh OSAT-equivalent*/
	/*approximated as MCV, no EW restrictions*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 350;
        $this->faction = "Drakh";
	$this->factionAge = 2; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	$this->phpclass = "DrakhMobileBeamPlatform";
	$this->shipClass = "Mobile Beam Platform";
	$this->imagePath = "img/ships/DrakhDefensePlatform.png";
	    $this->canvasSize = 100;
	$this->unofficial = true;
        $this->gravitic = true;
	$this->advancedArmor = true;
	$this->variantOf = "Mobile Defense Platform";
        $this->occurence = "common";
	$this->isd = 2255;
	    
	    $this->notes = "Boosted by Raider Controller.";
	    $this->notes .= "<br>Equivalent of OSAT, not a mobile unit (but can be taken in pickup battle with proper carrier).";
		
		
		$this->hangarRequired = "Weapon Platforms"; //...akin to OSATs, based on specifically allocated Raider ports on large Drakh ships
		$this->unitSize = 1; //let's say they use 2 standarized Raider slots like Heavy Raiders, but they need special preparations and/or other non-tactical considerations; somewhat like Orieni HKs; 
		//for simplifying make them size 1, as slots will be separate anyway
   
	$this->agile = true;
	$this->forwardDefense = 11;
	$this->sideDefense = 11;
	$this->turncost = 0.5;
	$this->turndelaycost = 0.5;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 2;
	$this->iniativebonus = 12 *5;
	    
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	    
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
	    
	$this->addPrimarySystem(new Reactor(4, 14, 0, 4));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    $sensors = new Scanner(4, 12, 4, 6);
		$sensors->markImproved();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(4, 9, 0, 4, 3));
	
	$this->addFrontSystem(new customMphasedBeamAcc(3, 0, 0, 300, 60)); 
	$this->addFrontSystem(new customLtPhaseDisruptorShip(3, 0, 0, 240, 120));
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 0,360));
	
	$this->addPrimarySystem(new AbsorbtionShield(2,6,4,1,0,360));
	$this->addPrimarySystem(new Structure( 4, 30));
	    
        $this->hitChart = array(
        		0=> array( //should never happen
        				8 => "Structure",
        				10 => "1:Multiphased Beam Accelerator",
		    			11 => "1:Light Phase Disruptor",
		    			12 => "1:Light Polarity Pulsar",
		    			14 => "Absorption Shield",
        				16 => "Engine",
        				18 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				8 => "Structure",
        				10 => "1:Multiphased Beam Accelerator",
		    			11 => "1:Light Phase Disruptor",
		    			12 => "1:Light Polarity Pulsar",
		    			14 => "0:Absorption Shield",
        				16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //PRIMARY hit table, effectively
        				8 => "Structure",
        				10 => "1:Multiphased Beam Accelerator",
		    			11 => "1:Light Phase Disruptor",
		    			12 => "1:Light Polarity Pulsar",
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
