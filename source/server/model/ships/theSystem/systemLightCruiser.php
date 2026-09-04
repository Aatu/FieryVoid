<?php

class systemLightCruiser extends BaseShipNoAft{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 3500;
        $this->faction = "The System";
        $this->phpclass = "systemLightCruiser";
        $this->imagePath = "img/ships/systemLightCruiser.png";
        $this->shipClass = "Light Cruiser";
		$this->shipSizeClass = 3;
		$this->unofficial = true;

		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->gravitic = true;
		$this->advancedArmor = true;  

	    $this->isd = 'Ancient';

		$this->notes = "Resistant to criticals";		

		$this->critRollMod -= 2;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;

        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 5;
        $this->pivotcost = 3;
        $this->iniativebonus = 15;

		$this->fighters = array("System Drone"=>6);

		/*The System use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'SystemShip');

		$this->addPrimarySystem(new Reactor(5, 18, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$scanner = new Scanner(6, 22, 0, 11);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(5, 12, 0, 14, 4));
		$this->addPrimarySystem(new Hangar(5, 4, 2));
		$this->addPrimarySystem(new ThirdspaceShieldGenerator(6, 15, 0, 30, 3, 8)); //$armor, $maxhealth, $power used, output, maxBoost, boostEfficiency
        $this->addPrimarySystem(new SelfRepair(6, 8, 6)); //armor, structure, output
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 7, 2));
		$this->addAftSystem(new GraviticThruster(5, 15, 0, 7, 2));
		$this->addAftSystem(new JumpEngine(6, 25, 6, 8));        

		$this->addFrontSystem(new GraviticThruster(5, 12, 0, 3, 1));
		$this->addFrontSystem(new GraviticThruster(5, 12, 0, 3, 1));
		$this->addFrontSystem(new GraviticThruster(5, 12, 0, 3, 1));
        $this->addFrontSystem(new FusionBomb(5, 9, 5, 240, 120));
        $this->addFrontSystem(new SeekerTorp(5, 6, 5, 270, 90));
        $this->addFrontSystem(new PlasmaArray(5, 8, 4, 270, 90));
		$this->addFrontSystem(new ThirdspaceShield(0, 160, 80, 300, 60, 'F'));	
		
		$this->addLeftSystem(new GraviticThruster(5, 25, 0, 8, 3));
        $this->addLeftSystem(new NeutronBeam(5, 14, 8, 300, 60));
        $this->addLeftSystem(new PlasmaArray(5, 8, 4, 180, 360));
		$this->addLeftSystem(new ThirdspaceShield(0, 200, 100, 180, 300, 'L'));			

		$this->addRightSystem(new GraviticThruster(5, 25, 0, 8, 4));
        $this->addRightSystem(new NeutronBeam(5, 14, 8, 300, 60));
        $this->addRightSystem(new PlasmaArray(5, 8, 4, 0, 180));
 		$this->addRightSystem(new ThirdspaceShield(0, 200, 100, 60, 180, 'R'));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 70));
        $this->addLeftSystem(new Structure( 6, 70));
        $this->addRightSystem(new Structure( 6, 70));
        $this->addPrimarySystem(new Structure( 6, 60));
    
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
        				5 => "Thruster",
        				7 => "Fusion Bomb",
        				9 => "Seeker Torpedo",
        				11 => "Plasma Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				5 => "Thruster",
        				8 => "Neutron Beam",
        				10 => "Plasma Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				8 => "Neutron Beam",
        				10 => "Plasma Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
