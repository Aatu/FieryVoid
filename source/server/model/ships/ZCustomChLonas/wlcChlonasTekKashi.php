
<?php
class wlcChlonasTekKashi extends HeavyCombatVesselLeftRight
{
/*Ch'Lonas Tek'Kashi Destroyer Leader (Es'Kashi variant), variant ISD 2250*/
	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 540;
		$this->phpclass = "wlcChlonasTekKashi";
		$this->imagePath = "img/ships/ChlonasEsKashi.png";
		$this->canvasSize = 200;
		$this->shipClass = "Tek'Kashi Destroyer Leader";

		$this->faction = "Ch'Lonas";
		$this->variantOf = "Es'Kashi Destroyer";
		$this->occurence = "rare";
		$this->isd = 2250;
		$this->unofficial = true;

		$this->forwardDefense = 13;
		$this->sideDefense = 14;

		$this->turncost = 0.66;
		$this->turndelaycost = 0.50;
		$this->accelcost = 2;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 7 *5;


		$this->addPrimarySystem(new Reactor(5, 17, 0, 0));
		$this->addPrimarySystem(new CnC(5, 15, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 13, 4, 7));
		$this->addPrimarySystem(new Engine(5, 11, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new Thruster(4, 15, 0, 4, 1));
		$this->addPrimarySystem(new Thruster(5, 21, 0, 8, 2));
		$this->addPrimarySystem(new CustomPulsarLaser(4, 0, 0, 300, 60));
		$this->addPrimarySystem(new CustomPulsarLaser(4, 0, 0, 300, 60));

		$this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
		$this->addLeftSystem(new CustomGatlingMattergunMedium(3, 0, 0, 240, 360));
		$this->addLeftSystem(new AssaultLaser(3, 6, 4, 240, 60));
		$this->addLeftSystem(new CustomGatlingMattergunLight(2, 0, 0, 180, 360));

		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
		$this->addRightSystem(new CustomGatlingMattergunMedium(3, 0, 0, 0, 120));
		$this->addRightSystem(new AssaultLaser(3, 6, 4, 300, 120));
		$this->addRightSystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 180));


		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addLeftSystem(new Structure( 4, 44));
		$this->addRightSystem(new Structure( 4, 44));
		$this->addPrimarySystem(new Structure( 5, 36 ));


		//d20 hit chart
		$this->hitChart = array(
			0=> array(
				5 => "Structure",
				7 => "Pulsar Laser",
				11 => "Thruster",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			
			3=> array(
				4 => "Thruster",
				7 => "Assault Laser",
				10 => "Gatling Mattergun",
				12 => "Light Gatling Mattergun",
				18 => "Structure",
				20 => "Primary",
			),
			
			4=> array(
				4 => "Thruster",
				7 => "Assault Laser",
				10 => "Gatling Mattergun",
				12 => "Light Gatling Mattergun",
				18 => "Structure",
				20 => "Primary",
			),
		);
	}
}

?>