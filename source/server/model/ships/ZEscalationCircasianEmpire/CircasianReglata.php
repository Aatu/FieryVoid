<?php
class CircasianReglata extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianReglata";
        $this->imagePath = "img/ships/EscalationWars/CircasianReglata.png";
        $this->shipClass = "Reglata Bombardment Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
		$this->limited = 10;


	$this->isd = 1950;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 13, 4, 6));
        $this->addPrimarySystem(new Engine(3, 13, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new CargoBay(3, 16));
   
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 300, 60));
        $this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));

        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
        $this->addAftSystem(new EWRocketLauncher(2, 4, 1, 120, 240));
        $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));

		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
        $this->addLeftSystem(new EWDualRocketLauncher(3, 6, 2, 240, 360));
        $this->addLeftSystem(new EWHeavyRocketLauncher(3, 6, 2, 240, 360));
        $this->addLeftSystem(new EWHeavyRocketLauncher(3, 6, 2, 240, 360));
        $this->addLeftSystem(new Thruster(2, 13, 0, 4, 3));
		
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));
        $this->addRightSystem(new EWDualRocketLauncher(3, 6, 2, 0, 120));
        $this->addRightSystem(new EWHeavyRocketLauncher(3, 6, 2, 0, 120));
        $this->addRightSystem(new EWHeavyRocketLauncher(3, 6, 2, 0, 120));
        $this->addRightSystem(new Thruster(2, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 44));
        $this->addAftSystem(new Structure(3, 48));
        $this->addLeftSystem(new Structure(3, 36));
        $this->addRightSystem(new Structure(3, 36));
        $this->addPrimarySystem(new Structure(3, 40));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Cargo Bay",
					12 => "Scanner",
					14 => "Engine",
					16 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Rocket Launcher",
					9 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					7 => "Rocket Launcher",
					9 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Dual Rocket Launcher",
					9 => "Heavy Rocket Launcher",
					10 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Dual Rocket Launcher",
					9 => "Heavy Rocket Launcher",
					10 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
