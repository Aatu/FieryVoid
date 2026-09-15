<?php
class systemFrigate extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 900;
		$this->faction = "The System";
        $this->phpclass = "systemFrigate";
        $this->imagePath = "img/ships/systemPursuitShip2.png";
        $this->shipClass = "Pursuit Ship";
        $this->agile = true;

		$this->unofficial = true;
	    
		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->gravitic = true;
		$this->advancedArmor = true;  

	    $this->isd = 'Ancient';

		$this->notes = "Resistant to criticals";		

		$this->critRollMod -= 2;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 75; 

		/*The System use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'SystemShip');

		$this->addPrimarySystem(new Reactor(5, 12, 0, 0));
		$this->addPrimarySystem(new CnC(6, 10, 0, 0));
		$scanner = new Scanner(6, 16, 0, 10);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(5, 12, 0, 12, 4));
		$this->addPrimarySystem(new Hangar(5, 1, 1));
		$this->addPrimarySystem(new ThirdspaceShieldGenerator(6, 12, 0, 20, 3, 6)); //$armor, $maxhealth, $power used, output, maxBoost, boostEfficiency
        $this->addPrimarySystem(new SelfRepair(6, 6, 3)); //armor, structure, output
		$this->addPrimarySystem(new GraviticThruster(5, 15, 0, 8, 3));
		$this->addPrimarySystem(new GraviticThruster(5, 15, 0, 8, 4));

		$this->addFrontSystem(new GraviticThruster(5, 10, 0, 5, 1));
		$this->addFrontSystem(new GraviticThruster(5, 10, 0, 5, 1));
        $this->addFrontSystem(new PlasmaArray(5, 8, 4, 270, 90));
        $this->addFrontSystem(new NeutronCannon(6, 16, 9, 330, 30));
        $this->addFrontSystem(new PlasmaArray(5, 8, 4, 270, 90));
 		$this->addFrontSystem(new ThirdspaceShield(0, 100, 50, 270, 90, 'F'));	

		$this->addAftSystem(new GraviticThruster(5, 15, 0, 6, 2));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 6, 2));
        $this->addAftSystem(new PlasmaArray(5, 8, 4, 180, 360));
        $this->addAftSystem(new PlasmaArray(5, 8, 4, 0, 180));
		$this->addAftSystem(new ThirdspaceShield(0, 80, 40, 90, 270, 'A'));		
		$this->addAftSystem(new JumpEngine(6, 10, 6, 15));        
       
        $this->addPrimarySystem(new Structure( 6, 75));

		$this->hitChart = array(
                0=> array(
                        7 => "Thruster",
						9 => "Self Repair",
                        11 => "Scanner",
                        15 => "Engine",
						16 => "Shield Generator",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        9 => "Neutron Cannon",
                        12 => "Plasma Array",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Plasma Array",
						11 => "Jump Engine",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
	    
		
    }

}



?>
