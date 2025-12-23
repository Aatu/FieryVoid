<?php
class ChoukaBrimstoneHeavyOSATAM extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 350;
		$this->faction = "Escalation Wars Chouka Theocracy";
		$this->phpclass = "ChoukaBrimstoneHeavyOSATAM";
		$this->shipClass = "Brimstone Heavy Orbital Satellite";
        $this->imagePath = "img/ships/EscalationWars/ChoukaHellfireOSAT.png";
        $this->canvasSize = 90;
		$this->isd = 1968;
        $this->unofficial = true;

		$this->forwardDefense = 13;
		$this->sideDefense = 13;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(80); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 80); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 5, 5));
		$this->addAftSystem(new Thruster(2, 10, 0, 0, 2));
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 240, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 240, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new EWTwinLaserCannon(2, 8, 4, 270, 90));
		$this->addFrontSystem(new EWTwinLaserCannon(2, 8, 4, 270, 90));
   		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60)); 
       	$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60)); 
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 3, 0, 360));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 3, 0, 360));
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 0, 120, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 0, 120, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 60));

			$this->hitChart = array(
                0=> array(
					6 => "Structure",
					8 => "2:Thruster",
					11 => "1:Heavy Plasma Cannon",
					13 => "1:Twin Laser Cannon",
					15 => "Class-S Missile Rack",
					16 => "2:Heavy Point Plasma Gun",
					18 => "Scanner",
					20 => "Reactor",
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