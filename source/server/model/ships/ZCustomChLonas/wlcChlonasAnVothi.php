<?php
class wlcChlonasAnVothi extends BaseShipNoAft
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 650;
		$this->phpclass = "wlcChlonasAnVothi";
		$this->imagePath = "img/ships/ChlonasAnVothi.png";
		$this->canvasSize = 200;
		$this->shipClass = "An'Vothi Cruiser";
		$this->fighters = array("heavy" => 12);
		$this->forwardDefense = 15;
		$this->sideDefense = 16;
		$this->turncost = 0.75;
		$this->turndelaycost = 0.75;
		$this->accelcost = 3;
		$this->rollcost = 3;
		$this->pivotcost = 3;

		$this->faction = "Ch'Lonas";
		$this->isd = 2245;
		$this->unofficial = true;
		$this->limited = 33;
		$this->iniativebonus = 3 *5;
		
		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 17, 5, 7));
		$this->addPrimarySystem(new Engine(4, 14, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(4, 14));
		$this->addPrimarySystem(new Thruster(4, 22, 0, 10, 2));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));

		$this->addFrontSystem(new CustomPulsarLaser(4, 0, 0, 300, 60));
		$this->addFrontSystem(new CustomPulsarLaser(4, 0, 0, 300, 60));
		$this->addFrontSystem(new CustomGatlingMattergunLight(2, 0, 0, 240, 60));
		$this->addFrontSystem(new CustomGatlingMattergunLight(2, 0, 0, 300, 120));
		$this->addFrontSystem(new Thruster(4, 12, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 12, 0, 3, 1));

		$this->addLeftSystem(new AssaultLaser(3, 6, 4, 180, 300));
		$this->addLeftSystem(new AssaultLaser(3, 6, 4, 240, 0));
		$this->addLeftSystem(new CustomPulsarLaser(4, 0, 0, 240, 60));
		$this->addLeftSystem(new CustomGatlingMattergunLight(2, 0, 0, 180, 360));
		$this->addLeftSystem(new Thruster(4, 16, 0, 5, 3));

		$this->addRightSystem(new AssaultLaser(3, 6, 4, 0, 120));
		$this->addRightSystem(new AssaultLaser(3, 6, 4, 60, 180));
		$this->addRightSystem(new CustomPulsarLaser(4, 0, 0, 300, 120));
		$this->addRightSystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 180));
		$this->addRightSystem(new Thruster(4, 16, 0, 5, 4));
		
		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(5, 36));
		$this->addLeftSystem(new Structure(5, 54));
		$this->addRightSystem(new Structure(5, 54));
		$this->addPrimarySystem(new Structure(5, 56));


		$this->hitChart = array(
			0=> array(
				6 => "Structure",
				8 => "Light Particle Beam",
				11 => "Thruster",
				13 => "Scanner",
				15 => "Hangar",
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				7 => "Pulsar Laser",
				10 => "Light Gatling Mattergun",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				3 => "Thruster",
				5 => "Pulsar Laser",
				8 => "Assault Laser",
				10 => "Light Gatling Mattergun",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Thruster",
				5 => "Pulsar Laser",
				8 => "Assault Laser",
				10 => "Light Gatling Mattergun",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}
?>
