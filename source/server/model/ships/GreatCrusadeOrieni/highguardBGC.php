<?php
class highguardBGC extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 250;
		//$this->faction = "Orieni Imperium (defenses)";
        $this->faction = "Great Crusade Orieni Imperium";	
		$this->phpclass = "highguardBGC";
		$this->shipClass = "Highguard-B Orbital Satellite (2248)";
		$this->imagePath = "img/ships/GChighguard.png";
		$this->canvasSize = 100;
		$this->isd = 2248;

		$this->unofficial = true;

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
		$this->addPrimarySystem(new Scanner(4, 4, 2, 7));
		$this->addAftSystem(new Thruster(4, 4, 0, 0, 2));
		$this->addFrontSystem(new WarLance(3, 9, 6, 270, 90));
		$this->addFrontSystem(new WarLance(3, 9, 6, 270, 90));
		$this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
		$this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 20));

		//Block some enhancements for OSAT units when bought
		Enhancements::nonstandardEnhancementSet($this, 'OSAT');

			$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "2:Thruster",
						14 => "1:War Lance",
						16 => "1:Improved Gatling Railgun",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );

	}

}

?>