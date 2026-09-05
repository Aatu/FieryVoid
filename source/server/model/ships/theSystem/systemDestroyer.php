<?php
class systemDestroyer extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 900;
        $this->faction = "The System";
        $this->phpclass = "systemDestroyer";
        $this->imagePath = "img/ships/systemBallisticsShip.png";
        $this->shipClass = "Ballistics Destroyer";
		$this->shipSizeClass = 2;
		$this->unofficial = true;

		$this->factionAge = 3; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->gravitic = true;
		$this->advancedArmor = true;  

	    $this->isd = 'Ancient';

		$this->notes = "Resistant to criticals";		

		$this->critRollMod -= 2;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

		/*The System use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'SystemShip');
         
		$this->addPrimarySystem(new Reactor(5, 14, 0, 0));
		$this->addPrimarySystem(new CnC(6, 12, 0, 0));
		$scanner = new Scanner(6, 22, 0, 10);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(5, 12, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(5, 2, 2));
		$this->addPrimarySystem(new ThirdspaceShieldGenerator(6, 12, 0, 20, 3, 6)); //$armor, $maxhealth, $power used, output, maxBoost, boostEfficiency
        $this->addPrimarySystem(new SelfRepair(6, 8, 4)); //armor, structure, output
		$this->addPrimarySystem(new GraviticThruster(5, 20, 0, 8, 3));
		$this->addPrimarySystem(new GraviticThruster(5, 20, 0, 8, 4));

		$this->addFrontSystem(new GraviticThruster(5, 10, 0, 5, 1));
		$this->addFrontSystem(new GraviticThruster(5, 10, 0, 5, 1));
        $this->addFrontSystem(new FusionBomb(5, 9, 5, 240, 120));
        $this->addFrontSystem(new SeekerTorp(5, 6, 5, 270, 90));
        $this->addFrontSystem(new SeekerTorp(5, 6, 5, 270, 90));
        $this->addFrontSystem(new SeekerTorp(5, 6, 5, 270, 90));
        $this->addFrontSystem(new PlasmaArray(5, 8, 4, 270, 90));
		$this->addFrontSystem(new ThirdspaceShield(0, 120, 60, 270, 90, 'F'));	

		$this->addAftSystem(new GraviticThruster(5, 18, 0, 5, 2));
		$this->addAftSystem(new GraviticThruster(5, 18, 0, 5, 2));
        $this->addAftSystem(new SeekerTorp(5, 6, 5, 90, 270));
        $this->addAftSystem(new SeekerTorp(5, 6, 5, 90, 270));
		$this->addAftSystem(new ThirdspaceShield(0, 120, 60, 90, 270, 'A'));		
		$this->addAftSystem(new JumpEngine(6, 15, 6, 12));        
        $this->addAftSystem(new PlasmaArray(5, 8, 4, 90, 270));
    
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 65));
        $this->addAftSystem(new Structure( 6, 60));
        $this->addPrimarySystem(new Structure( 6, 60));
        
        $this->hitChart = array(
                0=> array(
                    7 => "Structure",
					8 => "Self Repair",
                    10 => "Thruster",
                    12 => "Scanner",
                    15 => "Engine",
					16 => "Shield Generator",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    5 => "Thruster",
                    7 => "Fusion Bomb",
                    9 => "Plasma Array",
                    12 => "Seeker Torpedo",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    5 => "Thruster",
                    7 => "Jump Engine",
					9 => "Plasma Array",
					11 => "Seeker Torpedo",
                    18 => "Structure",
                    20 => "Primary",
			),
		); 
    }

}



?>
