<?php

class triadFiend extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 3200;
        $this->faction = "The Triad";
        $this->phpclass = "triadFiend";
        $this->imagePath = "img/ships/triadFiend.png";
        $this->shipClass = "Chaos: Fiend";
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 2; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->agile = true;
				
        $this->gravitic = true;
		$this->advancedArmor = true;  
		$this->skinDancer = true; 	

        $this->forwardDefense = 12;
        $this->sideDefense = 16;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        $this->iniativebonus = 40;

		$this->fighters = array("Triad Fighter"=>6);

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');
         
        $this->addPrimarySystem(new Reactor(8, 30, 0, 0));
        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
//		$scanner = new Scanner(8, 24, 0, 15);
//			$scanner->markAdvanced();
//			$this->addPrimarySystem($scanner);			
        $scanner = new ElintScanner(8, 16, 0, 12);
			$scanner->markMindrider();
			$this->addPrimarySystem($scanner);	        
		$this->addPrimarySystem(new Engine(8, 20, 0, 12, 3));
        $this->addPrimarySystem(new StructureSelfRepair(8, 18, 18)); //armor, structure, output
        $this->addPrimarySystem(new GraviticThruster(7, 20, 0, 6, 3)); 		
        $this->addPrimarySystem(new GraviticThruster(7, 20, 0, 6, 4)); 				

        $this->addFrontSystem(new GraviticThruster(7, 10, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(7, 10, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(7, 10, 0, 3, 1));
        $this->addFrontSystem(new PlasmaDriver(5, 6, 6, 300, 60));	
        $this->addFrontSystem(new HyperplasmaCutter(7, 16, 9, 240, 360));	
        $this->addFrontSystem(new SolarBlaster(6, 18, 10, 270, 90));	
        $this->addFrontSystem(new HyperplasmaCutter(7, 16, 9, 0, 120));	
        $this->addFrontSystem(new PlasmaDriver(5, 6, 6, 300, 60));	
        
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new PlasmaDriver(5, 6, 6, 120, 240));	
		$this->addAftSystem(new JumpEngine(8, 25, 5, 8));        
        $this->addAftSystem(new SelfRepair(8, 15, 6)); //armor, structure, output
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 60));
        $this->addAftSystem(new Structure( 8, 90));
        $this->addPrimarySystem(new Structure( 8, 60));

        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
					13 => "Structure Self Repair",
                    15 => "ELINT Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "Solar Blaster",
					9 => "Plasma Driver",
					12 => "Hyperplasma Cutter",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Self Repair",
                    10 => "Plasma Driver",
                    13 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
      );
    }
}

?>
