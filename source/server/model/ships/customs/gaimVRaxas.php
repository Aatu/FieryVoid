<?php
class gaimVRaxas extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1500;
		$this->faction = 'Custom Ships';
		$this->phpclass = "gaimVRaxas";
		$this->imagePath = "img/ships/GaimVRaxas.png";
		$this->shipClass = "V-Raxas Experimental Platform";
		$this->shipSizeClass = 3;
//		$this->fighters = array("normal"=>12);
		$this->occurence = "unique";
		$this->unofficial = true;
	    $this->notes = 'Unique ship created for the Queens Gambit campaign';				
	    
        $this->isd = 2266;

		$this->forwardDefense = 15;
		$this->sideDefense = 15;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 5;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = 0*5;

//		$this->advancedArmor = true;  			


		$this->addPrimarySystem(new Reactor(5, 24, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 3, 6));
		$this->addPrimarySystem(new Engine(5, 13, 0, 8, 4));
        $this->addPrimarySystem(new SelfRepair(6, 9, 3)); //armor, structure, output		
//		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new MolecularPulsar(3, 8, 2, 0, 360));
		$this->addPrimarySystem(new MolecularPulsar(3, 8, 2, 0, 360));
 		$AAC = $this->createAdaptiveArmorController(3, 1, 0); //$AAtotal, $AApertype, $AApreallocated
		$this->addPrimarySystem( $AAC );       		

		$this->addFrontSystem(new Thruster(4, 20, 0, 5, 1));
		$this->addFrontSystem(new ImprovedNeutronLaser(4, 11, 7, 300, 60));
		$this->addFrontSystem(new ImprovedNeutronLaser(4, 11, 7, 300, 60));
		$this->addFrontSystem(new BoltAccelerator(4, 9, 7, 240, 60));
		$this->addFrontSystem(new BoltAccelerator(4, 9, 7, 300, 120));				
//		$this->addFrontSystem(new Bulkhead(0, 2));
//		$this->addFrontSystem(new Bulkhead(0, 2));

		$this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
		$this->addAftSystem(new BoltAccelerator(4, 9, 5, 120, 240));
		$this->addAftSystem(new BoltAccelerator(4, 9, 5, 120, 240));
        $this->addAftSystem(new EMShield(4, 6, 0, 3, 120, 300));
        $this->addAftSystem(new EMShield(4, 6, 0, 3, 60, 240));		
//		$this->addAftSystem(new Bulkhead(0, 2));
//		$this->addAftSystem(new Bulkhead(0, 2));
			

		$this->addLeftSystem(new Thruster(4, 15, 0, 3, 3));
		$this->addLeftSystem(new MolecularPulsar(4, 8, 2, 180, 360));
		$this->addLeftSystem(new MolecularPulsar(4, 8, 2, 180, 360));
        $this->addLeftSystem(new EMShield(4, 6, 0, 3, 240, 60));			
//		$this->addLeftSystem(new Bulkhead(0, 2));
//		$this->addLeftSystem(new Bulkhead(0, 2));

		$this->addRightSystem(new Thruster(4, 15, 0, 3, 4));
		$this->addRightSystem(new MolecularPulsar(4, 8, 2, 0, 180));
		$this->addRightSystem(new MolecularPulsar(4, 8, 2, 0, 180));
        $this->addRightSystem(new EMShield(4, 6, 0, 3, 300, 120));		
//		$this->addRightSystem(new Bulkhead(0, 2));
//		$this->addRightSystem(new Bulkhead(0, 2));
        
        $this->addFrontSystem(new Structure( 5, 56));
        $this->addAftSystem(new Structure( 5, 44));
        $this->addLeftSystem(new Structure( 6, 65));
        $this->addRightSystem(new Structure( 6, 65));
        $this->addPrimarySystem(new Structure( 6, 65));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Molecular Pulsar",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Self Repair",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Improved Neutron Laser",
                        10 => "Heavy Bolt Accelerator",      
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Heavy Bolt Accelerator",
						11 => "EM Shield",                          
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Molecular Pulsar",
						11 => "EM Shield",                          
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Molecular Pulsar",
						11 => "EM Shield",                          
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
