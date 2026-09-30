<?php
class MakarNollonRefit extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 355;
        $this->faction = "Nexus Makar Federation";
        $this->phpclass = "MakarNollonRefit";
        $this->imagePath = "img/ships/Nexus/makar_brassert2.png";
        $this->shipClass = "Nollon Assault Frigate (2109)";
			$this->variantOf = "Brassert Frigate";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 80;
//        $this->agile = true;
	    $this->isd = 2109;

	    $this->notes = 'Atmospheric capable';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));        
        $this->addPrimarySystem(new Hangar(2, 1, 1));
        
		$this->addFrontSystem(new NexusXRayLaser(3, 5, 2, 300, 60));
		$this->addFrontSystem(new GrapplingClaw(3, 0, 0, 300, 60, 6, false));
		$this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 120));
		$this->addFrontSystem(new GrapplingClaw(3, 0, 0, 300, 60, 6, false));
		$this->addFrontSystem(new NexusXRayLaser(3, 5, 2, 300, 60));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
	    
		$this->addAftSystem(new NexusLightXRayLaser(1, 3, 1, 180, 360));
		$this->addAftSystem(new NexusLightXRayLaser(1, 3, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));    
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));    
        
        $this->addPrimarySystem(new Structure(4, 58));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			10 => "Hangar",
			14 => "Scanner",
			17 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			6 => "Grappling Claw",
			8 => "Rocket Launcher",
			10 => "X-Ray Laser",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			9 => "Light X-Ray Laser",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
