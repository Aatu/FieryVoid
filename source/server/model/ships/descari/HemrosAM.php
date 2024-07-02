<?php
class HemrosAM extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 200;
		$this->faction = "Descari Committees";
		$this->phpclass = "HemrosAM";
		$this->shipClass = "Hemros Orbital Satellite";
		$this->imagePath = "img/ships/DescariHemsar.png";
		$this->canvasSize = 80;
		$this->isd = 2242;

		$this->forwardDefense = 10;
		$this->sideDefense = 10;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;
		
		
		
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
		$this->enhancementOptionsEnabled[] = 'AMMO_I';//add enhancement options for other missiles - Class-I	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		$this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X				

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(4, 7, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 7, 2, 4));
		$this->addAftSystem(new Thruster(3, 6, 0, 0, 2));
		
		$this->addFrontSystem(new MediumPlasmaBolter(3, 0, 0, 270, 90));
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addPrimarySystem(new Structure(4, 25));

			$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "2:Thruster",
						13 => "1:Class-S Missile Rack",
						14 => "1:Medium Plasma Bolter",
						16 => "1:Light Particle Beam",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );

	}

}

?>
