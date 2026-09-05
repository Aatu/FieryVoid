<?php

class triadAngel extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 2850;
        $this->faction = "The Triad";
        $this->phpclass = "triadAngel";
        $this->imagePath = "img/ships/triadAngel.png";
        $this->shipClass = "Order: Angel";
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 2; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

		$this->triadOrder = true;	//Important to ensure immunity from Flare Generator!	

        $this->gravitic = true;
		$this->advancedArmor = true;  

        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.75;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 50;

		$this->fighters = array("Triad Fighter"=>6);

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');
         
        $this->addPrimarySystem(new Reactor(8, 25, 0, 0));
        $this->addPrimarySystem(new CnC(9, 16, 0, 0));
		$scanner = new Scanner(8, 20, 0, 14);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(8, 18, 0, 12, 3));
        $this->addPrimarySystem(new StructureSelfRepair(8, 20, 21)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(8, 15, 7)); //armor, structure, output
        $this->addPrimarySystem(new FlareGenerator(8, 16, 8, 0, 360));	
        $this->addPrimarySystem(new GraviticThruster(8, 20, 0, 7, 3)); 		
        $this->addPrimarySystem(new GraviticThruster(8, 20, 0, 7, 4)); 				

        $this->addFrontSystem(new GraviticThruster(8, 10, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(8, 10, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(8, 10, 0, 3, 1));
        $this->addFrontSystem(new PhotonicPrismBeam(8, 24, 8, 210, 30));	
        $this->addFrontSystem(new PhotonicPrismBeam(8, 24, 8, 330, 150));	
        
        $this->addAftSystem(new GraviticThruster(8, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(8, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(8, 15, 0, 4, 2));
        $this->addAftSystem(new PhotonicPrismBeam(8, 24, 8, 90, 270));	
		$this->addAftSystem(new JumpEngine(8, 15, 4, 8));        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 8, 75));
        $this->addAftSystem(new Structure( 8, 63));
        $this->addPrimarySystem(new Structure( 9, 65));

        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Thruster",
					11 => "Flare Generator",
					12 => "Self Repair",
					13 => "Structure Self Repair",
                    15 => "Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
                    9 => "Photonic Prism Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Photonic Prism Beam",
                    10 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
      );
    }
}

?>
