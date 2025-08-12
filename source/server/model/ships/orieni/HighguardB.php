<?php
class HighguardB extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 140;
		//$this->faction = "Orieni Imperium (defenses)";
        $this->faction = "Orieni Imperium";	
		$this->phpclass = "HighguardB";
		$this->shipClass = "Highguard-B Orbital Satellite (1803)";
			$this->variantOf = "Highguard-B Orbital Satellite";		
		$this->imagePath = "img/ships/OrieniHighguardOSAT.png";
		$this->canvasSize = 80;
		$this->isd = 1803;

		$this->forwardDefense = 9;
		$this->sideDefense = 9;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(4, 6, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 4, 2, 4));
		$this->addAftSystem(new Thruster(4, 4, 0, 0, 2));
		$this->addFrontSystem(new LaserLance(3, 6, 0, 270, 90));
		$this->addFrontSystem(new LaserLance(3, 6, 0, 270, 90));
		$this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
		$this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 20));

			$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "2:Thruster",
						14 => "1:Laser Lance",
						16 => "1:Gatling Railgun",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );

	}

}

?>