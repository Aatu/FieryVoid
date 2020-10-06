<?php
class Hemsar extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 125;
		$this->faction = "Descari";
		$this->phpclass = "Hemsar";
		$this->shipClass = "Hemsar Orbital Satellite";
		$this->imagePath = "img/ships/DescariHemsar.png";
		$this->canvasSize = 80;
		$this->isd = 2228;

		$this->forwardDefense = 10;
		$this->sideDefense = 10;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;
		
		$this->addPrimarySystem(new Reactor(4, 7, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 7, 2, 4));
		$this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2));
		$this->addPrimarySystem(new DualPlasmaCannon(3, 0, 0, 270, 90));
		$this->addPrimarySystem(new LightPlasma(3, 4, 2, 270, 90));
		$this->addPrimarySystem(new LightPlasma(3, 4, 2, 270, 90));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 25));

			$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "Thruster",
						12 => "DualPlasmaCannon",
						14 => "Light Plasma Cannon",
						16 => "Light Particle Beam",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );

	}

}

?>