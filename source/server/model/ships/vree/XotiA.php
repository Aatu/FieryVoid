<?php
class XotiA extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 225;
		$this->faction = "Vree Conglomerate";
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

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 7, 2, 7));
		$this->addAftSystem(new Thruster(4, 6, 0, 0, 2));
		$this->addFrontSystem(new AntimatterCannon(4, 9, 8, 270, 90));
		$this->addFrontSystem(new AntiprotonDefender(2, 4, 3, 180, 360));
		$this->addFrontSystem(new AntiprotonDefender(2, 4, 3, 0, 180));
		$this->addFrontSystem(new AntiprotonGun(3, 8, 4, 270, 90));
		$this->addFrontSystem(new AntiprotonGun(3, 8, 4, 270, 90));


		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 33));

			$this->hitChart = array( //Vree OSATs actually do NOT use "Weapon" tag on hit chart!
                0=> array(
                        9 => "Structure",
                        11 => "2:Thruster",
						12 => "1:Antimatter Cannon",
						14 => "1:Antiproton Gun",
						16 => "1:Antiproton Defender",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );

	}

}

?>