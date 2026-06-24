<?php
class skywatchGCAM extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 700;
        $this->faction = "Great Crusade Orieni Imperium";	
		$this->phpclass = "skywatchGCAM";
		$this->shipClass = "Skywatch Heavy Orbital Satellite (2260)";
		$this->imagePath = "img/ships/GCskywatch.png";
		$this->canvasSize = 165;
		$this->isd = 2260;

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
		
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(240); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 240); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_KK';//add enhancement options for other missiles - Class-KK               
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C
		
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 3, 8));
		$this->addAftSystem(new Thruster(4, 14, 0, 0, 2));
        $this->addPrimarySystem(new HKControlNodeOrieni(4, 12, 1, 1));
		
		$this->addFrontSystem(new AmmoMissileRackB(5, 9, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackB(5, 9, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackB(5, 9, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackB(5, 9, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		
        $this->addFrontSystem(new WarLance(5, 9, 6, 270, 90));
        $this->addFrontSystem(new WarLance(5, 9, 6, 270, 90));
		
		$this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
		$this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 0, 360));
		$this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 0, 360));
		$this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 86));

			$this->hitChart = array(
                0=> array(
					8 => "Structure",
                    10 => "2:Thruster",
					13 => "1:Class-B Missile Rack",
					15 => "1:War Lance",
					17 => "1:Improved Gatling Railgun",
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