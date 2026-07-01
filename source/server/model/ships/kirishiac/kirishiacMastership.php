<?php
class kirishiacMastership extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 2400;
	$this->faction = "Kirishiac Lords";
        $this->phpclass = "kirishiacMastership";
        $this->imagePath = "img/ships/kirishiacKingship.png";
        $this->shipClass = "Mastership";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>18);
        //$this->limited = 33;
	    $this->isd = "Ancient";
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	    $this->notes = 'Atmospheric capable.';

        $this->gravitic = true;
		$this->advancedArmor = true;   
		$this->hardAdvancedArmor = true;
		
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 2;
		$this->iniativebonus = 2*5;
	
        $this->addPrimarySystem(new Reactor(7, 35, 0, 0));
        $this->addPrimarySystem(new CnC(8, 24, 0, 0));
		$scanner = new Scanner(7, 24, 0, 14);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
        $this->addPrimarySystem(new Engine(7, 25, 0, 12, 4));
        $this->addPrimarySystem(new JumpEngine(8, 25, 6, 8));
        $this->addPrimarySystem(new SelfRepair(7, 12, 6)); //armor, structure, output

		$this->addFrontSystem(new GraviticAugmenter(7, 0, 0, 270, 90));
		$this->addFrontSystem(new AntigravityBeam(6, 6, 3, 270, 90));
		$this->addFrontSystem(new AntigravityBeam(6, 6, 3, 270, 90));        
		$this->addFrontSystem(new Hangar(5, 18, 12));        
        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(7, 15, 0, 4, 1));
		
		$this->addAftSystem(new GraviticAugmenter(7, 0, 0, 90, 270));
		$this->addAftSystem(new AntigravityBeam(6, 6, 3, 90, 270));
		$this->addAftSystem(new AntigravityBeam(6, 6, 3, 90, 270));   
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(7, 15, 0, 4, 2));

		$this->addLeftSystem(new GraviticAugmenter(7, 0, 0, 0, 180));
		$this->addLeftSystem(new AntigravityBeam(6, 6, 3, 0, 180));
		$this->addLeftSystem(new AntigravityBeam(6, 6, 3, 0, 180)); 
		$this->addLeftSystem(new Hangar(5, 18, 12, 5));         
        $this->addLeftSystem(new GraviticThruster(7, 25, 0, 7, 3));

		$this->addRightSystem(new GraviticAugmenter(7, 0, 0, 180, 360));
		$this->addRightSystem(new AntigravityBeam(6, 6, 3, 180, 360));
		$this->addRightSystem(new AntigravityBeam(6, 6, 3, 180, 360)); 
		$this->addRightSystem(new Hangar(5, 18, 12, 1));   
        $this->addRightSystem(new GraviticThruster(7, 25, 0, 7, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 8, 72));  
        $this->addAftSystem(new Structure( 8, 72));   //18 added for missing orbitals
        $this->addLeftSystem(new Structure( 8, 80));  //18 added for missing orbitals
        $this->addRightSystem(new Structure( 8, 80));  //18 added for missing orbitals
        $this->addPrimarySystem(new Structure( 8, 72));

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
                    6 => "Gravitic Augmenter",
                    9 => "Antigravity Beam",
                    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
                    9 => "Gravitic Augmenter",
                    11 => "Antigravity Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
                    6 => "Gravitic Augmenter",
                    9 => "Antigravity Beam",
                    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
                    6 => "Gravitic Augmenter",
                    9 => "Antigravity Beam",
                    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }

}
