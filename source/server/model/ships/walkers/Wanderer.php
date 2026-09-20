<?php
class Wanderer extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 8750;
		$this->faction = "Walkers of Sigma-957";
        $this->phpclass = "Wanderer";
        $this->shipClass = "The Wanderer";
        $this->imagePath = "img/ships/WalkerTraveler.png";
        $this->canvasSize = 250;
	    $this->isd = 'Ancient';
        $this->shipSizeClass = 3; 
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
		$this->occurence = 'Q'; //Unique
		$this->variantOf = "Traveler";
				
        $this->gravitic = true;
		$this->advancedArmor = true;  
        
        $this->forwardDefense = 16;
        $this->sideDefense = 13;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 6;
		$this->iniativebonus = 4 *5;

		//Has Elite Crew stats
		$this->notes = "Elite Crew";
		$this->notes .= "<br>Weapons begin battle fully charged";		
		$this->critRollMod = -1;
		$this->toHitBonus = 1;		

		/*Walkers will use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'WalkerShip');
		
         
		$this->addPrimarySystem(new Reactor(7, 30, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(7, 28, 0, 0));
		$scanner = new Scanner(7, 28, 0, 15);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(7, 28, 0, 17, 4));
        $this->addPrimarySystem(new SelfRepair(7, 36, 15)); //armor, structure, output
		$jumpEngine = new JumpEngine(7, 30, 12, 4);
		$jumpEngine->markExtraDimensional(); //Stage 20: a Walker drive (Stage 15) that can also abduct enemy units
		$this->addPrimarySystem($jumpEngine);		
		
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(6, 20, 0, 5, 1));			       
		$this->addFrontSystem(new LightningArray(6, 0, 0, 270, 90));
		$this->addFrontSystem(new ChromaticPulseDriver(6, 0, 0, 300, 60));
		$this->addFrontSystem(new EWDetector(6, 0, 0, 0));							


		$this->addAftSystem(new GraviticThruster(6, 30, 0, 8, 2));
		$this->addAftSystem(new GraviticThruster(6, 30, 0, 8, 2));
		$this->addAftSystem(new EnergyDrainingField(6, 40, 8, 6));


		$this->addLeftSystem(new GraviticThruster(6, 30, 0, 8, 3));
		$this->addLeftSystem(new ChromaticPulseDriver(6, 0, 0, 240, 360)); //STAGE 3
		$this->addLeftSystem(new EnergyDrainingMine(6, 0, 0, 240, 360)); //STAGE 3		


		$this->addRightSystem(new GraviticThruster(6, 30, 0, 8, 4));
		$this->addRightSystem(new ChromaticPulseDriver(6, 0, 0, 0, 120)); //STAGE 3
		$this->addRightSystem(new EnergyDrainingMine(6, 0, 0, 0, 120)); //STAGE 3				
				

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 80));
        $this->addAftSystem(new Structure( 7, 96));
        $this->addLeftSystem(new Structure( 7, 96));
        $this->addRightSystem(new Structure( 7, 96));
        $this->addPrimarySystem(new Structure( 7, 108 ));
		
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				9 => "Structure",
				11 => "Extra-Dimensional Jump Drive",
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
				9 => "Electronic Warfare Detector",				
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				10 => "Energy Draining Field",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array( //Fwd
				6 => "Thruster",
				8 => "Chromatic Pulse Driver",
				9 => "Energy Draining Mine",					
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Fwd
				6 => "Thruster",
				8 => "Chromatic Pulse Driver",
				9 => "Energy Draining Mine",					
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
