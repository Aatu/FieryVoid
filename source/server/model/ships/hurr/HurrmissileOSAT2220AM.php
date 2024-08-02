<?php
class HurrmissileOSAT2220AM extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 200;
		$this->faction = "Hurr Republic";
		$this->phpclass = "HurrmissileOSAT2220AM";
		$this->shipClass = "Missile Satellite (2220)";
		$this->imagePath = "img/ships/HurrOSAT.png";
		$this->canvasSize = 80;
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
		
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(48); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 48); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		//Hurr developed their missiles from Dilgar tech - they have L,H,F and A missiles (even if only after Dilgar War)
		//they developed P missiles as well (just before Show era), but they remain very rare (tabletop limit of 1 per ship (2 per dedicated missile ship)). I opted to skip these missiles instead. 

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(3, 5, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 5, 2, 4));
		$this->addAftSystem(new Thruster(3, 4, 0, 0, 2));
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(3, 26));
		
			$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "2:Thruster",
						15 => "Class-SO Missile Rack",
						17 => "Scanner",
                        19 => "Reactor",
                        20 => "Standard Particle Beam",
                ),
        );
		
	}
}

?>