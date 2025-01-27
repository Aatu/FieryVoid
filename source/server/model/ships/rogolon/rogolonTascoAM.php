<?php
class RogolonTascoAM extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 125;
		$this->faction = "Rogolon Dynasty";
		$this->phpclass = "RogolonTascoAM";
		$this->shipClass = "Tasco OSAT";
		$this->imagePath = "img/ships/RogolonTasco.png";
		$this->canvasSize = 80;
		$this->isd = 1950;

		$this->forwardDefense = 9;
		$this->sideDefense = 10;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;
		
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 24); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
		//Rogolons have ONLY Heavy Missiles available (besides Basic)

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(4, 6, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 6, 2, 4));
		$this->addAftSystem(new Thruster(4, 5, 0, 0, 2));
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 25));
		
			$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "2:Thruster",
						13 => "1:Heavy Plasma Cannon",
						16 => "1:Class-SO Missile Rack",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );
		
	}
}

?>