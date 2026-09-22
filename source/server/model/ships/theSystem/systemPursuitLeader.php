<?php

class systemPursuitLeader extends BaseShipNoAft{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 2500;
        $this->faction = "The System";
        $this->phpclass = "systemPursuitLeader";
        $this->imagePath = "img/ships/systemPursuitShip2.png";
        $this->shipClass = "Pursuit Leader";
		$this->shipSizeClass = 3;
		$this->unofficial = true;
        $this->limited = 10;

		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->gravitic = true;
		$this->advancedArmor = true;  

	    $this->isd = 'Ancient';

		$this->notes = "Resistant to criticals";		

		$this->critRollMod -= 2;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;

        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 5;
        $this->pivotcost = 3;
        $this->iniativebonus = 15;

		/*The System use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'SystemShip');

		$this->addPrimarySystem(new Reactor(5, 18, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$scanner = new Scanner(6, 22, 0, 11);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(5, 12, 0, 12, 4));
		$this->addPrimarySystem(new Hangar(5, 4, 2));
		$this->addPrimarySystem(new ThirdspaceShieldGenerator(6, 15, 0, 30, 3, 6)); //$armor, $maxhealth, $power used, output, maxBoost, boostEfficiency
        $this->addPrimarySystem(new SelfRepair(6, 7, 4)); //armor, structure, output
		$this->addAftSystem(new GraviticThruster(5, 20, 0, 12, 2));
		$this->addAftSystem(new JumpEngine(6, 25, 6, 8));        
		$this->addAftSystem(new ThirdspaceShield(0, 140, 70, 90, 270, 'A'));	

		$this->addFrontSystem(new GraviticThruster(5, 12, 0, 3, 1));
		$this->addFrontSystem(new GraviticThruster(5, 12, 0, 3, 1));
        $this->addFrontSystem(new FusionBomb(5, 9, 5, 240, 120));
        $this->addFrontSystem(new PlasmaArray(5, 8, 4, 270, 90));
        $this->addFrontSystem(new HvyNeutronCannon(6, 16, 9, 330, 30));
        $this->addFrontSystem(new SeekerTorp(5, 6, 5, 0, 360));
		$this->addFrontSystem(new ThirdspaceShield(0, 140, 70, 270, 90, 'F'));	
		
		$this->addLeftSystem(new GraviticThruster(5, 20, 0, 6, 3));
        $this->addLeftSystem(new PlasmaArray(5, 8, 4, 240, 60));
        $this->addLeftSystem(new PlasmaArray(5, 8, 4, 120, 300));

		$this->addRightSystem(new GraviticThruster(5, 20, 0, 6, 4));
        $this->addRightSystem(new PlasmaArray(5, 8, 4, 300, 120));
        $this->addRightSystem(new PlasmaArray(5, 8, 4, 60, 240));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 40));
        $this->addLeftSystem(new Structure( 6, 55));
        $this->addRightSystem(new Structure( 6, 55));
        $this->addPrimarySystem(new Structure( 6, 45));
    
            $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				9 => "2:Jump Engine",
        				11 => "2:Thruster",
        				13 => "Scanner",
        				15 => "Engine",
						16 => "Self Repair",
        				17 => "Hangar",
        				18 => "Shield Generator",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Fusion Bomb",
        				9 => "Heavy Neutron Cannon",
						10 => "Seeker Torpedo",
        				12 => "Plasma Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				5 => "Thruster",
        				9 => "Plasma Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				9 => "Plasma Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
