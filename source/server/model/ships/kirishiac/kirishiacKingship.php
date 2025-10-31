<?php
class kirishiacKingship extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 5700;
	$this->faction = "Kirishiac Lords";
        $this->phpclass = "kirishiacKingship";
        $this->imagePath = "img/ships/kirishiacKingship.png";
        $this->shipClass = "Kingship";
        $this->shipSizeClass = 3;
        $this->limited = 33;
	    $this->isd = "Ancient";
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	    $this->notes = 'Atmospheric capable.';

        $this->gravitic = true;
		$this->advancedArmor = true;   
		$this->hardAdvancedArmor = true;
		
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 2;
		$this->iniativebonus = 10;
	
        $this->addPrimarySystem(new Reactor(7, 35, 0, -30));
        $this->addPrimarySystem(new CnC(8, 24, 0, 0));
		$scanner = new Scanner(7, 24, 0, 14);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
        $this->addPrimarySystem(new Engine(7, 25, 0, 12, 4));
        $this->addPrimarySystem(new JumpEngine(8, 25, 6, 8));
        $this->addPrimarySystem(new SelfRepair(7, 12, 6)); //armor, structure, output

		$this->addFrontSystem(new HypergravitonBlaster(7, 30, 15, 300, 60));
		$this->addFrontSystem(new HypergravitonBlaster(7, 30, 15, 240, 360));
        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));
		
		$this->addAftSystem(new HypergravitonBlaster(7, 30, 15, 120, 240));
		$this->addAftSystem(new AntigravityBeam(6, 6, 3, 90, 270));
		$this->addAftSystem(new HypergravitonBlaster(7, 30, 15, 300, 60));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));

		$this->addLeftSystem(new HypergravitonBlaster(7, 30, 15, 180, 300));
		$this->addLeftSystem(new AntigravityBeam(6, 6, 3, 150, 330));
        $this->addLeftSystem(new GraviticThruster(7, 25, 0, 7, 3));

		$this->addRightSystem(new HypergravitonBlaster(7, 30, 15, 0, 120));
		$this->addRightSystem(new AntigravityBeam(6, 6, 3, 330, 150));
        $this->addRightSystem(new GraviticThruster(7, 25, 0, 7, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 8, 78));  
        $this->addAftSystem(new Structure( 8, 96));   //18 added for missing orbitals
        $this->addLeftSystem(new Structure( 8, 118));  //18 added for missing orbitals
        $this->addRightSystem(new Structure( 8, 118));  //18 added for missing orbitals
        $this->addPrimarySystem(new Structure( 8, 84));

        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "Self Repair",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Jump Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    10 => "Hypergraviton Blaster",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
                    6 => "Antigravity Beam",
					12 => "Hypergraviton Blaster",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
                    6 => "Antigravity Beam",
					10 => "Hypergraviton Blaster",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    5 => "Thruster",
                    6 => "Antigravity Beam",
					10 => "Hypergraviton Blaster",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }

}
