<?php
class HurrmissileOSAT2220 extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 200;
		$this->faction = "Hurr";
		$this->phpclass = "HurrmissileOSAT2220";
		$this->shipClass = "Missile Satellite (2220)";
		$this->imagePath = "img/ships/HurrOSAT.png";
		$this->canvasSize = 200;
		$this->isd = 2220;
		$this->occurence = "common";
		$this->variantOf = "Missile Satellite (2240)";

		$this->forwardDefense = 10;
		$this->sideDefense = 10;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;
		
		$this->addPrimarySystem(new Reactor(3, 5, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 5, 2, 4));
		$this->addPrimarySystem(new Thruster(3, 4, 0, 0, 2));
		$this->addPrimarySystem(new SoMissileRack(3, 6, 0, 270, 90));
		$this->addPrimarySystem(new SoMissileRack(3, 6, 0, 270, 90));
		$this->addPrimarySystem(new SoMissileRack(3, 6, 0, 270, 90));
		$this->addPrimarySystem(new SoMissileRack(3, 6, 0, 270, 90));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(3, 26));
		
			$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Thruster",
						15 => "Class-SO Missile Rack",
						17 => "Scanner",
                        19 => "Reactor",
                        20 => "Standard Particle Beam",
                ),
        );
		
	}
}

?>