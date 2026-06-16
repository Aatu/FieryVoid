<?php
class baradaGilazFleetCarrier extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 850;
		$this->faction = "Barada Imperium";
		$this->shipClass = "Gilaz Fleet Carrier";
		$this->phpclass = "baradaGilazFleetCarrier";
		$this->imagePath = "img/ships/baradaGilazFleetCarrier.png";
		$this->canvasSize = 280;
		$this->shipSizeClass = 3;
		$this->limited = 33;
		$this->fighters = array("LCVs"=>4,"normal"=>72);
		$this->isd = 2217;
		$this->unofficial = true;


		$this->forwardDefense = 15;
		$this->sideDefense = 17;

		$this->turncost = 1.0;
		$this->turndelaycost = 1.0;
		$this->accelcost = 4;
		$this->rollcost = 3;
		$this->pivotcost = 3;
		$this->iniativebonus = 0 *5;

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 20, 3, 5));
		$this->addPrimarySystem(new Engine(5, 16, 0, 4, 4));
		$GilazHangar = new Hangar(5, 74, 12);
		$GilazHangar->directions = array(0, 1, 5); 
		$this->addPrimarySystem($GilazHangar);
		$this->addPrimarySystem(new JumpEngine(5, 10, 2, 40)); 


        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
			
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));


        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
		$this->addLeftSystem(new Thruster(4, 6, 0, 2, 3));
		$this->addLeftSystem(new Thruster(4, 6, 0, 2, 3));
		$this->addLeftSystem(new Thruster(4, 6, 0, 2, 3));
		$LCVRail = new DockingCollar(3, 6);
        $LCVRail->displayName = "LCV Rail";
        $this->addLeftSystem($LCVRail);
        $LCVRail = new DockingCollar(3, 6);
        $LCVRail->displayName = "LCV Rail";
        $this->addLeftSystem($LCVRail);

        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
		$this->addRightSystem(new Thruster(4, 6, 0, 2, 4));
		$this->addRightSystem(new Thruster(4, 6, 0, 2, 4));
		$this->addRightSystem(new Thruster(4, 6, 0, 2, 4));
		$LCVRail = new DockingCollar(3, 6);
        $LCVRail->displayName = "LCV Rail";
        $this->addRightSystem($LCVRail);
        $LCVRail = new DockingCollar(3, 6);
        $LCVRail->displayName = "LCV Rail";
        $this->addRightSystem($LCVRail);

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(5, 62));
		$this->addAftSystem(new Structure(5, 58));
		$this->addLeftSystem(new Structure(5, 55));
		$this->addRightSystem(new Structure(5, 55));
		$this->addPrimarySystem(new Structure(4, 50));

		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Jump Engine",
				13 => "Scanner",
				15 => "Engine",
				18 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				7 => "Heavy Particle Beam",
				10 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				7 => "Thruster",
				10 => "Light Particle Beam",
				13 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				5 => "Thruster",
				7 => "Light Particle Beam",
				11 => "Light Particle Beam",
				13 => "LCV Rail",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				5 => "Thruster",
				7 => "Light Particle Beam",
				11 => "Light Particle Beam",
				13 => "LCV Rail",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}



?>
