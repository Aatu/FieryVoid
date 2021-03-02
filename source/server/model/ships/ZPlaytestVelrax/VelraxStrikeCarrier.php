<?php
class VelraxStrikeCarrier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 525;
	$this->faction = "ZPlaytest Velrax";
        $this->phpclass = "VelraxStrikeCarrier";
        $this->imagePath = "img/ships/Playtest/VelraxJumpCarrier.png";
        $this->shipClass = "Strike Carrier";
        $this->shipSizeClass = 3;
		$this->canvasSize = 150; //img has 200px per side
		$this->limited = 33;
		$this->unofficial = true;

        $this->fighters = array("light"=>18, "heavy"=>6);

		$this->isd = 2057;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(2, 6));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new NexusDartInterceptor(3, 4, 1, 180, 60));
		$this->addFrontSystem(new PlasmaWaveTorpedo(3, 7, 4, 300, 60));
		$this->addFrontSystem(new PlasmaWaveTorpedo(3, 7, 4, 300, 60));
        $this->addFrontSystem(new NexusDartInterceptor(3, 4, 1, 300, 180));

        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
		$this->addAftSystem(new JumpEngine(4, 20, 6, 35));
        $this->addAftSystem(new NexusLightParticleArray(2, 2, 2, 120, 300));
        $this->addAftSystem(new NexusHeavyLaserSpear(3, 6, 4, 180, 240));
        $this->addAftSystem(new NexusHeavyLaserSpear(3, 6, 4, 120, 180));
        $this->addAftSystem(new NexusLightParticleArray(2, 2, 2, 60, 240));

		$this->addLeftSystem(new NexusHeavyLaserSpear(3, 6, 4, 300, 60));
		$this->addLeftSystem(new NexusLaserSpear(3, 5, 3, 180, 360));
        $this->addLeftSystem(new NexusLightParticleArray(2, 2, 2, 240, 60));
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addLeftSystem(new Hangar(2, 7));
		$this->addLeftSystem(new Hangar(2, 7));

		$this->addRightSystem(new NexusHeavyLaserSpear(3, 6, 4, 300, 60));
		$this->addRightSystem(new NexusLaserSpear(3, 5, 3, 0, 180));
        $this->addRightSystem(new NexusLightParticleArray(2, 2, 2, 300, 120));
        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
		$this->addRightSystem(new Hangar(2, 7));
		$this->addRightSystem(new Hangar(2, 7));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 50));
        $this->addAftSystem(new Structure(3, 40));
        $this->addLeftSystem(new Structure(3, 45));
        $this->addRightSystem(new Structure(3, 45));
        $this->addPrimarySystem(new Structure(4, 50));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					12 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					6 => "Thruster",
					8 => "Plasma Wave",
					10 => "Dart Interceptor",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Heavy Laser Spear",
					9 => "Light Particle Array",
					12 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					6 => "Light Particle Array",
					8 => "Heavy Laser Spear",
					9 => "Laser Spear",
					12 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					6 => "Light Particle Array",
					8 => "Heavy Laser Spear",
					10 => "Laser Spear",
					12 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
