<?php
class Pathfinder extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 3150;
		$this->faction = "Walkers of Sigma-957";
        $this->phpclass = "Pathfinder";
        $this->shipClass = "Pathfinder";
        $this->imagePath = "img/ships/WalkerPathfinder.png";
        $this->canvasSize = 150;
	    $this->isd = 'Ancient';
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		//$this->variantOf = "NONE";
		$this->limited = 50;
				
        $this->gravitic = true;
		$this->advancedArmor = true;  
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 14 *5;
		//Docking Bay box cost (WALKERS_OF_SIGMA_PLAN.md 3.14, D18): 12 boxes.
		$this->unitSize = 1/12;

		$this->fighters = array("Mapmaker Probes"=>6);

		/*Walkers will use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'WalkerShip');
		
         
		$this->addPrimarySystem(new Reactor(6, 12, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
		$scanner = new ElintScanner(6, 28, 0, 16);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(5, 15, 0, 10, 3));		
        $this->addPrimarySystem(new SelfRepair(6, 3, 2)); //armor, structure, output
		$jumpEngine = new JumpEngine(6, 11, 9, 4);
		$jumpEngine->markWalker(); //Stage 15: leaves at the END of the turn, untargetable while it waits, no failure roll
		$this->addPrimarySystem($jumpEngine);
		$this->addPrimarySystem(new GraviticThruster(6, 20, 0, 5, 3));
		$this->addPrimarySystem(new GraviticThruster(6, 20, 0, 5, 4));		
		
		
        $this->addFrontSystem(new GraviticThruster(6, 12, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(6, 12, 0, 4, 1));			       		
		$this->addFrontSystem(new MediumLightningArray(6, 0, 0, 300, 60));
		$this->addFrontSystem(new ChromaticPulseDriver(6, 0, 0, 300, 60));
		$this->addFrontSystem(new EnergyDrainingMine(6, 0, 0, 300, 60));				

		$this->addAftSystem(new EnergyDrainingField(6, 24, 12, 2, true));
        $this->addAftSystem(new Hangar(6, 6, 6)); //armor, structure, output				
		$this->addAftSystem(new GraviticThruster(6, 12, 0, 5, 2));
		$this->addAftSystem(new GraviticThruster(6, 12, 0, 5, 2));

				

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 6, 84 ));
		
	
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
				3 => "Thruster",
				5 => "Energy Draining Mine",
				8 => "Medium Lightning Array",
				10 => "Chromatic Pulse Driver",				
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				9 => "Energy Draining Field",
				11 => "Hangar",				
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
