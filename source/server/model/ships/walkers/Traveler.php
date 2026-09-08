<?php
class Traveler extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 5400;
		$this->faction = "Walkers of Sigma-957";
        $this->phpclass = "Traveler";
        $this->shipClass = "Traveler";
        $this->imagePath = "img/ships/WalkerTraveler.png";
        $this->canvasSize = 200;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		$this->variantOf = "NONE";
				
        $this->gravitic = true;
		$this->advancedArmor = true;  
        
        $this->forwardDefense = 17;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 6;
		$this->iniativebonus = 2 *5;

		$this->fighters = array("Mapmaker Probes"=>36);

		/*Walkers will use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'WalkerShip');
		
         
		$this->addPrimarySystem(new Reactor(7, 30, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(7, 28, 0, 0));
		$scanner = new Scanner(7, 28, 0, 14);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(7, 28, 0, 16, 4));
        $this->addPrimarySystem(new SelfRepair(7, 22, 9)); //armor, structure, output
		$this->addPrimarySystem(new JumpEngine(7, 30, 12, 6));		
		
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 5, 1));			       
		$this->addFrontSystem(new LightningArray(6, 0, 0, 270, 90));
		$this->addFrontSystem(new ChromaticPulseDriver(6, 0, 0, 270, 90));


		$this->addAftSystem(new GraviticThruster(6, 30, 0, 8, 2));
        $this->addAftSystem(new Hangar(6, 24, 12));
		$this->addAftSystem(new GraviticThruster(6, 30, 0, 8, 2));
		$this->addAftSystem(new EnergyDrainingField(6, 0, 0));


		$this->addLeftSystem(new GraviticThruster(6, 30, 0, 8, 3));
		$this->addLeftSystem(new ChromaticPulseDriver(6, 0, 0, 240, 360)); //STAGE 3
        $this->addLeftSystem(new Hangar(6, 6, 6, 5));


		$this->addRightSystem(new GraviticThruster(6, 30, 0, 8, 4));
		$this->addRightSystem(new ChromaticPulseDriver(6, 0, 0, 0, 120)); //STAGE 3
        $this->addRightSystem(new Hangar(6, 6, 6, 1));
				

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 80));
        $this->addAftSystem(new Structure( 7, 96));
        $this->addLeftSystem(new Structure( 7, 96));
        $this->addRightSystem(new Structure( 7, 96));
        $this->addPrimarySystem(new Structure( 7, 108 ));
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				9 => "Structure",
				11 => "Jump Engine",
				13 => "Self Repair",
				15 => "Scanner",                
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				6 => "Lightning Array",
				8 => "Chromatic Pulse Driver",
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
			3=> array( //Fwd
				6 => "Thruster",
				8 => "Chromatic Pulse Driver",
				9 => "Hangar", 				
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Fwd
				6 => "Thruster",
				8 => "Chromatic Pulse Driver",
				9 => "Hangar", 				
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
