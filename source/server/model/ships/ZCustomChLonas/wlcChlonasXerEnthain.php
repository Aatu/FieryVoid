<?php
class wlcChlonasXerEnthain extends BaseShipNoAft
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 1050;
		$this->phpclass = "wlcChlonasXerEnthain";
		$this->imagePath = "img/ships/ChlonasXerEnthain.png";
		$this->canvasSize = 200;
		$this->shipClass = "Xer'Enthain Battlecruiser";
		$this->fighters = array("heavy" => 24);
		$this->forwardDefense = 16;
		$this->sideDefense = 19;
		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 3;
		$this->pivotcost = 3;

		$this->faction = "Ch'Lonas";
		$this->isd = 2254;
		$this->unofficial = true;
		$this->limited = 10;
		$this->occurence = "unique";
		$this->iniativebonus = 1*5;
		
		
		$this->addPrimarySystem(new Reactor(5, 25, 0, 0));
		$this->addPrimarySystem(new CnC(6, 18, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 6, 8));
		$this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(5, 10, 5, 40));
		$this->addPrimarySystem(new Hangar(4, 26));
		$this->addPrimarySystem(new Thruster(5, 24, 0, 12, 2));

		$this->addFrontSystem(new AssaultLaser(4, 6, 4, 240, 360));
		$this->addFrontSystem(new CustomGatlingMattergunHeavy(5, 0, 0, 300, 60));
		$this->addFrontSystem(new CustomPulsarLaser(4, 0, 0, 300, 60));
		$this->addFrontSystem(new CustomGatlingMattergunHeavy(5, 0, 0, 300, 60));
		$this->addFrontSystem(new AssaultLaser(4, 6, 4, 0, 120));
		$this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));

		$this->addLeftSystem(new CustomStrikeLaser(3, 0, 0, 240, 0));
		$this->addLeftSystem(new CustomPulsarLaser(4, 0, 0, 240, 0));
		$this->addLeftSystem(new CustomGatlingMattergunMedium(4, 0, 0, 240, 60));
		$this->addLeftSystem(new CustomMatterStream(4, 0, 0, 240, 60));
		$this->addLeftSystem(new CustomGatlingMattergunLight(3, 0, 0, 180, 360));
		$this->addLeftSystem(new CustomGatlingMattergunLight(3, 0, 0, 180, 360));
		$this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));

		$this->addRightSystem(new CustomStrikeLaser(3, 0, 0, 0, 120));
		$this->addRightSystem(new CustomPulsarLaser(4, 0, 0, 0, 120));
		$this->addRightSystem(new CustomGatlingMattergunMedium(4, 0, 0, 300, 120));
		$this->addRightSystem(new CustomMatterStream(4, 0, 0, 300, 120));
		$this->addRightSystem(new CustomGatlingMattergunLight(3, 0, 0, 0, 180));
		$this->addRightSystem(new CustomGatlingMattergunLight(3, 0, 0, 0, 180));
		$this->addRightSystem(new Thruster(4, 15, 0, 6, 4));
		
		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure(5, 50));
		$this->addLeftSystem(new Structure(5, 55));
		$this->addRightSystem(new Structure(5, 55));
		$this->addPrimarySystem(new Structure(5, 50));


		$this->hitChart = array(

		0=> array(
		8 => "Structure",
		9 => "Jump Engine",
		11 => "Thruster",
		13 => "Scanner",
		15 => "Hangar",
		17 => "Engine",
		19 => "Reactor",
		20 => "C&C",
		),
		1=> array(
		4 => "Thruster",
		7 => "Heavy Gatling Mattergun",
		8 => "Pulsar Laser",
		10 => "Assault Laser",
		18 => "Structure",
		20 => "Primary",
		),
		3=> array(
		3 => "Thruster",
		5 => "Matter Stream",
		7 => "Pulsar Laser",
		9 => "Gatling Mattergun",
		10 => "Strike Laser",
		12 => "Light Gatling Mattergun",
		18 => "Structure",
		20 => "Primary",
		),
		4=> array(
		3 => "Thruster",
		5 => "Matter Stream",
		7 => "Pulsar Laser",
		9 => "Gatling Mattergun",
		10 => "Strike Laser",
		12 => "Light Gatling Mattergun",
		18 => "Structure",
		20 => "Primary",
		),
		);
	}
}
?>
