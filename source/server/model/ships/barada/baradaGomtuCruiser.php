<?php
class baradaGomtuCruiser extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 725;
		$this->faction = "Barada Imperium";
		$this->shipClass = "Gomtu Cruiser";
		$this->phpclass = "baradaGomtuCruiser";
		$this->imagePath = "img/ships/baradaGomtuCruiser.png";
		$this->canvasSize = 220;
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>24);
		$this->isd = 2247;
		$this->unofficial = true;


		$this->forwardDefense = 14;
		$this->sideDefense = 16;

		$this->turncost = 1.0;
		$this->turndelaycost = 1.0;
		$this->accelcost = 3;
		$this->rollcost = 3;
		$this->pivotcost = 3;
		$this->iniativebonus = 0 *5;

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(6, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 20, 4, 5));
		$this->addPrimarySystem(new Engine(5, 16, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(5, 26, 12));
		
        $this->addFrontSystem(new HeavyParticleBeam(3, 6, 2, 270, 90));
        $this->addFrontSystem(new HeavyParticleBeam(3, 6, 2, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
			
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));

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

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(5, 56));
		$this->addAftSystem(new Structure(5, 52));
		$this->addLeftSystem(new Structure(5, 50));
		$this->addRightSystem(new Structure(5, 50));
		$this->addPrimarySystem(new Structure(5, 55));

		$this->hitChart = array(
			0=> array(
				11 => "Structure",
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
				8 => "Thruster",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				5 => "Thruster",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				5 => "Thruster",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}



?>
