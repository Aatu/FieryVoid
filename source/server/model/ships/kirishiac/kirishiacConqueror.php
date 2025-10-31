<?php
class kirishiacConqueror extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2300;
		$this->faction = "Kirishiac Lords";
        $this->phpclass = "kirishiacConqueror";
        $this->shipClass = "Conqueror";
        $this->imagePath = "img/ships/kirishiacConqueror2.png";
        $this->canvasSize = 200;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 2; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
	    $this->notes = 'Atmospheric capable.';
				
        $this->gravitic = true;
		$this->advancedArmor = true;   
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
		$this->iniativebonus = 8 *5;
		
        $this->addPrimarySystem(new CnC(7, 16, 0, 0));
		$scanner = new Scanner(6, 24, 0, 10);
		$scanner->markAdvanced();
		$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Reactor(6, 24, 0, 0));
		$this->addPrimarySystem(new Engine(7, 18, 0, 12, 4));
        $this->addPrimarySystem(new SelfRepair(7, 6, 3)); //armor, structure, output
        $this->addPrimarySystem(new GraviticThruster(7, 20, 0, 7, 3));
        $this->addPrimarySystem(new GraviticThruster(7, 20, 0, 7, 4));
		$this->addPrimarySystem(new JumpEngine(6, 16, 6, 12));
		
		$this->addFrontSystem(new MedAntigravityBeam(5, 6, 2, 210, 30));
        $this->addFrontSystem(new UltraMatterCannon(5, 13, 7, 240, 360));
        $this->addFrontSystem(new HypergravitonBeam(6, 20, 12, 300, 60));	
        $this->addFrontSystem(new MedAntigravityBeam(5, 6, 2, 270, 90));
        $this->addFrontSystem(new UltraMatterCannon(5, 13, 7, 0, 120));
        $this->addFrontSystem(new UltraMatterCannon(5, 13, 7, 300, 60));
        $this->addFrontSystem(new MedAntigravityBeam(5, 6, 2, 330, 150));
        $this->addFrontSystem(new GraviticThruster(6, 13, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(6, 13, 0, 4, 1));

        $this->addAftSystem(new MedAntigravityBeam(5, 6, 2, 150, 330));
        $this->addAftSystem(new MedAntigravityBeam(5, 6, 2, 90, 270));
        $this->addAftSystem(new MedAntigravityBeam(5, 6, 2, 30, 210));
        $this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(6, 13, 0, 4, 2));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(7, 108));  // NOTE: Currently does not have orbitals. 45 structure added to the forward structure
        $this->addAftSystem(new Structure(7, 60));
        $this->addPrimarySystem(new Structure(7, 105)); // NOTE: Currently does not have orbitals. 45 structure added to the aft structure
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				9 => "Structure",
				11 => "Thruster",
				12 => "Self Repair",
				14 => "Scanner",
				16 => "Engine",
				17 => "Jump Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				4 => "Thruster",
				8 => "Hypergraviton Beam",
				10 => "Ultra Matter Cannon",
				11 => "Medium Antigravity Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				7 => "Medium Antigravity Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
