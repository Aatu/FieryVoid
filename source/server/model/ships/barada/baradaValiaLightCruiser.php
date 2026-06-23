<?php
class baradaValiaLightCruiser extends BaseShip{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 475;
		$this->faction = "Barada Imperium";
		$this->shipClass = "Valia Light Cruiser";
		$this->phpclass = "baradaValiaLightCruiser";
		$this->imagePath = "img/ships/baradaValiaLightCruiser.png";
		$this->canvasSize = 190;
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>12);
		$this->isd = 2224;
		$this->unofficial = true;


		$this->forwardDefense = 11;
		$this->sideDefense = 13;

		$this->turncost = 1.0;
		$this->turndelaycost = 1.0;
		$this->accelcost = 2;
		$this->rollcost = 3;
		$this->pivotcost = 3;
		$this->iniativebonus = 0 *5;

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5,16, 4, 5));
		$this->addPrimarySystem(new Engine(5, 16, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(5, 14, 12));
		
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 0, 360));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 0, 360));
		
        $this->addFrontSystem(new HeavyParticleBeam(3, 6, 2, 270, 90));
        $this->addFrontSystem(new HeavyParticleBeam(3, 6, 2, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1,  270, 90));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1,  270, 90));

		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));

		

		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));


		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new Thruster(4, 6, 0, 2, 3));
		$this->addLeftSystem(new Thruster(4, 6, 0, 2, 3));
		$this->addLeftSystem(new Thruster(4, 6, 0, 2, 3));


		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new Thruster(4, 6, 0, 2, 4));
		$this->addRightSystem(new Thruster(4, 6, 0, 2, 4));
		$this->addRightSystem(new Thruster(4, 6, 0, 2, 4));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(5, 52));
		$this->addAftSystem(new Structure(5, 48));
		$this->addLeftSystem(new Structure(5, 45));
		$this->addRightSystem(new Structure(5, 45));
		$this->addPrimarySystem(new Structure(5, 50));

		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Standard Particle Beam",
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
				9 => "Thruster",
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
