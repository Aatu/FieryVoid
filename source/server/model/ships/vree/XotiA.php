<?php
class XotiA extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 225;
		$this->faction = "Vree";
		$this->phpclass = "XotiA";
		$this->shipClass = "Xoti Orbital Satellite A";
		$this->imagePath = "img/ships/VreeXoti.png";
		$this->canvasSize = 80;
		$this->isd = 2203;

		$this->forwardDefense = 9;
		$this->sideDefense = 9;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;
		
		$this->addPrimarySystem(new Reactor(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 7, 2, 7));
		$this->addPrimarySystem(new Thruster(4, 6, 0, 0, 2));
		$this->addPrimarySystem(new AntimatterCannon(4, 9, 8, 270, 90));
		$this->addPrimarySystem(new AntiprotonDefender(2, 4, 3, 180, 360));
		$this->addPrimarySystem(new AntiprotonDefender(2, 4, 3, 0, 180));
		$this->addPrimarySystem(new AntiprotonGun(3, 8, 4, 270, 90));
		$this->addPrimarySystem(new AntiprotonGun(3, 8, 4, 270, 90));


		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 33));

			$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Thruster",
						12 => "Antimatter Cannon",
						14 => "Antiproton Gun",
						16 => "Antiproton Defender",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );

	}

}

?>