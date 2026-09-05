<?php
class triadSeraph extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 900;
		$this->faction = "The Triad";
		$this->phpclass = "triadSeraph";
		$this->imagePath = "img/ships/triadSeraph.png";
		$this->shipClass = "Order: Seraph";
		$this->canvasSize = 100;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 1; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

		$this->triadOrder = true;	//Important to ensure immunity from Flare Generator!	

        $this->gravitic = true;
		$this->advancedArmor = true;  

		$this->forwardDefense = 13;
		$this->sideDefense = 13;

		$this->turncost = 0.33;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 3;
		$this->pivotcost = 3;
		$this->iniativebonus = 70;

		$this->notes = "Cannot control fighters";		

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');

		$this->addPrimarySystem(new Reactor(8, 25, 0, 0));
		$this->addPrimarySystem(new CnC(9, 12, 0, 0));
		$scanner = new Scanner(8, 20, 0, 13);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(8, 15, 0, 12, 3));
        $this->addPrimarySystem(new StructureSelfRepair(8, 12, 12)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(8, 9, 4)); //armor, structure, output
        $this->addPrimarySystem(new FlareShielding(8, 9, 6, 4, 0, 360));	
        $this->addPrimarySystem(new GraviticThruster(8, 15, 0, 5, 3)); 		
        $this->addPrimarySystem(new GraviticThruster(8, 15, 0, 5, 4)); 				

		$this->addFrontSystem(new GraviticThruster(8, 8, 0, 2, 1));
		$this->addFrontSystem(new GraviticThruster(8, 10, 0, 3, 1));
		$this->addFrontSystem(new GraviticThruster(8, 8, 0, 2, 1));
        $this->addFrontSystem(new PhotonicPrismBeam(8, 24, 8, 150, 30));	
        $this->addFrontSystem(new PhotonicPrismBeam(8, 24, 8, 330, 210));	

		$this->addAftSystem(new GraviticThruster(8, 13, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(8, 13, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(8, 13, 0, 4, 2));
		$this->addAftSystem(new JumpEngine(8, 15, 4, 10));        
	
        $this->addPrimarySystem(new Structure( 9, 75));
		
				$this->hitChart = array(
                0=> array(
                        9 => "Thruster",
						10 => "Flare Shielding",
						11 => "Self Repair",
						13 => "Structure Self Repair",
                        15 => "Scanner",
                        17 => "Engine",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Photonic Prism Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Jump Engine",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}



?>
