<?php
class skywatch extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 600;
		$this->faction = "Orieni";
		$this->phpclass = "skywatch";
		$this->shipClass = "Skywatch Heavy Orbital Satellite";
		$this->imagePath = "img/ships/OrieniSkywatchOSAT.png";
		$this->canvasSize = 150;
		$this->isd = 2007;

        $this->fighters = array("medium"=>6);
		$this->notes = "Hunter-killer fighters only.";

		$this->forwardDefense = 12;
		$this->sideDefense = 12;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 60;
		
		$this->addPrimarySystem(new Reactor(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 3, 6));
		$this->addPrimarySystem(new Thruster(4, 14, 0, 0, 2));
        $this->addPrimarySystem(new HKControlNode(4, 12, 1, 1));
		$this->addPrimarySystem(new SMissileRack(5, 6, 0, 270, 90, true));
		$this->addPrimarySystem(new SMissileRack(5, 6, 0, 270, 90, true));
        $this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 270, 90));
        $this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 270, 90));
		$this->addPrimarySystem(new SMissileRack(5, 6, 0, 270, 90, true));
		$this->addPrimarySystem(new SMissileRack(5, 6, 0, 270, 90, true));
		$this->addPrimarySystem(new RapidGatling(2, 4, 1, 180, 360));
		$this->addPrimarySystem(new RapidGatling(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new RapidGatling(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new RapidGatling(2, 4, 1, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 86));

			$this->hitChart = array(
                0=> array(
					8 => "Structure",
                    10 => "Thruster",
					13 => "Class-S Missile Rack",
					15 => "Heavy Laser Lance",
					17 => "Rapid Gatling Railgun",
					18 => "Scanner",
                    19 => "Reactor",
					20 => "HK Control Node",
                ),
				1=> array(
					20 => "Primary",
				),
				2=> array(
					20 => "Primary",
				),				
			);

	}

}

?>