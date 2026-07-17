<?php
class systemLiberator extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 4000;
		$this->faction = "The System";
		$this->phpclass = "systemLiberator";
		$this->imagePath = "img/ships/systemLiberator.png";
		$this->shipClass = "Liberator";
		$this->shipSizeClass = 3;
//		$this->fighters = array("normal"=>12);
		$this->unofficial = true;
	    
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->gravitic = true;
		$this->advancedArmor = true;  

	    $this->isd = 'Ancient';

		$this->notes = "Can control 12 drones";		

		$this->critRollMod -= 2;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';

		$this->forwardDefense = 17;
		$this->sideDefense = 17;

        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
		$this->iniativebonus = 4 *5;

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$scanner = new Scanner(6, 22, 0, 13);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(5, 12, 0, 14, 4));
		$this->addPrimarySystem(new Hangar(5, 4, 2));
		$this->addPrimarySystem(new ThirdspaceShieldGenerator(6, 20, 0, 40, 3, 8)); //$armor, $maxhealth, $power used, output, maxBoost, boostEfficiency
        $this->addPrimarySystem(new SelfRepair(6, 8, 4)); //armor, structure, output
        $this->addPrimarySystem(new SelfRepair(6, 8, 4)); //armor, structure, output

		$this->addFrontSystem(new GraviticThruster(5, 12, 0, 3, 1));
		$this->addFrontSystem(new GraviticThruster(5, 15, 0, 5, 1));
		$this->addFrontSystem(new GraviticThruster(5, 12, 0, 3, 1));
        $this->addFrontSystem(new NeutronBlaster(5, 15, 8, 300, 60));
        $this->addFrontSystem(new PlasmaDriver(5, 6, 6, 300, 60));
		$this->addFrontSystem(new ThirdspaceShield(0, 200, 100, 330, 30, 'F'));	

		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
		$this->addAftSystem(new GraviticThruster(5, 20, 0, 6, 2));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 4, 2));
        $this->addAftSystem(new PlasmaDriver(5, 6, 6, 120, 240));
		$this->addAftSystem(new ThirdspaceShield(0, 160, 80, 150, 210, 'A'));		
		$this->addAftSystem(new JumpEngine(6, 25, 6, 8));        
		
		$this->addLeftSystem(new GraviticThruster(5, 15, 0, 4, 3));
		$this->addLeftSystem(new GraviticThruster(5, 15, 0, 4, 3));
        $this->addLeftSystem(new NeutronBlaster(5, 15, 8, 300, 60));
        $this->addLeftSystem(new PlasmaDriver(5, 6, 6, 240, 360));
		$this->addLeftSystem(new ThirdspaceShield(0, 300, 150, 210, 330, 'L'));			

		$this->addRightSystem(new GraviticThruster(5, 15, 0, 4, 4));
		$this->addRightSystem(new GraviticThruster(5, 15, 0, 4, 4));
        $this->addRightSystem(new NeutronBlaster(5, 15, 8, 300, 60));
        $this->addRightSystem(new PlasmaDriver(5, 6, 6, 0, 120));
 		$this->addRightSystem(new ThirdspaceShield(0, 300, 150, 30, 150, 'R'));
       
        $this->addFrontSystem(new Structure( 6, 80));
        $this->addAftSystem(new Structure( 6, 70));
        $this->addLeftSystem(new Structure( 6, 80));
        $this->addRightSystem(new Structure( 6, 80));
        $this->addPrimarySystem(new Structure( 7, 70));
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        10 => "Shield Generator",
						12 => "Self Repair",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
                        8 => "Neutron Blaster",
						10 => "Plasma Driver",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
						8 => "Jump Engine",
                        10 => "Plasma Driver",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        5 => "Thruster",
                        8 => "Neutron Blaster",
						10 => "Plasma Driver",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        5 => "Thruster",
                        8 => "Neutron Blaster",
						10 => "Plasma Driver",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
