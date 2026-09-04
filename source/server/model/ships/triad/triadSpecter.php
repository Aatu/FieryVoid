<?php
class triadSpecter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1700;
		$this->faction = "The Triad";
		$this->phpclass = "triadSpecter";
		$this->imagePath = "img/ships/triadSpecter.png";
		$this->shipClass = "Neutrality: Specter";
		$this->canvasSize = 100;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 1; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

        $this->agile = true;

        $this->gravitic = true;
		$this->advancedArmor = true;  
		$this->skinDancer = true; 	

		$this->forwardDefense = 12;
		$this->sideDefense = 13;

		$this->turncost = 0.33;
		$this->turndelaycost = 0.33;
		$this->accelcost = 2;
		$this->rollcost = 3;
		$this->pivotcost = 2;
		$this->iniativebonus = 70;

		$this->notes = "Cannot control fighters";		
		$this->notes .= "<br>Atmospheric Capable";		

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');

		$this->addPrimarySystem(new Reactor(7, 20, 0, 0));
		$this->addPrimarySystem(new CnC(8, 12, 0, 0));
		$scanner = new Scanner(8, 20, 0, 12);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(7, 20, 0, 12, 4));
        $this->addPrimarySystem(new StructureSelfRepair(7, 12, 6)); //armor, structure, output
        $this->addPrimarySystem(new AdvParticleBlastGun(7, 16, 7, 0, 360));	
        $this->addPrimarySystem(new GraviticThruster(7, 15, 0, 9, 3)); 		
        $this->addPrimarySystem(new GraviticThruster(7, 15, 0, 9, 4)); 				

		$this->addFrontSystem(new GraviticThruster(6, 11, 0, 4, 1));
		$this->addFrontSystem(new GraviticThruster(6, 11, 0, 4, 1));
        $this->addFrontSystem(new AdvParticleBlastGun(6, 16, 8, 240, 360));	
        $this->addFrontSystem(new AdvParticleBlastGun(6, 16, 8, 300, 60));	
        $this->addFrontSystem(new HyperplasmaStream(7, 10, 7, 300, 60));	
        $this->addFrontSystem(new AdvParticleBlastGun(6, 16, 8, 300, 60));	
        $this->addFrontSystem(new AdvParticleBlastGun(6, 16, 8, 0, 120));	

		$this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));
        $this->addAftSystem(new AdvParticleBlastGun(6, 16, 8, 120, 240));	
        $this->addAftSystem(new SelfRepair(7, 4, 2)); //armor, structure, output
		$this->addAftSystem(new JumpEngine(7, 16, 7, 10));        
	
        $this->addPrimarySystem(new Structure( 8, 60));
		
				$this->hitChart = array(
                0=> array(
                        11 => "Thruster",
						12 => "Structure Self Repair",
                        14 => "Scanner",
                        16 => "Engine",
						17 => "Advanced Particle Blast Gun",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Hyperplasma Stream",
						9 => "Advanced Particle Blast Gun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
						9 => "Self Repair",
						11 => "Advanced Particle Blast Gun",
                        13 => "Jump Engine",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}



?>
