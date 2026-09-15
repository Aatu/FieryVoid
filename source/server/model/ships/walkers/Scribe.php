<?php
class Scribe extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 750;
		$this->faction = "Walkers of Sigma-957";
        $this->phpclass = "Scribe";
        $this->shipClass = "Scribe";
        $this->imagePath = "img/ships/WalkerScribe.png";
        $this->canvasSize = 125;
	    $this->isd = 'Ancient';
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		//$this->variantOf = "NONE";
				
        $this->gravitic = true;
		$this->advancedArmor = true;  
		$this->agile = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 14 *5;
		//Docking Bay box cost (WALKERS_OF_SIGMA_PLAN.md 3.14, D18): 4 boxes. Inert everywhere else - the
		//fleet check reads a HULL's unitSize only when it sets hangarRequired, which this one does not.
		$this->unitSize = 1/4;

		/*Walkers will use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'WalkerShip');
		
         
		$this->addPrimarySystem(new Reactor(5, 10, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(6, 8, 0, 0));
		$scanner = new Scanner(6, 18, 0, 8);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(6, 12, 0, 10, 2));
        $this->addPrimarySystem(new SelfRepair(5, 3, 2)); //armor, structure, output
		$jumpEngine = new JumpEngine(6, 9, 8, 8);
		$jumpEngine->markWalker(); //Stage 15: leaves at the END of the turn, untargetable while it waits, no failure roll
		$this->addPrimarySystem($jumpEngine);
		$this->addPrimarySystem(new GraviticThruster(6, 13, 0, 5, 3));
		$this->addPrimarySystem(new GraviticThruster(6, 13, 0, 5, 4));		
		
		
        $this->addFrontSystem(new GraviticThruster(6, 10, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(6, 10, 0, 4, 1));			       		
		$this->addFrontSystem(new ChromaticPulseDriver(6, 0, 0, 240, 360));
		$this->addFrontSystem(new ChromaticPulseDriver(6, 0, 0, 0, 120));
		//A sensor charge steers itself, so the transceiver has no firing arc to speak of - it is
		//in the Front section for damage, not for coverage. WALKERS_OF_SIGMA_PLAN.md 3.9.
		$this->addFrontSystem(new SensorChargeTransceiver(6, 0, 0, 300, 60));

		$this->addAftSystem(new EnergyDrainingNet(6, 0, 0));		
		$this->addAftSystem(new GraviticThruster(6, 10, 0, 5, 2));
		$this->addAftSystem(new GraviticThruster(6, 10, 0, 5, 2));

				

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 6, 48 ));
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				7 => "Thruster",			
				9 => "Jump Engine",			
				11 => "Self Repair",
				14 => "Scanner",                
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				5 => "Thruster",
				8 => "Sensor Charge Transceiver",
				10=> "Chromatic Pulse Driver",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				9 => "Energy Draining Net",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
