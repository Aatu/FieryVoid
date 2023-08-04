<?php
class CraytanEpiron extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "ZNexus Craytan";
        $this->phpclass = "CraytanEpiron";
        $this->imagePath = "img/ships/Nexus/CraytanEpiron.png";
        $this->shipClass = "Epiron Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; 
		$this->unofficial = true;
//        $this->limited = 33;

        $this->fighters = array("assault shuttles"=>6);

		$this->isd = 2047;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new CargoBay(4, 9));
		
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new NexusLightAssaultCannon(3, 6, 3, 240, 360));
		$this->addFrontSystem(new NexusLightAssaultCannon(3, 6, 3, 0, 120));
		$this->addFrontSystem(new NexusLightSentryGun(2, 5, 1, 240, 60));
		$this->addFrontSystem(new NexusLightSentryGun(2, 5, 1, 300, 120));

        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
		$this->addAftSystem(new NexusLightAssaultCannon(3, 6, 3, 180, 300));
		$this->addAftSystem(new NexusLightAssaultCannon(3, 6, 3, 60, 180));
		$this->addAftSystem(new NexusLightSentryGun(2, 5, 1, 120, 300));
		$this->addAftSystem(new NexusLightSentryGun(2, 5, 1, 60, 240));

        $this->addLeftSystem(new NexusAssaultCannon(4, 8, 5, 300, 360));
		$this->addLeftSystem(new NexusCIDS(2, 4, 2, 180, 360));
		$this->addLeftSystem(new NexusLightSentryGun(2, 5, 1, 180, 360));
		$this->addLeftSystem(new CargoBay(2, 16));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new NexusAssaultCannon(4, 8, 5, 0, 60));
		$this->addRightSystem(new NexusCIDS(2, 4, 2, 0, 180));
		$this->addRightSystem(new NexusLightSentryGun(2, 5, 1, 0, 180));
		$this->addRightSystem(new CargoBay(2, 16));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(3, 42));
        $this->addLeftSystem(new Structure(3, 48));
        $this->addRightSystem(new Structure(3, 48));
        $this->addPrimarySystem(new Structure(4, 54));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					10 => "Cargo Bay",
					13 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					8 => "Light Assault Cannon",
					10 => "Light Sentry Gun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Light Assault Cannon",
					10 => "Light Sentry Gun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Cargo Bay",
					7 => "Close-In Defense System",
					8 => "Light Sentry Gun",
					10 => "Assault Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Cargo Bay",
					7 => "Close-In Defense System",
					8 => "Light Sentry Gun",
					10 => "Assault Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
