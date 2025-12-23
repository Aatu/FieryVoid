<?php
class MakarKammin extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "MakarKammin";
        $this->imagePath = "img/ships/Nexus/makar_pycirin2.png";
        $this->shipClass = "Kammin Jumpcruiser";
			$this->variantOf = "Pycirin Heavy Cruiser";
			$this->occurence = "rare";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; 
		$this->unofficial = true;

		$this->isd = 2052;

        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 25, 0, 0));
        $this->addPrimarySystem(new CnC(4, 24, 0, 0));
 		$this->addPrimarySystem(new Scanner(4, 16, 5, 6));
		$this->addPrimarySystem(new Hangar(2, 4));
        $this->addPrimarySystem(new Engine(4, 20, 0, 10, 4));
		
        $this->addFrontSystem(new Thruster(4, 14, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 14, 0, 4, 1));
		$this->addFrontSystem(new NexusDefenseGun(2, 4, 1, 270, 90));
		$this->addFrontSystem(new NexusXRayLaser(3, 5, 2, 300, 60));
		$this->addFrontSystem(new EWHeavyRocketLauncher(3, 6, 2, 300, 60));
		$this->addFrontSystem(new NexusXRayLaser(3, 5, 2, 300, 60));
		$this->addFrontSystem(new NexusDefenseGun(2, 4, 1, 270, 90));

        $this->addAftSystem(new Thruster(3, 16, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 5, 2));
		$this->addAftSystem(new CargoBay(3, 10));
		$this->addAftSystem(new NexusDefenseGun(2, 4, 1, 180, 360));
		$this->addAftSystem(new NexusXRayLaser(3, 5, 2, 180, 300));
		$this->addAftSystem(new JumpEngine(3, 20, 4, 35));
		$this->addAftSystem(new NexusXRayLaser(3, 5, 2, 60, 180));
		$this->addAftSystem(new NexusDefenseGun(2, 4, 1, 0, 180));
		$this->addAftSystem(new CargoBay(3, 10));

		$this->addLeftSystem(new NexusDefenseGun(2, 4, 1, 180, 360));
		$this->addLeftSystem(new EWHeavyRocketLauncher(3, 6, 2, 240, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

		$this->addRightSystem(new NexusDefenseGun(2, 4, 1, 0, 180));
		$this->addRightSystem(new EWHeavyRocketLauncher(3, 6, 2, 0, 120));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 48));
        $this->addRightSystem(new Structure(4, 48));
        $this->addPrimarySystem(new Structure(4, 48));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Defense Gun",
					8 => "X-Ray Laser",
					10 => "Heavy Rocket Launcher",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					4 => "Thruster",
					5 => "Cargo Bay",
					7 => "Defense Gun",
					9 => "X-Ray Laser",
					11 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Heavy Rocket Launcher",
					9 => "Defense Gun",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Heavy Rocket Launcher",
					9 => "Defense Gun",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
