<?php
class triadDevil extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1625;
		$this->faction = "The Triad";
		$this->phpclass = "triadDevil";
		$this->imagePath = "img/ships/triadDevil.png";
		$this->shipClass = "Chaos: Devil";
		$this->canvasSize = 100;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 1; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

        $this->agile = true;

        $this->gravitic = true;
		$this->advancedArmor = true;  
		$this->skinDancer = true; 	

		$this->forwardDefense = 10;
		$this->sideDefense = 12;

		$this->turncost = 0.33;
		$this->turndelaycost = 0.33;
		$this->accelcost = 2;
		$this->rollcost = 1;
		$this->pivotcost = 3;
		$this->iniativebonus = 70;

		$this->notes = "Cannot control fighters";		
		$this->notes .= "<br>Atmospheric Capable";		

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');

		$this->addPrimarySystem(new Reactor(8, 25, 0, 0));
		$this->addPrimarySystem(new CnC(8, 12, 0, 0));
		$scanner = new Scanner(8, 16, 0, 12);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(8, 18, 0, 12, 3));
        $this->addPrimarySystem(new StructureSelfRepair(8, 12, 12)); //armor, structure, output
        $this->addPrimarySystem(new GraviticThruster(7, 15, 0, 4, 3)); 		
        $this->addPrimarySystem(new GraviticThruster(7, 15, 0, 4, 4)); 				

		$this->addFrontSystem(new GraviticThruster(7, 10, 0, 3, 1));
		$this->addFrontSystem(new GraviticThruster(7, 10, 0, 3, 1));
        $this->addFrontSystem(new PlasmaDriver(7, 6, 6, 300, 60));	
        $this->addFrontSystem(new PlasmaDriver(7, 6, 6, 300, 60));	
        $this->addFrontSystem(new AntimatterWave(5, 20, 8, 270, 90));	
        $this->addFrontSystem(new SpatialCutter(7, 22, 8, 180, 360));	
        $this->addFrontSystem(new SpatialCutter(7, 22, 8, 0, 180));	

		$this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new SelfRepair(8, 9, 4)); //armor, structure, output
		$this->addAftSystem(new JumpEngine(8, 15, 5, 8));        
	
        $this->addPrimarySystem(new Structure( 8, 90));
		
				$this->hitChart = array(
                0=> array(
                        9 => "Thruster",
						13 => "Structure Self Repair",
                        15 => "Scanner",
                        17 => "Engine",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
                        7 => "Antimatter Wave",
						10 => "Spatial Cutter",
						11 => "Plasma Driver",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
						8 => "Self Repair",
                        13 => "Jump Engine",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}



?>
